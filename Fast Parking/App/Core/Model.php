<?php

namespace App\Core;

class Model
{
  private static $conexao;

  public static function getConn()
  {
    if (!isset(self::$conexao)) {
      self::$conexao = new \PDO("mysql:host=localhost;port=3306;dbname=fastparking;", "root", "");
    }

    return self::$conexao;
  }
}
