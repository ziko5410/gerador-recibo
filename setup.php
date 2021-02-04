<?php

require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

try {
  $dotenv->load();
}
catch (Exception $e) {}

?>
