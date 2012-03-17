<?php
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