<?php

class DisqusForum extends Extension {

  private $DatabaseHandle;

  public function __construct() {

    Extension::setName("Disqus Forum");
  }

  public function manageHooks() {

    global $Hook;

    $Hook->addFilter(

      "comment_source",

      $this,

      "getDisqusForum"
    );
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  public function getDisqusForum() {

    $disqus_markup_file = dirname(__FILE__) . "/content/markup.php";

    if (file_exists($disqus_markup_file)) {

      $statement = "

        SELECT forum_name
        FROM " . DB_PREF . "extension_disqus
        WHERE 1 = 1
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or is empty.
        return "Comments have not been configured.";
      }
      else {

        // Fetch the result as an object.
        $result = $query->fetch(PDO::FETCH_OBJ);

        // Get the forum name.
        $forum_name = $result->forum_name;

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
