<?php defined('BLUDIT') or die('Bludit CMS.'); ?>
<aside class="sidebar-left" aria-labelledby="sidebarLeftHeading">
    <h3 id="sidebarLeftHeading">Rubriken</h3>
    <?php
    $leftCategories = getCategories();
    $leftCategoriesCount = count($leftCategories);
    $deptVisibleLimit = 8;
    $deptIndex = 0;
    $deptHiddenOpened = false;
    foreach ($leftCategories as $category):
        $deptIndex++;
        $pageKeys = $category->pages();
        $deptPanelId = 'dept-panel-' . htmlspecialchars($category->key());
        if ($deptIndex === ($deptVisibleLimit + 1) && $leftCategoriesCount > $deptVisibleLimit):
            $deptHiddenOpened = true;
    ?>
    <div class="dept-hidden-group" id="deptHiddenGroup">
    <?php endif; ?>
    <div class="dept-item">
        <button type="button" class="dept-toggle" aria-expanded="false" aria-controls="<?php echo $deptPanelId; ?>">
            <?php echo $category->name(); ?>
            <span class="arrow" aria-hidden="true">&#9656;</span>
        </button>
        <div class="dept-panel" id="<?php echo $deptPanelId; ?>">
            <?php if (empty($pageKeys)): ?>
            <span style="font-size:13px;color:var(--color-muted);font-style:italic;">Noch keine Artikel</span>
            <?php else:
                $shown = 0;
                foreach ($pageKeys as $pageKey):
                    if ($shown >= 8) { break; }
                    $deptPage = buildPage($pageKey);
                    if (!$deptPage) { continue; }
                    $shown++;
            ?>
            <a href="<?php echo $deptPage->permalink(); ?>"><?php echo $deptPage->title(); ?></a>
            <?php endforeach; endif; ?>
            <a href="<?php echo $category->permalink(); ?>" style="color:var(--color-primary); font-weight:700;">Zur Rubrik-Übersicht &rarr;</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if ($deptHiddenOpened): ?>
    </div>
    <?php endif; ?>
    <?php if ($leftCategoriesCount > $deptVisibleLimit):
        $deptMoreCount = $leftCategoriesCount - $deptVisibleLimit;
        $deptLabelMore = 'Alle Rubriken anzeigen (' . $deptMoreCount . ' weitere)';
    ?>
    <button type="button" class="dept-showall-toggle" id="deptShowAllToggle"
            aria-expanded="false" aria-controls="deptHiddenGroup"
            data-label-more="<?php echo htmlspecialchars($deptLabelMore); ?>"
            data-label-less="Weniger Rubriken anzeigen">
        <span class="label"><?php echo htmlspecialchars($deptLabelMore); ?></span>
        <span class="arrow" aria-hidden="true">&#9656;</span>
    </button>
    <?php endif; ?>
</aside>
