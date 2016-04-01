<?php

class Database extends Utility {

  private $Handle;

  public function __construct() {

    //
  }

  public function getTag($tag_title) {

    // Select the given tag.
    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = '{$tag_title}'
      ORDER BY id DESC
      LIMIT 1
    ";

    $query = $this->Handle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or returned zero rows.
      Utility::displayError("failed to get {$tag_title} tag");
    }

    // Return the contents of the given tag.
    return $query->fetch(PDO::FETCH_OBJ)->body;
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

    // Perform the given query statement.
    $query = $this->Handle->query($statement);

    if (!$query) {

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

    $query = $this->Handle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Table doesn't exist.
      return false;
    }
    else {

      // Table exists.
      return true;
    }
  }
}

?>
