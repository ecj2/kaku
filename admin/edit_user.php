<?php

session_start();

if (!isset($_SESSION["username"])) {

  header("Location: ./login.php");
}

require "../includes/configuration.php";

require "../includes/classes/utility.php";
require "../includes/classes/database.php";

require "../includes/classes/hook.php";
require "../includes/classes/output.php";

global $Hook;

$Hook = new Hook;

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

if (!file_exists("content/themes/{$theme_name}/template.html")) {

  // Theme template does not exist.
  $Utility->displayError("theme template file does not exist");
}

// Display the theme contents.
echo file_get_contents("content/themes/{$theme_name}/template.html");

$page_body = "";

$page_title = "Edit User";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "The user has been saved.";
  }
  else {

    $page_body .= "Failed to save user.";
  }

  $page_body .= "<a href=\"users.php\" class=\"button_return\">Return</a>";
}
else if (isset($_POST["username"]) && isset($_POST["nickname"]) && isset($_POST["email"]) && isset($_POST["password"])) {

  $statement = "

    UPDATE " . DB_PREF . "users
    SET username = ?, nickname = ?, email = ?, password = ?
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["username"]);
  $query->bindParam(2, $_POST["nickname"]);
  $query->bindParam(3, $_POST["email"]);
  $query->bindParam(4, $password);
  $query->bindParam(5, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Failed to update user.
    header("Location: ./edit_user.php?id=" . $_GET["id"] . "&result=failure");
  }

  // Successfully updated user.
  header("Location: ./edit_user.php?id=" . $_GET["id"] . "&result=success");
}
else {

  $statement = "

    SELECT username, nickname, email
    FROM " . DB_PREF . "users
    WHERE id = ?
  ";

  $query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $query->bindParam(1, $_GET["id"]);

  $query->execute();

  if (!$query) {

    // Query failed.
    $page_body .= "Failed to select user data.";
    $page_body .= "<a href=\"users.php\" class=\"button_return\">Return</a>";
  }
  else if ($query->rowCount() == 0) {

    // Query returned zero rows.
    $page_body .= "There exists no user with an ID of " . $_GET["id"] . ".";
    $page_body .= "<a href=\"users.php\" class=\"button_return\">Return</a>";
  }
  else {

    $user = $query->fetch(PDO::FETCH_OBJ);

    $username = $user->username;
    $nickname = $user->nickname;
    $email = $user->email;

    $page_body .= "

      <form method=\"post\" class=\"edit_user\">
        <label for=\"username\">Username</label>
        <input type=\"text\" id=\"username\" name=\"username\"
        value=\"{$username}\" required>
        <label for=\"nickname\">Nickname</label>
        <input type=\"text\" id=\"nickname\" name=\"nickname\"
        value=\"{$nickname}\" required>
        <label for=\"email\">Email</label>
        <input type=\"email\" id=\"email\" name=\"email\"
        value=\"{$email}\" required>
        <label for=\"password\">Password</label>
        <input type=\"password\" id=\"password\" name=\"password\" required>
        <input type=\"submit\" value=\"Save\">
      </form>
    ";
  }
}

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
