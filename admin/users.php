<?php

session_start();

if (!isset($_SESSION["username"])) {

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

if (!file_exists("content/themes/{$theme_name}/template.html")) {

  // Theme template does not exist.
  $Utility->displayError("theme template file does not exist");
}

// Display the theme contents.
echo file_get_contents("content/themes/{$theme_name}/template.html");

$page_body = "";

$page_title = "Users";

if (isset($_GET["result"])) {

  if ($_GET["result"] == "success") {

    $page_body .= "

      <span class=\"success\">
        The users has been added.
      </span>
    ";
  }
  else {

    $page_body .= "

      <span class=\"failure\">
        Failed to add user.
      </span>
    ";
  }
}

if (isset($_POST["username"]) && isset($_POST["nickname"]) && isset($_POST["email"]) && isset($_POST["password"])) {

  $statement = "

    INSERT INTO " . DB_PREF . "users (

      username,

      nickname,

      email,

      password
    )
    VALUES (

      ?,

      ?,

      ?,

      ?
    )
  ";

  $query = $Database->getHandle()->prepare($statement);

  $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

  // Prevent SQL injections.
  $query->bindParam(1, $_POST["username"]);
  $query->bindParam(2, $_POST["nickname"]);
  $query->bindParam(3, $_POST["email"]);
  $query->bindParam(4, $password);

  $query->execute();

  if (!$query) {

    // Failed to add user.
    header("Location: ./users.php?result=failure");
  }

  // Successfully added user.
  header("Location: ./users.php?result=success");
}
else {

  $page_body .= "

    <form method=\"post\" class=\"add_user\">
      <label for=\"username\">Username</label>
      <input type=\"text\" id=\"username\" name=\"username\" required>
      <label for=\"nickname\">Nickname</label>
      <input type=\"text\" id=\"nickname\" name=\"nickname\" required>
      <label for=\"email\">Email</label>
      <input type=\"email\" id=\"email\" name=\"email\" required>
      <label for=\"password\">Password</label>
      <input type=\"password\" id=\"password\" name=\"password\" required>
      <input type=\"submit\" value=\"Add User\">
    </form>
  ";

  $statement = "

    SELECT id, username
    FROM " . DB_PREF . "users
    ORDER BY id DESC
  ";

  $query = $Database->getHandle()->query($statement);

  if (!$query || $query->rowCount() == 0) {

    // Query failed or returned zero rows.
    $page_body .= "";
  }
  else {

    $page_body .= "<table class=\"users\">";

    $page_body .= "<tr>";
    $page_body .= "<th>Username</th>";
    $page_body .= "<th>Edit</th>";
    $page_body .= "<th>Delete</th>";
    $page_body .= "</tr>";

    while ($user = $query->fetch(PDO::FETCH_OBJ)) {

      $page_body .= "

        <tr>
          <td>{$user->username}</td>
          <td><a href=\"edit_user.php?id={$user->id}\">Edit</a></td>
          <td><a href=\"delete_user.php?id={$user->id}\">Delete</a></td>
        </tr>
      ";
    }

    $page_body .= "</table>";
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
