<?php

// Prevent direct access to this file.
if (!defined("KAKU_EXTENSION")) exit();

$name = "Redirect Manager";

class RedirectManager extends Utility {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function setDatabaseHandle($Handle) {

    $this->DatabaseHandle = $Handle;

    $this->manageRedirects();
  }

  private function manageRedirects() {

    // Select the redirect rules from the database.
    $statement = "

      SELECT redirect_rules
      FROM " . DB_PREF . "extension_redirect_manager
      WHERE 1 = 1
      LIMIT 1
    ";

    @$query = $this->DatabaseHandle->query($statement);

    if ($query && $query->rowCount() > 0) {

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      preg_match_all(

        "/(.+?) = (.+?)\;/",

        $result->redirect_rules,

        $redirect
      );

      for ($i = 0; $i < count($redirect[0]); ++$i) {

        $this_uri;

        if (isset($_SERVER["HTTPS"])) {

          $this_uri = "https://";
        }
        else {

          $this_uri = "http://";
        }

        // Get the current URI.
        $this_uri .= $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

        if (strtolower($this_uri) == strtolower($redirect[1][$i])) {

          // Redirect to specified URI.
          header("Location: " . $redirect[2][$i], true, 302);

          exit();
        }
      }
    }
  }
}

?>
