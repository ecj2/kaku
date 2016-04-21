<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

// Errors will be stored in this array.
$errors = [];

if (!$Database->checkTableExistence("tags")) {

  // Create the tags table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "tags (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      title VARCHAR(99) NOT NULL,

      body TEXT NOT NULL
    )"
  )) {

    // Failed to create the tags table.
    $errors[] = "failed to create " . DB_PREF . "tags table";
  }

  // Insert blog_title into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'blog_title',

      'Kaku'
    )"
  )) {

    // Failed to insert blog_title.
    $errors[] = "failed to insert blog_title into " . DB_PREF . "tags";
  }

  // Insert blog_language into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'blog_language',

      'en'
    )"
  )) {

    // Failed to insert blog_language.
    $errors[] = "failed to insert blog_language into " . DB_PREF . "tags";
  }

  // Insert blog_description into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'blog_description',

      'Just your average blog.'
    )"
  )) {

    // Failed to insert blog_description.
    $errors[] = "failed to insert blog_description into " . DB_PREF . "tags";
  }

  // Insert template_name into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'template_name',

      'default'
    )"
  )) {

    // Failed to insert template_name.
    $errors[] = "failed to insert template_name into " . DB_PREF . "tags";
  }

  // Insert admin_template_name into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'admin_template_name',

      'default'
    )"
  )) {

    // Failed to insert admin_template_name.
    $errors[] = "failed to insert admin_template_name into " . DB_PREF . "tags";
  }

  // Insert posts_per_page into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'posts_per_page',

      '3'
    )"
  )) {

    // Failed to insert posts_per_page.
    $errors[] = "failed to insert posts_per_page into " . DB_PREF . "tags";
  }

  // Insert footer into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'footer',

      'Powered by <a href=\"https://github.com/ecj2/kaku\">Kaku</a>'
    )"
  )) {

    // Failed to insert footer.
    $errors[] = "failed to insert footer into " . DB_PREF . "tags";
  }

  // Insert date_format into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'date_format',

      'F jS, Y'
    )"
  )) {

    // Failed to insert date_format.
    $errors[] = "failed to insert date_format into " . DB_PREF . "tags";
  }

  // Insert recursion_depth into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'recursion_depth',

      '2'
    )"
  )) {

    // Failed to insert recursion_depth.
    $errors[] = "failed to insert recursion_depth into " . DB_PREF . "tags";
  }

  // Insert comment_disabled_text into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'comment_disabled_text',

      'Comments have been disabled on this post.'
    )"
  )) {

    // Failed to insert comment_disabled_text.
    $errors[] = "failed to insert comment_disabled_text into " . DB_PREF . "tags";
  }

  // Insert blog_url into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'blog_url',

      '" . $GLOBALS["Utility"]->getRootAddress() . "'
    )"
  )) {

    // Failed to insert blog_url.
    $errors[] = "failed to insert blog_url into " . DB_PREF . "tags";
  }

  // Insert template_directory into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'template_directory',

      '{%blog_url%}/templates'
    )"
  )) {

    // Failed to insert template_directory.
    $errors[] = "failed to insert template_directory into " . DB_PREF . "tags";
  }

  // Insert 404_url into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      '404_url',

      '{%blog_url%}/page/page+not+found'
    )"
  )) {

    // Failed to insert 404_url.
    $errors[] = "failed to insert 404_url into " . DB_PREF . "tags";
  }

  // Insert keyword_prefix into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'keyword_prefix',

      '#'
    )"
  )) {

    // Failed to insert keyword_prefix.
    $errors[] = "failed to insert keyword_prefix into " . DB_PREF . "tags";
  }

  // Insert navigation_items into the tags table.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'navigation_items',

      '
      <ul>
        <li><a href=\"{%blog_url%}\">Home</a></li>
        <li><a href=\"{%blog_url%}/page/search\">Search</a></li>
      </ul>
      '
    )"
  )) {

    // Failed to insert navigation_items.
    $errors[] = "failed to insert navigation_items into " . DB_PREF . "tags";
  }
}

if (!$Database->checkTableExistence("pages")) {

  // Create the pages table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "pages (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      url VARCHAR(256) NOT NULL,

      title VARCHAR(256) NOT NULL,

      body MEDIUMTEXT NOT NULL,

      keywords TEXT NOT NULL,

      description VARCHAR(160) NOT NULL,

      show_on_search BOOL NOT NULL
    )"
  )) {

    // Failed to create the pages table.
    $errors[] = "failed to create " . DB_PREF . "pages table";
  }

  // Insert search page.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "pages (

      url,

      title,

      body,

      keywords,

      description,

      show_on_search
    )
    VALUES (

      'search',

      'Search',

      '{%search%}',

      '',

      'Search for posts and pages.',

      '0'
    )"
  )) {

    // Failed to insert search page.
    $errors[] = "failed to insert search into " . DB_PREF . "pages";
  }

  // Insert page not found page.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "pages (

      url,

      title,

      body,

      keywords,

      description,

      show_on_search
    )
    VALUES (

      'page+not+found',

      'Page Not Found',

      'Sorry, the page you were looking for could not be found.',

      '',

      'Error code 404: page not found.',

      '0'
    )"
  )) {

    // Failed to insert page not found page.
    $errors[] = "failed to insert page+not+found into " . DB_PREF . "pages";
  }
}

