<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Pagination extends Extension {

  private $page_number;

  public function __construct() {

    Extension::setName("Post Pagination");

    $GLOBALS["Hook"]->addFilter("next_page", $this, "getNextPage");
    $GLOBALS["Hook"]->addFilter("previous_page", $this, "getPreviousPage");

    if (!empty($_GET["path"]) && substr($_GET["path"], 0, 4) == "page") {

      // Extract the page number from the path.
      $this->page_number = preg_replace("/[^0-9]/", "", $_GET["path"]);
    }
  }

  public function getNextPage() {

    $next_page_text = null;

    // Select the next page text.
    $statement = "

      SELECT next_page_text
      FROM " . DB_PREF . "extension_pagination
      WHERE 1 = 1
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if (!$Query || $Query->rowCount() == 0) {

      // The query failed or returned zero rows.
      $next_page_text =  "Older posts";
    }
    else {

      // Fetch the result as an object.
      $Result = $Query->fetch(PDO::FETCH_OBJ);

      // Get the text.
      $next_page_text = $Result->next_page_text;
    }

    // Select the posts per page tag.
    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'posts_per_page'
      ORDER BY id DESC
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if (!$Query) {

      // Something went wrong.
      $GLOBALS["Utility"]->displayError("failed to select posts_per_page");
    }

    if ($Query->rowCount() == 0) {

      // The posts_per_page tag does not exit.
      $GLOBALS["Utility"]->displayError("posts_per_page tag does not exist");
    }

    // Get the posts per page.
    $posts_per_page = $Query->fetch(PDO::FETCH_OBJ)->body;

    // Replace nested tags in the posts per page.
    $posts_per_page = $GLOBALS["Utility"]->replaceNestedTags($posts_per_page);

    $offset = 0;
    $row_count = $posts_per_page + 1;

    if (!empty($this->page_number)) {

      $offset = ($this->page_number - 1) * $posts_per_page;
      $row_count = $posts_per_page + 1;
    }

    $statement = "

      SELECT 1
      FROM " . DB_PREF . "content
      WHERE draft = 0
      AND type = 0
      ORDER BY id DESC
      LIMIT {$offset}, {$row_count}
    ";

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if ($Query->rowCount() > $posts_per_page) {

      $url = "";

      if (!empty($this->page_number)) {

        $url = "{%blog_url%}/page/" . ($this->page_number + 1);
      }
      else {

        $url = "{%blog_url%}/page/2";
      }

      $message = "<a href=\"{$url}\">{$next_page_text}</a>";

      return $message;
    }

    return "";
  }

  public function getPreviousPage() {

    $next_page_text = "";

    $statement = "

      SELECT previous_page_text
      FROM " . DB_PREF . "extension_pagination
      WHERE 1 = 1
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if (!$Query || $Query->rowCount() == 0) {

      // Query failed or returned zero rows.
      $previous_page_text =  "Newer posts";
    }
    else {

      // Fetch the result as an object.
      $Result = $Query->fetch(PDO::FETCH_OBJ);

      // Get the text.
      $previous_page_text = $Result->previous_page_text;
    }

    $link = "{$previous_page_text}";

    if (!empty($this->page_number)) {

      $previous_page = $this->page_number - 1;

      if ($previous_page < 2) {

        return "<a href=\"{%blog_url%}\">{$link}</a>";
      }

      return "<a href=\"{%blog_url%}/page/{$previous_page}\">{$link}</a>";
    }

    return "";
  }
}

?>
