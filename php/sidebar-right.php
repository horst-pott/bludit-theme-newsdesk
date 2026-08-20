<?php defined('BLUDIT') or die('Bludit CMS.');

// Zeigt Beiträge aus den konfigurierten Rubriken (Standard: "rezepte" und
// "videos"), zusätzlich zu hochgeladenen Video-Clips. Einstellbar unter
// Plugins -> Video-Upload -> Einstellungen -> "Videos-Vorschau: Rubrik-
// Schlüssel" -- ohne aktives Plugin greift der Standard.
$extraKeys = function_exists('video_upload_get_preview_category_keys')
    ? video_upload_get_preview_category_keys()
    : array('rezepte', 'videos');
$extraItems = array();
$allCategoriesForExtras = getCategories(); // liefert nur tatsächlich existierende Kategorien
foreach ($allCategoriesForExtras as $cat) {
    if (!in_array($cat->key(), $extraKeys)) { continue; }
    foreach ($cat->pages() as $pageKey) {
        $p = buildPage($pageKey);
        if ($p) {
            $extraItems[] = array(
                'type' => 'modal',
                'page' => $p,
                'catKey' => $cat->key(),
                'catName' => $cat->name(),
                'sortDate' => $p->dateRaw(),
            );
        }
    }
}

// Zusätzlich: eigenständig hochgeladene Video-Clips (Plugin "Video-Upload").
// Diese führen beim Klick direkt zum hinterlegten Ziel-Link, statt ein Pop-up zu öffnen.
$videoClips = function_exists('video_upload_get_clips') ? video_upload_get_clips() : array();
foreach ($videoClips as $clip) {
    $extraItems[] = array(
        'type' => 'link',
        'title' => $clip['title'],
        'targetUrl' => $clip['targetUrl'],
        'sortDate' => isset($clip['timestamp']) ? $clip['timestamp'] : date('Y-m-d H:i:s'),
    );
}

// neueste zuerst, maximal 6 Kacheln
usort($extraItems, function($a, $b) { return strcmp($b['sortDate'], $a['sortDate']); });
$extraItems = array_slice($extraItems, 0, 6);
?>
<aside class="sidebar-right" aria-labelledby="sidebarRightHeading">
    <div class="widget">
        <h3 id="sidebarRightHeading">Empfehlungen</h3>
        <?php if (empty($extraItems)): ?>
            <p class="empty-hint">Sobald Artikel in den konfigurierten Rubriken ver&ouml;ffentlicht oder Video-Clips hochgeladen werden, erscheinen sie hier automatisch als Kachel. Welche Rubriken das sind, l&auml;sst sich unter Plugins &rarr; Video-Upload &rarr; Einstellungen anpassen.</p>
        <?php else: ?>
            <?php foreach ($extraItems as $i => $item): ?>
                <?php if ($item['type'] === 'modal'): $p = $item['page']; $modalId = 'popup-'.$i; ?>
                <button type="button" class="extra-item" id="<?php echo $modalId; ?>-trigger"
                        data-modal-target="<?php echo $modalId; ?>" aria-haspopup="dialog">
                    <?php if ($p->coverImage()): ?>
                    <img src="<?php echo $p->coverImage(); ?>" alt="">
                    <?php else: ?>
                    <div class="extra-thumb-placeholder" aria-hidden="true"><span><?php echo mb_substr($p->title(), 0, 1); ?></span></div>
                    <?php endif; ?>
                    <span class="extra-item-text">
                        <span class="extra-tag"><?php echo $item['catName']; ?></span>
                        <span class="extra-title"><?php echo $p->title(); ?></span>
                    </span>
                </button>
                <?php else: ?>
                <a class="extra-item" href="<?php echo $item['targetUrl']; ?>">
                    <span class="extra-thumb-placeholder" aria-hidden="true">&#9658;</span>
                    <span class="extra-item-text">
                        <span class="extra-tag">Video</span>
                        <span class="extra-title"><?php echo htmlspecialchars($item['title']); ?></span>
                    </span>
                </a>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</aside>

<?php foreach ($extraItems as $i => $item): if ($item['type'] !== 'modal') { continue; } $p = $item['page']; $modalId = 'popup-'.$i; $modalTitleId = 'popup-title-'.$i; ?>
<div class="modal-overlay" id="<?php echo $modalId; ?>" role="dialog" aria-modal="true" aria-labelledby="<?php echo $modalTitleId; ?>">
    <div class="modal-box">
        <button type="button" class="modal-close" data-modal-close aria-label="Schließen">&times;</button>
        <span class="extra-tag"><?php echo $item['catName']; ?></span>
        <h3 id="<?php echo $modalTitleId; ?>"><?php echo $p->title(); ?></h3>
        <div class="content"><?php echo $p->content(); ?></div>
    </div>
</div>
<?php endforeach; ?>
