<?php

class Post extends Utility {

  private $DatabaseHandle;

  public function getBody($url = "", $body = "") {

    if ($body == "") {

      // Remove truncate tag when viewing full post.
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

  public function getKeywords($post_tags = null) {

    if (!isset($_GET["post_url"])) {

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

      $statement = "";

      if (isset($_GET["page_number"])) {

        $offset = $posts_per_page * ($_GET["page_number"] - 1);

        $statement = "

          SELECT url, body, title, keywords, epoch, author_id
          FROM " . DB_PREF . "posts
          WHERE draft = '0'
          ORDER BY id DESC
          LIMIT {$posts_per_page}
          OFFSET {$offset}
        ";
      }
      else {

        $statement = "

          SELECT keywords
          FROM " . DB_PREF . "posts
          ORDER BY id DESC
          LIMIT {$posts_per_page}
        ";
      }

      $query = $this->DatabaseHandle->query($statement);

      if (!$query) {

        // Query failed.
        Utility::displayError("failed to get latest posts");
      }

      if ($query->rowCount() > 0) {

        $keywords = array();

        while ($post = $query->fetch(PDO::FETCH_OBJ)) {

          $tags_markup = "";

          $post_tags = $post->keywords;

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

          $keywords[] = $tags_markup;
        }

        return $keywords;
      }
    }
    else {

      //
      if ($post_tags == null) {

        $post_tags = $this->getData("keywords");
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
  }

  public function getTitle() {

    if (isset($_GET["post_url"])) {

      // Select post title.
      $statement = "

        SELECT title
        FROM " . DB_PREF . "posts
        WHERE url = ?
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post title does not exist.
        Utility::displayError("failed to select post title");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get post title.
      return $result->title;
    }
    else if (isset($_GET["page_number"])) {

      // Select posts per page.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'posts_per_page'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post title does not exist.
        Utility::displayError("failed to select posts per page");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get posts per page.
      $posts_per_page = $result->body;

      // Get range offset.
      $offset = $posts_per_page * ($_GET["page_number"] - 1);

      // Select post title.
      $statement = "

        SELECT title
        FROM " . DB_PREF . "posts
        ORDER BY id DESC
        LIMIT {$posts_per_page}
        OFFSET {$offset}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post title does not exist.
        Utility::displayError("failed to select post title");
      }

      $titles = null;

      // Fetch results as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get titles;
        $titles[] = $result->title;
      }

      return $titles;
    }
    else {

      // Select posts per page.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'posts_per_page'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post title does not exist.
        Utility::displayError("failed to select posts per page");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get posts per page.
      $posts_per_page = $result->body;

      // Select post title.
      $statement = "

        SELECT title
        FROM " . DB_PREF . "posts
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post title does not exist.
        Utility::displayError("failed to select post title");
      }

      $titles = null;

      // Fetch results as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get titles;
        $titles[] = $result->title;
      }

      return $titles;
    }
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

      SELECT url, body, title, keywords, epoch, author_id
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
      OFFSET {$offset}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts by range");
    }

    if ($query->rowCount() > 0) {

      $markup = "";

      $count = 1;

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $search = array();

        $replace = array();

        $search[] = "{%post_url%}";
        $search[] = "{%post_body%}";
        $search[] = "{%post_title%}";
        $search[] = "{%post_keywords%}";
        $search[] = "{%post_relative_epoch%}";
        $search[] = "{%post_absolute_epoch%}";
        $search[] = "{%post_date_time_epoch%}";
        $search[] = "{%post_author%}";

        $replace[] = "{%post_url_{$count}%}";
        $replace[] = "{%post_body_{$count}%}";
        $replace[] = "{%post_title_{$count}%}";
        $replace[] = "{%post_keywords_{$count}%}";
        $replace[] = "{%post_relative_epoch_{$count}%}";
        $replace[] = "{%post_absolute_epoch_{$count}%}";
        $replace[] = "{%post_date_time_epoch_{$count}%}";
        $replace[] = "{%post_author_{$count}%}";

        $markup .= str_replace($search, $replace, $post_block_markup);

        ++$count;
      }

