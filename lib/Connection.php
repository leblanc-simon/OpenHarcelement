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


class Connection
{
  private static $conn = null;
  
  public static function get()
  {
    if (Connection::$conn === null) {
      Connection::$conn = new PDO(Config::get('sql_dsn'), Config::get('sql_user'), Config::get('sql_pass'), Config::get('sql_options'));
      Connection::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    return Connection::$conn;
  }
  
  
  public static function updateData($table, $datas)
  {
    if (isset($datas['id']) === false) {
      $insert = true;
    } else {
      $insert = false;
    }
    
    $columns = array_keys($datas);
    $sql_column = '';
    $sql_value  = '';
    foreach ($columns as $column) {
      if (empty($sql_value) === false && $insert === true) {
        $sql_column .= ', ';
        $sql_value  .= ', ';
      } elseif (empty($sql_column) === false && $insert === false) {
        $sql_column .= ', ';
      }
      
      if ($insert === true) {
        $sql_column .= $column;
        $sql_value  .= ':'.$column;
      } else {
        $sql_column .= $column.' = :'.$column;
      }
    }
    
    if (isset($datas['id']) === false) {
      $sql = 'INSERT INTO '.$table.' ('.$sql_column.') VALUES ('.$sql_value.')';
    } else {
      $sql = 'UPDATE '.$table.' SET '.$sql_column.' WHERE id = :id';
    }
    
    $stmt = Connection::get()->prepare($sql);
    
    foreach ($datas as $column => $data) {
      $stmt->bindValue(':'.$column, $data['value'], $data['type']);
    }
    
    return $stmt->execute();
  }
  
  
  public static function deleteData($table, $id)
  {
    $sql = 'DELETE FROM '.$table.' WHERE id = :id';
    $stmt = Connection::get()->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    
    return $stmt->execute();
  }
}