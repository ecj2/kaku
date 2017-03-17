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

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

?>
