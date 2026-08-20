<?php defined('BLUDIT') or die('Bludit CMS.');
include(THEME_DIR_PHP.'parts/header.php');

// ---------- Suche auswerten ----------
// $themeCurrentSearchTerm wurde bereits in header.php aus $_GET['suche'] gelesen
$isSearching = ($themeCurrentSearchTerm !== '');
if ($isSearching) {
    global $pages;
    theme_log_search_term($themeCurrentSearchTerm);

    $needle = mb_strtolower($themeCurrentSearchTerm);
    $allKeys = $pages->getList(1, -1, true); // -1 = alle veröffentlichten Seiten
    $searchResults = array();
    foreach ($allKeys as $key) {
        $p = buildPage($key);
        if (!$p) { continue; }
        $haystack = mb_strtolower(strip_tags($p->title() . ' ' . $p->content()));
        if (mb_strpos($haystack, $needle) !== false) {
            $searchResults[] = $p;
        }
    }
    $content = $searchResults;
}
?>

<div class="wrapper main-layout">

    <?php include(THEME_DIR_PHP.'parts/sidebar-left.php'); ?>

    <main class="news-feed" id="main-content" tabindex="-1">
        <?php if ($isSearching): ?>
            <h2 class="section-title">Suchergebnisse für „<?php echo htmlspecialchars($themeCurrentSearchTerm); ?>“</h2>
        <?php else: ?>
            <h2 class="section-title">Aktuelle Nachrichten</h2>
        <?php endif; ?>

        <?php if (empty($content) && $isSearching): ?>
            <p>Keine Artikel gefunden, die zu „<?php echo htmlspecialchars($themeCurrentSearchTerm); ?>“ passen.</p>
        <?php elseif (empty($content)): ?>
            <p>Es wurden noch keine Artikel ver&ouml;ffentlicht.</p>
        <?php endif; ?>

        <?php foreach ($content as $page): ?>
        <?php if (!$isSearching && theme_is_archived_page($page)) { continue; } ?>
        <article class="news-card">
            <div class="thumb">
                <?php if ($page->coverImage()): ?>
                <img src="<?php echo $page->coverImage(); ?>" alt="<?php echo $page->title(); ?>">
                <?php else: ?>
                <div class="thumb-placeholder"><span><?php echo mb_substr($page->title(), 0, 1); ?></span></div>
                <?php endif; ?>
            </div>
            <div class="body">
                <?php $catName = $page->category(); ?>
                <?php if ($catName): ?>
                <span class="badge"><?php echo $catName; ?></span>
                <?php endif; ?>
                <h3><a href="<?php echo $page->permalink(); ?>"><?php echo $page->title(); ?></a></h3>
                <p class="excerpt"><?php echo theme_excerpt($page->contentBreak(), 180); ?></p>
                <div class="meta">
                    <?php echo $page->date(); ?> &middot; <?php echo $page->user('nickname'); ?> &middot; <?php echo $page->readingTime(); ?>
                </div>
            </div>
        </article>
        <?php endforeach; ?>

        <?php if (!$isSearching && Paginator::numberOfPages() > 1): ?>
        <div class="pagination">
            <?php if (Paginator::showPrev()): ?>
                <a href="<?php echo Paginator::previousPageUrl(); ?>">&larr; Neuere Artikel</a>
            <?php endif; ?>
            <?php if (Paginator::showNext()): ?>
                <a href="<?php echo Paginator::nextPageUrl(); ?>">&Auml;ltere Artikel &rarr;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!$isSearching): $themeArchivPage = buildPage('archiv'); if ($themeArchivPage): ?>
        <p class="archive-teaser"><a href="<?php echo $themeArchivPage->permalink(); ?>">Ältere Artikel (&gt; <?php echo theme_archive_threshold_days(); ?> Tage) im Archiv ansehen &rarr;</a></p>
        <?php endif; endif; ?>
    </main>

    <?php include(THEME_DIR_PHP.'parts/sidebar-right.php'); ?>

</div>

<?php include(THEME_DIR_PHP.'parts/footer.php'); ?>
