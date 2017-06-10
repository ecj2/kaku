<?php

require "common.php";

if (isset($_POST["title"]) && isset($_POST["body"])) {

  $statement = "

    INSERT INTO " . DB_PREF . "content (

      url,

      body,

      title,

      keywords,

      description,

      show_on_search,

      allow_comments,

      type,

      draft,

      author_id,

      epoch_created,

      identifier
    )
    VALUES (

      ?,

      ?,

      ?,

      ?,

      ?,

      ?,

      0,

      1,

      0,

      " . $_SESSION["user_id"] . ",

      " . time() . ",

      '" . md5(microtime()) . "'
    )
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

  $Query->execute();

  if (!$Query) {

    // Failed to create page.
    header("Location: pages.php?code=0&message=failed to create page");

    exit();
  }

  // Successfully added page.
  header("Location: pages.php?code=1&message=page created successfully");

  exit();
}

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

  Use the form below to create a new page.<br><br>

  <form method=\"post\" class=\"add_page\">

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

    <input type=\"checkbox\" id=\"show_on_search\" name=\"show_on_search\"> Show on search

    <input type=\"submit\" value=\"Create Page\">
  </form>
";

$statement = "

  SELECT id, title
  FROM " . DB_PREF . "content
  WHERE type = 1
  ORDER BY id DESC
";

$Query = $Database->getHandle()->query($statement);

if (!$Query) {

  // Something went wrong.
  $Utility->displayError("failed to select pages");
}

if ($Query->rowCount() > 0) {

  $body .= "

    Existing pages are displayed below.<br><br>

    <table class=\"two-column\">
      <tr>
        <th>Title</th>
        <th>Action</th>
      </tr>
  ";

  while ($Page = $Query->fetch(PDO::FETCH_OBJ)) {

    $edit_link = "{%blog_url%}/admin/edit_page.php?id={$Page->id}";
    $delete_link = "{%blog_url%}/admin/delete_page.php?id={$Page->id}";

    // Encode { and } to prevent them from being replaced by the output buffer.
    $title = str_replace(["{", "}"], ["&#123;", "&#125;"], $Page->title);

    $body .= "

      <tr>
        <td>{$title}</td>
        <td><a href=\"{$edit_link}\">Edit</a> - <a href=\"{$delete_link}\">Delete</a></td>
      </tr>
    ";
  }

  $body .= "</table>";
}

$replace[] = "Pages";
$replace[] = $body;

echo str_replace($search, $replace, $theme);

echo $Buffer->flush();

?>
