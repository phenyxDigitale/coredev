<?php
$type = '';
extract(Composer::shortcode_atts(array(
    'type' => 'horizontal'//horizontal, vertical, none
), $atts));

$css_class =  'twitter-share-button';

$ssl_enable = Configuration::get('EPH_SSL_ENABLED');
$base = ($ssl_enable == 1) ? 'https://' : 'http://';

$output = '<a href="'.$base.'twitter.com/share" class="'.$css_class.'" data-count="'.$type.'">'. ephenyx_manager()->l("Tweet", "js_composer") .'</a><script type="text/javascript" src="'.$base.'platform.twitter.com/widgets.js"></script>'.$this->endBlockComment('tweetmeme')."\n";

echo $output;