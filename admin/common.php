<?php

session_start();

$script_name = basename($_SERVER["SCRIPT_NAME"], ".php");

if ($script_name == "login" || $script_name == "reset_password") {

  if (isset($_SESSION["username"])) {

    // User is already logged in.
    header("Location: dashboard.php");

    exit();
  }
}
else {

  if (!isset($_SESSION["username"])) {

    // User is not logged in.
    header("Location: login.php");

    exit();
  }
}

require "../core/includes/common.php";

if ($script_name != "extensions") {

  $Extension->loadExtensions();
}

$theme = "";

if ($script_name == "login" || $script_name == "reset_password") {

  $theme = $Theme->getFileContents("login", true);
}
else {

  $theme = $Theme->getFileContents("template", true);
}

$search = [];
$replace = [];

$search[] = "{%page_title%}";
$search[] = "{%page_body%}";

$body = "";

// Clear these tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

?>
