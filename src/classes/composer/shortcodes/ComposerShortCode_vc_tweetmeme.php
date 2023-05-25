<?php
class ComposerShortCode_vc_tweetmeme extends ComposerShortCode {

    protected function contentInline($atts, $content = null) {

        extract(Composer::shortcode_atts([
            'type' => 'horizontal', //horizontal, vertical, none
        ], $atts));

        $css_class = 'vc_social-placeholder twitter-share-button vc_socialtype-' . $type;
        return '<div class="' . $css_class . '"></div>';
    }
}