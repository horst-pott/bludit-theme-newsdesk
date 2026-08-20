<?php defined('BLUDIT') or die('Bludit CMS.');

// Zentrale Weiche: je nachdem, welche Art von Seite aufgerufen wird,
// wird die passende Datei aus dem php/-Ordner geladen.
if ($WHERE_AM_I == 'page') {
    include(THEME_DIR_PHP.'page.php');
} elseif ($WHERE_AM_I == 'category') {
    include(THEME_DIR_PHP.'category.php');
} else {
    // home, tag und alle anderen Fälle: Startseiten-Layout mit Nachrichtenfeed
    include(THEME_DIR_PHP.'home.php');
}
