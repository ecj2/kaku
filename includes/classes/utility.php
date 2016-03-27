<?php

class Utility {

  public function __construct() {

    //
  }

  public static function displayError($message) {

    // Clear the buffer.
    ob_end_clean();

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

    // Return absolute URL of where Kaku is installed.
    return $protocol . $host . $sub_directory;
  }
}

?>
