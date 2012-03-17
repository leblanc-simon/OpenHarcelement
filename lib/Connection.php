<?php

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