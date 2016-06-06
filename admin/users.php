<?php

session_start();

if (!isset($_SESSION["username"])) {

  // User is not logged in.
  header("Location: ./login.php");

  exit();
}

require "../core/includes/common.php";

$Output->startBuffer();

$Output->loadExtensions();

// Get template markup.
$template = $Template->getFileContents("template", 0, 1);

$search = [];
$replace = [];

$search[] = "{%page_title%}";
$search[] = "{%page_body%}";

if (isset($_POST["username"]) && isset($_POST["password"])) {

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

  $Query = $Database->getHandle()->prepare($statement);

  $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["username"]);
  $Query->bindParam(2, $_POST["nickname"]);
  $Query->bindParam(3, $_POST["email"]);
  $Query->bindParam(4, $password);

  $Query->execute();

  if (!$Query) {

    // Failed to create user.
    header("Location: ./users.php?code=0&message=failed to create user");

    exit();
  }

  // Successfully added user.
  header("Location: ./users.php?code=1&message=user created successfully");

  exit();
}

$body = "";

if (isset($_GET["code"]) && isset($_GET["message"])) {

  if ($_GET["code"] == 0) {

    // Failure notice.
    $body .= "<span class=\"failure\">Notice: ";
  }
  else if ($_GET["code"] == 1) {

    // Success notice.
    $body .= "<span class=\"success\">Notice: ";
  }

  // Encode { and } to prevent them from being replaced by the output buffer.
  $body .= str_replace(["{", "}"], ["&#123;", "&#125;"], $_GET["message"]) . ".</span>";
}

$body .= "

  Use the form below to create a new user.<br><br>

  <form method=\"post\" class=\"add_user\">

    <label for=\"username\">Username</label>
    <input type=\"text\" id=\"username\" name=\"username\" required>

    <label for=\"nickname\">Nickname</label>
    <input type=\"text\" id=\"nickname\" name=\"nickname\" required>

    <label for=\"email\">Email</label>
    <input type=\"email\" id=\"email\" name=\"email\" required>

    <label for=\"password\">Password</label>
    <input type=\"password\" id=\"password\" name=\"password\" required>

    <input type=\"submit\" value=\"Create User\">
  </form>
";

$statement = "

  SELECT id, username
  FROM " . DB_PREF . "users
  ORDER BY id DESC
";

$Query = $Database->getHandle()->query($statement);

if (!$Query) {

  // Something went wrong.
  $Utility->displayError("failed to select users");
}

if ($Query->rowCount() > 0) {

  $body .= "

    Existing users are displayed below.<br><br>

    <table class=\"two-column\">
      <tr>
        <th>Username</th>
        <th>Action</th>
      </tr>
  ";

  while ($User = $Query->fetch(PDO::FETCH_OBJ)) {

    $edit_link = "{%blog_url%}/admin/edit_user.php?id={$User->id}";
    $delete_link = "{%blog_url%}/admin/delete_user.php?id={$User->id}";

    // Encode { and } to prevent them from being replaced by the output buffer.
    $username = str_replace(["{", "}"], ["&#123;", "&#125;"], $User->username);

    $body .= "

      <tr>
        <td>{$username}</td>
        <td><a href=\"{$edit_link}\">Edit</a> - <a href=\"{$delete_link}\">Delete</a></td>
      </tr>
    ";
  }

  $body .= "</table>";
}

$replace[] = "Users";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