if (!$Database->checkTableExistence("posts")) {

  // Create the posts table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "posts (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      url VARCHAR(99) NOT NULL,

      body MEDIUMTEXT NOT NULL,

      draft BOOL NOT NULL,

      epoch INT(11) NOT NULL,

      title VARCHAR(99) NOT NULL,

      keywords TEXT NOT NULL,

      author_id INT NOT NULL,

      description VARCHAR(160) NOT NULL,

      allow_comments BOOL NOT NULL
    )"
  )) {

    // Failed to create the posts table.
    $errors[] = "failed to create " . DB_PREF . "posts table";
  }

  // Create the body for the first post.
  $body = "Hi there. Welcome to Kaku. This is the very first post. ";
  $body .= "Head over to the <a href=\"{%blog_url%}/admin\">admin ";
  $body .= "panel</a> to get started. The username is admin";
  $body .= " and the password is password.";

  // Insert the first post.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "posts (

      url,

      body,

      draft,

      epoch,

      title,

      keywords,

      author_id,

      description,

      allow_comments
    )
    VALUES (

      'welcome+to+kaku',

      '{$body}',

      '0',

      UNIX_TIMESTAMP(),

      'Welcome to Kaku',

      'first, post',

      '1',

      'The very first post.',

      '0'
    )"
  )) {

    // Failed to insert the first post.
    $errors[] = "failed to insert welcome+to+kaku into " . DB_PREF . "posts";
  }
}

if (!$Database->checkTableExistence("users")) {

  // Create the users table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "users (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      username VARCHAR(99) NOT NULL,

      nickname VARCHAR(99) NOT NULL,

      email VARCHAR(99) NOT NULL,

      password VARCHAR(255) NOT NULL
    )"
  )) {

    // Failed to create the users table.
    $errors[] = "failed to create " . DB_PREF . "users table";
  }

  // Insert the admin user.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "users (

      username,

      nickname,

      email,

      password
    )
    VALUES (

      'admin',

      'Administrator',

      '',

      '" . password_hash("password", PASSWORD_BCRYPT) . "'
    )"
  )) {

    // Failed to insert the admin user.
    $errors[] = "failed to insert admin into " . DB_PREF . "users";
  }
}

if (!$Database->checkTableExistence("extensions")) {

  // Create the extensions table.
  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "extensions (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      title VARCHAR(99) NOT NULL,

      activate BOOL NOT NULL
    )"
  )) {

    // Failed to create the extensions table.
    $errors[] = "failed to create " . DB_PREF . "extensions table";
  }

  // Activate the search extension.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "extensions (

      title,

      activate
    )
    VALUES (

      'Search',

      '1'
    )"
  )) {

    // Failed to activate the search extension.
    $errors[] = "failed to insert Search into " . DB_PREF . "extensions";
  }

  // Activate the Disqus forum extension.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "extensions (

      title,

      activate
    )
    VALUES (

      'DisqusForum',

      '1'
    )"
  )) {

    // Failed to activate the Disqus forum extension.
    $errors[] = "failed to insert DisqusForum into " . DB_PREF . "extensions";
  }

  // Activate the pagination extension.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "extensions (

      title,

      activate
    )
    VALUES (

      'Pagination',

      '1'
    )"
  )) {

    // Failed to activate the pagination extension.
    $errors[] = "failed to insert Pagination into " . DB_PREF . "extensions";
  }

  // Activate the truncate extension.
  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "extensions (

      title,

      activate
    )
    VALUES (

      'Truncate',

      '1'
    )"
  )) {

    // Failed to activate the truncate extension.
    $errors[] = "failed to insert Truncate into " . DB_PREF . "extensions";
  }
}

// Get a list of the extension directories.
$directories = glob(KAKU_ROOT . "/extensions/*", GLOB_ONLYDIR);

if (count($directories) > 0) {

  foreach ($directories as $directory) {

    // Get the directory name without the path.
    $directory_name = str_replace(KAKU_ROOT . "/extensions/", "", $directory);

    $extension_full_path = "{$directory}/install.php";

    if (file_exists($extension_full_path)) {

      // Run the extension's install script.
      require $extension_full_path;
    }
  }
}

if (!empty($errors)) {

  if ($Database->checkTableExistence("tags")) {

    // Drop the tags table.
    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "tags"
    )) {

      // Failed to drop the tags table.
      $errors[] = "failed to drop " . DB_PREF . "tags table";
    }
  }

  if ($Database->checkTableExistence("pages")) {

    // Drop the pages table.
    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "pages"
    )) {

      // Failed to drop the pages table.
      $errors[] = "failed to drop " . DB_PREF . "pages table";
    }
  }

  if ($Database->checkTableExistence("posts")) {

    // Drop the posts table.
    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "posts"
    )) {

      // Failed to drop the posts table.
      $errors[] = "failed to drop " . DB_PREF . "posts table";
    }
  }

  if ($Database->checkTableExistence("users")) {

    // Drop the users table.
    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "users"
    )) {

      // Failed to drop the users table.
      $errors[] = "failed to drop " . DB_PREF . "users table";
    }
  }

  if ($Database->checkTableExistence("extensions")) {

    // Drop the extensions table.
    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "extensions"
    )) {

      // Failed to drop the extensions table.
      $errors[] = "failed to drop " . DB_PREF . "extensions table";
    }
  }

  if (ob_get_status()["level"] > 0) {

    // Clear the buffer.
    ob_end_clean();
  }

  echo "<b>Notice</b>: the following errors occurred during installation:<br><br>";

  foreach ($errors as $error) {

    // Display the errors.
    echo ucfirst($error) . ".<br>";
  }

  // Terminate.
  exit();
}

?>
