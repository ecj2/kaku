<?php

require "common.php";

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

echo str_replace($search, $replace, $theme);

?>
