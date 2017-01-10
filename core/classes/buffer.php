<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Buffer {

  private $search;
  private $replace;

  public function __construct() {

    ob_start();

    $this->search = [];
    $this->replace = [];
  }

  public function __destruct() {

    for ($i = 0; $i < 5; ++$i) {

      $this->replaceTags();
    }

    $buffer_contents = $this->replaceBufferContents(ob_get_clean());

    for ($i = 0; $i < 5; ++$i) {

      $buffer_contents = $this->replaceBufferContents($buffer_contents);
    }

    echo $buffer_contents;
  }

  public function replaceTags() {

    for ($i = 0; $i < 5; ++$i) {

      // Select tags from the database.
      $statement = "

        SELECT title, body
        FROM " . DB_PREF . "tags
        ORDER BY id DESC
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Query failed.
        $GLOBALS["Utility"]->displayError("failed to select tags");
      }

      if ($Query->rowCount() > 0) {

        while ($Tag = $Query->fetch(PDO::FETCH_OBJ)) {

          // Get the contents of the buffer.
          $buffer_contents = $this->replaceBufferContents(ob_get_contents());

          if (strpos($buffer_contents, $Tag->title) !== false) {

            // Allow each database tag to be manipulated by hook filters.

            $GLOBALS["Hook"]->addAction($Tag->title, $Tag->body);

            $this->addTagReplacement($Tag->title, $GLOBALS["Hook"]->doAction($Tag->title));
          }
        }
      }
    }

    // Find unfilled tags that do not exist in the database.
    preg_match_all("/\{\%(.*?)\%\}/", $this->replaceBufferContents(ob_get_contents()), $matches);

    for ($i = 0; $i < count($matches[1]); ++$i) {

      // Replaced unfilled tags with hook actions.

      $GLOBALS["Hook"]->addAction($matches[1][$i], "{%" . $matches[1][$i] . "%}");

      $this->addTagReplacement($matches[1][$i], $GLOBALS["Hook"]->doAction($matches[1][$i]));
    }
  }

  // @TODO: load extensions.

  private function addTagReplacement($tag_title, $replacement) {

    $this->search[$tag_title] = "{%{$tag_title}%}";
    $this->replace[$tag_title] = $replacement;
  }

  public function replaceBufferContents($buffer_contents) {

    // Replace the tags contained within the buffer.
    return str_replace($this->search, $this->replace, $buffer_contents);
  }
}

?>
