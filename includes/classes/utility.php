<?php

// Prevent direct access to this file.
if (!defined("KAKU_INCLUDE")) exit();

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

    $protocol = $_SERVER["SERVER_PROTOCOL"];

    $protocol =  strtolower(substr($protocol, 0, strpos($protocol, "/")));

    $protocol .= "://";

    $sub_directory = substr(

      dirname(dirname(__DIR__)),

      strlen($_SERVER["DOCUMENT_ROOT"])
    );

    // Return absolute URL of where Kaku is installed.
    return $protocol . $host . $sub_directory;
  }
}

?>
