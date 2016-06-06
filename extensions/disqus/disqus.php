<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class DisqusForum extends Extension {

  private $DatabaseHandle;

  public function __construct() {

    Extension::setName("Disqus Forum");

    $GLOBALS["Hook"]->addFilter(

      "comment_source",

      $this,

      "getDisqusForum"
    );
  }

  public function getDisqusForum() {

    $url = "";
    $identifier = "";

    $statement = "

      SELECT id, url
      FROM " . DB_PREF . "posts
      WHERE url = ?
      ORDER BY id DESC
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["post"]);

    $Query->execute();

    if (!$Query) {

      // Something went wrong.
      $GLOBALS["Utility"]->displayError("failed to get post ID and URL");
    }

    if ($Query->rowCount() > 0) {

      // Fetch the result as an object.
      $Result = $Query->fetch(PDO::FETCH_OBJ);

      $url = $Result->url;
      $identifier = $Result->id;
    }

    $disqus_markup_file = dirname(__FILE__) . "/content/markup.php";

    if (file_exists($disqus_markup_file)) {

      $statement = "

        SELECT forum_name
        FROM " . DB_PREF . "extension_disqus
        WHERE 1 = 1
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query || $Query->rowCount() == 0) {

        // Query failed or returned zero rows.
        return "Comments have not been configured.";
      }
      else {

        // Fetch the result as an object.
        $Result = $Query->fetch(PDO::FETCH_OBJ);

        // Get the forum name.
        $forum_name = $Result->forum_name;

        if ($forum_name == "") {

          return "Comments have not been configured.";
        }

        require $disqus_markup_file;

        // Display the Disqus forum.
        return str_replace("{%disqus_forum_name%}", $forum_name, $markup);
      }
    }
    else {

      // Disqus markup file does not exist.
      return "Failed to load Disqus forum.";
    }
  }
}

?>
