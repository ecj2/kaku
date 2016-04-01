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

$page_title = "Tags";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "

      <span class=\"success\">
        The tag has been added.
      </span>
    ";
  }
  else {

    $page_body .= "

      <span class=\"failure\">
        Failed to add tag.
      </span>
    ";
  }
}

if (isset($_POST["body"]) && isset($_POST["title"])) {

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

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["body"]);
  $query->bindParam(2, $_POST["title"]);

  $query->execute();

  if (!$query) {

    // Failed to add tag.
    header("Location: ./tags.php?result=failure");
  }

  // Successfully added tag.
  header("Location: ./tags.php?result=success");
}
else {

  $page_body .= "

    <form method=\"post\" class=\"add_tag\">
      <label for=\"title\">Title</label>
      <input type=\"text\" id=\"title\" name=\"title\" required>
      <label for=\"body\">Body</label>
      <textarea id=\"body\" name=\"body\" required></textarea>
      <input type=\"submit\" value=\"Add Tag\">
    </form>
  ";

  $statement = "

    SELECT id, title
    FROM " . DB_PREF . "tags
    ORDER BY id DESC
  ";

  $query = $Database->getHandle()->query($statement);

  if (!$query || $query->rowCount() == 0) {

    // Query failed or returned zero rows.
    $page_body .= "";
  }
  else {

    $page_body .= "<table class=\"tags\">";

    $page_body .= "<tr>";
    $page_body .= "<th>Title</th>";
    $page_body .= "<th>Edit</th>";
    $page_body .= "<th>Delete</th>";
    $page_body .= "</tr>";

    while ($tag = $query->fetch(PDO::FETCH_OBJ)) {

      // Encode { and } to prevent it from being replaced by the output buffer.
      $title = str_replace(["{", "}"], ["&#123;", "&#125;"], $tag->title);

      $page_body .= "

        <tr>
          <td>{$title}</td>
          <td><a href=\"edit_tag.php?id={$tag->id}\">Edit</a></td>
          <td><a href=\"delete_tag.php?id={$tag->id}\">Delete</a></td>
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
