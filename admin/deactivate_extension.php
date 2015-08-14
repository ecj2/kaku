<?php

session_start();

if (!isset($_SESSION["username"])) {

  header("Location: ./login.php");
}

require "../includes/configuration.php";

require "../includes/classes/utility.php";
require "../includes/classes/database.php";

require "../includes/classes/hook.php";
require "../includes/classes/output.php";

global $Hook;

$Hook = new Hook;

$Output = new Output;
$Utility = new Utility;
$Database = new Database;

$Database->connect();

$Output->setDatabaseHandle($Database->getHandle());

$Output->startBuffer();

$statement = "

  SELECT body
  FROM " . DB_PREF . "tags
  WHERE title = 'admin_theme_name'
  ORDER BY id DESC
";

$query = $Database->getHandle()->query($statement);

if (!$query || $query->rowCount() == 0) {

  // Query failed or returned zero rows.
  $Utility->displayError("failed to get admin theme name");
}

// Get the admin theme name.
$theme_name = $query->fetch(PDO::FETCH_OBJ)->body;

if (!file_exists("content/themes/{$theme_name}/template.html")) {

  // Theme template does not exist.
  $Utility->displayError("theme template file does not exist");
}

// Display the theme contents.
echo file_get_contents("content/themes/{$theme_name}/template.html");

$page_body = "";

$page_title = "Deactivate Extension";

if (isset($_GET["title"])) {

  $statement = "

    SELECT title
    FROM " . DB_PREF . "extensions
    WHERE title = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_GET["title"]);

  $query->execute();

  if (!$query) {

    // Query failed.
    $page_body = "A database error occurred...";
    $page_body .= "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
  }
  else if ($query->rowCount() == 0) {

    $title = $_GET["title"];

    // Extension doesn't exist in database.
    $page_body = "Error: there exists no extension by the title of \"{$title}\"!";
    $page_body .= "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
  }
  else {

    // Fetch result as object.
    $result = $query->fetch(PDO::FETCH_OBJ);

    // Get extension title.
    $title = $result->title;

    // Deactivate extension.
    $statement = "

      UPDATE " . DB_PREF . "extensions
      SET activate = '0'
      WHERE title = '{$title}'
    ";

    $query = $Database->getHandle()->query($statement);

    if (!$query) {

      $page_body = "Error: failed to deactivate extension!";
      $page_body .= "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
    }
    else {

      $page_body = "The extension has been deactivated.";
      $page_body .= "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
    }
  }
}
else {

  $page_body = "Error: no extension title specified!";
  $page_body .= "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
}

$Output->addTagReplacement(

  "page_body",

  $page_body
);

$Output->addTagReplacement(

  "page_title",

  $page_title
);

$Output->replaceTags();

$Output->flushBuffer();

$Database->disconnect();

?>
