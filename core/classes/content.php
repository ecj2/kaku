<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Content {

  // @TODO: Improve documentation.

  public function getAuthor() {

    // Create hook action to later get author nicknames.
    $this->getColumn("author_id");

    $author_id = $GLOBALS["Hook"]->doAction("content_author_id");

    if (is_array($author_id)) {

      for ($i = 0; $i < count($author_id); ++$i) {

        $statement = "

          SELECT nickname
          FROM " . DB_PREF . "users
          WHERE id = '" . $author_id[$i] . "'
          ORDER BY id DESC
          LIMIT 1
        ";

        $Query = $GLOBALS["Database"]->getHandle()->query($statement);

        if (!$Query) {

          // Selection failed.
          $GLOBALS["Utility"]->displayError("failed to select content author nickname");
        }

        // User nickname defaults to "Unknown" if it doesn't exist.
        $author_nickname = "Unknown";

        if ($Query->rowCount() > 0) {

          // Get user nickname.
          $author_nickname = $Query->fetch(PDO::FETCH_OBJ)->nickname;
        }

        $GLOBALS["Hook"]->addAction("content_author_{$i}", $author_nickname);
      }
    }
    else {

      $statement = "

        SELECT nickname
        FROM " . DB_PREF . "users
        WHERE id = '{$author_id}'
        ORDER BY id DESC
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Selection failed.
        $GLOBALS["Utility"]->displayError("failed to select content author nickname");
      }

      // User nickname defaults to "Unknown" if it doesn't exist.
      $author_nickname = "Unknown";

      if ($Query->rowCount() > 0) {

        // Get user nickname.
        $author_nickname = $Query->fetch(PDO::FETCH_OBJ)->nickname;
      }

      $GLOBALS["Hook"]->addAction("content_author", $author_nickname);
    }
  }

  public function getEpochs() {

    $this->getColumn("epoch_created");

    $date_format = $GLOBALS["Utility"]->getTag("date_format");

    $epoch_created = $GLOBALS["Hook"]->doAction("content_epoch_created");

    if (is_array($epoch_created)) {

      for ($i = 0; $i < count($epoch_created); ++$i) {

        // Remove action to be replaced later.
        $GLOBALS["Hook"]->removeAction("content_epoch_created_{$i}");

        // @TODO: Is it necessary to replace sub tags in these? Do tests.

        // @TODO: Do this to all other methods and replace exact matches. Move to own method?
        // Suffix nested content tags with numbers to be replaced later.
        $item = date($date_format, $epoch_created[$i]);
        $item = preg_replace("/content_([^%]+)/", "content_$1_{$i}", $item);

        // Replace action with new epoch value.
        $GLOBALS["Hook"]->addAction("content_epoch_created_{$i}", $item);

        // @TODO: Do this to all other methods and replace exact matches. Move to own method?
        // Suffix nested content tags with numbers to be replaced later.
        $epoch_date_time = date("Y-m-d H:i:sP", $epoch_created[$i]);
        $epoch_date_time = preg_replace("/content_([^%]+)/", "content_$1_{$i}", $epoch_date_time);

        $GLOBALS["Hook"]->addAction("content_epoch_date_time_{$i}", $epoch_date_time);
      }
    }
    else {

      // Remove action to be replaced later.
      $GLOBALS["Hook"]->removeAction("content_epoch_created");

      // Replace action with new epoch value.
      $GLOBALS["Hook"]->addAction("content_epoch_created", date($date_format, $epoch_created));

      $GLOBALS["Hook"]->addAction("content_epoch_date_time", date("Y-m-d H:i:sP", $epoch_created));
    }
  }

  public function getColumn($column) {

    if (!empty($_GET["path"]) && substr($_GET["path"], 0, 4) == "page") {

      // @TODO: Move the next few lines into its own method!

      $range = substr($_GET["path"], 5);

      $offset = $GLOBALS["Utility"]->getTag("posts_per_page") * ($range - 1);

      if (strlen($range) < 1 || !is_numeric($range) || $range < 1 || $offset < 1) {

        // No content exists within this range.

        $this->redirectInvalidAddress($column);

        return;
      }

      // @TODO: Change to read "content" instead of "posts" maybe?

      // Select posts by range.
      $statement = "

        SELECT *
        FROM " . DB_PREF . "content
        WHERE draft = 0
        AND type = 0
        ORDER BY epoch_created DESC
        LIMIT " . $GLOBALS["Utility"]->getTag("posts_per_page") . "
        OFFSET {$offset}
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Selection failed.
        $GLOBALS["Utility"]->displayError("failed to select posts within this range");
      }

      if ($Query->rowCount() == 0) {

        // No content exists within this range.

        $this->redirectInvalidAddress($column);

        return;
      }

      $columns = [];

      while ($Content = $Query->fetch(PDO::FETCH_OBJ)) {

        $column_data = $Content->$column;

        if (is_null($column_data)) {

          // Prevent null columns from being displayed.
          $column_data = "";
        }

        // Add the given column to the collection of columns.
        $columns[] = $column_data;
      }

      // Let extensions to hook into this.
      $GLOBALS["Hook"]->addAction("content_{$column}", $columns);

      for ($i = 0; $i < count($GLOBALS["Hook"]->doAction("content_{$column}")); ++$i) {

        // Suffix nested content tags with numbers to be replaced later.

        $item = $GLOBALS["Hook"]->doAction("content_{$column}")[$i];

        $item = preg_replace("/content_([^%]+)/", "content_$1_{$i}", $item);

        $GLOBALS["Hook"]->addAction("content_{$column}_{$i}", $item);
      }
    }
    else if (!empty($_GET["path"])) {

      // Select the given column.
      $statement = "

        SELECT {$column}
        FROM " . DB_PREF . "content
        WHERE url = ?
        AND draft = 0
        ORDER BY epoch_created DESC
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

      // Prevent SQL injections.
      $Query->bindParam(1, $_GET["path"]);

      if (!$Query->execute()) {

        // Selection failed.
        $GLOBALS["Utility"]->displayError("failed to select content '{$column}'");
      }

      if ($Query->rowCount() == 0) {

        // This content does not exist.

        $this->redirectInvalidAddress($column);

        return;
      }

      // Fetch the result as an object.
      $Content = $Query->fetch(PDO::FETCH_OBJ);

      $column_data = $Content->$column;

      if (is_null($column_data)) {

        // Prevent null columns from being displayed.
        $column_data = "";
      }

      // Let extensions hook into this column of data.
      $GLOBALS["Hook"]->addAction(

        "content_" . $column,

        $column_data
      );
    }
    else {

      // Select the given column.
      $statement = "

        SELECT {$column}
        FROM " . DB_PREF . "content
        WHERE draft = 0
        AND type = 0
        ORDER BY epoch_created DESC
        LIMIT " . $GLOBALS["Utility"]->getTag("posts_per_page") . "
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Selection failed.
        $GLOBALS["Utility"]->displayError("failed to select content {$column}");
      }

      $columns = [];

      while ($Content = $Query->fetch(PDO::FETCH_OBJ)) {

        $column_data = $Content->$column;

        if (is_null($column_data)) {

          // Prevent null columns from being displayed.
          $column_data = "";
        }

        // Add the given column to the collection of columns.
        $columns[] = $column_data;
      }

      // Let extensions to hook into this.
      $GLOBALS["Hook"]->addAction("content_{$column}", $columns);

      for ($i = 0; $i < count($GLOBALS["Hook"]->doAction("content_{$column}")); ++$i) {

        // Suffix nested content tags with numbers to be replaced later.

        $item = $GLOBALS["Hook"]->doAction("content_{$column}")[$i];

        $item = preg_replace("/content_([^%]+)/", "content_$1_{$i}", $item);

        $GLOBALS["Hook"]->addAction("content_{$column}_{$i}", $item);
      }
    }
  }

  public function getKeywords() {

    // Create hook action to later get keywords.
    $this->getColumn("keywords");

    $prefix = $GLOBALS["Utility"]->getTag("keyword_prefix");

    $keywords = $GLOBALS["Hook"]->doAction("content_keywords");

    $search_url = $GLOBALS["Utility"]->getTag("search_url");

    if (is_array($keywords)) {

      if (!empty($keywords)) {

        // Join array elements with a string.
        $keywords = implode(";", $keywords);

        $count = -1;

        foreach (explode(";", $keywords) as $index => $value) {

          ++$count;

          if (empty($value)) {

            // This post does not have any keywords.
            continue;
          }

          // Begin unordered list.
          $keywords_markup = "<ul class=\"keywords\">";

          foreach (explode(",", $value) as $item) {

            // Remove whitespace from the beginning and end of each keyword.
            $item = trim($item);

            // Encode spaces.
            $keyword_url = str_replace(" ", "%20", $item);

            // Create a list item for each keyword.
            $keywords_markup .= "

              <li>
                <a href=\"{$search_url}?keywords={$keyword_url}\">{$prefix}{$item}</a>
              </li>
            ";
          }

          // End unordered list.
          $keywords_markup .= "</ul>";

          $GLOBALS["Hook"]->removeAction("content_keywords_{$count}");

          $GLOBALS["Hook"]->addAction("content_keywords_{$count}", $keywords_markup);
        }
      }
    }
    else {

      if (!empty($keywords)) {

        // Begin unordered list.
        $keywords_markup = "<ul class=\"keywords\">";

        foreach (explode(",", $keywords) as $keyword) {

          // Remove whitespace from the beginning and end of each keyword.
          $keyword = trim($keyword);

          // Encode spaces.
          $keyword_url = str_replace(" ", "%20", $keyword);

          // Create a list item for each keyword.
          $keywords_markup .= "

            <li>
              <a href=\"{$search_url}?keywords={$keyword_url}\">{$prefix}{$keyword}</a>
            </li>
          ";
        }

        // End unordered list.
        $keywords_markup .= "</ul>";

        $GLOBALS["Hook"]->removeAction("content_keywords");

        $GLOBALS["Hook"]->addAction("content_keywords", $keywords_markup);
      }
    }
  }

  public function getPostBlocks() {

    $post_block_markup = $GLOBALS["Theme"]->getFileContents("post_block");

    $statement = "";

    // @TODO: Move the content pagination stuff to its own private method.

    if (!empty($_GET["path"]) && substr($_GET["path"], 0, 4) == "page") {

      $range = substr($_GET["path"], 5);

      $offset = $GLOBALS["Utility"]->getTag("posts_per_page") * ($range - 1);

      // Select posts by range.
      $statement = "

        SELECT *
        FROM " . DB_PREF . "content
        WHERE draft = 0
        AND type = 0
        ORDER BY epoch_created DESC
        LIMIT " . $GLOBALS["Utility"]->getTag("posts_per_page") . "
        OFFSET {$offset}
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Selection failed.
        $GLOBALS["Utility"]->displayError("failed to select posts within this range");
      }
    }
    else {

      // Select recent posts.
      $statement = "

        SELECT *
        FROM " . DB_PREF . "content
        WHERE draft = 0
        AND type = 0
        ORDER BY epoch_created DESC
        LIMIT " . $GLOBALS["Utility"]->getTag("posts_per_page") . "
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Selection failed.
        $GLOBALS["Utility"]->displayError("failed to select recent posts");
      }
    }

    $markup = "";

    if ($Query->rowCount() > 0) {

      $count = 0;

      while ($Query->fetch(PDO::FETCH_OBJ)) {

        // Suffix content tags with numbers to be replaced later.

        $markup .= preg_replace("/content_([^%]+)/", "content_$1_{$count}", $post_block_markup);

        ++$count;
      }
    }

    // Let extensions to hook into this.
    $GLOBALS["Hook"]->addAction("content_" . ((!empty($_GET["path"]) ? "range" : "recent")), $markup);
  }

  public function getDescriptions() {

    $this->getColumn("description");

    $description = $GLOBALS["Hook"]->doAction("content_description");

    if (is_array($description)) {

      $count = count($description);

      for ($i = 0; $i < $count; ++$i) {

        $new_description = empty($description[$i]) ? "No description." : $description[$i];

        $GLOBALS["Hook"]->removeAction("content_description_{$i}");

        $GLOBALS["Hook"]->addAction("content_description_{$i}", $new_description);
      }
    }
    else {

      $description = empty($description) ? "No description." : $description;

      $GLOBALS["Hook"]->removeAction("content_description");

      $GLOBALS["Hook"]->addAction("content_description", $description);
    }
  }

  public function getCommentSource() {

    $this->getColumn("allow_comments");

    // Check if comments are allowed on the current content.
    $allow_comments = $GLOBALS["Hook"]->doAction("content_allow_comments");

    if (!is_array($allow_comments)) {

      // Get the markup for the comment block.
      $comment_block_markup = $GLOBALS["Theme"]->getFileContents("comment_block");

      if (!$allow_comments) {

        // Comments are not allowed.

        $comment_block_markup = str_replace(

          "{%comment_source%}",

          "{%comment_disabled_text%}",

          $comment_block_markup
        );
      }

      // Display the comment block.
      $GLOBALS["Hook"]->addAction(

        "comments",

        $comment_block_markup
      );
    }
  }

  private function redirectInvalidAddress($column) {

    static $attempts = 1;

    if ($attempts > 1) {

      // Failed to get the 404 page. Redirect to index instead.
      header("Location: " . $GLOBALS["Utility"]->getRootAddress());

      exit();
    }

    ++$attempts;

    // Change the path to the 404 page URL.
    $_GET["path"] = trim(

      str_replace(

        $GLOBALS["Utility"]->getRootAddress(),

        "",

        $GLOBALS["Utility"]->getTag("404_url")
      ),

      "/"
    );

    // Attempt to get the information for the 404 page.
    $this->getColumn($column);
  }
}

?>
