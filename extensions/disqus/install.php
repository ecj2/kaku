<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

if (!$Database->checkTableExistence("extension_disqus")) {

  // Create the extension_disqus table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "extension_disqus (

      forum_name TEXT NOT NULL
    )"
  )) {

    array_push(

      $errors,

      "failed to create " . DB_PREF . "extension_disqus table"
    );
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

    array_push(

      $errors,

      "failed to insert into " . DB_PREF . "extension_disqus"
    );
  }
}

?>
