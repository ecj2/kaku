<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

if (isset($_POST["shortname"])) {

  $statement = "

    UPDATE " . DB_PREF . "extension_disqus
    SET shortname = ?
    WHERE 1 = 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["shortname"]);

  $Query->execute();

  if (!$Query) {

    // Failed to update shortname.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=failure");

    exit();
  }
  else {

    // Successfully updated shortname.
    header("Location: ./edit_extension.php?title=" . $_GET["title"] . "&result=success");

    exit();
  }
}
else {

  if (isset($_GET["result"])) {

    if ($_GET["result"] == "failure") {

      echo "Failed to update Disqus shortname.";
    }
    else {

      echo "Disqus shortname has been updated.";
    }

    echo "<a href=\"{%blog_url%}/admin/extensions.php\" class=\"button_return\">Return</a>";
  }
  else {

    $statement = "

      SELECT shortname
      FROM " . DB_PREF . "extension_disqus
      WHERE 1 = 1
      LIMIT 1
    ";

    $Query = $Database->getHandle()->query($statement);

    if (!$Query || $Query->rowCount() == 0) {

      // Query failed or is empty.

      echo "Error: failed to get Disqus shortname!";

      echo "<a href=\"{%blog_url%}/admin/extensions.php\" class=\"button_return\">Return</a>";
    }
    else {

      // Fetch the result as an object.
      $result = $Query->fetch(PDO::FETCH_OBJ);

      // Get shortname.
      $shortname = $result->shortname;

      // Preserve HTML entities.
      $shortname = htmlentities($shortname);

      // Encode { and } to prevent them from being replaced by the output buffer.
      $shortname = str_replace(["{", "}"], ["&#123;", "&#125;"], $shortname);

      echo "

        Use the form below to edit the extension.<br><br>

        <form method=\"post\" class=\"edit_shortname\">
          <label for=\"shortname\">Disqus shortname</label>
          <input type=\"text\" id=\"shortname\" name=\"shortname\" value=\"{$shortname}\">
          <input type=\"submit\" value=\"Save\">
        </form>
      ";
    }
  }
}

?>
