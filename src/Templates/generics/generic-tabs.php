<?php
/**
 * Context:
 *
 * @var array $tabs [
 *                    [
 *                      'class' => 'nav-tab nav-tab-active',
 *                      'url'   => 'https://.....',
 *                      'label' => 'The Label',
 *                    ],
 *                    [ 'class' => ... ],
 *                    ...
 *                  ]
 *
 * @link https://make.wordpress.org/core/2019/04/02/admin-tabs-semantic-improvements-in-5-2/
 *
 * <nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
 *   <a href="about.php" class="nav-tab nav-tab-active">What’s New</a>
 *   <a href="credits.php" class="nav-tab">Credits</a>
 *   <a href="freedoms.php" class="nav-tab">Freedoms</a>
 *   <a href="freedoms.php?privacy-notice" class="nav-tab">Privacy</a>
 * </nav>
 */
?>

<?php if (count($tabs) > 1) : ?>
    <nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e('Tabbed menu', 'axis3'); ?>">
        <?php foreach ($tabs as $tab) : ?>
            <?php
            $class = $tab['class'] ?? '';
            $url   = $tab['url'] ?? '#';
            $label = $tab['label'] ?? '';
            if ($label && $url && $class) : ?>
                <a href="<?php echo esc_url($url); ?>"
                   class="<?php echo sanitize_html_class($class); ?>"><?php echo esc_html($label); ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
<?php endif; ?>
