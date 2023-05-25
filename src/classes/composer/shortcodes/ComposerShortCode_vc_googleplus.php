<?php
class ComposerShortCode_vc_googleplus extends ComposerShortCode {

    protected function contentInline($atts, $content = null) {

        extract(Composer::shortcode_atts([
            'type'       => 'standard',
            'annotation' => 'bubble',
        ], $atts));

        if (strlen($type) == 0) {
            $type = 'standard';
        }

        if (strlen($annotation) == 0) {
            $annotation = 'bubble';
        }

        $css_class = 'wpb_googleplus vc_social-placeholder wpb_content_element vc_socialtype-' . $type . ' vc_annotation-' . $annotation;
        return '<div class="' . $css_class . '"></div>';
    }

}
