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

if (isset($_POST["title"]) && isset($_POST["body"])) {

  $statement = "

    INSERT INTO " . DB_PREF . "tags (

      body,

      title
    )
    VALUES (

      ?,

      ?
    )
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["body"]);
  $Query->bindParam(2, $_POST["title"]);

  $Query->execute();

  if (!$Query) {

    // Failed to create tag.
    header("Location: ./tags.php?code=0&message=failed to create tag");

    exit();
  }

  // Successfully added tag.
  header("Location: ./tags.php?code=1&message=tag created successfully");

  exit();
}

$body = "";

if (isset($_GET["code"]) && isset($_GET["message"])) {

  if ($_GET["code"] == 0) {

    // Failure notice.
    $body .= "<span class=\"failure\">Notice: ";
  }
  else if ($_GET["code"] == 1) {

    // Success notice.
    $body .= "<span class=\"success\">Notice: ";
  }

  // Encode { and } to prevent them from being replaced by the output buffer.
  $body .= str_replace(["{", "}"], ["&#123;", "&#125;"], $_GET["message"]) . ".</span>";
}

$body .= "

  Use the form below to create a new tag.<br><br>

  <form method=\"post\" class=\"add_tag\">

    <label for=\"title\">Title</label>
    <input type=\"text\" id=\"title\" name=\"title\" required>

    <label for=\"body\">Body</label>
    <textarea id=\"body\" name=\"body\" required></textarea>

    <input type=\"submit\" value=\"Create Tag\">
  </form>
";

$statement = "

  SELECT id, title
  FROM " . DB_PREF . "tags
  ORDER BY id DESC
";

$Query = $Database->getHandle()->query($statement);

if (!$Query) {

  // Something went wrong.
  $Utility->displayError("failed to select tags");
}

if ($Query->rowCount() > 0) {

  $body .= "

    Existing tags are displayed below.<br><br>

    <table class=\"two-column\">
      <tr>
        <th>Title</th>
        <th>Action</th>
      </tr>
  ";

  while ($Tag = $Query->fetch(PDO::FETCH_OBJ)) {

    $edit_link = "{%blog_url%}/admin/edit_tag.php?id={$Tag->id}";
    $delete_link = "{%blog_url%}/admin/delete_tag.php?id={$Tag->id}";

    // Encode { and } to prevent them from being replaced by the output buffer.
    $title = str_replace(["{", "}"], ["&#123;", "&#125;"], $Tag->title);

    $body .= "

      <tr>
        <td>{$title}</td>
        <td><a href=\"{$edit_link}\">Edit</a> - <a href=\"{$delete_link}\">Delete</a></td>
      </tr>
    ";
  }

  $body .= "</table>";
}

$replace[] = "Tags";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
