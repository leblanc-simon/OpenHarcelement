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
 * Classe gérant la configuration de l'application
 *
 * @package   OpenHarcelement
 * @author    Simon Leblanc <contact@leblanc-simon.eu>
 * @license   http://www.opensource.org/licenses/bsd-license.php BSD
 */
class Config
{
  /**
   * Propriété contenant l'ensemble de la configuration
   * @var   array
   * @access private
   * @static
   */
  private static $config = array();
  
  
  /**
   * Méthode permettant d'ajouter un ensemble de configuration en une fois
   *
   * @param   array   $datas  Le tableau contenant plusieurs configuration (écrase les anciennes conf)
   * @access  public
   * @static
   */
  public static function add($datas)
  {
    foreach ($datas as $key => $value) {
      Config::set($key, $value, true);
    }
  }
  
  
  /**
   * Méthode permettant d'ajouter une configuration
   *
   * @param   string  $name   Le nom de la configuration
   * @param   mixed   $value  La valeur de la configuration
   * @param   bool    $force  true pour forcer la mise à jour si la configuration existe déjà, false sinon
   * @return  bool            true si la mise à jour à bien eu lieu, false sinon
   * @access  public
   * @static
   */
  public static function set($name, $value, $force = true)
  {
    $name = (string)$name;
    
    if (isset(Config::$config[$name]) === true && $force === false) {
      return false;
    }
    
    Config::$config[$name] = $value;
    return true;
  }
  
  
  /**
   * Récupère une valeur de la configuration
   *
   * @param   string  $name     Le nom de la configuration à récupèrer
   * @param   mixed   $default  La valeur par défaut si la configuration est inexistante
   * @return  mixed             La valeur de la configuration
   * @access  public
   * @static
   */
  public static function get($name, $default = null)
  {
    $name = (string)$name;
    
    if (isset(Config::$config[$name]) === false) {
      return $default;
    }
    
    return Config::$config[$name];
  }
}