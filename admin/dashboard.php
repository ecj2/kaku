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

$statement = "

  SELECT nickname
  FROM " . DB_PREF . "users
  WHERE id = '" . $_SESSION["user_id"] . "'
  ORDER BY id DESC
  LIMIT 1
";

$Query = $Database->getHandle()->query($statement);

if (!$Query) {

  // Failed to select user's nickname.
  $Utility->displayError("failed to get user's nickname");
}

if ($Query->rowCount() == 0) {

  // No results for this user.
  $body = "Welcome to the dashboard. Use the navigation links to manage your blog.";
}
else {

  // Get the user's nickname.
  $nickname = $Query->fetch(PDO::FETCH_OBJ)->nickname;

  $body = "Welcome to the dashboard, {$nickname}. Use the navigation links to manage your blog.";
}

$replace[] = "Dashboard";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
