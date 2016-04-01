<?php

// Allow Kaku to access include files.
define("KAKU_ACCESS", true);

// Directory definitions.
define("KAKU_ROOT", dirname(__DIR__, 2));
define("KAKU_CORE", KAKU_ROOT . "/core");

// Database definitions.
define("DB_HOST", "localhost");
define("DB_NAME", "");
define("DB_USER", "");
define("DB_PASS", "");
define("DB_PREF", "kaku_");

?>
