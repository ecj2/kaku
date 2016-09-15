<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

if (isset($_POST["lure"])) {

  $statement = "

    UPDATE " . DB_PREF . "extension_truncate
    SET lure = ?
    WHERE 1 = 1
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["lure"]);

  $Query->execute();

  if (!$Query) {

    // Failed to update lure text.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=failure");

    exit();
  }
  else {

    // Successfully updated lure text.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=success");

    exit();
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

    echo "<a href=\"{%blog_url%}/admin/extensions.php\" class=\"button_return\">Return</a>";
  }
  else {

    $statement = "

      SELECT lure
      FROM " . DB_PREF . "extension_truncate
      WHERE 1 = 1
      LIMIT 1
    ";

    $Query = $Database->getHandle()->query($statement);

    if (!$Query || $Query->rowCount() == 0) {

      // Query failed or is empty.

      echo "Error: failed to get lure text!";

      echo "<a href=\"{%blog_url%}/admin/extensions.php\" class=\"button_return\">Return</a>";
    }
    else {

      // Fetch the result as an object.
      $result = $Query->fetch(PDO::FETCH_OBJ);

      // Get lure text.
      $lure = $result->lure;

      // Preserve HTML entities.
      $lure = htmlentities($lure);

      // Encode { and } to prevent them from being replaced by the output buffer.
      $lure = str_replace(["{", "}"], ["&#123;", "&#125;"], $lure);

      echo "

        Use the form below to edit the extension.<br><br>

        <form method=\"post\" class=\"edit_lure\">
          <label for=\"lure\">Lure text</label>
          <input type=\"text\" id=\"lure\" name=\"lure\" value=\"{$lure}\">
          <input type=\"submit\" value=\"Save\">
        </form>
      ";
    }
  }
}

?>
