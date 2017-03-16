<?php

session_start();

if (!isset($_SESSION["username"])) {

  // User is not logged in.
  header("Location: login.php");

  exit();
}

require "../core/includes/common.php";

// @TODO: Load extensions.

// Get template markup.
$theme = $Theme->getFileContents("template", true);

$search = [];
$replace = [];

$search[] = "{%page_title%}";
$search[] = "{%page_body%}";

if (isset($_GET["id"]) && isset($_POST["title"]) && isset($_POST["body"])) {

  $statement = "

    UPDATE " . DB_PREF . "content
    SET url = ?, body = ?, title = ?, keywords = ?, description = ?, show_on_search = ?
    WHERE id = ?
    AND type = 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  $show_on_search = "0";

  if (isset($_POST["show_on_search"])) {

    $show_on_search = "1";
  }

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["url"]);
  $Query->bindParam(2, $_POST["body"]);
  $Query->bindParam(3, $_POST["title"]);
  $Query->bindParam(4, $_POST["keywords"]);
  $Query->bindParam(5, $_POST["description"]);
  $Query->bindParam(6, $show_on_search);
  $Query->bindParam(7, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Failed to update page.
    header("Location: pages.php?code=0&message=failed to update page");

    exit();
  }

  // Successfully updated page.
  header("Location: pages.php?code=1&message=page updated successfully");

  exit();
}

$body = "";

if (isset($_GET["id"]) && !empty($_GET["id"])) {

  $statement = "

    SELECT url, body, title, keywords, description, show_on_search
    FROM " . DB_PREF . "content
    WHERE id = ?
    AND type = 1
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Failed to get page data.
    $Utility->displayError("failed to get page data");
  }

  if ($Query->rowCount() == 0) {

    // This page does not exist.
    $body .= "

      There exists no page with an ID of " . $_GET["id"] . ".

      <a href=\"pages.php\" class=\"button_return\">Return</a>
    ";
  }
  else {

    $Page = $Query->fetch(PDO::FETCH_OBJ);

    $page_url = $Page->url;
    $page_body = $Page->body;
    $page_title = $Page->title;
    $page_keywords = $Page->keywords;
    $page_description = $Page->description;
    $page_show_on_search = $Page->show_on_search;

    // Preserve HTML entities.
    $page_url = htmlentities($page_url);
    $page_body = htmlentities($page_body);
    $page_title = htmlentities($page_title);
    $page_keywords = htmlentities($page_keywords);
    $page_description = htmlentities($page_description);

    // Encode { and } to prevent them from being replaced by the output buffer.
    $page_url = str_replace(["{", "}"], ["&#123;", "&#125;"], $page_url);
    $page_body = str_replace(["{", "}"], ["&#123;", "&#125;"], $page_body);
    $page_title = str_replace(["{", "}"], ["&#123;", "&#125;"], $page_title);
    $page_keywords = str_replace(["{", "}"], ["&#123;", "&#125;"], $page_keywords);
    $page_description = str_replace(["{", "}"], ["&#123;", "&#125;"], $page_description);

    $body .= "

      Use the form below to edit the page.<br><br>

      <form method=\"post\" class=\"edit_page\">

        <label for=\"url\">URL</label>
        <input type=\"text\" id=\"url\" name=\"url\" value=\"{$page_url}\" required>

        <label for=\"title\">Title</label>
        <input type=\"text\" id=\"title\" name=\"title\" value=\"{$page_title}\" required>

        <label for=\"keywords\">Keywords (optional; comma separated)</label>
        <input type=\"text\" id=\"keywords\" name=\"keywords\" value=\"{$page_keywords}\">

        <label for=\"body\">Body</label>
        <textarea id=\"body\" name=\"body\" required>{$page_body}</textarea>

        <label for=\"description\">Description (optional)</label>
        <textarea id=\"description\" name=\"description\">{$page_description}</textarea>
    ";

    if ($page_show_on_search) {

      $body .= "<input type=\"checkbox\" id=\"show_on_search\" name=\"show_on_search\" checked> Show on search";
    }
    else {

      $body .= "<input type=\"checkbox\" id=\"show_on_search\" name=\"show_on_search\"> Show on search";
    }

    $body .= "

      <input type=\"submit\" value=\"Save\">
      </form>
    ";
  }
}
else {

  // No ID given.
  $body .= "

    No ID supplied.

    <a href=\"{%blog_url%}/admin/pages.php\" class=\"button_return\">Return</a>
  ";
}

$replace[] = "Edit Page";
$replace[] = $body;

echo str_replace($search, $replace, $theme);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

?>
