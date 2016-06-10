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

if (isset($_GET["id"]) && isset($_POST["title"]) && isset($_POST["body"])) {

  $statement = "

    UPDATE " . DB_PREF . "tags
    SET body = ?, title = ?
    WHERE id = ?
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["body"]);
  $Query->bindParam(2, $_POST["title"]);
  $Query->bindParam(3, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Failed to update tag.
    header("Location: ./tags.php?code=0&message=failed to update tag");

    exit();
  }

  // Successfully updated tag.
  header("Location: ./tags.php?code=1&message=tag updated successfully");

  exit();
}

$body = "";

if (isset($_GET["id"]) && !empty($_GET["id"])) {

  $statement = "

    SELECT body, title
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

    // Failed to get tag data.
    $Utility->displayError("failed to get tag data");
  }

  if ($Query->rowCount() == 0) {

    // This tag does not exist.
    $body .= "

      There exists no tag with an ID of " . $_GET["id"] . ".

      <a href=\"tags.php\" class=\"button_return\">Return</a>
    ";
  }
  else {

    $Tag = $Query->fetch(PDO::FETCH_OBJ);

    $tag_body = $Tag->body;
    $tag_title = $Tag->title;

    // Preserve HTML entities.
    $tag_body = htmlentities($tag_body);

    // Encode { and } to prevent them from being replaced by the output buffer.
    $tag_body = str_replace(["{", "}"], ["&#123;", "&#125;"], $tag_body);
    $tag_title = str_replace(["{", "}"], ["&#123;", "&#125;"], $tag_title);

    $body .= "

      Use the form below to edit the tag.<br><br>

      <form method=\"post\" class=\"edit_tag\">

        <label for=\"title\">Title</label>
        <input type=\"text\" id=\"title\" name=\"title\" value=\"{$tag_title}\" required>

        <label for=\"body\">Body</label>
        <textarea id=\"body\" name=\"body\" required>{$tag_body}</textarea>

        <input type=\"submit\" value=\"Save\">
      </form>
    ";
  }
}
else {

  // No ID given.
  $body .= "

    No ID supplied.

    <a href=\"{%blog_url%}/admin/tags.php\" class=\"button_return\">Return</a>
  ";
}

$replace[] = "Edit Tag";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
