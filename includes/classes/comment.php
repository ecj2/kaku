<?php

// Prevent direct access to this file.
if (!defined("KAKU_INCLUDE")) exit();

class Comment extends Utility {

  private $DatabaseHandle;

  public function getSource($comment_block_markup) {

    if (!isset($_GET["post_url"])) {

      // Disallow this method from being used if not viewing a post.
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
    else if ($query->fetch(PDO::FETCH_OBJ)->allow_comments) {

      // Display comment block.
      return $comment_block_markup;
    }
    else {

      // Comments are disabled on this post.
      return str_replace(

        "{%comment_source%}",

        "{%comment_disabled_text%}",

        $comment_block_markup
      );
    }
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }
}

?>
