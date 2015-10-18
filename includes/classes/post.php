<?php

// Prevent direct access to this file.
if (!defined("KAKU_INCLUDE")) exit();

class Post extends Utility {

  private $DatabaseHandle;

  public function getBody() {

    if (isset($_GET["post_url"])) {

      // Select post body.
      $statement = "

        SELECT body, draft
        FROM " . DB_PREF . "posts
        WHERE url = ?
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post body does not exist.
        Utility::displayError("failed to select post body");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      if ($result->draft) {

        // Do not allow drafts to be viewed publicly.

        $address = Utility::getRootAddress();

        header("Location: {$address}/error.php?code=404");
      }

      // Get post body.
      $body = $result->body;

      // Remove truncate tag.
      $body = str_replace("{%truncate%}", "", $body);

      // Display post body.
      return $body;
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

      // Select post body.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
        OFFSET {$offset}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post body does not exist.
        Utility::displayError("failed to select post body");
      }

      $bodies = null;

      $count = 1;

      // Fetch result as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post body.
        $body = $result->body;

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

      // Select post body.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post body does not exist.
        return [];
      }

      $bodies = null;

      $count = 1;

      // Fetch result as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post body.
        $body = $result->body;

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

  public function getKeywords() {

    if (isset($_GET["post_url"])) {

      // Select post keywords.
      $statement = "

        SELECT keywords
        FROM " . DB_PREF . "posts
        WHERE url = ?
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post keywords does not exist.
        Utility::displayError("failed to select post keywords");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get post keywords.
      $keywords = $result->keywords;

      $keywords_markup = null;

      if (!empty($keywords)) {

        $keywords_markup .= "<ul>";

        foreach (explode(", ", $keywords) as $keyword) {

          // Encode spaces.
          $keyword_url = str_replace(" ", "%20", $keyword);

          // Create a list item for each keyword.
          $keywords_markup .= "<li>";
          $keywords_markup .= "<a href=\"{%blog_url%}/page/search?term=";
          $keywords_markup .= "{$keyword_url}\">#{$keyword}</a>";
          $keywords_markup .= "</li>";
        }

        $keywords_markup .= "</ul>";
      }

      return $keywords_markup;
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

      // Select post keywords.
      $statement = "

        SELECT keywords
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
        OFFSET {$offset}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post keywords does not exist.
        Utility::displayError("failed to select post keywords");
      }

      $keywords_array = null;

      // Fetch result as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post keywords.
        $keywords = $result->keywords;

        $keywords_markup = null;

        $keywords_markup .= "<ul>";

        if (!empty($keywords)) {

          foreach (explode(", ", $keywords) as $keyword) {

            // Encode spaces.
            $keyword_url = str_replace(" ", "%20", $keyword);

            // Create a list item for each keyword.
            $keywords_markup .= "<li>";
            $keywords_markup .= "<a href=\"{%blog_url%}/page/search?term=";
            $keywords_markup .= "{$keyword_url}\">#{$keyword}</a>";
            $keywords_markup .= "</li>";
          }
        }

        $keywords_markup .= "</ul>";

        $keywords_array[] = $keywords_markup;
      }

      return $keywords_array;
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

      // Select post keywords.
      $statement = "

