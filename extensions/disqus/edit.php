<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

if (isset($_POST["forum_name"])) {

  $statement = "

    UPDATE " . DB_PREF . "extension_disqus
    SET forum_name = ?
    WHERE 1 = 1
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["forum_name"]);

  $query->execute();

  if (!$query) {

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

    $query = $Database->getHandle()->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or is empty.

      echo "Error: failed to get forum name!";

      echo "<a href=\"./extensions.php\" class=\"button_return\">Return</a>";
    }
    else {

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get forum name.
      $forum_name = $result->forum_name;

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
