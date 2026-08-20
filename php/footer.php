<?php defined('BLUDIT') or die('Bludit CMS.'); ?>
<footer class="site-footer">
    <div class="wrapper footer-inner">
        <div>
            <h4><?php echo $site->title(); ?></h4>
            <p style="max-width:420px; font-size:13.5px;"><?php echo $site->description(); ?></p>
        </div>
        <div>
            <h4>Rubriken</h4>
            <ul class="two-col-list">
                <?php foreach (getCategories() as $category): ?>
                <li><a href="<?php echo $category->permalink(); ?>"><?php echo $category->name(); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <h4>Seiten</h4>
            <ul class="two-col-list">
                <?php foreach ($staticContent as $staticPage): ?>
                <li><a href="<?php echo $staticPage->permalink(); ?>"><?php echo $staticPage->title(); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php if ($site->logo()): ?>
    <div class="footer-brand-row">
        <div class="wrapper">
            <a href="<?php echo $site->url(); ?>" class="footer-logo-link" title="<?php echo htmlspecialchars($site->title()); ?>">
                <img src="<?php echo $site->logo(); ?>" alt="<?php echo htmlspecialchars($site->title()); ?>" class="footer-logo">
            </a>
        </div>
    </div>
    <?php endif; ?>
    <div class="footer-bottom">
        <span>&copy; <?php echo date('Y'); ?> <?php echo $site->title(); ?> &middot; powered by Bludit</span>
    </div>
</footer>

<div class="scroll-buttons">
    <button type="button" id="scrollTopBtn" title="Nach oben" aria-label="Nach oben scrollen">&#9650;</button>
    <button type="button" id="scrollBottomBtn" title="Nach unten" aria-label="Nach unten scrollen">&#9660;</button>
</div>

<?php echo Theme::js('js/theme.js?v=' . filemtime(__DIR__ . '/../../js/theme.js')); ?>
<?php Theme::plugins('siteBodyEnd'); ?>
</body>
</html>
