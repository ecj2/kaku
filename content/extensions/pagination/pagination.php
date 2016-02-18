<?php

// Prevent direct access to this file.
if (!defined("KAKU_EXTENSION")) exit();

$name = "Post Pagination";

class Pagination extends Utility {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function manageHooks() {

    global $Hook;

    $Hook->addFilter(

      "next_page",

      $this,

      "getNextPage"
    );

    $Hook->addFilter(

      "previous_page",

      $this,

      "getPreviousPage"
    );
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  public function getNextPage() {

    $next_page_text = "";

    $statement = "

      SELECT next_page_text
      FROM " . DB_PREF . "extension_pagination
      WHERE 1 = 1
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or is empty.
      $next_page_text =  "Older posts";
    }
    else {

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get the text.
      $next_page_text = $result->next_page_text;
    }

    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'posts_per_page'
      ORDER BY id DESC
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or returned zero rows.
      exit("failed to get posts per page");
    }

    $posts_per_page = $query->fetch(PDO::FETCH_OBJ)->body;

    $offset = 0;
    $row_count = $posts_per_page + 1;

    if (isset($_GET["page_number"])) {

      if ($_GET["page_number"] == 0) {

        $root_address = Utility::getRootAddress();

        // Redirect to index.
        header("Location: {$root_address}");
      }

      $offset = ($_GET["page_number"] - 1) * $posts_per_page;
      $row_count = $posts_per_page + 1;
    }

    $statement = "SELECT 1 FROM " . DB_PREF . "posts WHERE ";
    $statement .= "draft = '0' ORDER BY id ";
    $statement .= "DESC LIMIT {$offset}, {$row_count}";

    $query = $this->DatabaseHandle->query($statement);

    if ($query->rowCount() > $posts_per_page) {

      if (isset($_GET["page_number"])) {

        $url = "{%blog_url%}/page/" . ($_GET["page_number"] + 1);
      }
      else {

        $url = "{%blog_url%}/page/2";
      }

      $message = "<a href=\"{$url}\">{$next_page_text}</a>";

      return $message;
    }
    else {

      return "";
    }
  }

  public function getPreviousPage() {

    $next_page_text = "";

    $statement = "

      SELECT previous_page_text
      FROM " . DB_PREF . "extension_pagination
      WHERE 1 = 1
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or is empty.
      $previous_page_text =  "Newer posts";
    }
    else {

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get the text.
      $previous_page_text = $result->previous_page_text;
    }

    $link = "{$previous_page_text}";

    if (isset($_GET["page_number"])) {

      $previous_page = $_GET["page_number"] - 1;

      if ($previous_page < 2) {

        return "<a href=\"{%blog_url%}\">{$link}</a>";
      }

      return "<a href=\"{%blog_url%}/page/{$previous_page}\">{$link}</a>";
    }

    return "";
  }
}

?>
