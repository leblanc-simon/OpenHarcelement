<?php

class Task
{
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
    
    mail($harcelement->getEmail(), 'Confirmation de votre harcelement', $message);
  }
  
  
  private static function sendMail(Harcelement $harcelement)
  {
    if (mail($harcelement->getEmailVictim(), $harcelement->getSubject(), $harcelement->getMessage())) {
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
}