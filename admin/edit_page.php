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

$page_title = "Edit Page";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "The page has been saved.";
  }
  else {

    $page_body .= "Failed to save page.";
  }

  $page_body .= "<a href=\"pages.php\" class=\"button_return\">Return</a>";
}
else if (isset($_POST["url"]) && isset($_POST["body"]) && isset($_POST["title"])) {

  $statement = "

    UPDATE " . DB_PREF . "pages
    SET url = ?, body = ?, keywords = ?, title = ?, description = ?,
    show_on_search = ?
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

  $show_on_search = "0";

  if (isset($_POST["show_on_search"])) {

    $show_on_search = "1";
  }

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["url"]);
  $query->bindParam(2, $_POST["body"]);
  $query->bindParam(3, $keywords);
  $query->bindParam(4, $_POST["title"]);
  $query->bindParam(5, $description);
  $query->bindParam(6, $show_on_search);
  $query->bindParam(7, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Failed to update page.
    header("Location: ./edit_page.php?id=" . $_GET["id"] . "&result=failure");
  }

  // Successfully updated page.
  header("Location: ./edit_page.php?id=" . $_GET["id"] . "&result=success");
}
else {

  $statement = "

    SELECT url, body, keywords, title, description, show_on_search
    FROM " . DB_PREF . "pages
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Query failed.
    $page_body .= "Failed to select page data.";
    $page_body .= "<a href=\"pages.php\" class=\"button_return\">Return</a>";
  }
  else if ($query->rowCount() == 0) {

    // Query returned zero rows.
    $page_body .= "There exists no page with an ID of " . $_GET["id"] . ".";
    $page_body .= "<a href=\"pages.php\" class=\"button_return\">Return</a>";
  }
  else {

    $page = $query->fetch(PDO::FETCH_OBJ);

    $url = $page->url;
    $body = $page->body;
    $keywords = $page->keywords;
    $title = $page->title;
    $description = $page->description;
    $show_on_search = $page->show_on_search;

    $page_body .= "

      <form method=\"post\" class=\"edit_page\">
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
        <input type=\"checkbox\" id=\"show_on_search\"
        name=\"show_on_search\"
    ";

    if ($show_on_search) {

      $page_body .= "checked";
    }

    $page_body .= "

      > Show on Search
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
