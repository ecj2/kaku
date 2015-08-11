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

$page_title = "Delete Page";

function pageExists($Database) {

  $statement = "

    SELECT id
    FROM " . DB_PREF . "pages
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Query failed.
    return false;
  }
  else if ($query->rowCount() == 0) {

    // Page does not exist.
    return false;
  }

  // Page exists.
  return true;
}

if (isset($_GET["delete"])) {

  //
  $statement = "

    DELETE FROM " . DB_PREF . "pages
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_GET["id"]);

  $query->execute();

  $id = $_GET["id"];

  if (!$query) {

    // Failed to delete page.
    header("Location: ./delete_page.php?id={$id}&result=failure");
  }

  // Successfully deleted page.
  header("Location: ./delete_page.php?id={$id}&result=success");
}
else {

  if (isset($_GET["result"])) {

    if ($_GET["result"] == "success") {

      $page_body .= "The page has been deleted.";
    }
    else {

      $page_body .= "Failed to delete page.";
    }

    $page_body .= "<a href=\"pages.php\" class=\"button_return\">Return</a>";
  }
  else {

    $page_body .= "Are you sure you want to delete this page?<br>";

    $id = $_GET["id"];

    $page_body .= "

      <a href=\"delete_page.php?id={$id}&delete=true\" class=\"button\">Yes</a>
      <a href=\"pages.php\" class=\"button\">No</a>
    ";

    if (!pageExists($Database)) {

      $page_body = "There exists no page with an ID of " . $_GET["id"] . ".";
      $page_body .= "<a href=\"pages.php\" class=\"button_return\">Return</a>";
    }
  }
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
