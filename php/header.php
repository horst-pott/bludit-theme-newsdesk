<?php defined('BLUDIT') or die('Bludit CMS.');

// Hilfsfunktion: kürzt einen Text auf eine feste Zeichenzahl für die Artikel-Vorschau
if (!function_exists('theme_excerpt')) {
    function theme_excerpt($text, $limit = 200) {
        $text = trim(strip_tags($text));
        if (mb_strlen($text) <= $limit) {
            return $text;
        }
        return mb_substr($text, 0, $limit) . '…';
    }
}

// Hilfsfunktion: Pfad zur Suchstatistik-Datei (liegt im Theme-Ordner selbst)
if (!function_exists('theme_search_log_file')) {
    function theme_search_log_file() {
        return dirname(dirname(__DIR__)) . '/search-log.json';
    }
}

// Hilfsfunktion: liest die häufigsten Suchbegriffe (Text -> Anzahl)
if (!function_exists('theme_top_search_terms')) {
    function theme_top_search_terms($limit = 8) {
        $file = theme_search_log_file();
        if (!file_exists($file)) {
            return array();
        }
        $log = json_decode(file_get_contents($file), true);
        if (!is_array($log)) {
            return array();
        }
        arsort($log);
        return array_slice($log, 0, $limit, true);
    }
}

// Hilfsfunktion: zählt einen Suchbegriff hoch und speichert (max. 30 Begriffe)
if (!function_exists('theme_log_search_term')) {
    function theme_log_search_term($term) {
        $term = trim(mb_strtolower($term));
        if ($term === '') { return; }
        $file = theme_search_log_file();
        $log = array();
        if (file_exists($file)) {
            $log = json_decode(file_get_contents($file), true);
            if (!is_array($log)) { $log = array(); }
        }
        $log[$term] = (isset($log[$term]) ? $log[$term] : 0) + 1;
        arsort($log);
        $log = array_slice($log, 0, 30, true);
        file_put_contents($file, json_encode($log), LOCK_EX);
    }
}

$themeTopSearchTerms = theme_top_search_terms(8);
$themeCurrentSearchTerm = isset($_GET['suche']) ? trim($_GET['suche']) : '';

// Abteilungen für das Header-Menü (admin-gepflegt über das Plugin "Abteilungen")
$themeDepartments = function_exists('abteilungen_get_list') ? abteilungen_get_list() : array();

// ------------------------------------------------------------------
// Archiv: Artikel gelten ab dieser Anzahl Tage als "alt" und werden
// aus Startseite/Rubrik ausgeblendet, tauchen dann nur noch im
// Archiv (Seite mit dem Slug "archiv") auf.
// ------------------------------------------------------------------
if (!function_exists('theme_archive_threshold_days')) {
    function theme_archive_threshold_days() {
        return 30;
    }
}
if (!function_exists('theme_is_archived_page')) {
    function theme_is_archived_page($page) {
        if (!$page) {
            return false;
        }
        $timestamp = strtotime($page->dateRaw());
        if ($timestamp === false) {
            return false;
        }
        $thresholdSeconds = theme_archive_threshold_days() * 86400;
        return (time() - $timestamp) > $thresholdSeconds;
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $site->title(); ?><?php if ($WHERE_AM_I=='page' || $WHERE_AM_I=='category') { echo ' – '.strip_tags($site->title()); } ?></title>
<?php echo Theme::css('css/style.css?v=' . filemtime(__DIR__ . '/../../css/style.css')); ?>
<?php Theme::plugins('siteHead'); ?>
</head>
<body>
<a href="#main-content" class="skip-link">Zum Inhalt springen</a>
<?php Theme::plugins('siteBodyBegin'); ?>
<header class="site-header">
    <div class="wrapper site-header-top">
        <a href="<?php echo $site->url(); ?>" class="site-logo">
            <?php if ($site->logo()): ?>
            <img src="<?php echo $site->logo(); ?>" alt="<?php echo htmlspecialchars($site->title()); ?>">
            <?php endif; ?>
            <span>
                <span class="site-title" style="display:block;"><?php echo $site->title(); ?></span>
                <span class="site-slogan"><?php echo $site->slogan(); ?></span>
            </span>
        </a>
        <form action="<?php echo $site->url(); ?>" method="get" class="site-search" id="siteSearchForm" autocomplete="off" role="search" aria-label="Website durchsuchen">
            <label for="siteSearchInput" class="visually-hidden">Suchbegriff</label>
            <input type="text" name="suche" id="siteSearchInput" placeholder="Suchen …" value="<?php echo htmlspecialchars($themeCurrentSearchTerm); ?>">
            <button type="submit" aria-label="Suchen">&#128269;</button>
            <?php if (!empty($themeTopSearchTerms)): ?>
            <div class="search-suggestions" id="searchSuggestions">
                <div class="search-suggestions-label">Häufig gesucht</div>
                <?php foreach ($themeTopSearchTerms as $term => $count): ?>
                <div class="suggestion-item" data-term="<?php echo htmlspecialchars($term); ?>"><?php echo htmlspecialchars($term); ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </form>
    </div>
    <!-- Abteilungen-Menü: Hover/Klick zeigt Kurzbeschreibung, "Mehr erfahren" öffnet die Unterseite -->
    <nav class="category-nav" aria-label="Abteilungen">
        <div class="wrapper">
            <ul>
                <?php foreach ($themeDepartments as $deptNav => $dept):
                    $deptName = isset($dept['name']) ? $dept['name'] : '';
                    $deptDesc = isset($dept['description']) ? $dept['description'] : '';
                    $deptPageKey = isset($dept['pageKey']) ? $dept['pageKey'] : '';
                    $deptPage = $deptPageKey ? buildPage($deptPageKey) : null;
                    $deptUrl = $deptPage ? $deptPage->permalink() : '#';
                    $deptDropdownId = 'dept-dropdown-' . $deptNav;
                ?>
                <li class="cat-item">
                    <a class="cat-toggle" href="<?php echo $deptUrl; ?>" aria-haspopup="true" aria-expanded="false" aria-controls="<?php echo $deptDropdownId; ?>"><?php echo htmlspecialchars($deptName); ?></a>
                    <div class="cat-dropdown" id="<?php echo $deptDropdownId; ?>">
                        <?php if ($deptDesc !== ''): ?>
                        <p class="dept-desc-text"><?php echo nl2br(htmlspecialchars($deptDesc)); ?></p>
                        <?php else: ?>
                        <span class="empty">Noch keine Kurzbeschreibung hinterlegt</span>
                        <?php endif; ?>
                        <a href="<?php echo $deptUrl; ?>" class="dept-desc-more">Mehr erfahren &rarr;</a>
                    </div>
                </li>
                <?php endforeach; ?>
                <?php if (empty($themeDepartments)): ?>
                <li class="cat-item"><span class="empty" style="padding:12px 18px; display:block; color:#fff;">Noch keine Abteilungen angelegt</span></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>
