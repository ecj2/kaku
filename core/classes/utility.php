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
}

?>
