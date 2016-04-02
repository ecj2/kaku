<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

if (isset($_POST["lure"])) {

  $statement = "

    UPDATE " . DB_PREF . "extension_truncate
    SET lure = ?
    WHERE 1 = 1
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["lure"]);

  $query->execute();

  if (!$query) {

    // Failed to update lure text.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=failure");
  }
  else {

    // Successfully updated lure text.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=success");
  }
}
else {

  if (isset($_GET["result"])) {

    if ($_GET["result"] == "failure") {

      echo "Failed to update lure text.";
    }
    else {

      echo "Lure text has been updated.";
    }

    echo "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
  }
  else {

    $statement = "

      SELECT lure
      FROM " . DB_PREF . "extension_truncate
      WHERE 1 = 1
    ";

    $query = $Database->getHandle()->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or is empty.

      echo "Error: failed to get lure text!";

      echo "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
    }
    else {

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get lure text.
      $lure = $result->lure;

      echo "

        <form method=\"post\" class=\"edit_lure\">
          <label for=\"lure\">Lure Text</label>
          <input type=\"text\" id=\"lure\" name=\"lure\" value=\"{$lure}\"
          required>
          <input type=\"submit\" value=\"Save\">
        </form>
      ";
    }
  }
}

?>
