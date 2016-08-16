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

if (isset($_GET["id"]) && !empty($_GET["id"])) {

  if (isset($_GET["delete"])) {

    $statement = "

      DELETE FROM " . DB_PREF . "users
      WHERE id = ?
    ";

    $Query = $Database->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["id"]);

    $Query->execute();

    if (!$Query) {

      $message = "failed to delete user";

      // Failed to delete user.
      header("Location: ./users.php?code=0&message={$message}");

      exit();
    }

    $message = "user deleted successfully";

    // User successfully deleted.
    header("Location: ./users.php?code=1&message={$message}");

    exit();
  }

  $statement = "

    SELECT username
    FROM " . DB_PREF . "users
    WHERE id = ?
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Something went wrong.
    $Utility->displayError("failed to select user name");
  }

  if ($Query->rowCount() == 0) {

    // This user does not exist.
    $body .= "

      There exists no user with an ID of " . $_GET["id"] . ".

      <a href=\"users.php\" class=\"button_return\">Return</a>
    ";
  }
  else {

    // Get the user's name, and encode { and } to prevent them from being replaced by the output buffer.
    $user_name = str_replace(["{", "}"], ["&#123;", "&#125;"], $Query->fetch(PDO::FETCH_OBJ)->username);

    $body .= "

      Are you sure you want to delete the \"{$user_name}\" user?<br>

      <a href=\"{%blog_url%}/admin/delete_user.php?id=" . $_GET["id"] . "&delete=true\" class=\"button\">Yes</a>
      <a href=\"{%blog_url%}/admin/users.php\" class=\"button\">No</a>
    ";
  }
}
else {

  // No ID given.
  $body .= "

    No ID supplied.

    <a href=\"{%blog_url%}/admin/users.php\" class=\"button_return\">Return</a>
  ";
}

$replace[] = "Delete User";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
