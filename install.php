<?php

$errors = array();

if (!$Database->checkTableExistence("tags")) {

  $Utility = new Utility;

  $this_url = $Utility->getRootAddress();

  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "tags (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      title VARCHAR(99) NOT NULL,

      body TEXT NOT NULL
    )"
  )) {

    array_push(

      $errors,

      "failed to create " . DB_PREF . "tags table"
    );
  }

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

    array_push(

      $errors,

      "failed to insert blog_title into " . DB_PREF . "tags"
    );
  }

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

    array_push(

      $errors,

      "failed to insert blog_language into " . DB_PREF . "tags"
    );
  }

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

    array_push(

      $errors,

      "failed to insert blog_description into " . DB_PREF . "tags"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'theme_name',

      'default'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert theme_name into " . DB_PREF . "tags"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'admin_theme_name',

      'default'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert admin_theme_name into " . DB_PREF . "tags"
    );
  }

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

    array_push(

      $errors,

      "failed to insert posts_per_page into " . DB_PREF . "tags"
    );
  }

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

    array_push(

      $errors,

      "failed to insert footer into " . DB_PREF . "tags"
    );
  }

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

    array_push(

      $errors,

      "failed to insert date_format into " . DB_PREF . "tags"
    );
  }

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

    array_push(

      $errors,

      "failed to insert recursion_depth into " . DB_PREF . "tags"
    );
  }

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

    array_push(

      $errors,

      "failed to insert comment_disabled_text into " . DB_PREF . "tags"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'blog_url',

      '{$this_url}'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert blog_url into " . DB_PREF . "tags"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      'theme_directory',

      '{%blog_url%}/content/themes'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert theme_directory into " . DB_PREF . "tags"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body
    )
    VALUES (

      '404_url',

      '{$this_url}/page/page-not-found'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert 404_url into " . DB_PREF . "tags"
    );
  }

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

    array_push(

      $errors,

      "failed to insert keyword_prefix into " . DB_PREF . "tags"
    );
  }

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

    array_push(

      $errors,

      "failed to insert navigation_items into " . DB_PREF . "tags"
    );
  }
}

if (!$Database->checkTableExistence("pages")) {

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

    array_push(

      $errors,

      "failed to create " . DB_PREF . "pages table"
    );
  }

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

    array_push(

      $errors,

      "failed to insert search into " . DB_PREF . "pages"
    );
  }

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

      'page-not-found',

      'Page Not Found',

      'Sorry, the page you were looking for could not be found.',

      '',

      'Error code 404: page not found.',

      '0'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert page-not-found into " . DB_PREF . "pages"
    );
  }
}

if (!$Database->checkTableExistence("posts")) {

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

    array_push(

      $errors,

      "failed to create " . DB_PREF . "posts table"
    );
  }

  $body = "Hi there. Welcome to Kaku. This is the very first post. ";
  $body .= "Head over to the <a href=\"{%blog_url%}/admin\">admin ";
  $body .= "panel</a> to get started. The username is <b>admin</b>";
  $body .= " and the password is <b>password</b>.";

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

      'welcome-to-kaku',

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

    array_push(

      $errors,

      "failed to insert welcome-to-kaku into " . DB_PREF . "posts"
    );
  }
}

if (!$Database->checkTableExistence("users")) {

  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "users (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      username VARCHAR(99) NOT NULL,

      nickname VARCHAR(99) NOT NULL,

      email VARCHAR(99) NOT NULL,

      password CHAR(60) NOT NULL
    )"
  )) {

    array_push(

      $errors,

      "failed to create " . DB_PREF . "users table"
    );
  }

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

    array_push(

      $errors,

      "failed to insert admin into " . DB_PREF . "users"
    );
  }
}

if (!$Database->checkTableExistence("extensions")) {

  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "extensions (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      title VARCHAR(99) NOT NULL,

      activate BOOL NOT NULL
    )"
  )) {

    array_push(

      $errors,

      "failed to create " . DB_PREF . "extensions table"
    );
  }

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

    array_push(

      $errors,

      "failed to insert Search into " . DB_PREF . "extensions"
    );
  }

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

    array_push(

      $errors,

      "failed to insert DisqusForum into " . DB_PREF . "extensions"
    );
  }

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

    array_push(

      $errors,

      "failed to insert Pagination into " . DB_PREF . "extensions"
    );
  }

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

    array_push(

      $errors,

      "failed to insert Truncate into " . DB_PREF . "extensions"
    );
  }
}

$directories = glob("./content/extensions/*", GLOB_ONLYDIR);

if (count($directories) > 0) {

  foreach ($directories as $directory) {

    // Get directory name without path.
    $directory_name = str_replace("./content/extensions/", "", $directory);

    $extension_full_path = "{$directory}/install.php";

    if (file_exists($extension_full_path)) {

      // Include the extension's install script.
      require $extension_full_path;
    }
  }
}

if (!empty($errors)) {

  if ($Database->checkTableExistence("tags")) {

    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "tags"
    )) {

      array_push($errors, "failed to drop " . DB_PREF . "tags table");
    }
  }

  if ($Database->checkTableExistence("pages")) {

    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "pages"
    )) {

      array_push($errors, "failed to drop " . DB_PREF . "pages table");
    }
  }

  if ($Database->checkTableExistence("posts")) {

    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "posts"
    )) {

      array_push($errors, "failed to drop " . DB_PREF . "posts table");
    }
  }

  if ($Database->checkTableExistence("users")) {

    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "users"
    )) {

      array_push($errors, "failed to drop " . DB_PREF . "users table");
    }
  }

  if ($Database->checkTableExistence("extensions")) {

    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "extensions"
    )) {

      array_push($errors, "failed to drop " . DB_PREF . "extensions table");
    }
  }

  echo "The following errors occurred during installation:<br><br>";

  foreach ($errors as $error) {

    echo ucfirst($error) . ".<br>";
  }

  exit();
}

?>
