<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class Utility {

  public function displayError($error_message) {

    if (ob_get_status()["level"] > 0) {

      // Clear the output buffer.
      ob_end_clean();
    }

    // Display the error message.
    exit("<b>Error</b>: {$error_message}.");
  }

  public function getProtocol() {

    if (!empty($_SERVER["HTTP_X_FORWARDED_PROTO"])) {

      return $_SERVER["HTTP_X_FORWARDED_PROTO"] . "://";
    }
    else {

      if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off") {

        return "https://";
      }
      else {

        return "http://";
      }
    }
  }

  public function getRootAddress() {

    $host = $_SERVER["HTTP_HOST"];

    $protocol = $this->getProtocol();

    $sub_directory = substr(dirname(dirname(__DIR__)), strlen($_SERVER["DOCUMENT_ROOT"]));

    // Get the absolute URL of where Kaku is installed.
    $root_address = $protocol . $host . $sub_directory;

    // Let extensions hook into the root address.
    $GLOBALS["Hook"]->addAction("root_address", $root_address);

    return $GLOBALS["Hook"]->doAction("root_address");
  }

  public function getTag($tag_title) {

    // Get the specified tag and replace any nested tags contained within it.
    return $this->replaceNestedTags("{%{$tag_title}%}");
  }

  public function replaceNestedTags($content) {

    $search = [];
    $replace = [];

    for ($i = 0; $i < 5; ++$i) {

      // Select the tags.
      $statement = "

        SELECT title, body
        FROM " . DB_PREF . "tags
        ORDER BY id DESC
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Selection failed.
        $this->displayError("failed to select tags");
      }

      if ($Query->rowCount() == 0) {

        // The tags table is empty.
        $this->displayError("\"tags\" table returned zero rows");
      }

      while ($Tag = $Query->fetch(PDO::FETCH_OBJ)) {

        if (strpos($content, $Tag->title) !== false) {

          // Replace tag calls with values from the database.

          $GLOBALS["Hook"]->addAction($Tag->title, $Tag->body);

          $search[] = "{%{$Tag->title}%}";
          $replace[] = $GLOBALS["Hook"]->doAction($Tag->title);
        }
      }

      // Replace nested tags within the given content.
      $content = str_replace($search, $replace, $content);
    }

    // Replace the protocol tag, assuming it is present.
    return str_replace("{%protocol%}", $this->getProtocol(), $content);
  }
}

?>
