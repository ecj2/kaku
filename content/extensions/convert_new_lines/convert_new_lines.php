<?php

  class ConvertNewLines {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function manageHooks() {

    global $Hook;

    if (isset($_GET["post_url"])) {

      if ($Hook->hasAction("post_body")) {

        $Hook->addAction(

          "post_body",

          $this,

          "singleConvertNewLines",

          $Hook->getCallback("post_body")
        );
      }
      else {

        $Hook->addAction(

          "post_body",

          $this,

          "singleConvertNewLines"
        );
      }
    }
    else if (isset($_GET["page_url"])) {

      if ($Hook->hasAction("page_body")) {

        $Hook->addAction(

          "page_body",

          $this,

          "singleConvertNewLines",

          $Hook->getCallback("page_body")
        );
      }
      else {

        $Hook->addAction(

          "page_body",

          $this,

          "singleConvertNewLines"
        );
      }
    }
    else {

      if ($Hook->hasAction("post_bodies")) {

        $Hook->addAction(

          "post_bodies",

          $this,

          "latestConvertNewLines",

          $Hook->getCallback("post_bodies")
        );
      }
      else {

        $Hook->addAction(

          "post_bodies",

          $this,

          "latestConvertNewLines"
        );
      }
    }
  }

  public function singleConvertNewLines($callback_content = "") {

    if ($callback_content == "") {

      $statement = "";

      if (isset($_GET["post_url"])) {

        $statement = "

          SELECT body
          FROM " . DB_PREF . "posts
          WHERE url = ?
        ";

        $query = $this->DatabaseHandle->prepare($statement);

        // Prevent SQL injections.
        $query->bindParam(1, $_GET["post_url"]);
      }
      else if (isset($_GET["page_url"])) {

        $statement = "

          SELECT body
          FROM " . DB_PREF . "pages
          WHERE url = ?
        ";

        $query = $this->DatabaseHandle->prepare($statement);

        // Prevent SQL injections.
        $query->bindParam(1, $_GET["page_url"]);
      }

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post's body does not exist.
        return "Failure";
      }

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      return str_replace("\n", "<br>", $result->body);
    }
    else {

      return str_replace("\n", "<br>", $callback_content);
    }
  }

  public function latestConvertNewLines($callback_content = "") {

    if ($callback_content == "") {

      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'posts_per_page'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post's body does not exist.
        return "Failure";
      }

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      $posts_per_page = $result->body;

      $statement = "

        SELECT body
        FROM " . DB_PREF . "posts
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post's body does not exist.
        return "Failure";
      }

      $bodies = array();

      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        $bodies[] = str_replace("\n", "<br>", $result->body);
      }

      return $bodies;
    }
    else {

      return str_replace("\n", "<br>", $callback_content);
    }
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }
}

?>
