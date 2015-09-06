<?php

$name = "Post Pagination";

class Pagination {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function getTags() {

    return array(

      "next_page",

      "previous_page"
    );
  }

  public function getReplacements() {

    return array(

      $this->getNextPage(),

      $this->getPreviousPage()
    );
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  private function getNextPage() {

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

      $message = "<a href=\"{$url}\">{%next_page_text%}</a>";

      return $message;
    }
    else {

      return "";
    }
  }

  private function getPreviousPage() {

    $link = "{%previous_page_text%}";

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
