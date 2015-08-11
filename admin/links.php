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

$page_title = "Links";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "

      <span class=\"success\">
        The link has been added.
      </span>
    ";
  }
  else {

    $page_body .= "

      <span class=\"failure\">
        Failed to add link.
      </span>
    ";
  }
}

if (isset($_POST["uri"]) && isset($_POST["title"]) && isset($_POST["target"])) {

  $statement = "

    INSERT INTO " . DB_PREF . "links (

      uri,

      title,

      target
    )
    VALUES (

      ?,

      ?,

      ?
    )
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["uri"]);
  $query->bindParam(2, $_POST["title"]);
  $query->bindParam(3, $_POST["target"]);

  $query->execute();

  if (!$query) {

    // Failed to add link.
    header("Location: ./links.php?result=failure");
  }

  // Successfully added link.
  header("Location: ./links.php?result=success");
}
else {

  $page_body .= "

    <form method=\"post\" class=\"add_link\">
      <label for=\"uri\">URI</label>
      <input type=\"text\" id=\"uri\" name=\"uri\" required>
      <label for=\"title\">Title</label>
      <input type=\"text\" id=\"title\" name=\"title\" required>
      <label for=\"target\">Target</label>
      <input type=\"text\" id=\"target\" name=\"target\" required>
      <input type=\"submit\" value=\"Add Link\">
    </form>
  ";

  $statement = "

    SELECT id, title
    FROM " . DB_PREF . "links
    ORDER BY id ASC
  ";

  $query = $Database->getHandle()->query($statement);

  if (!$query || $query->rowCount() == 0) {

    // Query failed or returned zero rows.
    $page_body .= "";
  }
  else {

    $page_body .= "<table class=\"links\">";

    $page_body .= "<tr>";
    $page_body .= "<th>Title</th>";
    $page_body .= "<th>Edit</th>";
    $page_body .= "<th>Delete</th>";
    $page_body .= "</tr>";

    while ($link = $query->fetch(PDO::FETCH_OBJ)) {

      $page_body .= "

        <tr>
          <td>{$link->title}</td>
          <td><a href=\"edit_link.php?id={$link->id}\">Edit</a></td>
          <td><a href=\"delete_link.php?id={$link->id}\">Delete</a></td>
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
