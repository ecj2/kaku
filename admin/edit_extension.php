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

$page_title = "Edit Extension";

if (!isset($_GET["title"]) || empty($_GET["title"])) {

  $page_body .= "No extension specified.";

  $page_body .= "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
}
else if (!file_exists("../content/extensions/" . $_GET["title"] . "/edit.php")) {

  $page_body .= "This extension lacks an edit page.";

  $page_body .= "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
}
else {

  // Start a new buffer for the extension's edit.php file.
  ob_start();

  $extension_directory = "../content/extensions/" . $_GET["title"];

  if (file_exists("{$extension_directory}/install.php")) {

    // Run the extension's install script.

    require "../install.php";

    require "{$extension_directory}/install.php";
  }

  require "{$extension_directory}/edit.php";

  // The extensions file's contents will be thrown to the buffer.
  $page_body .= ob_get_contents();

  // End the extension buffer.
  ob_end_clean();
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
