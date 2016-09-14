<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

if (isset($_POST["next_page_text"]) && isset($_POST["previous_page_text"])) {

  $statement = "

    UPDATE " . DB_PREF . "extension_pagination
    SET next_page_text = ?, previous_page_text = ?
    WHERE 1 = 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["next_page_text"]);
  $Query->bindParam(2, $_POST["previous_page_text"]);

  $Query->execute();

  if (!$Query) {

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

    $Query = $Database->getHandle()->query($statement);

    if (!$Query || $Query->rowCount() == 0) {

      // Query failed or is empty.

      echo "Error: failed to get pagination text!";

      echo "<a href=\"./extensions.php\" class=\"button_return\">Return</a>";
    }
    else {

      // Fetch the result as an object.
      $result = $Query->fetch(PDO::FETCH_OBJ);

      // Get texts.
      $next_page_text = $result->next_page_text;
      $previous_page_text = $result->previous_page_text;

      // Preserve HTML entities.
      $next_page_text = htmlentities($next_page_text);
      $previous_page_text = htmlentities($previous_page_text);

      // Encode { and } to prevent them from being replaced by the output buffer.
      $next_page_text = str_replace(["{", "}"], ["&#123;", "&#125;"], $next_page_text);
      $previous_page_text = str_replace(["{", "}"], ["&#123;", "&#125;"], $previous_page_text);

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
