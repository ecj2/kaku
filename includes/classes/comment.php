<?php

class Comment extends Utility {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function getSource($comment_block_markup) {

    if (!isset($_GET["post_url"])) {

      return;
    }

    $statement = "

      SELECT allow_comments
      FROM " . DB_PREF . "posts
      WHERE url = ?
    ";

    $query = $this->DatabaseHandle->prepare($statement);

    // Prevent SQL injections.
    $query->bindParam(1, $_GET["post_url"]);

    $query->execute();

    if (!$query || $query->rowCount() == 0) {

      // Query failed or returned zero rows.
      Utility::displayError("failed to get comments");
    }

    if ($query->fetch(PDO::FETCH_OBJ)->allow_comments) {

      return $comment_block_markup;
    }

    return str_replace(

      "{%comment_source%}",

      "{%comment_disabled_text%}",

      $comment_block_markup
    );
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }
}

?>
