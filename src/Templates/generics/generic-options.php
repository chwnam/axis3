<?php
/**
 * Context
 *
 * @var string $option_group
 * @var string $page
 * @var string $button_text
 */

if (!isset($button_text)) {
    $button_text = null;
}
?>

<form method="post" action="<?= esc_url(admin_url('options.php')) ?>">

    <?php settings_fields($option_group); ?>

    <?php do_settings_sections($page); ?>

    <?php submit_button($button_text); ?>

</form>
