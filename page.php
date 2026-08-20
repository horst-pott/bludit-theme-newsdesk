<?php defined('BLUDIT') or die('Bludit CMS.');
include(THEME_DIR_PHP.'parts/header.php');

// ------------------------------------------------------------------
// Sonderfall: die Seite mit dem Slug "archiv" zeigt keinen normalen
// Seiteninhalt, sondern automatisch alle Artikel, die älter als
// theme_archive_threshold_days() sind, gruppiert nach Rubrik.
// ------------------------------------------------------------------
$themeIsArchivePage = ($page->slug() === 'archiv');

if ($themeIsArchivePage) {
    global $pages;
    $themeAllKeys = $pages->getList(1, -1, true); // alle veröffentlichten Seiten
    $themeArchiveByCategory = array();
    foreach ($themeAllKeys as $themeKey) {
        $themeP = buildPage($themeKey);
        if (!$themeP) { continue; }
        $themeCatKey = $themeP->category();
        if (!$themeCatKey) { continue; } // statische Seiten (ohne Rubrik) nie ins Archiv
        if (!theme_is_archived_page($themeP)) { continue; } // nur Artikel älter als Schwelle
        if (!isset($themeArchiveByCategory[$themeCatKey])) {
            $themeCatObj = new Category($themeCatKey);
            $themeArchiveByCategory[$themeCatKey] = array('name' => $themeCatObj->name(), 'items' => array());
        }
        $themeArchiveByCategory[$themeCatKey]['items'][] = $themeP;
    }
    foreach ($themeArchiveByCategory as $themeCatKey => $themeData) {
        usort($themeArchiveByCategory[$themeCatKey]['items'], function ($a, $b) {
            return strcmp($b->dateRaw(), $a->dateRaw());
        });
    }
    uasort($themeArchiveByCategory, function ($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });
}
?>

<div class="wrapper main-layout">

    <?php include(THEME_DIR_PHP.'parts/sidebar-left.php'); ?>

    <main id="main-content" tabindex="-1">
        <?php if ($themeIsArchivePage): ?>
        <article class="article-view archive-view">
            <span class="badge">Archiv</span>
            <h1><?php echo $page->title(); ?></h1>
            <p class="archive-intro">Hier findest du alle Artikel, die &auml;lter als <?php echo theme_archive_threshold_days(); ?> Tage sind, sortiert nach Rubrik.</p>

            <?php if (empty($themeArchiveByCategory)): ?>
                <p>Noch keine archivierten Artikel vorhanden.</p>
            <?php else: ?>
                <?php foreach ($themeArchiveByCategory as $themeCatKey => $themeData): ?>
                <details class="archive-group" id="archiv-<?php echo htmlspecialchars($themeCatKey); ?>">
                    <summary class="archive-group-title"><?php echo htmlspecialchars($themeData['name']); ?> <span class="archive-count">(<?php echo count($themeData['items']); ?>)</span></summary>
                    <ul class="archive-list">
                        <?php foreach ($themeData['items'] as $themeP): ?>
                        <li>
                            <a href="<?php echo $themeP->permalink(); ?>"><?php echo $themeP->title(); ?></a>
                            <span class="archive-date"><?php echo $themeP->date(); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </details>
                <?php endforeach; ?>
            <?php endif; ?>
        </article>
        <?php else: ?>
        <article class="article-view">
            <?php $catName = $page->category(); ?>
            <?php if ($catName): ?>
            <span class="badge"><?php echo $catName; ?></span>
            <?php endif; ?>

            <h1><?php echo $page->title(); ?></h1>

            <div class="meta">
                <?php echo $page->date(); ?> &middot; <?php echo $page->user('nickname'); ?> &middot; <?php echo $page->readingTime(); ?>
            </div>

            <?php if ($page->coverImage()): ?>
            <div class="cover">
                <img src="<?php echo $page->coverImage(); ?>" alt="<?php echo $page->title(); ?>">
            </div>
            <?php endif; ?>

            <div class="content">
                <?php echo $page->content(); ?>
            </div>
        </article>
        <?php endif; ?>
    </main>

    <?php include(THEME_DIR_PHP.'parts/sidebar-right.php'); ?>

</div>

<?php include(THEME_DIR_PHP.'parts/footer.php'); ?>
