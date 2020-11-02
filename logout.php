<?php

require("includes/database.php"); // importeer database
session_destroy(); // Verwijder sessie cookie, zodat gebruiker weer moet inloggen

header("Location: index.php"); // Naar de homepagina sturen.

?>