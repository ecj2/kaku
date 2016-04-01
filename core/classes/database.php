<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class Database {

  private $Handle;

  public function connect() {

    try {

      // MySQL will be used for the connection.
      $data_source_name = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;

      // Attempt to connect to the database.
      $this->Handle = new PDO($data_source_name, DB_USER, DB_PASS);
    }
    catch (PDOException $exception) {

      // Connection attempt failed.
      $GLOBALS["Utility"]->displayError("failed to connect to the database");
    }
  }

  public function disconnect() {

    // Disconnect from the database.
    $this->Handle = null;
  }

  public function getHandle() {

    return $this->Handle;
  }
}

?>
