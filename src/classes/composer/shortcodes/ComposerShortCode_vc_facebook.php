<?php
class ComposerShortCode_vc_facebook extends ComposerShortCode {

    protected function contentInline($atts, $content = null) {

        extract(Composer::shortcode_atts([
            'type' => 'standard', //standard, button_count, box_count
            'url'  => '',
        ], $atts));

        $id_cms = Tools::getValue('id_cms');

        if ($url == '') {
            $url = Composer::getCMSLink($id_cms);
        }

        $css_class = 'vc_social-placeholder fb_like wpb_content_element vc_socialtype-' . $type;
        return '<a href="' . $url . '" class="' . $css_class . '"></a>';
    }

}
