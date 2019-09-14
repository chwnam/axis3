<?php
/**
 * Context:
 *
 * @var array  $attributes 순차배열. 각 원소는 길이 3인 순차 배열.
 *                         원소로 들어간 배열 정보:
 *                         인덱스 0: input 태그 속성
 *                         인덱스 1: label 태그 속성
 *                         인덱스 2: 레이블 텍스트
 * @var string $direction  출력 방향과 관련된 CSS 클래스 이름.
 */

use function Shoplic\Axis3\Functions\closeTag;
use function Shoplic\Axis3\Functions\inputTag;
use function Shoplic\Axis3\Functions\openTag;

?>
<ul class="axis3-checkbox-radio <?php echo sanitize_html_class($direction); ?>">
    <?php foreach ($attributes as $attribute): ?>
        <li>
            <?php
            list($input_attrs, $label_attrs, $description) = $attribute;
            inputTag($input_attrs);
            {
                openTag('label', $label_attrs);
                echo wp_kses_post($description);
                closeTag('label');
            }
            ?>
        </li>
    <?php endforeach; ?>
</ul>
<div class="wp-clearfix"></div>
