<?php
class ComposerShortCode_vc_pinterest extends ComposerShortCode {

    protected function contentInline($atts, $content = null) {

        extract(Composer::shortcode_atts([
            'type' => 'horizontal',
        ], $atts));

        $css_class = 'vc_social-placeholder wpb_pinterest wpb_content_element vc_socialtype-' . $type;
        return '<div class="' . $css_class . '"></div>';
    }
}