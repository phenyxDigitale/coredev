<?php
$vc_manager = ephenyx_manager();
$output = $title = $type = $count = $interval = $slides_content = $link = '';
$custom_links = $thumb_size = $posttypes = $posts_in = $categories = '';
$orderby = $order = $el_class = $link_image_start = '';
extract( Composer::shortcode_atts( array(
	'title' => '',
	'type' => 'flexslider_fade',
	'count' => 3,
	'interval' => 3,
	'slides_content' => '',
	'slides_title' => '',
	'link' => 'link_post',
	'custom_links' => Tools::getHttpHost(true).__EPH_BASE_URI__,
	'thumb_size' => '',
	'posttypes' => '',
	'posts_in' => '',
	'categories' => '',
	'orderby' => NULL,
	'order' => 'DESC',
	'el_class' => ''
), $atts ) );

$gal_images = '';
$link_start = '';
$link_end = '';
$el_start = '';
$el_end = '';
$slides_wrap_start = '';
$slides_wrap_end = '';

$el_class = $this->getExtraClass( $el_class );

if ( $type == 'nivo' ) {
	$type = ' wpb_slider_nivo theme-default';
        if(Configuration::get('vc_load_nivo_js') != 'no'){
        	Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/nivoslider/jquery.nivo.slider.pack.js' );
    	}
        if(Configuration::get('vc_load_nivo_css') != 'no'){
        	Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/nivoslider/nivo-slider.css');
    	}
        Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/nivoslider/themes/default/default.css');
	$slides_wrap_start = '<div class="nivoSlider">';
	$slides_wrap_end = '</div>';
} else if ( $type == 'flexslider' || $type == 'flexslider_fade' || $type == 'flexslider_slide' || $type == 'fading' ) {
	$el_start = '<li>';
	$el_end = '</li>';
	$slides_wrap_start = '<ul class="slides">';
	$slides_wrap_end = '</ul>';
    	if(Configuration::get('vc_load_flex_js') != 'no'){
    		Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/flexslider/jquery.flexslider-min.js' );
		}
        if(Configuration::get('vc_load_flex_css') != 'no'){
        	Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/flexslider/flexslider.css');
    	}
}
$flex_fx = '';
if ( $type == 'flexslider' || $type == 'flexslider_fade' || $type == 'fading' ) {
	$type = ' wpb_flexslider flexslider_fade flexslider';
	$flex_fx = ' data-flex_fx="fade"';
} else if ( $type == 'flexslider_slide' ) {
	$type = ' wpb_flexslider flexslider_slide flexslider';
	$flex_fx = ' data-flex_fx="slide"';
}

if ( $link == 'link_image' ) {
    Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/js/jquery.prettyPhoto.js' );
    Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/css/prettyPhoto.css' );
        
}


$db = Db::getInstance();
$context = Context::getContext();
$id_lang = $context->language->id;
$id_company = $context->company->id;

$cat_where = $posts_in_where = "";

$sql = "SELECT sbpl.* FROM "._DB_PREFIX_."smart_blog_post_lang sbpl ";

$sql .= " INNER JOIN "._DB_PREFIX_."smart_blog_post_shop sbps ON sbpl.id_smart_blog_post=sbps.id_smart_blog_post";

$sql .= " INNER JOIN "._DB_PREFIX_."smart_blog_post sbp ON sbps.id_smart_blog_post=sbp.id_smart_blog_post";

if ( $categories != '' ) {
    $sql .= " INNER JOIN "._DB_PREFIX_."smart_blog_category_lang sbcl ON sbp.id_category=sbcl.id_smart_blog_category";
    $exploded_cat = explode(',',$categories);
    
    $cat_where = " AND sbcl.link_rewrite IN(";
    foreach ($exploded_cat as $n=>$ecat){
        if($n > 0 && $ecat != '')
            $cat_where .= ',';
        $cat_where .= "'{$ecat}'";
    }
    $cat_where .= ')';
}

if ( $posts_in != '' ) {
    $posts_in_where = " AND sbpl.id_smart_blog_post IN({$posts_in})"; 
} 

