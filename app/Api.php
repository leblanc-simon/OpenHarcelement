<?php

class Api
{
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