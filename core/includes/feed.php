<?php

if (!defined("KAKU_ACCESS")) {

  // Deny direct access to this file.
  exit();
}

// Select published posts.
$statement = "

  SELECT url, title, epoch, description
  FROM " . DB_PREF . "posts
  WHERE draft = '0'
  ORDER BY id DESC
";

$Query = $Database->getHandle()->query($statement);

if (!$Query) {

  // Something went wrong.
  $GLOBALS["Utility"]->displayError("failed to get posts for feed");
}

// Pretend to be an XML document.
header("Content-Type: application/xml; charset=utf-8");

// Start the XML document.
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
echo "<rss version=\"2.0\">\n";

// Describe the channel.
echo "<channel>\n";
echo "<title>{%blog_title%}</title>\n";
echo "<link>{%blog_url%}</link>\n";
echo "<description>{%blog_description%}</description>\n";
echo "<language>{%blog_language%}</language>\n";

if ($Query && $Query->rowCount() > 0) {

  while ($Post = $Query->fetch(PDO::FETCH_OBJ)) {

    // Generate an item for each post.

    echo "<item>\n";
    echo "<title>{$Post->title}</title>\n";
    echo "<link>{%blog_url%}/post/{$Post->url}</link>\n";

    echo "<description>\n";

    if (strlen(trim($Post->description)) == 0) {

      // This post lacks a description.
      echo "No description.";
    }
    else {

      echo trim($Post->description);
    }

    echo "</description>\n";
    echo "<pubDate>" . date("D, d M Y H:i:s O", $Post->epoch) ."</pubDate>\n";
    echo "</item>\n";
  }
}

// End the XML RSS document.
echo "</channel>\n";
echo "</rss>";

?>
