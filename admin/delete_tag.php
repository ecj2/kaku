<?php

require "common.php";

if (isset($_GET["id"]) && !empty($_GET["id"])) {

  if (isset($_GET["delete"])) {

    $statement = "

      DELETE FROM " . DB_PREF . "tags
      WHERE id = ?
    ";

    $Query = $Database->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["id"]);

    $Query->execute();

    if (!$Query) {

      $message = "failed to delete tag";

      // Failed to delete tag.
      header("Location: tags.php?code=0&message={$message}");

      exit();
    }

    $message = "tag deleted successfully";

    // Tag successfully deleted.
    header("Location: tags.php?code=1&message={$message}");

    exit();
  }

  $statement = "

    SELECT title
    FROM " . DB_PREF . "tags
    WHERE id = ?
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Something went wrong.
    $Utility->displayError("failed to select tag title");
  }

  if ($Query->rowCount() == 0) {

    // This tag does not exist.
    $body .= "

      There exists no tag with an ID of " . $_GET["id"] . ".

      <a href=\"tags.php\" class=\"button_return\">Return</a>
    ";
  }
  else {

    // Get the tag's name, and encode { and } to prevent them from being replaced by the output buffer.
    $tag_name = str_replace(["{", "}"], ["&#123;", "&#125;"], $Query->fetch(PDO::FETCH_OBJ)->title);

    $body .= "

      Are you sure you want to delete the \"{$tag_name}\" tag?<br>

      <a href=\"{%blog_url%}/admin/delete_tag.php?id=" . $_GET["id"] . "&delete=true\" class=\"button\">Yes</a>
      <a href=\"{%blog_url%}/admin/tags.php\" class=\"button\">No</a>
    ";
  }
}
else {

  // No ID given.
  $body .= "

    No ID supplied.

    <a href=\"{%blog_url%}/admin/tags.php\" class=\"button_return\">Return</a>
  ";
}

$replace[] = "Delete Tag";
$replace[] = $body;

echo str_replace($search, $replace, $theme);

echo $Buffer->flush();

?>
