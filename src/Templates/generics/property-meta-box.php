<?php
/**
 * Context:
 *
 * @var string                 $content_header
 * @var string                 $content_footer
 *
 * @var string                 $table_header
 * @var string                 $table_footer
 *
 * @var string                 $nonce_action
 * @var string                 $nonce_param
 *
 * @var FieldWidgetInterface[] $field_widgets
 */

use Shoplic\Axis3\Interfaces\Views\Admin\FieldWidgets\FieldWidgetInterface;

$default = [
    'content_header' => '',
    'content_footer' => '',
    'table_header'   => '',
    'table_footer'   => '',
    'nonce_action'   => '',
    'nonce_param'    => '_wpnonce',
    'field_widgets'  => [],
];

foreach ($default as $var => $val) {
    if (!isset(${$var})) {
        ${$var} = $val;
    }
}

if (!$nonce_action) {
    echo '<p>';
    esc_html_e('Please set NONCE action string to complete this property meta box correctly.', 'axis3');
    echo '</p>';
    return;
}
?>

<?php echo $content_header; ?>
    <table class="form-table">
        <?php echo $table_header; ?>
        <?php foreach ($field_widgets as $widget) : ?>
            <?php $widget->renderFormTableTr(); ?>
        <?php endforeach; ?>
        <?php echo $table_footer; ?>
    </table>
<?php wp_nonce_field($nonce_action, $nonce_param); ?>
<?php echo $content_footer;
