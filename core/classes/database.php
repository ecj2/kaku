<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Database {

  private $Handle;

  public function __construct() {

    try {

      // Attempt to connect using MySQL.
      $this->Handle = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    }
    catch (PDOException $Exception) {

      // Failed to connect.
      $GLOBALS["Utility"]->displayError("failed to connect to the database");
    }
  }

  public function __destruct() {

    // Disconnect from the database.
    $this->Handle = null;
  }

  public function getHandle() {

    return $this->Handle;
  }
}

?>
