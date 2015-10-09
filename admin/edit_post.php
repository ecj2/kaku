<?php

// Allow access to include files.
define("KAKU_INCLUDE", true);

session_start();

if (!isset($_SESSION["username"])) {

  header("Location: ./login.php");
}

require "../includes/configuration.php";

require "../includes/classes/utility.php";
require "../includes/classes/database.php";

require "../includes/classes/hook.php";
require "../includes/classes/output.php";

global $Hook;

$Hook = new Hook;

$Output = new Output;
$Utility = new Utility;
$Database = new Database;

$Database->connect();

$Output->setDatabaseHandle($Database->getHandle());

$Output->startBuffer();

$statement = "

  SELECT body
  FROM " . DB_PREF . "tags
  WHERE title = 'admin_theme_name'
  ORDER BY id DESC
";

$query = $Database->getHandle()->query($statement);

if (!$query || $query->rowCount() == 0) {

  // Query failed or returned zero rows.
  $Utility->displayError("failed to get admin theme name");
}

// Get the admin theme name.
$theme_name = $query->fetch(PDO::FETCH_OBJ)->body;

if (!file_exists("content/themes/{$theme_name}/template.html")) {

  // Theme template does not exist.
  $Utility->displayError("theme template file does not exist");
}

// Display the theme contents.
echo file_get_contents("content/themes/{$theme_name}/template.html");

$page_body = "";

$page_title = "Edit Post";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "The post has been saved.";
  }
  else {

    $page_body .= "Failed to save post.";
  }

  $page_body .= "<a href=\"posts.php\" class=\"button_return\">Return</a>";
}
else if (isset($_POST["url"]) && isset($_POST["body"]) && isset($_POST["title"]) && isset($_POST["epoch"])) {

  $statement = "

    UPDATE " . DB_PREF . "posts
    SET url = ?, body = ?, keywords = ?, draft = ?, title = ?, description = ?,
    allow_comments = ?, epoch = ?
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  $keywords = "";

  if (isset($_POST["keywords"])) {

    $keywords = $_POST["keywords"];
  }

  $description = "";

  if (isset($_POST["description"])) {

    $description = $_POST["description"];
  }

  $draft = "1";

  if (isset($_POST["draft"])) {

    $draft = "0";
  }

  $allow_comments = "0";

  if (isset($_POST["allow_comments"])) {

    $allow_comments = "1";
  }

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["url"]);
  $query->bindParam(2, $_POST["body"]);
  $query->bindParam(3, $keywords);
  $query->bindParam(4, $draft);
  $query->bindParam(5, $_POST["title"]);
  $query->bindParam(6, $description);
  $query->bindParam(7, $allow_comments);
  $query->bindParam(8, $_POST["epoch"]);
  $query->bindParam(9, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Failed to update post.
    header("Location: ./edit_post.php?id=" . $_GET["id"] . "&result=failure");
  }

  // Successfully updated post.
  header("Location: ./edit_post.php?id=" . $_GET["id"] . "&result=success");
}
else {

  $statement = "

    SELECT url, body, keywords, epoch, title, draft, description, allow_comments
    FROM " . DB_PREF . "posts
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Query failed.
    $page_body .= "Failed to select post data.";
    $page_body .= "<a href=\"posts.php\" class=\"button_return\">Return</a>";
  }
  else if ($query->rowCount() == 0) {

    // Query returned zero rows.
    $page_body .= "There exists no post with an ID of " . $_GET["id"] . ".";
    $page_body .= "<a href=\"posts.php\" class=\"button_return\">Return</a>";
  }
  else {

    $post = $query->fetch(PDO::FETCH_OBJ);

    $url = $post->url;
    $body = $post->body;
    $keywords = $post->keywords;
    $epoch = $post->epoch;
    $draft = $post->draft;
    $title = $post->title;
    $description = $post->description;
    $allow_comments = $post->allow_comments;

    $page_body .= "

      <form method=\"post\" class=\"edit_post\">
        <label for=\"url\">URL</label>
        <input type=\"text\" id=\"url\" name=\"url\"
         value=\"{$url}\" required>
        <label for=\"title\">Title</label>
        <input type=\"text\" id=\"title\" name=\"title\"
         value=\"{$title}\" required>
        <label for=\"keywords\">Keywords (Optional)</label>
        <input type=\"text\" id=\"keywords\" name=\"keywords\" value=\"{$keywords}\">
        <label for=\"body\">Body</label>
        <textarea id=\"body\" name=\"body\" required>{$body}</textarea>
        <label for=\"description\">Description (Optional)</label>
        <textarea id=\"description\" name=\"description\">
        {$description}</textarea>
        <label for=\"epoch\">Epoch</label>
        <input type=\"text\" id=\"epoch\" name=\"epoch\"
         value=\"{$epoch}\" required>
        <input type=\"checkbox\" id=\"draft\"
        name=\"draft\"
    ";

    if (!$draft) {

      $page_body .= "checked";
    }

    $page_body .= "

      > Published<br>
      <input type=\"checkbox\" id=\"allow_comments\" name=\"allow_comments\"
    ";

    if ($allow_comments) {

      $page_body .= "checked";
    }

    $page_body .= "

      > Allow Comments
      <input type=\"submit\" value=\"Save\">
      </form>
  ";
  }
}

$Output->addTagReplacement(

  "page_body",

  $page_body
);

$Output->addTagReplacement(

  "page_title",

  $page_title
);

$Output->replaceTags();

$Output->flushBuffer();

$Database->disconnect();

?>
