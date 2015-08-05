<?php

class Post extends Utility {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function getBody($url = "", $body = "") {

    if ($body == "") {

      return str_replace("{%truncate%}", "", $this->getData("body"));
    }
    else {

      if (strpos($body, "{%truncate%}") !== false) {

        $truncate_position = strpos($body, "{%truncate%}");

        // Cut the post at the truncate length.
        $body = substr($body, 0, $truncate_position);

        // Include a "read more" link to the full post.
        $body .= "

          <a href=\"{%blog_url%}/post/{$url}\">{%lure_text%}</a>
        ";
      }

      return $body;
    }
  }

  public function getTags($post_tags = null) {

    if ($post_tags == null) {

      $post_tags = $this->getData("tags");
    }

    $tags_markup = "";

    if (!empty($post_tags)) {

      $tags_markup .= "<ul>";

      foreach (explode(", ", $post_tags) as $tag) {

        // Encode spaces to work with URLs.
        $tag_url = str_replace(" ", "%20", $tag);

        $tags_markup .= "

          <li>
            <a href=\"{%blog_url%}/page/search?term={$tag_url}\">#{$tag}</a>
          </li>
        ";
      }

      $tags_markup .= "</ul>";
    }

    return $tags_markup;
  }

  public function getTitle() {

    return $this->getData("title");
  }

  public function getRange($post_block_markup) {

    if (!isset($_GET["page_number"])) {

      $address = Utility::getRootAddress();

      header("Location: {$address}");
    }

    if ($_GET["page_number"] < 2) {

      $address = Utility::getRootAddress();

      header("Location: {$address}");
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
      Utility::displayError("failed to get posts per page");
    }

    $posts_per_page = $query->fetch(PDO::FETCH_OBJ)->body;

    $offset = $posts_per_page * ($_GET["page_number"] - 1);

    $statement = "

      SELECT url, body, tags, title, epoch
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
      OFFSET {$offset}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get recent posts");
    }

    if ($query->rowCount() > 0) {

      $markup = "";

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $search = array(

          "{%post_url%}",
          "{%post_body%}",
          "{%post_tags%}",
          "{%post_title%}",
          "{%post_relative_epoch%}",
          "{%post_absolute_epoch%}"
        );

        $replace = array(

          $post->url,
          $this->getBody($post->url, $post->body),
          $this->getTags($post->tags),
          $post->title,
          $this->getRelativeEpoch($post->epoch),
          $this->getAbsoluteEpoch($post->epoch)
        );

        $markup .= str_replace($search, $replace, $post_block_markup);
      }

      return $markup;
    }
    else {

      $address = Utility::getRootAddress();

      header("Location: {$address}");
    }
  }

  public function getLatest($post_block_markup) {

    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'posts_per_page'
      ORDER BY id DESC
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or returned zero rows.
      Utility::displayError("failed to get posts per page");
    }

    $posts_per_page = $query->fetch(PDO::FETCH_OBJ)->body;

    $statement = "

      SELECT url, body, tags, title, epoch
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get recent posts");
    }

    if ($query->rowCount() > 0) {

      $markup = "";

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $search = array(

          "{%post_url%}",
          "{%post_body%}",
          "{%post_tags%}",
          "{%post_title%}",
          "{%post_relative_epoch%}",
          "{%post_absolute_epoch%}"
        );

        $replace = array(

          $post->url,
          $this->getBody($post->url, $post->body),
          $this->getTags($post->tags),
          $post->title,
          $this->getRelativeEpoch($post->epoch),
          $this->getAbsoluteEpoch($post->epoch)
        );

        $markup .= str_replace($search, $replace, $post_block_markup);
      }

      return $markup;
    }
  }

  public function getDescription() {

    return $this->getData("description");
  }

  public function getAbsoluteEpoch($epoch = 0) {

    if ($epoch == 0) {

      $epoch = $this->getData("epoch");
    }

    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'date_format'
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or returned zero rows.
      Utility::displayError("failed to get date format");
    }

    return date($query->fetch(PDO::FETCH_OBJ)->body, $epoch);
  }

  public function getRelativeEpoch($epoch = 0) {

    if ($epoch == 0) {

      $epoch = $this->getData("epoch");
    }

    $difference = time() - $epoch;

    $relative_epoch = "";

    if ($difference < 60) {

      // Seconds.
      $relative_epoch = $difference . " second";
    }
    else if ($difference < 3600) {

      // Minutes.
      $relative_epoch = $difference / 60 . " minute";
    }
    else if ($difference < 86400) {

      // Hours.
      $relative_epoch = $difference / 3600 . " hour";
    }
    else if ($difference < 604800) {

      // Days.
      $relative_epoch = $difference / 86400 . " day";
    }
    else if ($difference < 2419200) {

      // Weeks.
      $relative_epoch = $difference / 604800 . " week";
    }
    else if ($difference < 29030400) {

      // Months.
      $relative_epoch = $difference / 2419200 . " month";
    }
    else {

      // Years.
      $relative_epoch = $difference / 29030400 . " year";
    }

    $epoch_value = explode(" ", $relative_epoch);

    if (floor($epoch_value[0]) == 0 || floor($epoch_value[0]) > 1) {

      // Make time counter plural.
      $epoch_value[1] .= "s";
    }

    return floor($epoch_value[0]) . " {$epoch_value[1]}";
  }

  public function setDatabaseHandle($handle) {

    $this->DatabaseHandle = $handle;
  }

  private function getData($column) {

    if (!isset($_GET["post_url"])) {

      return;
    }

    $statement = "

      SELECT {$column}
      FROM " . DB_PREF . "posts
      WHERE url = ?
    ";

    $query = $this->DatabaseHandle->prepare($statement);

    // Prevent SQL injections.
    $query->bindParam(1, $_GET["post_url"]);

    $query->execute();

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get post");
    }

    if ($query->rowCount() == 0) {

      $address = Utility::getRootAddress();

      // Query returned zero rows.
      header("Location: {$address}/error.php?code=404");
    }

    return $query->fetch(PDO::FETCH_OBJ)->$column;
  }
}

?>
