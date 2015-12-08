<?php

if (isset($_POST["redirect_rules"])) {

  $statement = "

    UPDATE " . DB_PREF . "extension_redirect_manager
    SET redirect_rules = ?
    WHERE 1 = 1
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["redirect_rules"]);

  $query->execute();

  if (!$query) {

    // Failed to update redirect_rules.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=failure");
  }
  else {

    // Successfully updated redirect_rules.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=success");
  }
}
else {

  if (isset($_GET["result"])) {

    if ($_GET["result"] == "failure") {

      echo "Failed to update redirect rules.";
    }
    else {

      echo "Redirect rules have been updated.";
    }

    echo "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
  }
  else {

    $statement = "

      SELECT redirect_rules
      FROM " . DB_PREF . "extension_redirect_manager
      WHERE 1 = 1
    ";

    $query = $Database->getHandle()->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or is empty.

      echo "Error: failed to get redirect rules!";

      echo "<a href=\"extensions.php\" class=\"button_return\">Return</a>";
    }
    else {

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get redirect rules.
      $redirect_rules = $result->redirect_rules;

      echo "

        <form method=\"post\" class=\"edit_redirect_rules\">
          <label for=\"redirect_rules\">Redirect Rules</label>
          <textarea id=\"body\" name=\"redirect_rules\" required>{$redirect_rules}</textarea>
          <input type=\"submit\" value=\"Save\">
        </form>
      ";
    }
  }
}

?>
