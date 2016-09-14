<?php

// Deny direct access to this file.
if (!defined("KAKU_ACCESS")) exit();

$markup = "

  <div id=\"disqus_thread\"></div>

  <script>

    var disqus_shortname = \"{%disqus_forum_name%}\";

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

?>
