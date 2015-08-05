<?php

class Utility {

  public function __construct() {

    //
  }

  public static function displayError($message) {

    // Clear the buffer.
    ob_end_clean();

    exit("Error: {$message}.");
  }

  public static function getRootAddress() {

    $host = $_SERVER["HTTP_HOST"];

    $protocol = "http://";

    $sub_directory = substr(

      dirname(dirname(__DIR__)),

      strlen($_SERVER["DOCUMENT_ROOT"])
    );

    return $protocol . $host . $sub_directory;
  }
}

?>
