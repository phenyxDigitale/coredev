<?php
global $vc_teaser_box;
$grid_link = $grid_layout_mode = $title = $filter = '';
$vc_manager = ephenyx_manager();
$posts = array();
extract( Composer::shortcode_atts( array(
	'title' => '',
	'grid_columns_count' => 4,
	'grid_teasers_count' => 8,
	'grid_layout' => 'title,thumbnail,text', // title_thumbnail_text, thumbnail_title_text, thumbnail_text, thumbnail_title, thumbnail, title_text
	'grid_link_target' => '_self',
	'filter' => '', //grid,
	'grid_thumb_size' => '',
	'grid_layout_mode' => 'fitRows',
	'el_class' => '',
	'teaser_width' => '12',
	'orderby' => NULL,
	'order' => 'DESC',
	'loop' => '',
), $atts ) );
$this->resetTaxonomies();
if ( empty( $loop ) ) return;
$this->getLoop( $loop );
$my_query = $this->query;
$args = $this->loop_args;
$teaser_blocks = vc_sorted_list_parse_value( $grid_layout );


$smartblog_url = _PLUGIN_DIR_.'smartblog/images/';

foreach ( $my_query as $qpost ) {

	$post = new stdClass(); 
	$post->id = (int)$qpost['id_smart_blog_post'];
	$post->link = smartblog::GetSmartBlogLink('smartblog_post',array('id_post'=>$post->id , 'slug' =>$qpost['link_rewrite']));  	
        $post->custom_user_teaser = false;
        $post->title = $qpost['meta_title'];
        $post->title_attribute = $vc_manager->esc_attr($post->title);
        $post->post_type = 0;
        $post->content = $qpost['content'];
        $post->excerpt = $qpost['short_description'];
        $post->thumbnail_data = $this->getPostThumbnail( $post->id, $grid_thumb_size );
        $post->thumbnail = "<img alt='{$post->title_attribute}' src='{$smartblog_url}{$post->thumbnail_data}' />";

        $post->image_link = $smartblog_url.$this->getPostThumbnail( $post->id);

	$post->categories_css = $this->getCategoriesCss( $post->id );

	$posts[] = $post;
}

$post_types_teasers = '';
if ( ! empty( $args['post_type'] ) && is_array( $args['post_type'] ) ) {
	foreach ( $args['post_type'] as $post_type ) {
		$post_types_teasers .= 'wpb_teaser_grid_' . $post_type . ' ';
	}
}
$el_class = $this->getExtraClass( $el_class );
$li_span_class = $this->spanClass( $grid_columns_count );

$css_class = 'wpb_row wpb_teaser_grid wpb_content_element ' .
  $this->getMainCssClass( $filter ) .
  ' columns_count_' . $grid_columns_count . 
  ' columns_count_' . $grid_columns_count . 
  ' ' . $post_types_teasers . 
  $el_class; 

$this->setLinktarget( $grid_link_target );

?>
<div
  class="<?php echo  $css_class ?>">
	<div class="wpb_wrapper">
		<?php echo widget_title( array( 'title' => $title, 'extraclass' => 'wpb_teaser_grid_heading' ) ) ?>
		<div class="teaser_grid_container">
			<?php if ( $filter === 'yes' && ! empty( $this->filter_categories ) ):
			$categories_array = $this->getFilterCategories();
			?>
			<ul class="categories_filter vc_col-sm-12 vc_clearfix">
				<li class="active"><a href="#" data-filter="*"><?php echo $this->l('All') ?></a></li>
				<?php foreach ( $this->getFilterCategories() as $cat ): ?>
				<li><a href="#"
					   data-filter=".grid-cat-<?php echo $cat->term_id ?>"><?php echo esc_attr( $cat->name ) ?></a></li>
				<?php endforeach; ?>
			</ul>
			<div class="vc_clearfix"></div>
			<?php endif; ?>
			<ul class="wpb_thumbnails wpb_thumbnails-fluid vc_clearfix"
				data-layout-mode="<?php echo $grid_layout_mode ?>">
				<?php
				
				Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_.'/composer/isotope/dist/isotope.pkgd.min.js' );
				Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_.'/composer/isotope.css' );
				?>
				<?php if ( count( $posts ) > 0 ): ?>
				<?php foreach ( $posts as $post ): ?>
					<?php
					$blocks_to_build = $post->custom_user_teaser === true ? $post->custom_teaser_blocks : $teaser_blocks;
					$block_style = isset( $post->bgcolor ) ? ' style="background-color: ' . $post->bgcolor . '"' : '';
					?>
					<li
					  class="isotope-item <?php echo  $li_span_class . $post->categories_css ?>"<?php echo $block_style ?>>
						<div class="isotope-inner">
						<?php foreach ( $blocks_to_build as $block_data ): ?>
							<?php include $this->getBlockTemplate() ?>
						<?php endforeach; ?>
						</div>
					</li> <?php echo $this->endBlockComment( 'single teaser' ); ?>
					<?php endforeach; ?>
				<?php else: ?>
				<li class="<?php echo $this->spanClass( 1 ); ?>"><?php echo $this->l('Nothing found.') ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div> <?php echo $this->endBlockComment( '.wpb_wrapper' ) ?>
	<div class="clear"></div>
</div> <?php echo $this->endBlockComment( '.wpb_teaser_grid' ) ?>