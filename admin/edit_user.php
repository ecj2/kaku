<?php

session_start();

if (!isset($_SESSION["username"])) {

  // User is not logged in.
  header("Location: login.php");

  exit();
}

require "../core/includes/common.php";

// @TODO: Load extensions.

// Get template markup.
$theme = $Theme->getFileContents("template", true);

$search = [];
$replace = [];

$search[] = "{%page_title%}";
$search[] = "{%page_body%}";

if (isset($_GET["id"]) && isset($_POST["username"]) && isset($_POST["password"])) {

  $statement = "

    UPDATE " . DB_PREF . "users
    SET username = ?, nickname = ?, email = ?, password = ?
    WHERE id = ?
  ";

  $Query = $Database->getHandle()->prepare($statement);

  $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["username"]);
  $Query->bindParam(2, $_POST["nickname"]);
  $Query->bindParam(3, $_POST["email"]);
  $Query->bindParam(4, $password);
  $Query->bindParam(5, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Failed to update user.
    header("Location: users.php?code=0&message=failed to update user");

    exit();
  }

  // Successfully updated user.
  header("Location: users.php?code=1&message=user updated successfully");

  exit();
}

$body = "";

if (isset($_GET["id"]) && !empty($_GET["id"])) {

  $statement = "

    SELECT username, nickname, email
    FROM " . DB_PREF . "users
    WHERE id = ?
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_GET["id"]);

  $Query->execute();

  if (!$Query) {

    // Failed to get user data.
    $Utility->displayError("failed to get user data");
  }

  if ($Query->rowCount() == 0) {

    // This user does not exist.
    $body .= "

      There exists no user with an ID of " . $_GET["id"] . ".

      <a href=\"users.php\" class=\"button_return\">Return</a>
    ";
  }
  else {

    $User = $Query->fetch(PDO::FETCH_OBJ);

    $user_username = $User->username;
    $user_nickname = $User->nickname;
    $user_email = $User->email;

    // Preserve HTML entities.
    $user_username = htmlentities($user_username);
    $user_nickname = htmlentities($user_nickname);
    $user_email = htmlentities($user_email);

    // Encode { and } to prevent them from being replaced by the output buffer.
    $user_username = str_replace(["{", "}"], ["&#123;", "&#125;"], $user_username);
    $user_nickname = str_replace(["{", "}"], ["&#123;", "&#125;"], $user_nickname);
    $user_email = str_replace(["{", "}"], ["&#123;", "&#125;"], $user_email);

    $body .= "

      Use the form below to edit the user.<br><br>

      <form method=\"post\" class=\"edit_user\">

        <label for=\"username\">Username</label>
        <input type=\"text\" id=\"username\" name=\"username\" value=\"{$user_username}\" required>

        <label for=\"nickname\">Nickname</label>
        <input type=\"text\" id=\"nickname\" name=\"nickname\" value=\"{$user_nickname}\" required>

        <label for=\"email\">Email</label>
        <input type=\"email\" id=\"email\" name=\"email\" value=\"{$user_email}\" required>

        <label for=\"password\">Password</label>
        <input type=\"password\" id=\"password\" name=\"password\" required>

        <input type=\"submit\" value=\"Save\">
      </form>
    ";
  }
}
else {

  // No ID given.
  $body .= "

    No ID supplied.

    <a href=\"{%blog_url%}/admin/users.php\" class=\"button_return\">Return</a>
  ";
}

$replace[] = "Edit User";
$replace[] = $body;

echo str_replace($search, $replace, $theme);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

?>
