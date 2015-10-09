<?php

// Allow access to include files.
define("KAKU_INCLUDE", true);

require "includes/configuration.php";

require "includes/classes/utility.php";
require "includes/classes/database.php";

$Utility = new Utility;
$Database = new Database;

$Database->connect();

$root_address = $Utility->getRootAddress();

if (!isset($_GET["code"])) {

  // Redirect to index.
  header("Location: {$root_address}");
}

switch ($_GET["code"]) {

  case 404:

    // Select URL for 404 page.
    $statement = "

      SELECT body
      FROM " . DB_PREF . "tags
      WHERE title = '404_url'
    ";

    $query = $Database->getHandle()->query($statement);

    if (!$query) {

      // Query failed. Redirect to root index.
      header("Location: {$root_address}");
    }
    else if ($query->rowCount() == 0) {

      // The tag 404_url does not exist. Redirect to root index.
      header("Location: {$root_address}");
    }
    else {

      // Fetch the result as an object.
      $result = $query->fetch(PDO::FETCH_OBJ);

      // Redirect to 404 page.
      header("Location: {$result->body}");
    }
  break;

  default:

    // Redirect to index.
    header("Location: {$root_address}");
  break;
}

$Database->disconnect();

?>
