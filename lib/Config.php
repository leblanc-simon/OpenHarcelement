<?php

class Config
{
  private static $config = array();
  
  public static function add($datas)
  {
    foreach ($datas as $key => $value) {
      Config::set($key, $value, true);
    }
  }
  
  public static function set($name, $value, $force = true)
  {
    $name = (string)$name;
    
    if (isset(Config::$config[$name]) === true && $force === false) {
      return false;
    }
    
    Config::$config[$name] = $value;
    return true;
  }
  
  public static function get($name, $default = null)
  {
    $name = (string)$name;
    
    if (isset(Config::$config[$name]) === false) {
      return $default;
    }
    
    return Config::$config[$name];
  }
}