<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class Post {

  public function getBody() {

    $this->getData("body");
  }

  public function getTitle() {

    $this->getData("title");
  }

  public function getAuthor() {

    if (isset($_GET["post"])) {

      // Select the author ID.
      $statement = "

        SELECT author_id
        FROM " . DB_PREF . "posts
        WHERE url = ? AND draft = 0
        ORDER BY epoch DESC
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

      // Prevent SQL injections.
      $Query->bindParam(1, $_GET["post"]);

      $Query->execute();

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select post author ID");
      }

      if ($Query->rowCount() == 0) {

        // The post author ID does not exist, or the post is a draft.
        header("Location: " . $GLOBALS["Utility"]->getRootAddress() . "/error?code=404");
      }

      // Get the post's author ID.
      $author_id = $Query->fetch(PDO::FETCH_OBJ)->author_id;

      // Select the user's nickname.
      $statement = "

        SELECT nickname
        FROM " . DB_PREF . "users
        WHERE id = '{$author_id}'
        ORDER BY id DESC
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select post author nickname");
      }

      if ($Query->rowCount() == 0) {

        // The author nickname does not exist.
        $GLOBALS["Hook"]->addAction(

          "post_author",

          "Unknown"
        );
      }
      else {

        // Allow extensions to hook into the author nickname.
        $GLOBALS["Hook"]->addAction(

          "post_author",

          $Query->fetch(PDO::FETCH_OBJ)->nickname
        );
      }
    }
    else {

      // Select posts per page.
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
        $GLOBALS["Utility"]->displayError("failed to select posts per page tag");
      }

      if ($Query->rowCount() == 0) {

        // Posts per page does not exist.
        $GLOBALS["Utility"]->displayError("posts per page tag does not exist");
      }

      // Get the posts per page.
      $posts_per_page = $Query->fetch(PDO::FETCH_OBJ)->body;

      // Replace nested tags in the posts per page.
      $posts_per_page = $GLOBALS["Utility"]->replaceNestedTags($posts_per_page);

      $statement = "";

      if (isset($_GET["range"])) {

        // Get range offset.
        $offset = $posts_per_page * ($_GET["range"] - 1);

        // Select the author IDs.
        $statement = "

          SELECT author_id
          FROM " . DB_PREF . "posts
          WHERE draft = 0
          ORDER BY epoch DESC
          LIMIT {$posts_per_page}
          OFFSET {$offset}
        ";
      }
      else {

        // Select the author IDs.
        $statement = "

          SELECT author_id
          FROM " . DB_PREF . "posts
          WHERE draft = 0
          ORDER BY epoch DESC
          LIMIT {$posts_per_page}
        ";
      }

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select post author IDs");
      }

      $authors = [];

      while ($Result = $Query->fetch(PDO::FETCH_OBJ)) {

        // Get the posts' author IDs.
        $author_id = $Result->author_id;

        // Select the user's nickname.
        $statement = "

          SELECT nickname
          FROM " . DB_PREF . "users
          WHERE id = '{$author_id}'
          ORDER BY id DESC
          LIMIT 1
        ";

        $SubQuery = $GLOBALS["Database"]->getHandle()->query($statement);

        if (!$SubQuery) {

          // Something went wrong.
          $GLOBALS["Utility"]->displayError("failed to select post author nicknames");
        }

        if ($SubQuery->rowCount() == 0) {

          // The author nickname does not exist.
          $authors[] = "Unknown";
        }
        else {

          // Get the user's nickname.
          $authors[] = $SubQuery->fetch(PDO::FETCH_OBJ)->nickname;
        }
      }

      $GLOBALS["Hook"]->addAction(

        "post_author",

        $authors
      );

      $count = 0;

      foreach ($GLOBALS["Hook"]->doAction("post_author") as $item) {

        $GLOBALS["Hook"]->addAction("post_author_{$count}", $item);

        ++$count;
      }
    }
  }

  public function getKeywords() {

    $this->getData("keywords");
  }

  public function getDescription() {

    $this->getData("description");
  }

  public function getAbsoluteEpoch() {

    $this->getEpoch("absolute");
  }

  public function getRelativeEpoch() {

    $this->getEpoch("relative");
  }

  public function getDateTimeEpoch() {

    $this->getEpoch("date_time");
  }

  public function getEpoch($type) {

    if (isset($_GET["post"])) {

      // Select the date format.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'date_format'
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select date format tag");
      }

      if ($Query->rowCount() == 0) {

        // The date format tag does not exist.
        $GLOBALS["Utility"]->displayError("date format tag does not exist");
      }

      // Get the date format.
      $date_format = $Query->fetch(PDO::FETCH_OBJ)->body;

      // Replace nested tags in the date format.
      $date_format = $GLOBALS["Utility"]->replaceNestedTags($date_format);

      // Select the post's epoch.
      $statement = "

        SELECT epoch
        FROM " . DB_PREF . "posts
        WHERE url = ?
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

      // Prevent SQL injections.
      $Query->bindParam(1, $_GET["post"]);

      $Query->execute();

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select post epoch");
      }

      if ($Query->rowCount() == 0) {

        // The post does not exist or is a draft.
        header("Location: " . $GLOBALS["Utility"]->getRootAddress() . "/error?code=404");
      }

      switch ($type) {

        case "absolute":

          // Allow extensions to hook into the absolute epoch.
          $GLOBALS["Hook"]->addAction(

            "post_{$type}_epoch",

            date($date_format, $Query->fetch(PDO::FETCH_OBJ)->epoch)
          );
        break;

        case "relative":

          $difference = time() - $Query->fetch(PDO::FETCH_OBJ)->epoch;

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

          // Allow extensions to hook into the relative epoch.
          $GLOBALS["Hook"]->addAction(

            "post_{$type}_epoch",

            floor($epoch_value[0]) . " " . $epoch_value[1]
          );
        break;

        case "date_time":

          // Allow extensions to hook into the date time epoch.
          $GLOBALS["Hook"]->addAction(

            "post_{$type}_epoch",

            date("Y-m-d H:i:sP", $Query->fetch(PDO::FETCH_OBJ)->epoch)
          );
        break;
      }
    }
    else {

      // Select the date format.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'date_format'
        ORDER BY id DESC
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select date format tag");
      }

      if ($Query->rowCount() == 0) {

        // The date format tag does not exist.
        $GLOBALS["Utility"]->displayError("date format tag does not exist");
      }

      // Get the date format.
      $date_format = $Query->fetch(PDO::FETCH_OBJ)->body;

      // Replace nested tags in the date format.
      $date_format = $GLOBALS["Utility"]->replaceNestedTags($date_format);

      // Select posts per page.
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
        $GLOBALS["Utility"]->displayError("failed to select posts per page tag");
      }

      if ($Query->rowCount() == 0) {

        // Posts per page does not exist.
        $GLOBALS["Utility"]->displayError("posts per page tag does not exist");
      }

      // Get the posts per page.
      $posts_per_page = $Query->fetch(PDO::FETCH_OBJ)->body;

      // Replace nested tags in the posts per page.
      $posts_per_page = $GLOBALS["Utility"]->replaceNestedTags($posts_per_page);

      $statement = "";

      if (isset($_GET["range"])) {

        // Get range offset.
        $offset = $posts_per_page * ($_GET["range"] - 1);

        // Select the epochs.
        $statement = "

          SELECT epoch
          FROM " . DB_PREF . "posts
          WHERE draft = 0
          ORDER BY epoch DESC
          LIMIT {$posts_per_page}
          OFFSET {$offset}
        ";
      }
      else {

        // Select the epochs.
        $statement = "

          SELECT epoch
          FROM " . DB_PREF . "posts
          WHERE draft = 0
          ORDER BY epoch DESC
          LIMIT {$posts_per_page}
        ";
      }

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select post epochs");
      }

      switch ($type) {

        case "absolute":

          $epochs = [];

          while ($Result = $Query->fetch(PDO::FETCH_OBJ)) {

            $epochs[] = date($date_format, $Result->epoch);
          }

          $GLOBALS["Hook"]->addAction(

            "post_{$type}_epoch",

            $epochs
          );

          $count = 0;

          foreach ($GLOBALS["Hook"]->doAction("post_{$type}_epoch") as $item) {

            $GLOBALS["Hook"]->addAction("post_{$type}_epoch_{$count}", $item);

            ++$count;
          }
        break;

        case "relative":

          $epochs = [];

          while ($Result = $Query->fetch(PDO::FETCH_OBJ)) {

            $difference = time() - $Result->epoch;

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

            $epochs[] = floor($epoch_value[0]) . " " . $epoch_value[1];
          }

          $GLOBALS["Hook"]->addAction(

            "post_{$type}_epoch",

            $epochs
          );

          $count = 0;

          foreach ($GLOBALS["Hook"]->doAction("post_{$type}_epoch") as $item) {

            $GLOBALS["Hook"]->addAction("post_{$type}_epoch_{$count}", $item);

            ++$count;
          }
        break;

        case "date_time":

          $epochs = [];

          while ($Result = $Query->fetch(PDO::FETCH_OBJ)) {

            $epochs[] = date("Y-m-d H:i:sP", $Result->epoch);
          }

          $GLOBALS["Hook"]->addAction(

            "post_{$type}_epoch",

            $epochs
          );

          $count = 0;

          foreach ($GLOBALS["Hook"]->doAction("post_{$type}_epoch") as $item) {

            $GLOBALS["Hook"]->addAction("post_{$type}_epoch_{$count}", $item);

            ++$count;
          }
        break;
      }
    }
  }

  public function getUniformResourceLocator() {

    $this->getData("url");
  }

  public function getData($column) {

    if (isset($_GET["post"])) {

      // Select the given column.
      $statement = "

        SELECT {$column}
        FROM " . DB_PREF . "posts
        WHERE url = ? AND draft = 0
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

      // Prevent SQL injections.
      $Query->bindParam(1, $_GET["post"]);

      $Query->execute();

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select post {$column}");
      }

      if ($Query->rowCount() == 0) {

        // The column does not exist or belongs to a draft.
        header("Location: " . $GLOBALS["Utility"]->getRootAddress() . "/error?code=404");
      }

      // Allow extensions to hook into this column of data.
      $GLOBALS["Hook"]->addAction(

        "post_{$column}",

        $Query->fetch(PDO::FETCH_OBJ)->$column
      );
    }
    else {

      // Select posts per page.
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
        $GLOBALS["Utility"]->displayError("failed to select posts per page tag");
      }

      if ($Query->rowCount() == 0) {

        // Posts per page does not exist.
        $GLOBALS["Utility"]->displayError("posts per page tag does not exist");
      }

      // Get the posts per page.
      $posts_per_page = $Query->fetch(PDO::FETCH_OBJ)->body;

      // Replace nested tags in the posts per page.
      $posts_per_page = $GLOBALS["Utility"]->replaceNestedTags($posts_per_page);

      $statement = "";

      if (isset($_GET["range"])) {

        // Get range offset.
        $offset = $posts_per_page * ($_GET["range"] - 1);

        // Select the given column.
        $statement = "

          SELECT {$column}
          FROM " . DB_PREF . "posts
          WHERE draft = 0
          ORDER BY epoch DESC
          LIMIT {$posts_per_page}
          OFFSET {$offset}
        ";
      }
      else {

        // Select the given column.
        $statement = "

          SELECT {$column}
          FROM " . DB_PREF . "posts
          WHERE draft = 0
          ORDER BY epoch DESC
          LIMIT {$posts_per_page}
        ";
      }

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Something went wrong.
        $GLOBALS["Utility"]->displayError("failed to select post {$column}");
      }

      $columns = [];

      while ($Result = $Query->fetch(PDO::FETCH_OBJ)) {

        // Add the given column to the collection of columns.
        $columns[] = $Result->$column;
      }

      // Allow extensions to hook into this.
      $GLOBALS["Hook"]->addAction(

        "post_{$column}",

        $columns
      );

      $count = 0;

      foreach ($GLOBALS["Hook"]->doAction("post_{$column}") as $item) {

        // Suffix each of the columns with a number.
        $GLOBALS["Hook"]->addAction("post_{$column}_{$count}", $item);

        ++$count;
      }
    }
  }

  public function getBlocks() {

    $post_block_markup = $GLOBALS["Template"]->getFileContents("post_block");

    // Select the number of posts allowed per page.
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
      $GLOBALS["Utility"]->displayError("failed to select posts per page tag");
    }

    if ($Query->rowCount() == 0) {

      // The posts per page tag does not exist.
      $GLOBALS["Utility"]->displayError("posts per page tag does not exist");
    }

    // Get the posts per page.
    $posts_per_page = $Query->fetch(PDO::FETCH_OBJ)->body;

    // Replace nested tags in the posts per page.
    $posts_per_page = $GLOBALS["Utility"]->replaceNestedTags($posts_per_page);

    $statement = "";

    if (isset($_GET["range"])) {

      $offset = $posts_per_page * ($_GET["range"] - 1);

      // Select everything from the latest posts by range.
      $statement = "

        SELECT *
        FROM " . DB_PREF . "posts
        WHERE draft = 0
        ORDER BY epoch DESC
        LIMIT {$posts_per_page}
        OFFSET {$offset}
      ";
    }
    else {

      // Select everything from the latest posts.
      $statement = "

        SELECT *
        FROM " . DB_PREF . "posts
        WHERE draft = 0
        ORDER BY epoch DESC
        LIMIT {$posts_per_page}
      ";
    }

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if (!$Query) {

      // Something went wrong.
      $GLOBALS["Utility"]->displayError("failed to select the latest posts");
    }

    $markup = "";

    if ($Query->rowCount() > 0) {

      $count = 0;

      while ($Post = $Query->fetch(PDO::FETCH_OBJ)) {

        // These will be suffixed with numbers.
        $search = [

          "{%post_url%}",

          "{%post_body%}",

          "{%post_title%}",

          "{%post_keywords%}",

          "{%post_description%}",

          "{%post_relative_epoch%}",

          "{%post_absolute_epoch%}",

          "{%post_date_time_epoch%}",

          "{%post_author%}"
        ];

        $replace = [];

        foreach ($search as $item) {

          // Suffix the search items with numbers.
          $replace[] = preg_replace("~{%(.+)%}~", "{%$1_{$count}%}", $item);
        }

        $markup .= str_replace($search, $replace, $post_block_markup);

        ++$count;
      }
    }

    if (isset($_GET["range"])) {

      $GLOBALS["Hook"]->addAction(

        "post_range",

        $markup
      );
    }
    else {

      $GLOBALS["Hook"]->addAction(

        "post_latest",

        $markup
      );
    }
  }
}

?>
