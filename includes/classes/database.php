<?php

class Database extends Utility {

  private $Handle;

  public function __construct() {

    //
  }

  public function connect() {

    try {

      $data_source_name = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;

      // Attempt to connect.
      $this->Handle = new PDO($data_source_name, DB_USER, DB_PASS);
    }
    catch (PDOException $exception) {

      // Connection attempt failed.
      Utility::displayError("failed to connect to the database");
    }
  }

  public function getHandle() {

    return $this->Handle;
  }

  public function disconnect() {

    $this->Handle = null;
  }

  public function performQuery($statement) {

    $query = $this->Handle->query($statement);

    if (!$query) {

      // Query failed.
      return false;
    }

    // Query was successful.
    return true;
  }

  public function checkTableExistence($table_name) {

    $statement = "SHOW TABLES LIKE '" . DB_PREF . "{$table_name}'";

    $query = $this->Handle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Table doesn't exist.
      return false;
    }

    // Table exists.
    return true;
  }
}

?>
