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

      // Select the recursion depth tag.
      $statement = "

        SELECT body
        FROM " . DB_PREF . "tags
        WHERE title = 'recursion_depth'
        ORDER BY id DESC
        LIMIT 1
      ";

      $Query = $GLOBALS["Database"]->getHandle()->query($statement);

      if (!$Query || $Query->rowCount() == 0) {

        // Something went wrong.
        header("Location: {$root_address}");
      }

      // Get the recursion depth.
      $recursion_depth = $Query->fetch(PDO::FETCH_OBJ)->body;

      $search = [];
      $replace = [];

      for ($i = 0; $i < $recursion_depth; ++$i) {

        // Select the tags from the database.
        $statement = "

          SELECT title, body
          FROM " . DB_PREF . "tags
          ORDER BY id DESC
        ";

        $Query = $GLOBALS["Database"]->getHandle()->query($statement);

        if (!$Query || $Query->rowCount() == 0) {

          // Something went wrong.
          header("Location: {$root_address}");
        }

        while ($Tag = $Query->fetch(PDO::FETCH_OBJ)) {

          if (strpos($error_destination, $Tag->title) !== false) {

            // Replace tag calls with values from the database.

            $GLOBALS["Hook"]->addAction(

              $Tag->title,

              $Tag->body
            );

            $search[] = "{%{$Tag->title}%}";
            $replace = $GLOBALS["Hook"]->doAction($Tag->title);
          }
        }
      }

      // Replace nested tags inside the error destination.
      $error_destination = str_replace($search, $replace, $error_destination);

      header("Location: {$error_destination}");
    }
  break;

  default:

    // Unknown error code. Redirect to index.
    header("Location: {$root_address}");
  break;
}

?>
