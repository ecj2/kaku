<?php

require "includes/configuration.php";

require "includes/classes/utility.php";
require "includes/classes/database.php";

require "includes/classes/hook.php";
require "includes/classes/page.php";
require "includes/classes/post.php";
require "includes/classes/theme.php";
require "includes/classes/output.php";
require "includes/classes/comment.php";

global $Hook;

$Hook = new Hook;
$Page = new Page;
$Post = new Post;
$Theme = new Theme;
$Output = new Output;
$Comment = new Comment;
$Utility = new Utility;
$Database = new Database;

$Database->connect();

$Page->setDatabaseHandle($Database->getHandle());
$Post->setDatabaseHandle($Database->getHandle());
$Theme->setDatabaseHandle($Database->getHandle());
$Output->setDatabaseHandle($Database->getHandle());
$Comment->setDatabaseHandle($Database->getHandle());

$path = [];

if (isset($_GET["path"])) {

  // Break the path components up into an array.
  $path = explode("/", $_GET["path"]);

  foreach ($path as $part) {

    static $counter = 0;

    if (empty($part)) {

      // Remove empty array elements.
      unset($path[$counter]);
    }

    ++$counter;
  }
}

$Output->startBuffer();

if (file_exists("install.php")) {

  // Allow access to install file.
  define("KAKU_INCLUDE", true);

  require "install.php";
}

$Output->loadExtensions();

