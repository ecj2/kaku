<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class Comment {

  public function getSource() {

    // Get the markup for the comment block.
    $comment_block_markup = $GLOBALS["Template"]->getFileContents("comment_block");

    // Determine if comments are allowed on this post.
    $statement = "

      SELECT allow_comments
      FROM " . DB_PREF . "posts
      WHERE url = ?
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["post"]);

    $Query->execute();

    if (!$Query) {

      // Something went wrong.
      $GLOBALS["Utility"]->displayError("failed to get comments");
    }

    if ($Query->rowCount() == 0) {

      // The post does not exist.
      header("Location: " . $GLOBALS["Utility"]->getRootAddress() . "/error?code=404");

      exit();
    }

    if ($Query->fetch(PDO::FETCH_OBJ)->allow_comments) {

      // Display the comment block.
      $GLOBALS["Hook"]->addAction(

        "comments",

        $comment_block_markup
      );
    }
    else {

      // Commenting has been disabled on this post.
      $disabled_message = str_replace(

        "{%comment_source%}",

        "{%comment_disabled_text%}",

        $comment_block_markup
      );

      $GLOBALS["Hook"]->addAction(

        "comments",

        $disabled_message
      );
    }
  }
}

?>
