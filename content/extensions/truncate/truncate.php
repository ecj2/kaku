<?php

// Prevent direct access to this file.
if (!defined("KAKU_EXTENSION")) exit();

$name = "Truncate Posts";

class Truncate {

  public function __construct() {

    //
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

      foreach ($callback as $body) {

        if (strpos($body, "{%truncate%}") !== false) {

          $truncate_position = strpos($body, "{%truncate%}");

          // Cut the body at the truncate position.
          $body = substr($body, 0, $truncate_position);

          // Include a "read more" link to the full post.
          $body .= "

            <a href=\"{%blog_url%}/post/{%post_url_{$count}%}\">
              {%lure_text%}
            </a>
          ";
        }

        $bodies[] = $body;

        ++$count;
      }

      return $bodies;
    }
  }
}

?>
