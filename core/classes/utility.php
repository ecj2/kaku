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

  public static function getRootAddress() {

    $host = $_SERVER["HTTP_HOST"];

    $protocol;

    if (!empty($_SERVER["HTTP_X_FORWARDED_PROTO"])) {

      $protocol = $_SERVER["HTTP_X_FORWARDED_PROTO"] . "://";
    }
    else {

      if (!empty($_SERVER["HTTPS"])) {

        $protocol = "https://";
      }
      else {

        $protocol = "http://";
      }
    }

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
}

?>
