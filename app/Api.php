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
 * Classe gérant l'api de l'application
 *
 * @package   OpenHarcelement
 * @author    Simon Leblanc <contact@leblanc-simon.eu>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 */
class Api
{
  /**
   * Méthode permettant l'ajout d'un harcèlement
   *
   * @param   array   $post   Le tableau contenant les données à ajouter
   * @return  int             Le code http à renvoyer (201 en cas de succès, sinon des codes HTTP d'erreur)
   * @access  public
   * @static
   */
  public static function set($post)
  {
    if (($response_code = Api::checkSet($post)) !== true) {
      return $response_code;
    }
    
    $harcelement = new Harcelement();
    $harcelement->setHash(sha1(rand(0, 9999999).microtime()));
    $harcelement->setName($post['name']);
    $harcelement->setEmail($post['email']);
    $harcelement->setEmailVictim($post['email_victim']);
    $harcelement->setTime($post['time']);
    $harcelement->setSubject($post['subject']);
    $harcelement->setMessage($post['message']);
    $harcelement->setActive(true);
    $harcelement->setNumberSend(0);
    
    if ($harcelement->save() === true) {
      Task::alertCreator($harcelement);
      return Response::CREATED;
    } else {
      return Response::ERROR;
    }
  }
  
  
  /**
   * Méthode permettant de supprimer un harcèlement
   *
   * @param   array   $server   Le tableau contenant les données du serveur (pour récupérer le QUERY_STRING)
   * @return  int               Le code http à renvoyer (200 en cas de succès, sinon des codes HTTP d'erreur)
   * @access  public
   * @static
   */
  public static function cancel($server)
  {
    if (isset($server['QUERY_STRING']) === false || empty($server['QUERY_STRING']) === true) {
      return Response::ERROR;
    }
    
    $sql = 'SELECT * FROM '.Harcelement::TABLE_NAME.' WHERE hash = :hash';
    $stmt = Connection::get()->prepare($sql);
    $stmt->bindValue(':hash', $server['QUERY_STRING'], PDO::PARAM_STR);
    if ($stmt->execute() === false) {
      return Response::ERROR;
    }
    
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row === false) {
      return Response::NOT_FOUND;
    }
    
    $harcelement = new Harcelement();
    $harcelement->populate($row);
    
    if ($harcelement->delete() === false) {
      return Response::ERROR;
    }
    
    return Response::DELETED;
  }
  
  
  /**
   * Méthode permettant de vérifier que les données pour l'ajout d'un harcèlement sont correctes
   *
   * @param   array   $post   Le tableau contenant les données à vérifier
   * @return  int|bool        Le code http d'erreur à renvoyer en cas d'erreur, true en cas de succès
   * @access  private
   * @static
   * 
   */
  private static function checkSet($post)
  {
    $required_datas = array(
      'name',
      'email',
      'email_victim',
      'time',
      'subject',
      'message',
    );
    
    foreach ($required_datas as $key) {
      if (isset($post[$key]) === false || empty($post[$key]) === true) {
        return Response::ERROR;
      }
    }
    
    if (filter_var($post['email'], FILTER_VALIDATE_EMAIL) === false) {
      return Response::ERROR;
    }
    
    if (filter_var($post['email_victim'], FILTER_VALIDATE_EMAIL) === false) {
      return Response::ERROR;
    }
    
    $available_time = Config::get('available_time');
    if ($available_time !== null && is_array($available_time) === true) {
      $available_time = array_keys($available_time);
      if (in_array($post['time'], $available_time) === false) {
        return Response::ERROR;
      }
    }
    
    return true;
  }
}