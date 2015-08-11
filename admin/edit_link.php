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

$page_title = "Edit Link";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "The link has been saved.";
  }
  else {

    $page_body .= "Failed to save link.";
  }

  $page_body .= "<a href=\"links.php\" class=\"button_return\">Return</a>";
}
else if (isset($_POST["uri"]) && isset($_POST["title"]) && isset($_POST["target"])) {

  $statement = "

    UPDATE " . DB_PREF . "links
    SET uri = ?, title = ?, target = ?
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["uri"]);
  $query->bindParam(2, $_POST["title"]);
  $query->bindParam(3, $_POST["target"]);
  $query->bindParam(4, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Failed to update link.
    header("Location: ./edit_link.php?id=" . $_GET["id"] . "&result=failure");
  }

  // Successfully updated link.
  header("Location: ./edit_link.php?id=" . $_GET["id"] . "&result=success");
}
else {

  $statement = "

    SELECT uri, title, target
    FROM " . DB_PREF . "links
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Query failed.
    $page_body .= "Failed to select link data.";
    $page_body .= "<a href=\"links.php\" class=\"button_return\">Return</a>";
  }
  else if ($query->rowCount() == 0) {

    // Query returned zero rows.
    $page_body .= "There exists no link with an ID of " . $_GET["id"] . ".";
    $page_body .= "<a href=\"links.php\" class=\"button_return\">Return</a>";
  }
  else {

    $link = $query->fetch(PDO::FETCH_OBJ);

    $uri = $link->uri;
    $title = $link->title;
    $target = $link->target;

    $page_body .= "

      <form method=\"post\" class=\"edit_link\">
        <label for=\"uri\">URI</label>
        <input type=\"text\" id=\"uri\" name=\"uri\" value=\"{$uri}\"
        required>
        <label for=\"title\">Title</label>
        <input type=\"text\" id=\"title\" name=\"title\" value=\"{$title}\"
        required>
        <label for=\"target\">Target</label>
        <input type=\"text\" id=\"target\" name=\"target\" value=\"{$target}\"
        required>
        <input type=\"submit\" value=\"Save\">
      </form>
    ";
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
