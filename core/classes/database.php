<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class Database {

  private $Handle;

  public function __construct() {

    try {

      // Connect using MySQL.
      $data_source_name = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;

      // Attempt to connect.
      $this->Handle = new PDO($data_source_name, DB_USER, DB_PASS);
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

  public function performQuery($statement) {

    // Perform the given query statement.
    $Query = $this->Handle->query($statement);

    if (!$Query) {

      // Query failed.
      return false;
    }
    else {

      // Query was successful.
      return true;
    }
  }

  public function checkTableExistence($table_name) {

    // Check if the given table exists.
    $statement = "SHOW TABLES LIKE '" . DB_PREF . "{$table_name}'";

    $Query = $this->Handle->query($statement);

    if (!$Query || $Query->rowCount() == 0) {

      // The table doesn't exist.
      return false;
    }
    else {

      // Table exists.
      return true;
    }
  }
}

?>
