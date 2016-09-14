<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

if (!$Database->checkTableExistence("extension_disqus")) {

  // Create the extension_disqus table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "extension_disqus (

      forum_name TEXT NOT NULL
    )"
  )) {

    // Failed to create the extension_disqus table.
    $errors[] = "failed to create " . DB_PREF . "extension_disqus table";
  }

  // Set a default value for the forum name.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "extension_disqus (

      forum_name
    )
    VALUES (

      ''
    )"
  )) {

    // Failed to set a default value for the forum name.
    $errors[] = "failed to insert into " . DB_PREF . "extension_disqus";
  }
}

?>
