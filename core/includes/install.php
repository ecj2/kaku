<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

function performQuery($statement) {

  // Perform the given query statement.
  $Query = $GLOBALS["Database"]->getHandle()->query($statement);

  if (!$Query) {

    // The query failed.
    return false;
  }

  // The query was successful.
  return true;
}

function checkTableExistence($table_name) {

  // Determine if the given table exists.
  $Query = $GLOBALS["Database"]->getHandle()->query("SHOW TABLES LIKE '" . DB_PREF . "{$table_name}'");

  if (!$Query || $Query->rowCount() == 0) {

    // The table does not exist.
    return false;
  }

  // The table exists.
  return true;
}

$errors = [];

if (!checkTableExistence("tags")) {

  if (!performQuery("

    CREATE TABLE " . DB_PREF . "tags (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      title VARCHAR(99) NOT NULL,

      body TEXT NOT NULL
    )
  ")) {

    $errors[] = "failed to create \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'back_theme_name',

      'default'
    )
  ")) {

    $errors[] = "failed to insert \"back_theme_name\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'front_theme_name',

      'kantan'
    )
  ")) {

    $errors[] = "failed to insert \"front_theme_name\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'blog_url',

      '" . str_replace(["http://", "https://"], "{%protocol%}", $GLOBALS["Utility"]->getRootAddress()) . "'
    )
  ")) {

    $errors[] = "failed to insert \"blog_url\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'blog_title',

      'Kaku'
    )
  ")) {

    $errors[] = "failed to insert \"blog_title\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'blog_language',

      'en'
    )
  ")) {

    $errors[] = "failed to insert \"blog_language\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'navigation_items',

      '" . str_replace("  ", "", trim("

        <ul>
          <li><a href=\"{%blog_url%}\">Home</a></li>
          <li><a href=\"{%blog_url%}/search\">Search</a></li>
        </ul>
      ")) . "'
    )
  ")) {

    $errors[] = "failed to insert \"navigation_items\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'footer',

      'Powered by <a href=\"https://github.com/ecj2/kaku\">Kaku</a>'
    )
  ")) {

    $errors[] = "failed to insert \"footer\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'posts_per_page',

      '3'
    )
  ")) {

    $errors[] = "failed to insert \"posts_per_page\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      '404_url',

      '{%blog_url%}/404'
    )
  ")) {

    $errors[] = "failed to insert \"404_url\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'search_url',

      '{%blog_url%}/search'
    )
  ")) {

    $errors[] = "failed to insert \"search_url\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'date_format',

      'F jS, Y'
    )
  ")) {

    $errors[] = "failed to insert \"date_format\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'blog_description',

      'A Kaku blog.'
    )
  ")) {

    $errors[] = "failed to insert \"blog_description\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'keyword_prefix',

      '#'
    )
  ")) {

    $errors[] = "failed to insert \"keyword_prefix\" into \"" . DB_PREF . "tags\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'comment_disabled_text',

      'Comments have been disabled on this post.'
    )
  ")) {

    $errors[] = "failed to insert \"comment_disabled_text\" into \"" . DB_PREF . "tags\" table";
  }
}

