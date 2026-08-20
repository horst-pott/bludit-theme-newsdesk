<?php defined('BLUDIT') or die('Bludit CMS.');
include(THEME_DIR_PHP.'parts/header.php');

$categoryKey = $url->slug();
$category = new Category($categoryKey);
?>

<div class="wrapper main-layout">

    <?php include(THEME_DIR_PHP.'parts/sidebar-left.php'); ?>

    <main class="news-feed" id="main-content" tabindex="-1">
        <h2 class="section-title">Rubrik: <?php echo $category->name(); ?></h2>
        <?php if ($category->description()): ?>
            <p style="color:var(--color-muted); margin-top:-10px;"><?php echo $category->description(); ?></p>
        <?php endif; ?>

        <?php
        $catPageKeys = $category->pages();
        $catVisibleKeys = array();
        $catHasArchived = false;
        foreach ($catPageKeys as $pageKey) {
            $checkPage = buildPage($pageKey);
            if (!$checkPage) { continue; }
            if (theme_is_archived_page($checkPage)) {
                $catHasArchived = true;
                continue;
            }
            $catVisibleKeys[] = $pageKey;
        }
        if (empty($catVisibleKeys)):
        ?>
            <p>In dieser Rubrik wurden noch keine Artikel ver&ouml;ffentlicht.</p>
        <?php else: ?>
            <?php foreach ($catVisibleKeys as $pageKey):
                $page = buildPage($pageKey);
                if (!$page) { continue; }
            ?>
            <article class="news-card">
                <div class="thumb">
                    <?php if ($page->coverImage()): ?>
                    <img src="<?php echo $page->coverImage(); ?>" alt="<?php echo $page->title(); ?>">
                    <?php else: ?>
                    <div class="thumb-placeholder"><span><?php echo mb_substr($page->title(), 0, 1); ?></span></div>
                    <?php endif; ?>
                </div>
                <div class="body">
                    <h3><a href="<?php echo $page->permalink(); ?>"><?php echo $page->title(); ?></a></h3>
                    <p class="excerpt"><?php echo theme_excerpt($page->contentBreak(), 180); ?></p>
                    <div class="meta">
                        <?php echo $page->date(); ?> &middot; <?php echo $page->user('nickname'); ?> &middot; <?php echo $page->readingTime(); ?>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($catHasArchived): $themeArchivPage = buildPage('archiv'); if ($themeArchivPage): ?>
        <p class="archive-teaser"><a href="<?php echo $themeArchivPage->permalink(); ?>#archiv-<?php echo htmlspecialchars($categoryKey); ?>">&Auml;ltere Artikel dieser Rubrik im Archiv ansehen &rarr;</a></p>
        <?php endif; endif; ?>
    </main>

    <?php include(THEME_DIR_PHP.'parts/sidebar-right.php'); ?>

</div>

<?php include(THEME_DIR_PHP.'parts/footer.php'); ?>
