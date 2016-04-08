<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

// Get the absolute URL of where Kaku is installed.
$root_address = $Utility->getRootAddress();

if (!isset($_GET["code"])) {

  // No error code was given. Redirect to index.
  header("Location: {$root_address}");
}

switch ($_GET["code"]) {

  case 404:

    // Select the URL for 404 page.
    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = '404_url'
      LIMIT 1
    ";

    $Query = $Database->getHandle()->query($statement);

    if (!$Query) {

      // Query failed. Redirect to root index.
      header("Location: {$root_address}");
    }
    else if ($Query->rowCount() == 0) {

      // The tag 404_url does not exist. Redirect to root index.
      header("Location: {$root_address}");
    }
    else {

      // Get the URL for the 404 page.
      $error_destination = $Query->fetch(PDO::FETCH_OBJ)->body;

      // Replace nested tags in 404 page URL.
      $error_destination = $GLOBALS["Utility"]->replaceNestedTags($error_destination);

      // Redirect to the 404 page.
      header("Location: {$error_destination}");
    }
  break;

  default:

    // Unknown error code. Redirect to index.
    header("Location: {$root_address}");
  break;
}

?>