if (!checkTableExistence("users")) {

  if (!performQuery("

    CREATE TABLE " . DB_PREF . "users (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      username VARCHAR(99) NOT NULL,

      nickname VARCHAR(99) NOT NULL,

      email VARCHAR(254) NOT NULL,

      password VARCHAR(255) NOT NULL,

      reset_password TINYINT(1) NOT NULL,

      reset_hash VARCHAR(255)
    )
  ")) {

    $errors[] = "failed to create \"" . DB_PREF . "users\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "users (

      username,

      nickname,

      email,

      password,

      reset_password
    )
    VALUES (

      'admin',

      'Admin',

      'admin@" . $_SERVER["HTTP_HOST"] . "',

      '" . password_hash("password", PASSWORD_BCRYPT) . "',

      '0'
    )
  ")) {

    $errors[] = "failed to insert \"admin\" into \"" . DB_PREF . "users\" table";
  }
}

if (!checkTableExistence("content")) {

  if (!performQuery("

    CREATE TABLE " . DB_PREF . "content (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      url VARCHAR(99) NOT NULL,

      title VARCHAR(99) NOT NULL,

      body MEDIUMTEXT NOT NULL,

      description VARCHAR(160),

      keywords TEXT,

      epoch_created INT(11) NOT NULL,

      identifier CHAR(32) NOT NULL,

      author_id INT NOT NULL,

      type TINYINT(1) NOT NULL,

      draft TINYINT(1) NOT NULL,

      show_on_search TINYINT(1) NOT NULL,

      allow_comments TINYINT(1) NOT NULL
    )
  ")) {

    $errors[] = "failed to create \"" . DB_PREF . "content\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "content (

      url,

      title,

      body,

      description,

      keywords,

      epoch_created,

      identifier,

      author_id,

      type,

      draft,

      show_on_search,

      allow_comments
    )
    VALUES (

      'welcome-to-kaku',

      'Welcome to Kaku',

      '" . str_replace(["  ", "\n"], ["", " "], trim("

        Hi there. Welcome to Kaku. This is the very first post.
        Head over to the <a href=\"{%blog_url%}/admin\">admin panel</a>
        to get started. The username is <strong>admin</strong> and the
        password is <strong>password</strong>.
      ")) . "',

      'The very first post.',

      'first, post',

      UNIX_TIMESTAMP(),

      '" . md5(microtime()) . "',

      1,

      0,

      0,

      1,

      0
    )
  ")) {

    $errors[] = "failed to insert \"welcome-to-kaku\" into \"" . DB_PREF . "content\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "content (

      url,

      title,

      body,

      description,

      epoch_created,

      identifier,

      author_id,

      type,

      draft,

      show_on_search,

      allow_comments
    )
    VALUES (

      'search',

      'Search',

      '{%search%}',

      'Search for posts and pages.',

      UNIX_TIMESTAMP(),

      '" . md5(microtime()) . "',

      1,

      1,

      0,

      0,

      0
    )
  ")) {

    $errors[] = "failed to insert \"search\" into \"" . DB_PREF . "content\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "content (

      url,

      title,

      body,

      description,

      epoch_created,

      identifier,

      author_id,

      type,

      draft,

      show_on_search,

      allow_comments
    )
    VALUES (

      '404',

      'Content Not Found',

      'Sorry, the content you were looking for could not be found.',

      'Error code 404: page not found.',

      UNIX_TIMESTAMP(),

      '" . md5(microtime()) . "',

      1,

      1,

      0,

      0,

      0
    )
  ")) {

    $errors[] = "failed to insert \"404\" into \"" . DB_PREF . "content\" table";
  }
}

if (!checkTableExistence("extensions")) {

  if (!performQuery("

    CREATE TABLE " . DB_PREF . "extensions (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      hash CHAR(32) NOT NULL,

      status TINYINT(1) NOT NULL
    )
  ")) {

    $errors[] = "failed to create \"" . DB_PREF . "extensions\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "extensions (

      hash,

      status
    )
    VALUES (

      '" . md5("Search") . "',

      1
    )
  ")) {

    $errors[] = "failed to insert \"Search\" into \"" . DB_PREF . "extensions\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "extensions (

      hash,

      status
    )
    VALUES (

      '" . md5("DisqusForum") . "',

      1
    )
  ")) {

    $errors[] = "failed to insert \"DisqusForum\" into \"" . DB_PREF . "extensions\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "extensions (

      hash,

      status
    )
    VALUES (

      '" . md5("Pagination") . "',

      1
    )
  ")) {

    $errors[] = "failed to insert \"Pagination\" into \"" . DB_PREF . "extensions\" table";
  }

  if (!performQuery("

    INSERT INTO " . DB_PREF . "extensions (

      hash,

      status
    )
    VALUES (

      '" . md5("Truncate") . "',

      1
    )
  ")) {

    $errors[] = "failed to insert \"Truncate\" into \"" . DB_PREF . "extensions\" table";
  }
}

// Get a list of extension directories.
$directories = glob(KAKU_ROOT . "/extensions/*", GLOB_ONLYDIR);

if (count($directories) > 0) {

  foreach ($directories as $directory) {

    // Get the directory name without the path.
    $directory_name = str_replace(KAKU_ROOT . "/extensions/", "", $directory);

    $extension_full_path = $directory . "/install.php";

    if (file_exists($extension_full_path)) {

      // Run the extension's installation script.
      require $extension_full_path;
    }
  }
}

if (!empty($errors)) {

  if (ob_get_status()["level"] > 0) {

    // Clear the output buffer.
    ob_end_clean();
  }

  if (checkTableExistence("tags")) {

    if (!performQuery("DROP TABLE " . DB_PREF . "tags")) {

      $errors[] = "failed to drop \"" . DB_PREF . "tags\" table";
    }
  }

  if (checkTableExistence("users")) {

    if (!performQuery("DROP TABLE " . DB_PREF . "users")) {

      $errors[] = "failed to drop \"" . DB_PREF . "users\" table";
    }
  }

  if (checkTableExistence("content")) {

    if (!performQuery("DROP TABLE " . DB_PREF . "content")) {

      $errors[] = "failed to drop \"" . DB_PREF . "content\" table";
    }
  }

  if (checkTableExistence("extensions")) {

    if (!performQuery("DROP TABLE " . DB_PREF . "extensions")) {

      $errors[] = "failed to drop \"" . DB_PREF . "extensions\" table";
    }
  }

  foreach ($errors as $error) {

    // Display errors that occurred during installation.
    echo "<b>Error</b>: {$error}.<br>";
  }

  exit();
}

?>