$sql .= " WHERE sbps.id_shop={$id_company} AND sbpl.id_lang={$id_lang} AND sbp.active=1".$cat_where.$posts_in_where;

if ( $orderby != NULL ) {	
    if($orderby == 'meta_title' || $orderby == 'link_rewrite'){
        $orderby = "sbpl.{$orderby}";
    }else{
        $orderby = "sbp.{$orderby}";
    }                    
    $sql .= " ORDER BY {$orderby}";
    $sql .= " {$order}";
}

if ( $count != '' && is_numeric( $count ) ) 
    $sql .= " LIMIT {$count}";

// Run query
$my_query = $db->executeS($sql,true,false);


$pretty_rel_random = 'rel-' . rand();
if ( $link == 'custom_link' ) {
	$custom_links = explode( ',', $custom_links );
}
$teasers = '';
$i = - 1;
$smartblog_url = _PLUGIN_DIR_.'smartblog/images/';

if(!empty($my_query))
foreach ( $my_query as $qpost  ) {
	$i++;
	
	$post_title = $qpost['meta_title'];
        $post_title_attribute = $vc_manager->esc_attr($post_title);
	$post_id = (int)$qpost['id_smart_blog_post'];
        $post_link = smartblog::GetSmartBlogLink('smartblog_post',array('id_post'=>$post_id , 'slug' =>$qpost['link_rewrite']));
	if ( $slides_content == 'teaser' ) {
		$content =  $qpost['short_description']; 
	} else {
		$content = '';
	}

	$post_thumbnail = $p_img_large = '';

        
        
        
        $thumbnail = $this->getPostThumbnail( $post_id, $thumb_size );
        $post_thumbnail .= "<img alt='{$post_title_attribute}' src='{$smartblog_url}{$thumbnail}' />";
        
        $thumbnail = $post_thumbnail;
        
	$p_img_large = $smartblog_url.$this->getPostThumbnail( $post_id);

	if ( $link != 'link_no' ) {
		if ( $link == 'link_post' ) {
			$link_image_start = '<a class="link_image" href="' . $post_link . '" title="' . sprintf( $vc_manager->esc_attr__( 'Permalink to %s' ), $post_title_attribute ) . '">';
		} else if ( $link == 'link_image' ) {
				$p_link = $p_img_large; 
			$link_image_start = '<a class="link_image prettyphoto" href="' . $p_link . '" title="' . $post_title_attribute . '" >';
		} else if ( $link == 'custom_link' ) {
			if ( isset( $custom_links[$i] ) ) {
				$slide_custom_link = $custom_links[$i];
			} else {
				$slide_custom_link = $custom_links[0];
			}
			$link_image_start = '<a class="link_image" href="' . $slide_custom_link . '">';
		}

		$link_image_end = '</a>';
	} else {
		$link_image_start = '';
		$link_image_end = '';
	}

	$description = '';
	if ( $slides_content != '' && $content != '' && ( $type == ' wpb_flexslider flexslider_fade flexslider' || $type == ' wpb_flexslider flexslider_slide flexslider' ) ) {
		$description = '<div class="flex-caption">';
		if ( $slides_title == true ) $description .= '<h2 class="post-title">' . $link_image_start . $post_title . $link_image_end . '</h2>';
		$description .= $content;
		$description .= '</div>';
	}

	$teasers .= $el_start . $link_image_start . $thumbnail . $link_image_end . $description . $el_end;
} 

if ( $teasers ) {
	$teasers = $slides_wrap_start . $teasers . $slides_wrap_end;
} else {
	$teasers = $this->l( "Nothing found.");
}

$css_class =  'wpb_gallery wpb_posts_slider wpb_content_element' . $el_class;

$output .= "\n\t" . '<div class="' . $css_class . '">';
$output .= "\n\t\t" . '<div class="wpb_wrapper">';
$output .= widget_title( array( 'title' => $title, 'extraclass' => 'wpb_posts_slider_heading' ) );
$output .= '<div class="wpb_gallery_slides' . $type . '" data-interval="' . $interval . '"' . $flex_fx . '>' . $teasers . '</div>';
$output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
$output .= "\n\t" . '</div> ' . $this->endBlockComment( '.wpb_gallery' );

echo $output;