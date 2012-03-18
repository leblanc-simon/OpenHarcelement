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


class Harcelement
{
  const TABLE_NAME = 'harcelement';
  
  private $id;
  private $hash;
  private $name;
  private $email;
  private $email_victim;
  private $time;
  private $subject;
  private $message;
  private $next;
  private $active;
  private $number_send;
  
  public function __construct($id = 0, $hash = '', $name = '', $email = '', $email_victim = '', $time = '+1 days', $subject = '', $message = '', $next = null, $active = true, $number_send = 0)
  {
    $this->setId($id);
    $this->setHash($hash);
    $this->setName($name);
    $this->setEmail($email);
    $this->setEmailVictim($email_victim);
    $this->setTime($time);
    $this->setSubject($subject);
    $this->setMessage($message);
    $this->setNext($next);
    $this->setActive($active);
    $this->setNumberSend($number_send);
  }
  
  
  public function save()
  {
    $datas = array(
      'name'          => array('value' => $this->getName(), 'type' => PDO::PARAM_STR),
      'hash'          => array('value' => $this->getHash(), 'type' => PDO::PARAM_STR),
      'email'         => array('value' => $this->getEmail(), 'type' => PDO::PARAM_STR),
      'email_victim'  => array('value' => $this->getEmailVictim(), 'type' => PDO::PARAM_STR),
      'time'          => array('value' => $this->getTime(), 'type' => PDO::PARAM_STR),
      'subject'       => array('value' => $this->getSubject(), 'type' => PDO::PARAM_STR),
      'message'       => array('value' => $this->getMessage(), 'type' => PDO::PARAM_STR),
      'next'          => array('value' => $this->getNext(true), 'type' => PDO::PARAM_STR),
      'active'        => array('value' => $this->getActive(), 'type' => PDO::PARAM_BOOL),
      'number_send'   => array('value' => $this->getNumberSend(), 'type' => PDO::PARAM_INT),
    );
    
    if ($this->id !== 0) {
      $datas['id'] = array('value' => $this->getId(), 'type' => PDO::PARAM_INT);
    }
    
    return Connection::updateData(Harcelement::TABLE_NAME, $datas);
  }
  
  
  public function delete()
  {
    if ($this->id === 0) {
      return false;
    }
    
    return Connection::deleteData(Harcelement::TABLE_NAME, $this->id);
  }
  
  
  public function populate($datas)
  {
    $this->setId($datas['id']);
    $this->setHash($datas['hash']);
    $this->setName($datas['name']);
    $this->setEmail($datas['email']);
    $this->setEmailVictim($datas['email_victim']);
    $this->setTime($datas['time']);
    $this->setSubject($datas['subject']);
    $this->setMessage($datas['message']);
    $this->setNext($datas['next']);
    $this->setActive($datas['active']);
    $this->setNumberSend($datas['number_send']);
  }
  
  
  public function setId($v)
  {
    $this->id = (int)$v;
  }
  
  
  public function setNext($v)
  {
    if ($v !== null) {
      if ($v instanceof DateTime) {
        $this->next = $v;
      } else {
        $this->next = new DateTime($v);
      }
    } else {
      $this->next = null;
    }
  }
  
  
  public function setActive($v)
  {
    $this->active = (bool)$v;
  }
  
  
  public function setNumberSend($v)
  {
    $this->number_send = (int)$v;
  }
  
  public function getNext($to_string = false)
  {
    if ($this->next === null) {
      return null;
    }
    
    if ($to_string === true) {
      return $this->next->format('Y-m-d H:i:s');
    } else {
      return $this->next;
    }
  }
  
  
  public function __call($name, $arguments)
  {
    if (substr($name, 0, 3) === 'set') {
      $properties = preg_replace('/([A-Z])/e', "'_'.strtolower('\\1')", lcfirst(substr($name, 3)));
      if (property_exists($this, $properties) === false) {
        trigger_error(__CLASS__.'::'.$name.' doesn\'t exist.', E_USER_WARNING);
      }
      
      $this->$properties = (string)implode('', $arguments);
    } elseif (substr($name, 0, 3) === 'get') {
      $properties = preg_replace('/([A-Z])/e', "'_'.strtolower('\\1')", lcfirst(substr($name, 3)));
      if (property_exists($this, $properties) === false) {
        trigger_error(__CLASS__.'::'.$name.' doesn\'t exist.', E_USER_WARNING);
      }
      
      return $this->$properties;
    } else {
      trigger_error(__CLASS__.'::'.$name.' doesn\'t exist.', E_USER_WARNING);
    }
  }
}