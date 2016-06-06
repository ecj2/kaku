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

    INSERT INTO " . DB_PREF . "posts (

      url,

      body,

      draft,

      epoch,

      title,

      keywords,

      author_id,

      description,

      allow_comments
    )
    VALUES (

      ?,

      ?,

      ?,

      UNIX_TIMESTAMP(),

      ?,

      ?,

      ?,

      ?,

      ?
    )
  ";

  $Query = $Database->getHandle()->prepare($statement);

  $draft = "0";

  if (isset($_POST["draft"])) {

    $draft = "1";
  }

  $allow_comments = "0";

  if (isset($_POST["allow_comments"])) {

    $allow_comments = "1";
  }

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["url"]);
  $Query->bindParam(2, $_POST["body"]);
  $Query->bindParam(3, $draft);
  $Query->bindParam(4, $_POST["title"]);
  $Query->bindParam(5, $_POST["keywords"]);
  $Query->bindParam(6, $_SESSION["user_id"]);
  $Query->bindParam(7, $_POST["description"]);
  $Query->bindParam(8, $allow_comments);

  $Query->execute();

  if (!$Query) {

    // Failed to create post.
    header("Location: ./posts.php?code=0&message=failed to create post");

    exit();
  }

  // Successfully added post.
  header("Location: ./posts.php?code=1&message=post created successfully");

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

  Use the form below to create a new post.<br><br>

  <form method=\"post\" class=\"add_post\">

    <label for=\"url\">URL</label>
    <input type=\"text\" id=\"url\" name=\"url\" required>

    <label for=\"title\">Title</label>
    <input type=\"text\" id=\"title\" name=\"title\" required>

    <label for=\"keywords\">Keywords (optional; comma separated)</label>
    <input type=\"text\" id=\"keywords\" name=\"keywords\">

    <label for=\"body\">Body</label>
    <textarea id=\"body\" name=\"body\" required></textarea>

    <label for=\"description\">Description (optional)</label>
    <textarea id=\"description\" name=\"description\"></textarea>

    <input type=\"checkbox\" id=\"draft\" name=\"draft\"> Save as draft<br>

    <input type=\"checkbox\" id=\"allow_comments\" name=\"allow_comments\"> Allow comments

    <input type=\"submit\" value=\"Create Post\">
  </form>
";

$statement = "

  SELECT id, title
  FROM " . DB_PREF . "posts
  ORDER BY id DESC
";

$Query = $Database->getHandle()->query($statement);

if (!$Query) {

  // Something went wrong.
  $Utility->displayError("failed to select posts");
}

if ($Query->rowCount() > 0) {

  $body .= "

    Existing posts are displayed below.<br><br>

    <table class=\"two-column\">
      <tr>
        <th>Title</th>
        <th>Action</th>
      </tr>
  ";

  while ($Post = $Query->fetch(PDO::FETCH_OBJ)) {

    $edit_link = "{%blog_url%}/admin/edit_post.php?id={$Post->id}";
    $delete_link = "{%blog_url%}/admin/delete_post.php?id={$Post->id}";

    // Encode { and } to prevent them from being replaced by the output buffer.
    $title = str_replace(["{", "}"], ["&#123;", "&#125;"], $Post->title);

    $body .= "

      <tr>
        <td>{$title}</td>
        <td><a href=\"{$edit_link}\">Edit</a> - <a href=\"{$delete_link}\">Delete</a></td>
      </tr>
    ";
  }

  $body .= "</table>";
}

$replace[] = "Posts";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
