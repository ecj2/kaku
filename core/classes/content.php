<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Content {

  // @TODO: Improve documentation.

  private $identification;

  public function __construct($identification = null) {

    if ($identification === null) {

      if (!empty($_GET["path"]) && substr($_GET["path"], 0, 4) != "page") {

        $identification = $this->getColumn("id");
      }
    }

    $this->identification = $identification;
  }

  public function getAuthor() {

    // Create hook action to later get author nicknames.
    $this->getColumn("author_id");

    $author_id = $GLOBALS["Hook"]->doAction("content_author_id");

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

  public function getEpoch() {

    $this->getColumn("epoch_created");

    $date_format = $GLOBALS["Utility"]->getTag("date_format");

    $epoch_created = $GLOBALS["Hook"]->doAction("content_epoch_created");

    // Remove action to be replaced later.
    $GLOBALS["Hook"]->removeAction("content_epoch_created");

    // Replace action with new epoch value.
    $GLOBALS["Hook"]->addAction("content_epoch_created", date($date_format, $epoch_created));

    $GLOBALS["Hook"]->addAction("content_epoch_date_time", date("Y-m-d H:i:sP", $epoch_created));
  }

  public function getColumn($column) {

    // Select the given column.
    if ($this->identification === null) {

      $statement = "

        SELECT {$column}
        FROM " . DB_PREF . "content
        WHERE url = ?
        AND draft = 0
        ORDER BY epoch_created DESC
        LIMIT 1
      ";
    }
    else {

      $statement = "

        SELECT {$column}
        FROM " . DB_PREF . "content
        WHERE id = ?
        AND draft = 0
        ORDER BY epoch_created DESC
        LIMIT 1
      ";
    }

    $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

    // Prevent SQL injections.
    if ($this->identification === null) {

      $Query->bindParam(1, $_GET["path"]);
    }
    else {

      $Query->bindParam(1, $this->identification);
    }

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
    $GLOBALS["Hook"]->addAction("content_" . $column, $column_data);
  }

  public function getKeywords() {

    // Create hook action to later get keywords.
    $this->getColumn("keywords");

    $prefix = $GLOBALS["Utility"]->getTag("keyword_prefix");

    $keywords = $GLOBALS["Hook"]->doAction("content_keywords");

    $search_url = $GLOBALS["Utility"]->getTag("search_url");

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

  public function getDescription() {

    $this->getColumn("description");

    $description = $GLOBALS["Hook"]->doAction("content_description");

    $description = empty($description) ? "No description." : $description;

    $GLOBALS["Hook"]->removeAction("content_description");

    $GLOBALS["Hook"]->addAction("content_description", $description);
  }

  public function getCommentSource() {

    $this->getColumn("allow_comments");

    // Check if comments are allowed on the current content.
    $allow_comments = $GLOBALS["Hook"]->doAction("content_allow_comments");

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

  public function redirectInvalidAddress($column) {

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
