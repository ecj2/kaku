<?php

session_start();

if (!isset($_SESSION["username"])) {

  // User is not logged in.
  header("Location: login.php");

  exit();
}

require "../core/includes/common.php";

// @TODO: Load extensions.

// Get template markup.
$theme = $Theme->getFileContents("template", true);

$search = [];
$replace = [];

$search[] = "{%page_title%}";
$search[] = "{%page_body%}";

$body = "";
$title = "";

if (isset($_GET["code"]) && isset($_GET["title"])) {

  $statement = "

    SELECT title
    FROM " . DB_PREF . "extensions
    WHERE title = ?
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_GET["title"]);

  $Query->execute();

  if (!$Query) {

    // Something went wrong.
    $Utility->displayError("failed to select extension title");
  }

  if ($Query->rowCount() == 0) {

    // Extension doesn't exist in database.
    $statement = "

      INSERT INTO " . DB_PREF . "extensions (

        title,

        activate
      )
      VALUES (

        ?,

        1
      )
    ";

    $Query = $Database->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["title"]);

    $Query->execute();

    if (!$Query) {

      // Something went wrong.
      $Utility->displayError("failed to insert new extension");
    }
    else {

      $code = $_GET["code"];
      $title = $_GET["title"];

      // Refresh the page to assign new activation status.
      header("Location: toggle_extension.php?code={$code}&title={$title}");

      exit();
    }
  }
  else {

    // Get extension's title.
    $extension_title = $Query->fetch(PDO::FETCH_OBJ)->title;

    $message = "";

    if ($_GET["code"] == 0) {

      $title = "Activate Extension";

      // Activate extension.
      $statement = "

        UPDATE " . DB_PREF . "extensions
        SET activate = '1'
        WHERE title = '{$extension_title}'
      ";

      $Query = $Database->getHandle()->query($statement);

      if (!$Query) {

        $message = "Failed to activate extension.";
      }
      else {

        $message = "The extension has been activated.";
      }
    }
    else {

      $title = "Deactivate Extension";

      // Deactivate extension.
      $statement = "

        UPDATE " . DB_PREF . "extensions
        SET activate = '0'
        WHERE title = '{$extension_title}'
      ";

      $Query = $Database->getHandle()->query($statement);

      if (!$Query) {

        $message = "Failed to deactivate extension.";
      }
      else {

        $message = "The extension has been deactivated.";
      }
    }

    $body = "

      {$message}

      <a href=\"{%blog_url%}/admin/extensions.php\" class=\"button_return\">Return</a>
    ";
  }
}
else {

  $body = "

    No extension title and/or code specified.

    <a href=\"{%blog_url%}/admin/extensions.php\" class=\"button_return\">Return</a>
  ";
}

$replace[] = $title;
$replace[] = $body;

echo str_replace($search, $replace, $theme);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

?>