if (in_array("feed", $path)) {

  // Allow access to feed file.
  define("KAKU_INCLUDE", true);

  // Viewing the feed.

  require "feed.php";
}
else if (in_array("post", $path)) {

  // Viewing a post.

  // Get the post URL from the path.
  $_GET["post_url"] = $path[array_search("post", $path) + 1];

  $Hook->addAction(

    "post_file_contents",

    $Theme,

    "getFileContents",

    "post"
  );

  echo $Hook->doAction("post_file_contents");

  $Hook->addAction("post_body", $Post, "getBody");

  $Hook->addAction(

    "post_url",

    $Post,

    "getUniformResourceLocator"
  );

  $Output->addTagReplacement(

    "post_url",

    $Hook->doAction("post_url")
  );

  $Output->addTagReplacement(

    "post_body",

    $Hook->doAction("post_body")
  );

  $Hook->addAction("post_keywords", $Post, "getKeywords");

  $Output->addTagReplacement(

    "post_keywords",

    $Hook->doAction("post_keywords")
  );

  $Hook->addAction("post_title", $Post, "getTitle");

  $Output->addTagReplacement(

    "post_title",

    $Hook->doAction("post_title")
  );

  $Hook->addAction("post_author", $Post, "getAuthor");

  $Output->addTagReplacement(

    "post_author",

    $Hook->doAction("post_author")
  );

  $Hook->addAction("post_description", $Post, "getDescription");

  $Output->addTagReplacement(

    "post_description",

    $Hook->doAction("post_description")
  );

  $Hook->addAction("post_absolute_epoch", $Post, "getAbsoluteEpoch");

  $Output->addTagReplacement(

    "post_absolute_epoch",

    $Hook->doAction("post_absolute_epoch")
  );

  $Hook->addAction("post_relative_epoch", $Post, "getRelativeEpoch");

  $Output->addTagReplacement(

    "post_relative_epoch",

    $Hook->doAction("post_relative_epoch")
  );

  $Hook->addAction("post_date_time_epoch", $Post, "getDateTimeEpoch");

  $Output->addTagReplacement(

    "post_date_time_epoch",

    $Hook->doAction("post_date_time_epoch")
  );

  $Hook->addAction(

    "comment_block_file_contents",

    $Theme,

    "getFileContents",

    "comment_block"
  );

  $Hook->addAction(

    "comments",

    $Comment,

    "getSource",

    $Hook->doAction("comment_block_file_contents")
  );

  $Output->addTagReplacement(

    "comments",

    $Hook->doAction("comments")
  );
}
else if (in_array("page", $path)) {

  // Viewing a page.

  // Get the page URL from the path.
  $_GET["page_url"] = $path[array_search("page", $path) + 1];

  $Hook->addAction(

    "page_file_contents",

    $Theme,

    "getFileContents",

    "page"
  );

  echo $Hook->doAction("page_file_contents");

  $Hook->addAction("page_body", $Page, "getBody");

  $Output->addTagReplacement(

    "page_body",

    $Hook->doAction("page_body")
  );

  $Hook->addAction("page_title", $Page, "getTitle");

  $Output->addTagReplacement(

    "page_title",

    $Hook->doAction("page_title")
  );

  $Hook->addAction("page_description", $Page, "getDescription");

  $Output->addTagReplacement(

    "page_description",

    $Hook->doAction("page_description")
  );
}
else if (in_array("range", $path)) {

  // Viewing posts by range.

  // Get the page_number from the path.
  $_GET["page_number"] = $path[array_search("range", $path) + 1];

  $Hook->addAction(

    "range_file_contents",

    $Theme,

    "getFileContents",

    "range"
  );

  echo $Hook->doAction("range_file_contents");

  $Hook->addAction(

    "post_block_file_contents",

    $Theme,

    "getFileContents",

    "post_block"
  );

  $Hook->addAction(

    "posts_range",

    $Post,

    "getRange",

    $Hook->doAction("post_block_file_contents")
  );

  $Output->addTagReplacement(

    "posts_range",

    $Hook->doAction("posts_range")
  );

  $Hook->addAction(

    "post_title",

    $Post,

    "getTitle"
  );

  $count = 1;

  foreach ($Hook->doAction("post_title") as $title) {

    $Output->addTagReplacement(

      "post_title_{$count}",

      $title
    );

    ++$count;
  }

  $Hook->addAction(

    "post_body",

    $Post,

    "getBody"
  );

  $count = 1;

  foreach ($Hook->doAction("post_body") as $body) {

    $Output->addTagReplacement(

      "post_body_{$count}",

      $body
    );

    ++$count;
  }

  $Hook->addAction(

    "post_url",

    $Post,

    "getUniformResourceLocator"
  );

  $count = 1;

  foreach ($Hook->doAction("post_url") as $url) {

    $Output->addTagReplacement(

      "post_url_{$count}",

      $url
    );

    ++$count;
  }

  $Hook->addAction(

    "post_keywords",

    $Post,

    "getKeywords"
  );

  $count = 1;

  foreach ($Hook->doAction("post_keywords") as $keywords) {

    $Output->addTagReplacement(

      "post_keywords_{$count}",

      $keywords
    );

    ++$count;
  }

  $Hook->addAction(

    "post_relative_epoch",

    $Post,

    "getRelativeEpoch"
  );

  $count = 1;

  foreach ($Hook->doAction("post_relative_epoch") as $relative_epoch) {

    $Output->addTagReplacement(

      "post_relative_epoch_{$count}",

      $relative_epoch
    );

    ++$count;
  }

  $Hook->addAction(

    "post_absolute_epoch",

    $Post,

    "getAbsoluteEpoch"
  );

  $count = 1;

  foreach ($Hook->doAction("post_absolute_epoch") as $absolute_epoch) {

    $Output->addTagReplacement(

      "post_absolute_epoch_{$count}",

      $absolute_epoch
    );

    ++$count;
  }

  $Hook->addAction(

    "post_date_time_epoch",

    $Post,

    "getDateTimeEpoch"
  );

  $count = 1;

  foreach ($Hook->doAction("post_date_time_epoch") as $date_time_epoch) {

    $Output->addTagReplacement(

      "post_date_time_epoch_{$count}",

      $date_time_epoch
    );

    ++$count;
  }

  $Hook->addAction(

    "post_author",

    $Post,

    "getAuthor"
  );

  $count = 1;

  foreach ($Hook->doAction("post_author") as $author) {

    $Output->addTagReplacement(

      "post_author_{$count}",

      $author
    );

    ++$count;
  }
}
else {

  if (!empty($path)) {

    // Redirect to 404.
    header("Location: " . $Utility->getRootAddress() . "/error.php?code=404");
  }

  // Viewing latest posts.

  $Hook->addAction(

    "latest_file_contents",

    $Theme,

    "getFileContents",

    "latest"
  );

  echo $Hook->doAction("latest_file_contents");

  $Hook->addAction(

    "post_block_file_contents",

    $Theme,

    "getFileContents",

    "post_block"
  );

  $Hook->addAction(

    "latest_posts",

    $Post,

    "getLatest",

    $Hook->doAction("post_block_file_contents")
  );

  $Output->addTagReplacement(

    "latest_posts",

    $Hook->doAction("latest_posts")
  );

  $Hook->addAction(

    "post_title",

    $Post,

    "getTitle"
  );

  $count = 1;

  foreach ($Hook->doAction("post_title") as $title) {

    $Output->addTagReplacement(

      "post_title_{$count}",

      $title
    );

    ++$count;
  }

  $Hook->addAction(

    "post_body",

    $Post,

    "getBody"
  );

  $count = 1;

  foreach ($Hook->doAction("post_body") as $body) {

    $Output->addTagReplacement(

      "post_body_{$count}",

      $body
    );

    ++$count;
  }

  $Hook->addAction(

    "post_url",

    $Post,

    "getUniformResourceLocator"
  );

  $count = 1;

  foreach ($Hook->doAction("post_url") as $url) {

    $Output->addTagReplacement(

      "post_url_{$count}",

      $url
    );

    ++$count;
  }

  $Hook->addAction(

    "post_keywords",

    $Post,

    "getKeywords"
  );

  $count = 1;

  foreach ($Hook->doAction("post_keywords") as $keywords) {

    $Output->addTagReplacement(

      "post_keywords_{$count}",

      $keywords
    );

    ++$count;
  }

  $Hook->addAction(

    "post_relative_epoch",

    $Post,

    "getRelativeEpoch"
  );

  $count = 1;

  foreach ($Hook->doAction("post_relative_epoch") as $relative_epoch) {

    $Output->addTagReplacement(

      "post_relative_epoch_{$count}",

      $relative_epoch
    );

    ++$count;
  }

  $Hook->addAction(

    "post_absolute_epoch",

    $Post,

    "getAbsoluteEpoch"
  );

  $count = 1;

  foreach ($Hook->doAction("post_absolute_epoch") as $absolute_epoch) {

    $Output->addTagReplacement(

      "post_absolute_epoch_{$count}",

      $absolute_epoch
    );

    ++$count;
  }

  $Hook->addAction(

    "post_date_time_epoch",

    $Post,

    "getDateTimeEpoch"
  );

  $count = 1;

  foreach ($Hook->doAction("post_date_time_epoch") as $date_time_epoch) {

    $Output->addTagReplacement(

      "post_date_time_epoch_{$count}",

      $date_time_epoch
    );

    ++$count;
  }

  $Hook->addAction(

    "post_author",

    $Post,

    "getAuthor"
  );

  $count = 1;

  foreach ($Hook->doAction("post_author") as $author) {

    $Output->addTagReplacement(

      "post_author_{$count}",

      $author
    );

    ++$count;
  }
}

$Hook->addAction(

  "head_content",

  ""
);

// Remove head_content tag if no extension uses it.
$Output->addTagReplacement(

  "head_content",

  $Hook->doAction("head_content")
);

$Hook->addAction(

  "body_content",

  ""
);

// Remove body_content tag if no extension uses it.
$Output->addTagReplacement(

  "body_content",

  $Hook->doAction("body_content")
);

$Output->replaceTags();

$Output->flushBuffer();

$Database->disconnect();

?>
