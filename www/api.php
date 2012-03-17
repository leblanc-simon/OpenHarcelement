<?php

require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'include.php';

try {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = Api::set($_POST);
  } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $response = Api::cancel($_SERVER);
  } else {
    $response = Response::NOT_ALLOWED;
  }
} catch (Exception $e) {
  $response = Response::FATAL_ERROR;
  die($e->getMessage());
}

Response::send($response);