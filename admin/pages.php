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

$page_title = "Pages";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "

      <span class=\"success\">
        The page has been added.
      </span>
    ";
  }
  else {

    $page_body .= "

      <span class=\"failure\">
        Failed to add page.
      </span>
    ";
  }
}

if (isset($_POST["url"]) && isset($_POST["title"]) && isset($_POST["body"])) {

  $statement = "

    INSERT INTO " . DB_PREF . "pages (

      url,

      body,

      tags,

      title,

      description,

      show_on_search
    )
    VALUES (

      ?,

      ?,

      ?,

      ?,

      ?,

      ?
    )
  ";

  $query = $Database->getHandle()->prepare($statement);

  $tags = "";

  if (isset($_POST["tags"])) {

    $tags = $_POST["tags"];
  }

  $description = "";

  if (isset($_POST["description"])) {

    $description = $_POST["description"];
  }

  $show_on_search = "0";

  if (isset($_POST["show_on_search"])) {

    $show_on_search = "1";
  }

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["url"]);
  $query->bindParam(2, $_POST["body"]);
  $query->bindParam(3, $tags);
  $query->bindParam(4, $_POST["title"]);
  $query->bindParam(5, $description);
  $query->bindParam(6, $show_on_search);

  $query->execute();

  if (!$query) {

    // Failed to add page.
    header("Location: ./pages.php?result=failure");
  }

  // Successfully added page.
  header("Location: ./pages.php?result=success");
}
else {

  $page_body .= "

    <form method=\"post\" class=\"add_page\">
      <label for=\"url\">URL</label>
      <input type=\"text\" id=\"url\" name=\"url\" required>
      <label for=\"title\">Title</label>
      <input type=\"text\" id=\"title\" name=\"title\" required>
      <label for=\"tags\">Tags (Optional)</label>
      <input type=\"text\" id=\"tags\" name=\"tags\">
      <label for=\"body\">Body</label>
      <textarea id=\"body\" name=\"body\" required></textarea>
      <label for=\"description\">Description (Optional)</label>
      <textarea id=\"description\" name=\"description\"></textarea>
      <input type=\"checkbox\" id=\"show_on_search\"
       name=\"show_on_search\"> Show on Search
      <input type=\"submit\" value=\"Add Page\">
    </form>
  ";

  $statement = "

    SELECT id, title
    FROM " . DB_PREF . "pages
    ORDER BY id DESC
  ";

  $query = $Database->getHandle()->query($statement);

  if (!$query || $query->rowCount() == 0) {

    // Query failed or returned zero rows.
    $page_body .= "";
  }
  else {

    $page_body .= "<table class=\"pages\">";

    $page_body .= "<tr>";
    $page_body .= "<th>Title</th>";
    $page_body .= "<th>Edit</th>";
    $page_body .= "<th>Delete</th>";
    $page_body .= "</tr>";

    while ($page = $query->fetch(PDO::FETCH_OBJ)) {

      $page_body .= "

        <tr>
          <td>{$page->title}</td>
          <td><a href=\"edit_page.php?id={$page->id}\">Edit</a></td>
          <td><a href=\"delete_page.php?id={$page->id}\">Delete</a></td>
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
