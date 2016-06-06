<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

if (isset($_POST["next_page_text"]) && isset($_POST["previous_page_text"])) {

  $statement = "

    UPDATE " . DB_PREF . "extension_pagination
    SET next_page_text = ?, previous_page_text = ?
    WHERE 1 = 1
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["next_page_text"]);
  $query->bindParam(2, $_POST["previous_page_text"]);

  $query->execute();

  if (!$query) {

    // Failed to update.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=failure");

    exit();
  }
  else {

    // Successfully updated.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=success");

    exit();
  }
}
else {

  if (isset($_GET["result"])) {

    if ($_GET["result"] == "failure") {

      echo "Failed to pagination text.";
    }
    else {

      echo "Pagination text has been updated.";
    }

    echo "<a href=\"./extensions.php\" class=\"button_return\">Return</a>";
  }
  else {

    $statement = "

      SELECT *
      FROM " . DB_PREF . "extension_pagination
      WHERE 1 = 1
      LIMIT 1
    ";

    $query = $Database->getHandle()->query($statement);

    if (!$query || $query->rowCount() == 0) {

      // Query failed or is empty.

      echo "Error: failed to get pagination text!";

      echo "<a href=\"./extensions.php\" class=\"button_return\">Return</a>";
    }
    else {

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Get texts.
      $next_page_text = $result->next_page_text;
      $previous_page_text = $result->previous_page_text;

      echo "

        Use the form below to edit the extension.<br><br>

        <form method=\"post\" class=\"edit_pagination_text\">
          <label for=\"next_page_text\">Next page text</label>
          <input type=\"text\" id=\"next_page_text\" name=\"next_page_text\" value=\"{$next_page_text}\">
          <label for=\"previous_page_text\">Previous page text</label>
          <input type=\"text\" id=\"previous_page_text\" name=\"previous_page_text\" value=\"{$previous_page_text}\">
          <input type=\"submit\" value=\"Save\">
        </form>
      ";
    }
  }
}

?>
