<?php

session_start();

if (isset($_SESSION["username"])) {

  // User is already logged in.
  header("Location: dashboard.php");

  exit();
}

// User is not logged in.
header("Location: login.php");

exit();

?>
