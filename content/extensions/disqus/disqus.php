<?php

// Prevent direct access to this file.
if (!defined("KAKU_EXTENSION")) exit();

$name = "Disqus Forum";

class DisqusForum {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function getTags() {

    return array(

      "comment_source"
    );
  }

  public function getReplacements() {

    return array(

      $this->getDisqusForum()
    );
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  private function getDisqusForum() {

    $disqus_markup_file = dirname(__FILE__) . "/content/markup.php";

    if (file_exists($disqus_markup_file)) {

      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'disqus_forum_name'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or returned zero rows.
        return "Comments have not been configured.";
      }
      else {

        if ($query->fetch(PDO::FETCH_OBJ)->body == "") {

          // Disqus forum name hasn't been configured.
          return "Comments have not been configured.";
        }
        else {

          require $disqus_markup_file;

          // Display the Disqus forum.
          return $markup;
        }
      }
    }
    else {

      // Disqus markup file does not exist.
      return "Failed to load Disqus forum.";
    }
  }
}

?>
