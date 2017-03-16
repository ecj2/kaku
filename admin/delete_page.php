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

if (isset($_GET["id"]) && !empty($_GET["id"])) {

  if (isset($_GET["delete"])) {

    $statement = "

      DELETE FROM " . DB_PREF . "content
      WHERE id = ?
      AND type = 1
    ";

    $Query = $Database->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["id"]);

    $Query->execute();

    if (!$Query) {

      $message = "failed to delete page";

      // Failed to delete page.
      header("Location: pages.php?code=0&message={$message}");

      exit();
    }

    $message = "page deleted successfully";

    // Page successfully deleted.
    header("Location: pages.php?code=1&message={$message}");

    exit();
  }

  $statement = "

    SELECT title
    FROM " . DB_PREF . "content
    WHERE id = ?
    AND type = 1
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Something went wrong.
    $Utility->displayError("failed to select page title");
  }

  if ($Query->rowCount() == 0) {

    // This page does not exist.
    $body .= "

      There exists no page with an ID of " . $_GET["id"] . ".

      <a href=\"pages.php\" class=\"button_return\">Return</a>
    ";
  }
  else {

    // Get the page's name, and encode { and } to prevent them from being replaced by the output buffer.
    $page_name = str_replace(["{", "}"], ["&#123;", "&#125;"], $Query->fetch(PDO::FETCH_OBJ)->title);

    $body .= "

      Are you sure you want to delete the \"{$page_name}\" page?<br>

      <a href=\"{%blog_url%}/admin/delete_page.php?id=" . $_GET["id"] . "&delete=true\" class=\"button\">Yes</a>
      <a href=\"{%blog_url%}/admin/pages.php\" class=\"button\">No</a>
    ";
  }
}
else {

  // No ID given.
  $body .= "

    No ID supplied.

    <a href=\"{%blog_url%}/admin/pages.php\" class=\"button_return\">Return</a>
  ";
}

$replace[] = "Delete Page";
$replace[] = $body;

echo str_replace($search, $replace, $theme);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

?>
