<?php

// Prevent direct access to this file.
if (!defined("KAKU_EXTENSION")) exit();

$name = "Redirect Manager";

class RedirectManager extends Utility {

  private $DatabaseHandle;

  public function __construct() {

    //
  }

  public function getTags() {

    return [

      "body_content"
    ];
  }

  public function getReplacements() {

    return [

      $this->manageRedirects()
    ];
  }

  public function setDatabaseHandle($Handle) {

    $this->DatabaseHandle = $Handle;
  }

  private function manageRedirects() {

    if (isset($_GET["post_url"])) {

      // Select the redirect rules from the database.
      $statement = "

        SELECT redirect_rules
        FROM " . DB_PREF . "extension_redirect_manager
        WHERE 1 = 1
        LIMIT 1
      ";

      $query = $this->DatabaseHandle->query($statement);

      if (!$query || $query->rowCount() == 0) {

        // Query failed or returned an empty set.
      }
      else {

        // Fetch the result as an object.
        $result = $query->fetch(PDO::FETCH_OBJ);

        preg_match_all(

          "/(.+?) = (.+?)\;/",

          $result->redirect_rules,

          $redirect
        );

        for ($i = 0; $i < count($redirect[0]); ++$i) {

          $this_url = Utility::getRootAddress() . "/post/";
          $this_url .= $_GET["post_url"];

          if (strstr($this_url, $redirect[1][$i])) {

            $new_url = Utility::getRootAddress() . "/post/";
            $new_url .= $redirect[2][$i];

            header("HTTP/1.1 301 Moved Permanently");

            header("Location: {$new_url}");

            exit();
          }
        }
      }
    }
  }
}

?>
