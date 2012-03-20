<?php
/*
Copyright (c) 2012, Simon Leblanc
All rights reserved.

Redistribution and use in source and binary forms, with or without modification
, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
    this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice
    , this list of conditions and the following disclaimer in the documentation
     and/or other materials provided with the distribution.
    * Neither the name of the Simon Leblanc nor the names of its contributors
    may be used to endorse or promote products derived from this software
    without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/


/**
 * Classe gérant les tâches d'envoi de mail
 *
 * @package   OpenHarcelement
 * @author    Simon Leblanc <contact@leblanc-simon.eu>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 */
class Task
{
  /**
   * Tâche permettant d'envoyer les harcèlements en cours
   *
   * @access public
   * @static
   */
  public static function send()
  {
    $sql = 'SELECT * FROM harcelement WHERE active = :active AND (next <= :next OR next IS NULL)';
    $stmt = Connection::get()->prepare($sql);
    
    $date = new DateTime();
    $stmt->bindValue(':active', true, PDO::PARAM_BOOL);
    $stmt->bindValue(':next', $date->format('Y-m-d H:i:s'), PDO::PARAM_STR);
    if ($stmt->execute() === false) {
      throw new Exception('Impossible to get harcelement rows');
    }
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $harcelement = new Harcelement();
      $harcelement->populate($row);
      
      Task::sendMail($harcelement);
    }
  }
  
  
  /**
   * Tâche permettant d'envoyer un mail de confirmation à l'auteur du harcèlement
   *
   * @param   Harcelement   $harcelement  Le harcèlement concerné
   * @access  public
   * @static
   */
  public static function alertCreator(Harcelement $harcelement)
  {
    $email_tpl = Config::get('email_tpl');
    if ($email_tpl === null) {
      return;
    }
    
    $search = array('%%name%%', '%%hash%%', '%%email%%', '%%email_victim%%', '%%time%%', '%%subject%%', '%%message%%');
    $replace = array(
      $harcelement->getName(),
      $harcelement->getHash(),
      $harcelement->getEmail(),
      $harcelement->getEmailVictim(),
      $harcelement->getTime(),
      $harcelement->getSubject(),
      $harcelement->getMessage(),
    );
    $message = str_replace($search, $replace, $email_tpl);
    
    $to      = array($harcelement->getEmail() => $harcelement->getName());
    $from    = Config::get('from');
    $subject = 'Confirmation de votre harcèlement';
    $replyto = null;
    
    return Task::processMail($to, $from, $subject, $message, $replyto);
  }
  
  
  /**
   * Méthode permettant d'envoyer un mail de harcèlement et de préparer le suivant
   *
   * @param   Harcelement   $harcelement  Le harcèlement concerné
   * @access  private
   * @static
   */
  private static function sendMail(Harcelement $harcelement)
  {
    $to      = $harcelement->getEmailVictim();
    $from    = Config::get('from');
    $subject = $harcelement->getSubject();
    $message = $harcelement->getMessage();
    $replyto = array($harcelement->getEmail() => $harcelement->getName());
    
    if (Task::processMail($to, $from, $subject, $message, $replyto)) {
      $harcelement->setNumberSend($harcelement->getNumberSend() + 1);
      if ($harcelement->getNext() === null) {
        $next = new DateTime();
      } else {
        $next = $harcelement->getNext();
      }
      $harcelement->setNext($next->add(new DateInterval($harcelement->getTime())));
      $harcelement->save();
    }
  }
  
  
  /**
   * Méthode permettant d'envoyer un mail via SwiftMailer
   *
   * @param   string|array  $to       L'adresse mail vers laquelle on envoi le mail
   * @param   string|array  $from     L'adresse mail qui envoi le mail
   * @param   string        $subject  Le sujet du message
   * @param   string        $message  Le message
   * @param   string|array  $replyto  L'adresse mail où répondre
   * @return  bool                    Vrai si le mail a été envoyé, faux sinon
   * @access  private
   * @static
   */
  private static function processMail($to, $from, $subject, $message, $replyto = null)
  {
    try {
      $transport_bin = Config::get('sendmail_bin');
      if ($transport_bin === null) {
        $transport = Swift_MailTransport::newInstance();
      } else {
        $transport = Swift_SendmailTransport::newInstance($transport_bin);
      }
      
      $mailer = Swift_Mailer::newInstance($transport);
      
      $message = Swift_Message::newInstance($subject)
                  ->setFrom($from)
                  ->setTo($to)
                  ->setBody($message);
      
      if ($replyto !== null) {
        $message->setReplyTo($replyto);
      }
      
      $result = $mailer->send($message);
    } catch (Exception $e) {
      // Erreur lors de l'envoi du mail
      $result = 0;
    }
    
    if ($result > 0) {
      return true;
    }
    
    return false;
  }
}