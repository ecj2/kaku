<?php

require "common.php";

if (isset($_GET["id"]) && isset($_POST["title"]) && isset($_POST["body"])) {

  $statement = "

    UPDATE " . DB_PREF . "content
    SET url = ?, body = ?, draft = ?, epoch_created = ?, title = ?, keywords = ?, description = ?, allow_comments = ?
    WHERE id = ?
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

  $date_time = $_POST["year"] . "-" . $_POST["month"] . "-" . $_POST["day"];
  $date_time .= " " . $_POST["hour"] . ":" . $_POST["minute"] . ":" . $_POST["second"];

  $epoch = strtotime($date_time);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["url"]);
  $Query->bindParam(2, $_POST["body"]);
  $Query->bindParam(3, $draft);
  $Query->bindParam(4, $epoch);
  $Query->bindParam(5, $_POST["title"]);
  $Query->bindParam(6, $_POST["keywords"]);
  $Query->bindParam(7, $_POST["description"]);
  $Query->bindParam(8, $allow_comments);
  $Query->bindParam(9, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Failed to update post.
    header("Location: posts.php?code=0&message=failed to update post");

    exit();
  }

  // Successfully updated post.
  header("Location: posts.php?code=1&message=post updated successfully");

  exit();
}

if (isset($_GET["id"]) && !empty($_GET["id"])) {

  $statement = "

    SELECT url, body, title, epoch_created, keywords, description, draft, allow_comments
    FROM " . DB_PREF . "content
    WHERE id = ?
    AND type = 0
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Failed to get post data.
    $Utility->displayError("failed to get post data");
  }

  if ($Query->rowCount() == 0) {

    // This post does not exist.
    $body .= "

      There exists no post with an ID of " . $_GET["id"] . ".

      <a href=\"posts.php\" class=\"button_return\">Return</a>
    ";
  }
  else {

    $Post = $Query->fetch(PDO::FETCH_OBJ);

    $post_url = $Post->url;
    $post_body = $Post->body;
    $post_draft = $Post->draft;
    $post_epoch = $Post->epoch_created;
    $post_title = $Post->title;
    $post_keywords = $Post->keywords;
    $post_description = $Post->description;
    $post_allow_comments = $Post->allow_comments;

    // Preserve HTML entities.
    $post_url = htmlentities($post_url);
    $post_body = htmlentities($post_body);
    $post_epoch = htmlentities($post_epoch);
    $post_title = htmlentities($post_title);
    $post_keywords = htmlentities($post_keywords);
    $post_description = htmlentities($post_description);

    // Encode { and } to prevent them from being replaced by the output buffer.
    $post_url = str_replace(["{", "}"], ["&#123;", "&#125;"], $post_url);
    $post_body = str_replace(["{", "}"], ["&#123;", "&#125;"], $post_body);
    $post_epoch = str_replace(["{", "}"], ["&#123;", "&#125;"], $post_epoch);
    $post_title = str_replace(["{", "}"], ["&#123;", "&#125;"], $post_title);
    $post_keywords = str_replace(["{", "}"], ["&#123;", "&#125;"], $post_keywords);
    $post_description = str_replace(["{", "}"], ["&#123;", "&#125;"], $post_description);

    $body .= "

      Use the form below to edit the post.<br><br>

      <form method=\"post\" class=\"edit_post\">

        <label for=\"url\">URL</label>
        <input type=\"text\" id=\"url\" name=\"url\" value=\"{$post_url}\" required>

        <label for=\"title\">Title</label>
        <input type=\"text\" id=\"title\" name=\"title\" value=\"{$post_title}\" required>

        <label for=\"keywords\">Keywords (optional; comma separated)</label>
        <input type=\"text\" id=\"keywords\" name=\"keywords\" value=\"{$post_keywords}\">

        <label for=\"body\">Body</label>
        <textarea id=\"body\" name=\"body\" required>{$post_body}</textarea>

        <label for=\"description\">Description (optional)</label>
        <textarea id=\"description\" name=\"description\">{$post_description}</textarea>
    ";

    $body .= "

      <label for=\"year\">Year</label>
      <select id=\"year\" name=\"year\">
    ";

    for ($i = 1970; $i < 2099; ++$i) {

      if ($i == date("Y", $post_epoch)) {

        $body .= "<option value=\"{$i}\" selected>{$i}</option>";
      }
      else {

        $body .= "<option value=\"{$i}\">{$i}</option>";
      }
    }

    $body .= "</select>";

    $body .= "

      <label for=\"month\">Month</label>
      <select id=\"month\" name=\"month\">
    ";

    for ($i = 1; $i < 13; ++$i) {

      if ($i == date("n", $post_epoch)) {

        $body .= "<option value=\"{$i}\" selected>{$i}</option>";
      }
      else {

        $body .= "<option value=\"{$i}\">{$i}</option>";
      }
    }

    $body .= "</select>";

    $body .= "

      <label for=\"day\">Day</label>
      <select id=\"day\" name=\"day\">
    ";

    for ($i = 1; $i < 32; ++$i) {

      if ($i == date("j", $post_epoch)) {

        $body .= "<option value=\"{$i}\" selected>{$i}</option>";
      }
      else {

        $body .= "<option value=\"{$i}\">{$i}</option>";
      }
    }

    $body .= "</select>";

    $body .= "

      <label for=\"hour\">Hour</label>
      <select id=\"hour\" name=\"hour\">
    ";

    for ($i = 0; $i < 24; ++$i) {

      if ($i == date("H", $post_epoch)) {

        $body .= "<option value=\"{$i}\" selected>{$i}</option>";
      }
      else {

        $body .= "<option value=\"{$i}\">{$i}</option>";
      }
    }

    $body .= "</select>";

    $body .= "

      <label for=\"minute\">Minute</label>
      <select id=\"minute\" name=\"minute\">
    ";

    for ($i = 0; $i < 60; ++$i) {

      if ($i == date("i", $post_epoch)) {

        $body .= "<option value=\"{$i}\" selected>{$i}</option>";
      }
      else {

        $body .= "<option value=\"{$i}\">{$i}</option>";
      }
    }

    $body .= "</select>";

    $body .= "

      <label for=\"second\">Second</label>
      <select id=\"second\" name=\"second\">
    ";

    for ($i = 0; $i < 60; ++$i) {

      if ($i == date("s", $post_epoch)) {

        $body .= "<option value=\"{$i}\" selected>{$i}</option>";
      }
      else {

        $body .= "<option value=\"{$i}\">{$i}</option>";
      }
    }

    $body .= "</select>";

    if ($post_draft) {

      $body .= "<input type=\"checkbox\" id=\"draft\" name=\"draft\" checked> Save as draft";
    }
    else {

      $body .= "<input type=\"checkbox\" id=\"draft\" name=\"draft\"> Save as draft";
    }

    $body .= "

      <br>
    ";

    if ($post_allow_comments) {

      $body .= "<input type=\"checkbox\" id=\"allow_comments\" name=\"allow_comments\" checked> Allow comments";
    }
    else {

      $body .= "<input type=\"checkbox\" id=\"allow_comments\" name=\"allow_comments\"> Allow comments";
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

    <a href=\"{%blog_url%}/admin/posts.php\" class=\"button_return\">Return</a>
  ";
}

$replace[] = "Edit Post";
$replace[] = $body;

echo str_replace($search, $replace, $theme);

?>
