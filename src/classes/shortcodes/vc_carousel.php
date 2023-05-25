<?php
global $vc_teaser_box;
$posts_query = $el_class = $args = $my_query = $speed = $mode = $swiper_options = '';
$content = $link = $layout = $thumb_size = $link_target = $slides_per_view = $wrap = '';
$autoplay = $hide_pagination_control = $hide_prev_next_buttons = $title = '';
$posts = array();
$vc_manager = ephenyx_manager();
extract( Composer::shortcode_atts( array(
	'el_class' => '',
	'posts_query' => '',
	'mode' => 'horizontal',
	'speed' => '5000',
	'slides_per_view' => '1',
	'swiper_options' => '',
	'wrap' => '',
	'autoplay' => 'no',
	'hide_pagination_control' => '',
	'hide_prev_next_buttons' => '',
	'layout' => 'title,thumbnail,excerpt',
	'link_target' => '',
	'thumb_size' => '',
	'partial_view' => '',
	'title' => ''
), $atts ) );
list( $args, $my_query ) = vc_build_loop_query( $posts_query ); //
$teaser_blocks = vc_sorted_list_parse_value( $layout );
$smartblog_url = _PLUGIN_DIR_.'smartblog/images/';
foreach ( $my_query as $qpost ) {
	
	$post = new stdClass(); // Creating post object.
	$post->id = (int)$qpost['id_smart_blog_post'];
	$post->link = smartblog::GetSmartBlogLink('smartblog_post',array('id_post'=>$post->id , 'slug' =>$qpost['link_rewrite']));
	
        $post->custom_user_teaser = false;
        $post->title = $qpost['meta_title'];
        $post->title_attribute = $vc_manager->esc_attr($post->title);
        $post->post_type = 0;
        $post->content = $qpost['content'];
        $post->excerpt = $qpost['short_description'];
        
        $post->thumbnail_data = $this->getPostThumbnail( $post->id, $thumb_size );
        $post->thumbnail = "<img alt='{$post->title_attribute}' src='{$smartblog_url}{$post->thumbnail_data}' />";
        
        $post->image_link = $smartblog_url.$this->getPostThumbnail( $post->id);
	$post->categories_css = $this->getCategoriesCss( $post->id );

	$posts[] = $post;
}


$tmp_options = parse_options_string( $swiper_options, $this->shortcode, 'swiper_options' );

$this->setLinktarget( $link_target );
$vc_manager->front_css[] = _EPH_JS_DIR_ . 'composer/vc_carousel/css/vc_carousel.css';
$vc_manager->front_js[] = _EPH_JS_DIR_ . 'composer/vc_carousel/js/vc_carousel.js';
Context::getContext()->controller->addCSS(_EPH_JS_DIR_ . 'composer/vc_carousel/css/vc_carousel.css');
Context::getContext()->controller->addJS(_EPH_JS_DIR_ . 'composer/vc_carousel/js/vc_carousel.js');

$options = array();
// Convert keys to Camel case.
foreach ( $tmp_options as $key => $value ) {
	$key = preg_replace( '/_([a-z])/e', "strtoupper('\\1')", $key );
	$options[$key] = $value;
}
if ( (int)$slides_per_view > 0 ) $options['slidesPerView'] = (int)$slides_per_view;
if ( (int)$autoplay > 0 ) $options['autoplay'] = (int)$autoplay;
$options['mode'] = $mode;
// $options['calculateHeight'] = true;
$css_class = $this->settings['base'] . ' wpb_content_element vc_carousel_slider_' . $slides_per_view . ' vc_carousel_' . $mode . ( empty( $el_class ) ? '' : ' ' . $el_class );
$carousel_id = 'vc_carousel-' . ComposerShortCode_vc_carousel::getCarouselIndex();
?>
<div class="<?php echo  $css_class ?>">
	<div class="wpb_wrapper">
		<?php echo  wpb_widget_title( array( 'title' => $title, 'extraclass' => 'wpb_gallery_heading' ) ) ?>
		<div id="<?php echo $carousel_id ?>" data-ride="vc_carousel"
			 data-wrap="<?php echo $wrap === 'yes' ? 'true' : 'false' ?>"
			 data-interval="<?php echo $autoplay == 'yes' ? $speed : 0 ?>" data-auto-height="true"
			 data-mode="<?php echo $mode ?>" data-partial="<?php echo $partial_view === 'yes' ? 'true' : 'false' ?>"
			 data-per-view="<?php echo $slides_per_view ?>"
			 data-hide-on-end="<?php echo $autoplay == 'yes' ? 'false' : 'true' ?>" class="vc_carousel vc_slide">
			<?php if ( $hide_pagination_control !== 'yes' ): ?>
			<!-- Indicators -->
			<ol class="vc_carousel-indicators">
				<?php for ( $i = 0; $i < count( $posts ); $i ++ ): ?>
				<li data-target="#<?php echo $carousel_id ?>" data-slide-to="<?php echo $i ?>"></li>
				<?php endfor; ?>
			</ol>
			<?php endif; ?>
			<!-- Wrapper for slides -->
			<div class="vc_carousel-inner">
				<div class="vc_carousel-slideline">
					<div class="vc_carousel-slideline-inner">
						<?php foreach ( $posts as $post ): ?>
						<?php
						$blocks_to_build = $post->custom_user_teaser === true ? $post->custom_teaser_blocks : $teaser_blocks;
						$block_style = isset( $post->bgcolor ) ? ' style="background-color: ' . $post->bgcolor . '"' : '';
						?>
						<div class="vc_item vc_slide_<?php echo $post->post_type ?>"<?php echo $block_style ?>>
							<div class="vc_inner">
								<?php foreach ( $blocks_to_build as $block_data ): ?>
								<?php include $this->getBlockTemplate() ?>
								<?php endforeach; ?>
							</div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<?php if ( $hide_prev_next_buttons !== 'yes' ): ?>
			<!-- Controls -->
			<a class="vc_left vc_carousel-control" href="#<?php echo $carousel_id ?>" data-slide="prev">
				<span class="icon-prev"></span>
			</a>
			<a class="vc_right vc_carousel-control" href="#<?php echo $carousel_id ?>" data-slide="next">
				<span class="icon-next"></span>
			</a>
			<?php endif; ?>
		</div>
	</div>
</div>
<?php return; ?>