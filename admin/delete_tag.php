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

      SELECT title
      FROM " . DB_PREF . "tags
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
      $Utility->displayError("failed to select tag title");
    }

    $tag_title = "";

    if ($Query->rowCount() > 0) {

      // Get the tag title.
      $tag_title = $Query->fetch(PDO::FETCH_OBJ)->title;
    }

    $statement = "

      DELETE FROM " . DB_PREF . "tags
      WHERE id = ?
    ";

    $Query = $Database->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["id"]);

    $Query->execute();

    $message = "";

    if (!$Query) {

      if ($tag_title == "") {

        $message = "failed to delete tag";
      }
      else {

        $message = "failed to delete \"{$tag_title}\" tag";
      }

      // Failed to delete tag.
      header("Location: ./tags.php?code=0&message={$message}");

      exit();
    }

    if ($tag_title == "") {

      $message = "tag deleted successfully";
    }
    else {

      $message = "\"{$tag_title}\" tag deleted successfully";
    }

    // Tag successfully deleted.
    header("Location: ./tags.php?code=1&message={$message}");

    exit();
  }

  $statement = "

    SELECT title
    FROM " . DB_PREF . "tags
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
    $Utility->displayError("failed to select tag title");
  }

  if ($Query->rowCount() == 0) {

    // This tag does not exist.
    $body .= "

      There exists no tag with an ID of " . $_GET["id"] . ".

      <a href=\"tags.php\" class=\"button_return\">Return</a>
    ";
  }
  else {

    // Get the tag's name, and encode { and } to prevent them from being replaced by the output buffer.
    $tag_name = str_replace(["{", "}"], ["&#123;", "&#125;"], $Query->fetch(PDO::FETCH_OBJ)->title);

    $body .= "

      Are you sure you want to delete the \"{$tag_name}\" tag?<br>

      <a href=\"./delete_tag.php?id=" . $_GET["id"] . "&delete=true\" class=\"button\">Yes</a>
      <a href=\"./tags.php\" class=\"button\">No</a>
    ";
  }
}
else {

  // No ID given.
  $body .= "

    No ID supplied.

    <a href=\"./tags.php\" class=\"button_return\">Return</a>
  ";
}

$replace[] = "Delete Tag";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
