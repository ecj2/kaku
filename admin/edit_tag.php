<?php

session_start();

if (!isset($_SESSION["username"])) {

  header("Location: ./login.php");
}

require "../includes/configuration.php";

require "../includes/classes/utility.php";
require "../includes/classes/database.php";

require "../includes/classes/output.php";

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

$page_title = "Edit Tag";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "The tag has been saved.";
  }
  else {

    $page_body .= "Failed to save tag.";
  }

  $page_body .= "<a href=\"tags.php\" class=\"button_return\">Return</a>";
}
else if (isset($_POST["body"]) && isset($_POST["title"])) {

  $statement = "

    UPDATE " . DB_PREF . "tags
    SET body = ?, title = ?, evaluate = ?
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  $evaluate = false;

  if (isset($_POST["evaluate"])) {

    $evaluate = true;
  }

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["body"]);
  $query->bindParam(2, $_POST["title"]);
  $query->bindParam(3, $evaluate);
  $query->bindParam(4, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Failed to update tag.
    header("Location: ./edit_tag.php?id=" . $_GET["id"] . "&result=failure");
  }

  // Successfully updated tag.
  header("Location: ./edit_tag.php?id=" . $_GET["id"] . "&result=success");
}
else {

  $statement = "

    SELECT body, title, evaluate
    FROM " . DB_PREF . "tags
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Query failed.
    $page_body .= "Failed to select tag data.";
    $page_body .= "<a href=\"tags.php\" class=\"button_return\">Return</a>";
  }
  else if ($query->rowCount() == 0) {

    // Query returned zero rows.
    $page_body .= "There exists no tag with an ID of " . $_GET["id"] . ".";
    $page_body .= "<a href=\"tags.php\" class=\"button_return\">Return</a>";
  }
  else {

    $tag = $query->fetch(PDO::FETCH_OBJ);

    $body = $tag->body;
    $title = $tag->title;
    $evaluate = $tag->evaluate;

    $page_body .= "

      <form method=\"post\" class=\"edit_tag\">
        <label for=\"title\">Title</label>
        <input type=\"text\" id=\"title\" name=\"title\" value=\"{$title}\"
        required>
        <label for=\"body\">Body</label>
        <textarea id=\"body\" name=\"body\" required>{$body}</textarea>
        <input type=\"checkbox\" id=\"evaluate\" name=\"evaluate\"
      ";

      if ($evaluate) {

        $page_body .= " checked";
      }

      $page_body .= "

        > Evaluate as PHP Code<br>
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
