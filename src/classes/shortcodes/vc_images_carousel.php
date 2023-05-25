<?php
$vc_manager = ephenyx_manager();
$output = $title = $eventclick = $custom_links = $img_size = $custom_links_target = $images = $el_class = $partial_view = '';
$mode = $slides_per_view = $wrap = $autoplay = $hide_pagination_control = $hide_prev_next_buttons = $speed = '';
extract(Composer::shortcode_atts(array(
            'title' => '',
            'eventclick' => 'link_image',
            'custom_links' => '',
            'custom_links_target' => '',
            'img_size' => 'thumbnail',
            'images' => '',
            'el_class' => '',
            'mode' => 'horizontal',
            'slides_per_view' => '1',
            'wrap' => '',
            'autoplay' => '',
            'hide_pagination_control' => '',
            'hide_prev_next_buttons' => '',
            'speed' => '5000',
            'partial_view' => ''
                ), $atts));
$gal_images = '';
$link_start = '';
$link_end = '';
$el_start = '';
$el_end = '';
$slides_wrap_start = '';
$slides_wrap_end = '';
$pretty_rand = $eventclick == 'link_image' ? rand() : '';

    $vc_manager->front_css[] = _EPH_JS_DIR_ . 'composer/vc_carousel/css/vc_carousel.css';
    $vc_manager->front_js[] = _EPH_JS_DIR_ . 'composer/vc_carousel/js/transition.js';
    $vc_manager->front_js[] = _EPH_JS_DIR_ . 'composer/vc_carousel/js/vc_carousel.js';
    Context::getContext()->controller->addCSS(_EPH_JS_DIR_ . 'composer/vc_carousel/css/vc_carousel.css');
    Context::getContext()->controller->addJS(_EPH_JS_DIR_ . 'composer/vc_carousel/js/transition.js');
    Context::getContext()->controller->addJS(_EPH_JS_DIR_ . 'composer/vc_carousel/js/vc_carousel.js');


if ($eventclick == 'link_image') {
        $vc_manager->front_css[] = _EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/css/prettyPhoto.css';
        $vc_manager->front_js[] = _EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/js/jquery.prettyPhoto.js';
        Context::getContext()->controller->addCSS(_EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/css/prettyPhoto.css');
        Context::getContext()->controller->addJS(_EPH_ADMIN_THEME_DIR_. '/composer/prettyphoto/js/jquery.prettyPhoto.js');


}

$el_class = $this->getExtraClass($el_class);

if ($images == '') return false;
//    $images = '-1,-2,-3';

if ($eventclick == 'custom_link') {
    $custom_links = explode(',', $custom_links);
}

$images = explode(',', $images);
$i = - 1;
$css_class = 'wpb_images_carousel wpb_content_element' . $el_class . ' vc_clearfix';
$carousel_id = 'vc_images-carousel-' . ComposerShortCode_vc_images_carousel::getCarouselIndex();
$slider_width = $this->getSliderWidth($img_size);
?>
<div class="<?php echo $css_class ?>">
    <div class="wpb_wrapper">
<?php echo widget_title(array('title' => $title, 'extraclass' => 'wpb_gallery_heading')) ?>
        <div id="<?php echo $carousel_id ?>" data-ride="vc_carousel"
             data-wrap="<?php echo $wrap === 'yes' ? 'true' : 'false' ?>" style="width: <?php echo $slider_width ?>;"
             data-interval="<?php echo $autoplay == 'yes' ? $speed : 0 ?>" data-auto-height="yes"
             data-mode="<?php echo $mode ?>" data-partial="<?php echo $partial_view === 'yes' ? 'true' : 'false' ?>"
             data-per-view="<?php echo $slides_per_view ?>"
             data-hide-on-end="<?php echo $autoplay == 'yes' ? 'false' : 'true' ?>" class="vc_slide vc_images_carousel">
<?php if ($hide_pagination_control !== 'yes'): ?>
                <!-- Indicators -->
                <ol class="vc_carousel-indicators">
                    <?php for ($z = 0; $z < count($images); $z++): ?>
                        <li data-target="#<?php echo $carousel_id ?>" data-slide-to="<?php echo $z ?>"></li>
                <?php endfor; ?>
                </ol>
<?php endif; ?>
            <!-- Wrapper for slides -->
            <div class="vc_carousel-inner">
                <div class="vc_carousel-slideline">
                    <div class="vc_carousel-slideline-inner">
                        <?php foreach ($images as $attach_id): ?>
                            <?php
                            $i++;
                            if ($attach_id > 0) {
                                $post_thumbnail = getImageBySize(array('attach_id' => $attach_id, 'thumb_size' => $img_size));
                            } else {
                                $post_thumbnail = array();
                                $post_thumbnail['thumbnail'] = '<img src="' . vc_asset_url('vc/no_image.png') . '" />';
                                $post_thumbnail['p_img_large'] = vc_asset_url('vc/no_image.png');
                            }
                            $thumbnail = isset($post_thumbnail['thumbnail']) ? $post_thumbnail['thumbnail'] : '';
                            ?>
                            <div class="vc_item">
                                <div class="vc_inner">
                                    <?php if ($eventclick == 'link_image'): ?>
        <?php $p_img_large = isset($post_thumbnail['p_img_large']) ? $post_thumbnail['p_img_large'] : ''; ?>
                                        <a class="prettyphoto"
                                           href="<?php echo $p_img_large ?>" <?php echo ' rel="prettyPhoto[rel-' . $pretty_rand . ']"' ?> target="_self">
                                        <?php echo $thumbnail ?>
                                        </a>
    <?php elseif ($eventclick == 'custom_link' && isset($custom_links[$i]) && $custom_links[$i] != ''): ?>
                                        <a
                                            href="<?php echo $custom_links[$i] ?>"<?php echo (!empty($custom_links_target) ? ' target="' . $custom_links_target . '"' : '' ) ?>>
                                        <?php echo $thumbnail ?>
                                        </a>
                                    <?php else: ?>
                                        <?php echo $thumbnail ?>
    <?php endif; ?>
                                </div>
                            </div>
<?php endforeach; ?>
                    </div>
                </div>
            </div>
<?php if ($hide_prev_next_buttons !== 'yes'): ?>
                <!-- Controls -->
                <a class="vc_left vc_carousel-control" href="#<?php echo $carousel_id ?>" data-slide="prev">
                    <span class="icon-prev"></span>
                </a>
                <a class="vc_right vc_carousel-control" href="#<?php echo $carousel_id ?>" data-slide="next">
                    <span class="icon-next"></span>
                </a>
<?php endif; ?>
        </div>
    </div><?php echo $this->endBlockComment('.wpb_wrapper') ?>
</div><?php echo $this->endBlockComment('.wpb_images_carousel') ?>