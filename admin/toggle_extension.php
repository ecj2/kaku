<?php

require "common.php";

if (isset($_GET["code"]) && isset($_GET["title"])) {

  $statement = "

    SELECT hash
    FROM " . DB_PREF . "extensions
    WHERE hash = ?
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  $hash = md5($_GET["title"]);

  // Prevent SQL injections.
  $Query->bindParam(1, $hash);

  $Query->execute();

  if (!$Query) {

    // Something went wrong.
    $Utility->displayError("failed to select extension title");
  }

  if ($Query->rowCount() == 0) {

    // Extension doesn't exist in database.
    $statement = "

      INSERT INTO " . DB_PREF . "extensions (

        hash,

        status
      )
      VALUES (

        ?,

        1
      )
    ";

    $Query = $Database->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $hash);

    $Query->execute();

    $code = 1;

    $message = "extension has been activated";

    if (!$Query) {

      // Something went wrong.
      $Utility->displayError("failed to insert new extension");

      $code = 0;

      $message = "failed to activate extension";
    }
    else {

      // Refresh the page to assign new activation status.
      header("Location: extensions.php?code={$code}&message={$message}");

      exit();
    }
  }
  else {

    // Get extension's hash.
    $extension_hash = $Query->fetch(PDO::FETCH_OBJ)->hash;

    $message = "";

    $code = 1;

    if ($_GET["code"] == 0) {

      // Activate extension.
      $statement = "

        UPDATE " . DB_PREF . "extensions
        SET status = 1
        WHERE hash = '{$extension_hash}'
      ";

      $Query = $Database->getHandle()->query($statement);

      if (!$Query) {

        $message = "failed to activate extension";

        $code = 0;
      }
      else {

        $message = "extension has been activated";
      }
    }
    else {

      // Deactivate extension.
      $statement = "

        UPDATE " . DB_PREF . "extensions
        SET status = 0
        WHERE hash = '{$extension_hash}'
      ";

      $Query = $Database->getHandle()->query($statement);

      if (!$Query) {

        $message = "failed to deactivate extension";

        $code = 0;
      }
      else {

        $message = "extension has been deactivated";
      }
    }

    header("Location: extensions.php?code={$code}&message={$message}");

    exit();
  }
}
else {

  header("Location: extensions.php?code=0&message=no extension information specified");

  exit();
}

?>
