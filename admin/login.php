<?php

session_start();

if (isset($_SESSION["username"])) {

  header("Location: ./login.php");
}

require "../includes/configuration.php";

require "../includes/classes/utility.php";
require "../includes/classes/database.php";

require "../includes/classes/output.php";

$Output = new Output;
$Utility = new Utility;
$Database = new Database;

$Database->connect();

$Output->setDatabaseHandle($Database->getHandle());

$Output->startBuffer();

$statement = "

  SELECT body
  FROM " . DB_PREF . "tags
  WHERE title = 'admin_theme_name'
  ORDER BY id DESC
";

$query = $Database->getHandle()->query($statement);

if (!$query || $query->rowCount() == 0) {

  // Query failed or returned zero rows.
  $Utility->displayError("failed to get admin theme name");
}

// Get the admin theme name.
$theme_name = $query->fetch(PDO::FETCH_OBJ)->body;

if (!file_exists("content/themes/{$theme_name}/login.html")) {

  // Theme template does not exist.
  $Utility->displayError("theme template file does not exist");
}

// Display the theme contents.
echo file_get_contents("content/themes/{$theme_name}/login.html");

$page_body = "";

$page_title = "Login";

if (isset($_POST["username"]) && isset($_POST["password"])) {

  $statement = "

    SELECT password
    FROM " . DB_PREF . "users
    WHERE username = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["username"]);

  $query->execute();

  if (!$query) {

    // Query failed.
    header("Location: ./login.php?error=An error occurred!");
  }
  else if ($query->rowCount() == 0) {

    // Username doesn't exist.
    header("Location: ./login.php?error=Incorrect login credentials!");
  }
  else {

    // Fetch result as an object.
    $result = $query->fetch(PDO::FETCH_OBJ);

    if (password_verify($_POST["password"], $result->password)) {

      // Correct password.

      $_SESSION["username"] = $_POST["username"];

      header("Location: ./index.php");
    }
    else {

      // Incorrect password.
      header("Location: ./login.php?error=Incorrect login credentials!");
    }
  }
}

if (isset($_GET["error"])) {

  $page_body .= "<span class=\"failure\">" . $_GET["error"] . "</span>";
}

$page_body .= "

  <form method=\"post\">
    <label for=\"username\">Username</label>
    <input type=\"text\" id=\"username\" name=\"username\" required>
    <label for=\"password\">Password</label>
    <input type=\"password\" id=\"password\" name=\"password\" required>
    <input type=\"submit\" value=\"Login\">
  </form>
";

$Output->addTagReplacement(

  "page_body",

  $page_body
);

$Output->addTagReplacement(

  "page_title",

  $page_title
);

$Output->replaceTags();

$Output->flushBuffer();

$Database->disconnect();

?>
