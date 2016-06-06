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

if (isset($_GET["verify"])) {

  $statement = "

    SELECT 1
    FROM " . DB_PREF . "users
    WHERE reset_hash = ?
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_GET["verify"]);

  $Query->execute();

  if (!$Query) {

    // Something went wrong.
    $Utility->displayError("failed to select user based on reset hash");
  }

  if ($Query->rowCount() == 0) {

    // This hash does not exist.
    Header("Location: ./reset_password.php?code=0&message=no matches for that verification hash");

    exit();
  }
  else {

    $statement = "

      UPDATE " . DB_PREF . "users
      SET password = ?, reset_hash = '', reset_password = '0'
      WHERE reset_hash = ?
    ";

    $Query = $Database->getHandle()->prepare($statement);

    $new_password_string = "";

    $letters = range("a", "z");

    for ($i = 0; $i < 3; ++$i) {

      // Generate new password.
      $new_password_string .= $letters[rand(0, 25)] . rand(0, 9);
    }

    $new_password_hash = password_hash($new_password_string, PASSWORD_BCRYPT);

    // Prevent SQL injections.
    $Query->bindParam(1, $new_password_hash);
    $Query->bindParam(2, $_GET["verify"]);

    $Query->execute();

    if (!$Query) {

      // Something went wrong.
      $Utility->displayError("failed to reset user password");
    }

    Header("Location: ./reset_password.php?code=1&message=password reset to \"{$new_password_string}\"");

    exit();
  }
}
if (isset($_POST["email"])) {

  $statement = "

    SELECT email, nickname
    FROM " . DB_PREF . "users
    WHERE email = ?
    ORDER BY id DESC
    LIMIT 1
  ";

  $Query = $Database->getHandle()->prepare($statement);

  // Prevent SQL injections.
  $Query->bindParam(1, $_POST["email"]);

  $Query->execute();

  if (!$Query) {

    // Something went wrong.
    $Utility->displayError("failed to select user email");
  }

  if ($Query->rowCount() == 0) {

    // The email does not exist in the database.
    Header("Location: ./reset_password.php?code=0&message=that email address does not belong to any user");

    exit();
  }
  else {

    // Fetch the results.
    $Result = $Query->fetch(PDO::FETCH_OBJ);

    $email = $Result->email;
    $nickname = $Result->nickname;

    $statement = "

      UPDATE " . DB_PREF . "users
      SET reset_password = 1, reset_hash = ?
      WHERE email = '{$email}'
    ";

    $Query = $Database->getHandle()->prepare($statement);

    $reset_hash = (time() . sha1($email));

    // Prevent SQL injections.
    $Query->bindParam(1, $reset_hash);

    $Query->execute();

    if (!$Query) {

      // Something went wrong.
      $Utility->displayError("failed to update user password resets");
    }

    $message = "Hi {$nickname},\n\nNavigate to the following address to reset your Kaku account password: ";
    $message .= "{%blog_url%}/admin/reset_password.php?verify={$reset_hash}\n\nTake care,\n\nKaku";

    // Fill the blog_url tag in the reset message.
    $message = $Utility->replaceNestedTags($message);

    if (mail($email, "Kaku Password Reset", $message)) {

      // Successfully sent reset instructions.
      Header("Location: ./reset_password.php?code=1&message=an email has been sent to you with reset instructions");
    }
    else {

      // Failed to send reset instructions.
      Header("Location: ./reset_password.php?code=0&message=the server failed to send you a reset email");
    }

    exit();
  }
}
else {

  if (isset($_GET["code"]) && isset($_GET["message"])) {

    if ($_GET["code"] == 0) {

      // Failure notice.
      $body .= "<span class=\"failure\">Notice: ";
    }
    else if ($_GET["code"] == 1) {

      // Success notice.
      $body .= "<span class=\"success\">Notice: ";
    }

    $body .= $_GET["message"] . ".</span>";
  }

  $body .= "

    Use the form below to reset your password.<br><br>

    <form method=\"post\">

      <label for=\"email\">Email</label>
      <input type=\"email\" id=\"email\" name=\"email\" required>

      <input type=\"submit\" value=\"Reset Password\">
    </form>

    Click <a href=\"./index.php\">here</a> to return to the login page.
  ";
}

$replace[] = "Reset Password";
$replace[] = $body;

echo str_replace($search, $replace, $template);

// Clear the admin_head_content and admin_body_content tags if they go unused.
$Hook->addAction("admin_head_content", "");
$Hook->addAction("admin_body_content", "");

$Output->replaceTags();

$Output->flushBuffer();

?>
