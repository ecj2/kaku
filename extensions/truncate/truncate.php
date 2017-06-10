<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Truncate extends Extension {

  public function __construct() {

    Extension::setName("Truncate Posts");

    $GLOBALS["Hook"]->addFilter("content_body", $this, "truncatePostBody");
  }

  public function truncatePostBody($content_body) {

    if (!empty($_GET["path"]) && substr($_GET["path"], 0, 4) != "page") {

      // Remove truncate tag when viewing a post.
      return str_replace("{%truncate%}", "", $content_body);
    }

    if (strpos($content_body, "{%truncate%}") !== false) {

      $truncate_position = strpos($content_body, "{%truncate%}");

      // Cut the body at the truncate position.
      $content_body = substr($content_body, 0, $truncate_position);

      // Include a "read more" link to the full post.
      $content_body .= "<a href=\"{%blog_url%}/{%content_url%}\">" . $this->getLureText() . "</a>";
    }

    return $content_body;
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

    // Get the lure text.
    return $Query->fetch(PDO::FETCH_OBJ)->lure;
  }
}

?>
