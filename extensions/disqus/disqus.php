<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

class DisqusForum extends Extension {

  public function __construct() {

    Extension::setName("Disqus Forum");

    $GLOBALS["Hook"]->addFilter("comment_source", $this, "getDisqusForum");
  }

  public function getDisqusForum() {

    $identifier = "";

    $statement = "

      SELECT identifier
      FROM " . DB_PREF . "content
      WHERE url = ?
      ORDER BY id DESC
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->prepare($statement);

    // Prevent SQL injections.
    $Query->bindParam(1, $_GET["path"]);

    $Query->execute();

    if (!$Query) {

      // Something went wrong.
      $GLOBALS["Utility"]->displayError("failed to get content identifier");
    }

    if ($Query->rowCount() > 0) {

      // Fetch the result as an object.
      $Result = $Query->fetch(PDO::FETCH_OBJ);

      $identifier = $Result->identifier;
    }

    $statement = "

      SELECT shortname
      FROM " . DB_PREF . "extension_disqus
      WHERE 1 = 1
      LIMIT 1
    ";

    $Query = $GLOBALS["Database"]->getHandle()->query($statement);

    if (!$Query || $Query->rowCount() == 0) {

      // Query failed or returned zero rows.
      return "Comments have not been configured.";
    }
    else {

      // Fetch the result as an object.
      $Result = $Query->fetch(PDO::FETCH_OBJ);

      // Get the forum name.
      $shortname = $Result->shortname;

      if ($shortname == "") {

        return "Comments have not been configured.";
      }

      $markup = "

        <div id=\"disqus_thread\"></div>

        <script>

          var disqus_shortname = \"{$shortname}\";

          var disqus_config = function () {

            this.page.identifier = \"{$identifier}\";
          };

          (

            function() {

              var d = document, s = d.createElement(\"script\");

              s.src = \"//\" + disqus_shortname + \".disqus.com/embed.js\";

              s.setAttribute(\"data-timestamp\", +new Date());
              (d.head || d.body).appendChild(s);
            }
          )();
        </script>

        <noscript>

          Please enable JavaScript to view the
          <a href=\"https://disqus.com/?ref_noscript\" rel=\"nofollow\">comments powered by Disqus.</a>
        </noscript>
      ";

      // Display the Disqus forum.
      return $markup;
    }
  }
}

?>
