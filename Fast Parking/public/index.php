<?php
  require_once("../vendor/autoload.php");

  date_default_timezone_set("America/Sao_Paulo");

  header("Content-type: application/json");

  new App\Core\Router();