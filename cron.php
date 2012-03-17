#!/usr/bin/php
<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'include.php';

try {
  Task::send();
  exit(0);
} catch (Exception $e) {
  echo $e->getMessage();
  exit(1);
}