<?php

session_start();

if (!isset($_SESSION["username"])) {

  // User is not logged in.
  header("Location: ./login.php");

  exit();
}

require "../core/includes/common.php";

$Output->startBuffer();

$Output->loadExtensions();

// Get template markup.
$template = $Template->getFileContents("template", 0, 1);

$search = [];
$replace = [];

$search[] = "{%page_title%}";
$search[] = "{%page_body%}";

$body = "";

if (!isset($_GET["title"]) || empty($_GET["title"])) {

  $body .= "

    No extension specified.

    <a href=\"./extensions.php\" class=\"button_return\">Return</a>
  ";
}
else if (!file_exists("../extensions/" . $_GET["title"] . "/edit.php")) {

  $body .= "

    This extension lacks an edit page.

    <a href=\"./extensions.php\" class=\"button_return\">Return</a>
  ";
}
else {

  // Start a new buffer for the extension's edit.php file.
  ob_start();

  $extension_directory = "../extensions/" . $_GET["title"];

  if (file_exists("{$extension_directory}/install.php")) {

    // Install the extension.
    require "{$extension_directory}/install.php";
  }

  require "{$extension_directory}/edit.php";

  // The extensions file's contents will be thrown to the buffer.
  $body .= ob_get_contents();

  // End the extension buffer.
  ob_end_clean();
}

$replace[] = "Edit Extension";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
