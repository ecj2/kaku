<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

if (isset($_POST["forum_name"])) {

  $statement = "

    UPDATE " . DB_PREF . "extension_disqus
    SET forum_name = ?
    WHERE 1 = 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["forum_name"]);

  $Query->execute();

  if (!$Query) {

    // Failed to update forum_name.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=failure");

    exit();
  }
  else {

    // Successfully updated forum_name.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=success");

    exit();
  }
}
else {

  if (isset($_GET["result"])) {

    if ($_GET["result"] == "failure") {

      echo "Failed to update forum name.";
    }
    else {

      echo "Forum name has been updated.";
    }

    echo "<a href=\"./extensions.php\" class=\"button_return\">Return</a>";
  }
  else {

    $statement = "

      SELECT forum_name
      FROM " . DB_PREF . "extension_disqus
      WHERE 1 = 1
      LIMIT 1
    ";

    $Query = $Database->getHandle()->query($statement);

    if (!$Query || $Query->rowCount() == 0) {

      // Query failed or is empty.

      echo "Error: failed to get forum name!";

      echo "<a href=\"./extensions.php\" class=\"button_return\">Return</a>";
    }
    else {

      // Fetch the result as an object.
      $result = $Query->fetch(PDO::FETCH_OBJ);

      // Get forum name.
      $forum_name = $result->forum_name;

      // Preserve HTML entities.
      $forum_name = htmlentities($forum_name);

      // Encode { and } to prevent them from being replaced by the output buffer.
      $forum_name = str_replace(["{", "}"], ["&#123;", "&#125;"], $forum_name);

      echo "

        Use the form below to edit the extension.<br><br>

        <form method=\"post\" class=\"edit_forum_name\">
          <label for=\"forum_name\">Forum Name</label>
          <input type=\"text\" id=\"forum_name\" name=\"forum_name\" value=\"{$forum_name}\">
          <input type=\"submit\" value=\"Save\">
        </form>
      ";
    }
  }
}

?>
