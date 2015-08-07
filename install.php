<?php

$errors = array();

if (!$Database->checkTableExistence("tags")) {

  $Utility = new Utility;

  $this_url = $Utility->getRootAddress();

  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "tags (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      title VARCHAR(99) NOT NULL,

      body TEXT NOT NULL,

      evaluate BOOL NOT NULL
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

      body,

      evaluate
    )
    VALUES (

      'blog_title',

      'Kaku',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'blog_language',

      'en',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'blog_description',

      'Just your average blog.',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'theme_name',

      'default',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'admin_theme_name',

      'default',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'posts_per_page',

      '3',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'footer',

      'Powered by <a href=\"https://github.com/ecj2/kaku\">Kaku</a>',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'date_format',

      'F jS, Y',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'recursion_depth',

      '2',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'lure_text',

      'Read more...',

      '0'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert lure_text into " . DB_PREF . "tags"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body,

      evaluate
    )
    VALUES (

      'comment_disabled_text',

      'Comments have been disabled on this post.',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      'next_page_text',

      'Older posts',

      '0'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert next_page_text into " . DB_PREF . "tags"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body,

      evaluate
    )
    VALUES (

      'previous_page_text',

      'Newer posts',

      '0'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert previous_page_text into " . DB_PREF . "tags"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body,

      evaluate
    )
    VALUES (

      'disqus_forum_name',

      '',

      '0'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert disqus_forum_name into " . DB_PREF . "tags"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "tags (

      title,

      body,

      evaluate
    )
    VALUES (

      'blog_url',

      '{$this_url}',

      '0'
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

      body,

      evaluate
    )
    VALUES (

      '404_url',

      '{$this_url}/page/page-not-found',

      '0'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert 404_url into " . DB_PREF . "tags"
    );
  }
}

if (!$Database->checkTableExistence("links")) {

  if (!$Database->performQuery(

    "CREATE TABLE " . DB_PREF . "links (

      id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,

      uri VARCHAR(256) NOT NULL,

      title VARCHAR(99) NOT NULL,

      target VARCHAR(6) NOT NULL
    )"
  )) {

    array_push(

      $errors,

      "failed to create " . DB_PREF . "links table"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "links (

      uri,

      title,

      target
    )
    VALUES (

      '{%blog_url%}',

      'Home',

      '_self'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert home into " . DB_PREF . "links"
    );
  }

  if (!$Database->performQuery(

    "INSERT INTO " . DB_PREF . "links (

      uri,

      title,

      target
    )
    VALUES (

      '{%blog_url%}/page/search',

      'Search',

      '_self'
    )"
  )) {

    array_push(

      $errors,

      "failed to insert search into " . DB_PREF . "links"
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

      'The very first.',

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

if (!empty($errors)) {

  if ($Database->checkTableExistence("tags")) {

    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "tags"
    )) {

      array_push($errors, "failed to drop " . DB_PREF . "tags table");
    }
  }

  if ($Database->checkTableExistence("links")) {

    if (!$Database->performQuery(

      "DROP TABLE " . DB_PREF . "links"
    )) {

      array_push($errors, "failed to drop " . DB_PREF . "links table");
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

  echo "The following errors occurred during installation:<br><br>";

  foreach ($errors as $error) {

    echo ucfirst($error) . ".<br>";
  }

  exit();
}

?>
