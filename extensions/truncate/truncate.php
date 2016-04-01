<?php

class Truncate extends Extension {

  private $DatabaseHandle;

  public function __construct() {

    Extension::setName("Truncate Posts");
  }

  public function manageHooks() {

    global $Hook;

    $Hook->addFilter(

      "post_body",

      $this,

      "truncatePostBody"
    );
  }

  public function truncatePostBody($callback) {

    if (isset($_GET["post_url"])) {

      // Remove truncate tag.
      return str_replace("{%truncate%}", "", $callback);
    }
    else {

      $bodies = [];

      $count = 1;

      $lure_text = $this->getLureText();

      foreach ($callback as $body) {

        if (strpos($body, "{%truncate%}") !== false) {

          $truncate_position = strpos($body, "{%truncate%}");

          // Cut the body at the truncate position.
          $body = substr($body, 0, $truncate_position);

          // Include a "read more" link to the full post.
          $body .= "

            <a href=\"{%blog_url%}/post/{%post_url_{$count}%}\">
              {$lure_text}
            </a>
          ";
        }

        $bodies[] = $body;

        ++$count;
      }

      return $bodies;
    }
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  private function getLureText() {

    $statement = "

      SELECT lure
      FROM " . DB_PREF . "extension_truncate
      WHERE 1 = 1
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or is empty.
      return "Read more...";
    }
    else {

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get lure text.
      return $result->lure;
    }
  }
}

?>