      return $markup;
    }
    else {

      $address = Utility::getRootAddress();

      header("Location: {$address}");
    }
  }

  public function getAuthor() {

    if (isset($_GET["post_url"])) {

      // Select post author ID.
      $statement = "

        SELECT author_id
        FROM " . DB_PREF . "posts
        WHERE url = ?
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or author ID does not exist.
        Utility::displayError("failed to select post author ID");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get post author ID.
      $author_id = $result->author_id;

      // Select user nickname.
      $statement = "

        SELECT nickname
        FROM " . DB_PREF . "users
        WHERE id = '{$author_id}'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post title does not exist.
        Utility::displayError("failed to select user nickname");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get user nickname.
      return $result->nickname;
    }
    else if (isset($_GET["page_number"])) {

      // Select posts per page.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'posts_per_page'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or posts per page does not exist.
        Utility::displayError("failed to select posts per page");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get posts per page.
      $posts_per_page = $result->body;

      // Get range offset.
      $offset = $posts_per_page * ($_GET["page_number"] - 1);

      // Select post author ID.
      $statement = "

        SELECT author_id
        FROM " . DB_PREF . "posts
        ORDER BY id DESC
        LIMIT {$posts_per_page}
        OFFSET {$offset}
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or author ID does not exist.
        Utility::displayError("failed to select post author ID");
      }

      $authors = null;

      // Fetch results as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post author ID.
        $author_id = $result->author_id;

        // Select user nickname.
        $statement = "

          SELECT nickname
          FROM " . DB_PREF . "users
          WHERE id = '{$author_id}'
        ";

        $query = $this->DatabaseHandle->query($statement);

        if (!$query || $query->rowCount() == 0) {

          // Query failed or post title does not exist.
          Utility::displayError("failed to select user nickname");
        }

        // Fetch result as an object.
        $result = $query->fetch(PDO::FETCH_OBJ);

        // Get user nickname.
        $authors[] = $result->nickname;
      }

      return $authors;
    }
    else {

      // Select posts per page.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'posts_per_page'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or posts per page does not exist.
        Utility::displayError("failed to select posts per page");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get posts per page.
      $posts_per_page = $result->body;

      // Select post author ID.
      $statement = "

        SELECT author_id
        FROM " . DB_PREF . "posts
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or author ID does not exist.
        Utility::displayError("failed to select post author ID");
      }

      $authors = null;

      // Fetch results as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post author ID.
        $author_id = $result->author_id;

        // Select user nickname.
        $statement = "

          SELECT nickname
          FROM " . DB_PREF . "users
          WHERE id = '{$author_id}'
        ";

        $query = $this->DatabaseHandle->query($statement);

        if (!$query || $query->rowCount() == 0) {

          // Query failed or post title does not exist.
          Utility::displayError("failed to select user nickname");
        }

        // Fetch result as an object.
        $result = $query->fetch(PDO::FETCH_OBJ);

        // Get user nickname.
        $authors[] = $result->nickname;
      }

      return $authors;
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

      SELECT url, body, title, keywords, epoch, author_id
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $markup = "";

      $count = 1;

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $search = array();

        $replace = array();

        $search[] = "{%post_url%}";
        $search[] = "{%post_body%}";
        $search[] = "{%post_title%}";
        $search[] = "{%post_keywords%}";
        $search[] = "{%post_relative_epoch%}";
        $search[] = "{%post_absolute_epoch%}";
        $search[] = "{%post_date_time_epoch%}";
        $search[] = "{%post_author%}";

        $replace[] = "{%post_url_{$count}%}";
        $replace[] = "{%post_body_{$count}%}";
        $replace[] = "{%post_title_{$count}%}";
        $replace[] = "{%post_keywords_{$count}%}";
        $replace[] = "{%post_relative_epoch_{$count}%}";
        $replace[] = "{%post_absolute_epoch_{$count}%}";
        $replace[] = "{%post_date_time_epoch_{$count}%}";
        $replace[] = "{%post_author_{$count}%}";

        $markup .= str_replace($search, $replace, $post_block_markup);

        ++$count;
      }

      return $markup;
    }
  }

  public function getRelativeEpochs() {

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

      SELECT epoch
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $epochs = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $epochs[] = $this->getRelativeEpoch($post->epoch);
      }

      return $epochs;
    }
  }

  public function getRelativeEpochsRange() {

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

      SELECT url, body, title, keywords, epoch, author_id
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
      OFFSET {$offset}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $epochs = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $epochs[] = $this->getRelativeEpoch($post->epoch);
      }

      return $epochs;
    }
  }

  public function getDateTimeEpochs() {

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

      SELECT epoch
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $epochs = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $epochs[] = $this->getDateTimeEpoch($post->epoch);
      }

      return $epochs;
    }
  }

  public function getDateTimeEpochsRange() {

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

      SELECT url, body, title, keywords, epoch, author_id
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
      OFFSET {$offset}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $epochs = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $epochs[] = $this->getDateTimeEpoch($post->epoch);
      }

      return $epochs;
    }
  }

  public function getAbsoluteEpochs() {

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

      SELECT epoch
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $epochs = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $epochs[] = $this->getAbsoluteEpoch($post->epoch);
      }

      return $epochs;
    }
  }

  public function getAbsoluteEpochsRange() {

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

      SELECT url, body, title, keywords, epoch, author_id
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
      OFFSET {$offset}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $epochs = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $epochs[] = $this->getAbsoluteEpoch($post->epoch);
      }

      return $epochs;
    }
  }

  public function getUrls() {

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

      SELECT url
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $urls = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $urls[] = $post->url;
      }

      return $urls;
    }
  }

  public function getUrlsRange() {

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

      SELECT url, body, title, keywords, epoch, author_id
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
      OFFSET {$offset}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $urls = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $urls[] = $post->url;
      }

      return $urls;
    }
  }

  public function getBodies() {

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

      SELECT body
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $bodies = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $bodies[] = $post->body;
      }

      return $bodies;
    }
  }

  public function getBodiesRange() {

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

      SELECT url, body, title, keywords, epoch, author_id
      FROM " . DB_PREF . "posts
      WHERE draft = '0'
      ORDER BY id DESC
      LIMIT {$posts_per_page}
      OFFSET {$offset}
    ";

    $query = $this->DatabaseHandle->query($statement);

    if (!$query) {

      // Query failed.
      Utility::displayError("failed to get latest posts");
    }

    if ($query->rowCount() > 0) {

      $bodies = array();

      while ($post = $query->fetch(PDO::FETCH_OBJ)) {

        $bodies[] = $post->body;
      }

      return $bodies;
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

  public function getDateTimeEpoch($epoch = "") {

    if ($epoch == "") {

      $epoch = $this->getData("epoch");
    }

    return date("Y-m-d H:i:sP", $epoch);
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
