<?php

if (!$Database->checkTableExistence("extension_redirect_manager")) {

  // Create the extension_redirect_manager table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "extension_redirect_manager (

      redirect_rules TEXT NOT NULL
    )"
  )) {

    array_push(

      $errors,

      "failed to create " . DB_PREF . "extension_redirect_manager table"
    );
  }

  // Set a default value for the redirect rules
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "extension_redirect_manager (

      redirect_rules
    )
    VALUES (

      '{%blog_url%}/post/old-post-url = {%blog_url%}/post/new-post-url;'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert into " . DB_PREF . "extension_redirect_manager"
    );
  }
}

?>
