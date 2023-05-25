<?php
class ComposerShortCode_vc_flickr extends ComposerShortCode {

    protected function contentInline($atts, $content = null) {

        $output = '';
        extract(Composer::shortcode_atts([
            'el_class'  => '',
            'title'     => '',
            'flickr_id' => '95572727@N00',
            'count'     => '6',
            'type'      => 'user',
            'display'   => 'latest',
        ], $atts));

        $el_class = $this->getExtraClass($el_class);
        $css_class = 'wpb_flickr_widget wpb_content_element' . $el_class;

        $output .= "\n\t" . '<div class="' . $css_class . '">';
        $output .= "\n\t\t" . '<div class="wpb_wrapper">';
        $output .= widget_title(['title' => $title, 'extraclass' => 'wpb_flickr_heading']);
        $output .= "\n\t\t\t" . '<div class="vc_flickr-inline-placeholder" data-link="http://www.flickr.com/badge_code_v2.gne?count=' . $count . '&amp;display=' . $display . '&amp;size=s&amp;layout=x&amp;source=' . $type . '&amp;' . $type . '=' . $flickr_id . '"></div>';
        $output .= "\n\t\t\t" . '<p class="flickr_stream_wrap"><a class="wpb_follow_btn wpb_flickr_stream" href="http://www.flickr.com/photos/' . $flickr_id . '">' . ephenyx_manager()->l("View stream on flickr") . '</a></p>';
        $output .= "\n\t\t" . '</div>' . $this->endBlockComment('.wpb_wrapper');
        $output .= "\n\t" . '</div>' . $this->endBlockComment('.wpb_flickr_widget') . "\n";
        return $output;
    }
}