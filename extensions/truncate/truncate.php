<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class Truncate extends Extension {

  public function __construct() {

    Extension::setName("Truncate Posts");

    $GLOBALS["Hook"]->addFilter(

      "post_body",

      $this,

      "truncatePostBody"
    );
  }

  public function truncatePostBody($callback) {

    if (isset($_GET["post"])) {

      // Remove truncate tag when viewing a post.
      return str_replace("{%truncate%}", "", $callback);
    }
    else {

      $bodies = [];

      $count = 0;

      $lure_text = $this->getLureText();

      foreach ($callback as $body) {

        if (strpos($body, "{%truncate%}") !== false) {

          $truncate_position = strpos($body, "{%truncate%}");

          // Cut the body at the truncate position.
          $body = substr($body, 0, $truncate_position);

          // Include a "read more" link to the full post.
          $body .= "<a href=\"{%blog_url%}/post/{%post_url_{$count}%}\">{$lure_text}</a>";
        }

        $bodies[] = $body;

        ++$count;
      }

      return $bodies;
    }
  }

  private function getLureText() {

    $statement = "

      SELECT lure
      FROM " . DB_PREF . "extension_truncate
      WHERE 1 = 1
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if (!$Query || $Query->rowCount() == 0) {

      // Query failed or returned zero rows.
      return "Read more...";
    }
    else {

      // Fetch the result as an object.
      $Result = $Query->fetch(PDO::FETCH_OBJ);

      // Get the lure text.
      return $Result->lure;
    }
  }
}

?>
