<?php

session_start();

if (isset($_SESSION["username"])) {

  // User is already logged in.
  header("Location: ./dashboard.php");

  exit();
}

require "../core/includes/common.php";

$Output->startBuffer();

$Output->loadExtensions();

// Get login markup.
$template = $Template->getFileContents("login", 0, 1);

$search = [];
$replace = [];

$search[] = "{%page_title%}";
$search[] = "{%page_body%}";

$body = "";

if (isset($_POST["username"]) && isset($_POST["password"])) {

  $statement = "

    SELECT id, password
    FROM " . DB_PREF . "users
    WHERE username = ?
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["username"]);

  $Query->execute();

  if (!$Query) {

    // Something went wrong.
    header("Location: ./login.php?error=an unknown error occurred");

    exit();
  }
  else if ($Query->rowCount() == 0) {

    // Username doesn't exist.
    header("Location: ./login.php?error=incorrect username and/or password");

    exit();
  }
  else {

    // Fetch result as an object.
    $Result = $Query->fetch(PDO::FETCH_OBJ);

    if (password_verify($_POST["password"], $Result->password)) {

      // Correct password.

      $_SESSION["user_id"] = $Result->id;
      $_SESSION["username"] = $_POST["username"];

      // Redirect to dashboard.
      header("Location: ./dashboard.php");

      exit();
    }
    else {

      // Incorrect password.
      header("Location: ./login.php?error=incorrect username and/or password");

      exit();
    }
  }
}

if (isset($_GET["error"])) {

  $body .= "<span class=\"failure\">Notice: " . $_GET["error"] . ".</span>";
}

$body .= "

  Use the form below to log in.<br><br>

  <form method=\"post\">

    <label for=\"username\">Username</label>
    <input type=\"text\" id=\"username\" name=\"username\" required>

    <label for=\"password\">Password</label>
    <input type=\"password\" id=\"password\" name=\"password\" required>

    <input type=\"submit\" value=\"Login\">
  </form>

  <a href=\"{%blog_url%}/admin/reset_password.php\">Forgot password?</a>
";

$replace[] = "Login";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
