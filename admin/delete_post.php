<?php

require "common.php";

if (isset($_GET["id"]) && !empty($_GET["id"])) {

  if (isset($_GET["delete"])) {

    $statement = "

      DELETE FROM " . DB_PREF . "content
      WHERE id = ?
    ";

    $Query = $Database->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["id"]);

    $Query->execute();

    if (!$Query) {

      $message = "failed to delete post";

      // Failed to delete post.
      header("Location: posts.php?code=0&message={$message}");

      exit();
    }

    $message = "post deleted successfully";

    // Post successfully deleted.
    header("Location: posts.php?code=1&message={$message}");

    exit();
  }

  $statement = "

    SELECT title
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

    // Something went wrong.
    $Utility->displayError("failed to select post title");
  }

  if ($Query->rowCount() == 0) {

    // This post does not exist.
    $body .= "

      There exists no post with an ID of " . $_GET["id"] . ".

      <a href=\"posts.php\" class=\"button_return\">Return</a>
    ";
  }
  else {

    // Get the post's name, and encode { and } to prevent them from being replaced by the output buffer.
    $post_name = str_replace(["{", "}"], ["&#123;", "&#125;"], $Query->fetch(PDO::FETCH_OBJ)->title);

    $body .= "

      Are you sure you want to delete the \"{$post_name}\" post?<br>

      <a href=\"{%blog_url%}/admin/delete_post.php?id=" . $_GET["id"] . "&delete=true\" class=\"button\">Yes</a>
      <a href=\"{%blog_url%}/admin/posts.php\" class=\"button\">No</a>
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

$replace[] = "Delete Post";
$replace[] = $body;

echo str_replace($search, $replace, $theme);

?>