        SELECT keywords
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post keywords does not exist.
        return [];
      }

      $keywords_array = null;

      // Fetch result as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post keywords.
        $keywords = $result->keywords;

        $keywords_markup = null;

        $keywords_markup .= "<ul>";

        if (!empty($keywords)) {

          foreach (explode(", ", $keywords) as $keyword) {

            // Encode spaces.
            $keyword_url = str_replace(" ", "%20", $keyword);

            // Create a list item for each keyword.
            $keywords_markup .= "<li>";
            $keywords_markup .= "<a href=\"{%blog_url%}/page/search?term=";
            $keywords_markup .= "{$keyword_url}\">#{$keyword}</a>";
            $keywords_markup .= "</li>";
          }
        }

        $keywords_markup .= "</ul>";

        $keywords_array[] = $keywords_markup;
      }

      return $keywords_array;
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
        WHERE draft = '0'
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
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post title does not exist.
        return [];
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
        return "Unknown";
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
        WHERE draft = '0'
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
          $authors[] = "Unknown";
        }
        else {

          // Fetch result as an object.
          $result = $query->fetch(PDO::FETCH_OBJ);

          // Get user nickname.
          $authors[] = $result->nickname;
        }
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
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or author ID does not exist.
        return [];
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
          $authors[] = "Unknown";
        }
        else {

          // Fetch result as an object.
          $result = $query->fetch(PDO::FETCH_OBJ);

          // Get user nickname.
          $authors[] = $result->nickname;
        }
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

  public function getUniformResourceLocator() {

    if (isset($_GET["post_url"])) {

      // Select post url.
      $statement = "

        SELECT url
        FROM " . DB_PREF . "posts
        WHERE url = ?
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post URL does not exist.

        $address = Utility::getRootAddress();

        header("Location: {$address}/error.php?code=404");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get post url.
      return $result->url;
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

      // Select post URL.
      $statement = "

        SELECT url
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
        OFFSET {$offset}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post URL does not exist.
        Utility::displayError("failed to select post URL");
      }

      $uniform_resource_locators = null;

      // Fetch results as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post URL;
        $uniform_resource_locators[] = $result->url;
      }

      return $uniform_resource_locators;
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

      // Select post URL.
      $statement = "

        SELECT url
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post URL does not exist.
        return [];
      }

      $uniform_resource_locators = null;

      // Fetch results as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post URL;
        $uniform_resource_locators[] = $result->url;
      }

      return $uniform_resource_locators;
    }
  }

  public function getDescription() {

    // Select post description.
    $statement = "

      SELECT description
      FROM " . DB_PREF . "posts
      WHERE url = ?
    ";

    $query = $this->DatabaseHandle->prepare($statement);

    // Prevent SQL injections.
    $query->bindParam(1, $_GET["post_url"]);

    $query->execute();

    if (!$query || $query->rowCount() == 0) {

      // Query failed or post description does not exist.
      Utility::displayError("failed to select post description");
    }

    // Fetch result as an object.
    $result = $query->fetch(PDO::FETCH_OBJ);

    // Get post description.
    $description = $result->description;

    return $description;
  }

  public function getAbsoluteEpoch() {

    if (isset($_GET["post_url"])) {

      // Select date format.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'date_format'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or date format does not exist.
        Utility::displayError("failed to select date format");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get date format.
      $date_format = $result->body;

      // Select post epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE url = ?
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post epoch does not exist.
        Utility::displayError("failed to select post epoch");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get post epoch.
      return date($date_format, $result->epoch);
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

      // Select date format.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'date_format'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or date format does not exist.
        Utility::displayError("failed to select date format");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get date format.
      $date_format = $result->body;

      // Select post epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
        OFFSET {$offset}
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post epoch does not exist.
        Utility::displayError("failed to select post epoch");
      }

      $epochs = null;

      // Fetch result as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post epochs.
        $epochs[] = date($date_format, $result->epoch);
      }

      return $epochs;
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

      // Select date format.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'date_format'
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or date format does not exist.
        Utility::displayError("failed to select date format");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get date format.
      $date_format = $result->body;

      // Select post epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post epoch does not exist.
        return [];
      }

      $epochs = null;

      // Fetch result as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post epochs.
        $epochs[] = date($date_format, $result->epoch);
      }

      return $epochs;
    }
  }

  public function getDateTimeEpoch() {

    if (isset($_GET["post_url"])) {

      // Select post epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE url = ?
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post epoch does not exist.
        Utility::displayError("failed to select post epoch");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get post epoch.
      return date("Y-m-d H:i:sP", $result->epoch);
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

      // Select post epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
        OFFSET {$offset}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post epoch does not exist.
        Utility::displayError("failed to select post epoch");
      }

      $epochs = null;

      // Fetch results as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get epochs;
        $epochs[] = date("Y-m-d H:i:sP", $result->epoch);
      }

      return $epochs;
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

      // Select post epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post epoch does not exist.
        return [];
      }

      $epochs = null;

      // Fetch results as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get epochs;
        $epochs[] = date("Y-m-d H:i:sP", $result->epoch);
      }

      return $epochs;
    }
  }

  public function getRelativeEpoch() {

    if (isset($_GET["post_url"])) {

      // Select post epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE url = ?
      ";

      $query = $this->DatabaseHandle->prepare($statement);

      // Prevent SQL injections.
      $query->bindParam(1, $_GET["post_url"]);

      $query->execute();

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post epoch does not exist.
        Utility::displayError("failed to select post epoch");
      }

      // Fetch result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get post epoch.
      $epoch = $result->epoch;

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

      // Select post epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
        OFFSET {$offset}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post epoch does not exist.
        Utility::displayError("failed to select post epoch");
      }

      $epochs = null;

      // Fetch result as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post epoch.
        $epoch = $result->epoch;

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

        $epochs[] = floor($epoch_value[0]) . " {$epoch_value[1]}";
      }

      return $epochs;
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

      // Select post epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE draft = '0'
        ORDER BY id DESC
        LIMIT {$posts_per_page}
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or post epoch does not exist.
        return [];
      }

      $epochs = null;

      // Fetch result as an object.
      while ($result = $query->fetch(PDO::FETCH_OBJ)) {

        // Get post epoch.
        $epoch = $result->epoch;

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

        $epochs[] = floor($epoch_value[0]) . " {$epoch_value[1]}";
      }

      return $epochs;
    }
  }

  public function setDatabaseHandle($Handle) {

    $this->DatabaseHandle = $Handle;
  }
}

?>
