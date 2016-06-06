<?php

session_start();

unset($_SESSION["user_id"]);
unset($_SESSION["username"]);

// Redirect to login.
header("Location: ./login.php");

exit();

?>
