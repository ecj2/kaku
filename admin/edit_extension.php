<?php

require "common.php";

if (!isset($_GET["title"]) || empty($_GET["title"])) {

  $body .= "

    No extension specified.

    <a href=\"{%blog_url%}/admin/extensions.php\" class=\"button_return\">Return</a>
  ";
}
else if (!file_exists("../extensions/" . $_GET["title"] . "/edit.php")) {

  $body .= "

    This extension lacks an edit page.

    <a href=\"{%blog_url%}/admin/extensions.php\" class=\"button_return\">Return</a>
  ";
}
else {

  // Start a new buffer for the extension's edit.php file.
  ob_start();

  require "../extensions/" . $_GET["title"] . "/edit.php";

  // The extensions file's contents will be thrown to the buffer.
  $body .= ob_get_contents();

  // End the extension buffer.
  ob_end_clean();
}

$replace[] = "Edit Extension";
$replace[] = $body;

echo str_replace($search, $replace, $theme);

echo $Buffer->flush();

?>
