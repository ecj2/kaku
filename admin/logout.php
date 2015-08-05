<?php

session_start();

if (!isset($_SESSION["username"])) {

  // Redirect to login.
  header("Location: ./login.php");
}

unset($_SESSION["username"]);

// Redirect to login.
header("Location: ./login.php");

?>
