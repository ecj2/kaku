<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

class Utility {

  public function displayError($message) {

    if (ob_get_status()["level"] > 0) {

      // Clear the buffer.
      ob_end_clean();
    }

    // Terminate with an error message.
    exit("Error: {$message}.");
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

    $sub_directory = substr(

      dirname(dirname(__DIR__)),

      strlen($_SERVER["DOCUMENT_ROOT"])
    );

    // Get the absolute URL of where Kaku is installed.
    $root_address = $protocol . $host . $sub_directory;

    $GLOBALS["Hook"]->addAction(

      "root_address",

      $root_address
    );

    return $GLOBALS["Hook"]->doAction("root_address");
  }

  public function replaceNestedTags($content) {

    // Select the recursion depth tag.
    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = 'recursion_depth'
      ORDER BY id DESC
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if (!$Query) {

      // Something went wrong.
      $this->displayError("failed to select recursion_depth");
    }

    if ($Query->rowCount() == 0) {

      // The recursion_depth tag does not exist.
      $this->displayError("the recursion_depth tag does not exist");
    }

    // Get the recursion depth.
    $recursion_depth = $Query->fetch(PDO::FETCH_OBJ)->body;

    $search = [];
    $replace = [];

    for ($i = 0; $i < $recursion_depth; ++$i) {

      // Select the tags.
      $statement = "

        SELECT title, body
        FROM " . DB_PREF . "tags
        ORDER BY id DESC
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query) {

        // Something went wrong.
        $this->displayError("failed to select tags");
      }

      if ($Query->rowCount() == 0) {

        // The tags do not exist.
        $this->displayError("tags do not exist");
      }

      while ($Tag = $Query->fetch(PDO::FETCH_OBJ)) {

        if (strpos($content, $Tag->title) !== false) {

          // Replace tag calls with values from the database.

          $GLOBALS["Hook"]->addAction(

            $Tag->title,

            $Tag->body
          );

          $search[] = "{%{$Tag->title}%}";
          $replace[] = $GLOBALS["Hook"]->doAction($Tag->title);
        }
      }

      // Replace nested tags inside the content.
      $content = str_replace($search, $replace, $content);
    }

    // Replace the protocol tag, assuming it is present.
    return str_replace("{%protocol%}", $this->getProtocol(), $content);
  }
}

?>
