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

require_once __DIR__.DIRECTORY_SEPARATOR.'include.php';

$config = array(
  'sql_dsn' => 'sqlite:'.__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'db'.DIRECTORY_SEPARATOR.'OpenHarcelement.sqlite',
  'available_time' => array(
    'PT1M' => '1 minutes',
    'PT10M' => '10 minutes',
    'PT6H' => '6 heures',
    'P1D' => '1 jour',
  ),
  'email_tpl' => <<<EOF
Bonjour %%name%%,

Votre harcelement a bien été enregistré et commencera dans quelques minutes.

Vous avez choisi d'harceler l'adresse %%email_victim%%, si vous regretez ce
choix ou si vous souhaitez arrêter à un moment donné, il suffira d'appeler
l'api avec la methode DELETE et l'élément %%hash%%

Cordialement,
OpenHarcelement team
EOF
);

Config::add($config);