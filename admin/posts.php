<?php

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

$page_title = "Posts";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "

      <span class=\"success\">
        The post has been added.
      </span>
    ";
  }
  else {

    $page_body .= "

      <span class=\"failure\">
        Failed to add post.
      </span>
    ";
  }
}

if (isset($_POST["url"]) && isset($_POST["title"]) && isset($_POST["body"])) {

  $statement = "

    INSERT INTO " . DB_PREF . "posts (

      url,

      body,

      keywords,

      draft,

      epoch,

      title,

      author_id,

      description,

      allow_comments
    )
    VALUES (

      ?,

      ?,

      ?,

      ?,

      UNIX_TIMESTAMP(),

      ?,

      ?,

      ?,

      ?
    )
  ";

  $query = $Database->getHandle()->prepare($statement);

  $keywords = "";

  if (isset($_POST["keywords"])) {

    $keywords = $_POST["keywords"];
  }

  $draft = "0";

  if (isset($_POST["draft"])) {

    $draft = "1";
  }

  $description = "";

  if (isset($_POST["description"])) {

    $description = $_POST["description"];
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
  $query->bindParam(6, $_SESSION["user_id"]);
  $query->bindParam(7, $description);
  $query->bindParam(8, $allow_comments);

  $query->execute();

  if (!$query) {

    // Failed to add post.
    header("Location: ./posts.php?result=failure");
  }

  // Successfully added post.
  header("Location: ./posts.php?result=success");
}
else {

  $page_body .= "

    <form method=\"post\" class=\"add_post\">
      <label for=\"url\">URL</label>
      <input type=\"text\" id=\"url\" name=\"url\" required>
      <label for=\"title\">Title</label>
      <input type=\"text\" id=\"title\" name=\"title\" required>
      <label for=\"keywords\">Keywords (Optional)</label>
      <input type=\"text\" id=\"keywords\" name=\"keywords\">
      <label for=\"body\">Body</label>
      <textarea id=\"body\" name=\"body\" required></textarea>
      <label for=\"description\">Description (Optional)</label>
      <textarea id=\"description\" name=\"description\"></textarea>
      <input type=\"checkbox\" id=\"draft\"
       name=\"draft\"> Save as Draft<br>
      <input type=\"checkbox\" id=\"allow_comments\"
       name=\"allow_comments\"> Allow Comments
      <input type=\"submit\" value=\"Add Post\">
    </form>
  ";

  $statement = "

    SELECT id, title
    FROM " . DB_PREF . "posts
    ORDER BY id DESC
  ";

  $query = $Database->getHandle()->query($statement);

  if (!$query || $query->rowCount() == 0) {

    // Query failed or returned zero rows.
    $page_body .= "";
  }
  else {

    $page_body .= "<table class=\"posts\">";

    $page_body .= "<tr>";
    $page_body .= "<th>Title</th>";
    $page_body .= "<th>Edit</th>";
    $page_body .= "<th>Delete</th>";
    $page_body .= "</tr>";

    while ($post = $query->fetch(PDO::FETCH_OBJ)) {

      $page_body .= "

        <tr>
          <td>{$post->title}</td>
          <td><a href=\"edit_post.php?id={$post->id}\">Edit</a></td>
          <td><a href=\"delete_post.php?id={$post->id}\">Delete</a></td>
        </tr>
      ";
    }

    $page_body .= "</table>";
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
