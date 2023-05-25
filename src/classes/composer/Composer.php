<?php
use Thunder\Shortcode\HandlerContainer\HandlerContainer;
use Thunder\Shortcode\Parser\RegularParser;
use Thunder\Shortcode\Processor\Processor;
use Thunder\Shortcode\Shortcode\ShortcodeInterface;

class Composer {

    public $context;
    public $post_custom_css;
    public $cawobj;
    public $factory = [];
    public $image_sizes_dropdown = [];

    protected static $sc = [];
    protected static $carousel_index = 1;
    public static $shortcode_tags = [];
    public static $sds_action_hooks = [];
    public static $static_shortcode_tags = [];
    public static $VCBackofficeShortcodesAction = [];
    public static $registeredJS = [];
    public static $registeredCSS = [];
    public static $sds_current_hook;
    public static $staticShortcodeHandler;

    public $mode = 'admin_page';
    public $css_class = 'vc_navbar';
    public $controls_filter_name = 'vc_nav_controls';
    public $shortcodeHandler;
    public $seetings_maps = [];

    public $front_js = [];

    public $front_css = [];

    public $row_layouts = [
        ['cells' => '11', 'mask' => '12', 'title' => '1/1', 'icon_class' => 'l_11'],
        ['cells' => '12_12', 'mask' => '26', 'title' => '1/2 + 1/2', 'icon_class' => 'l_12_12'],
        ['cells' => '23_13', 'mask' => '29', 'title' => '2/3 + 1/3', 'icon_class' => 'l_23_13'],
        ['cells' => '13_13_13', 'mask' => '312', 'title' => '1/3 + 1/3 + 1/3', 'icon_class' => 'l_13_13_13'],
        ['cells' => '14_14_14_14', 'mask' => '420', 'title' => '1/4 + 1/4 + 1/4 + 1/4', 'icon_class' => 'l_14_14_14_14'],
        ['cells' => '14_34', 'mask' => '212', 'title' => '1/4 + 3/4', 'icon_class' => 'l_14_34'],
        ['cells' => '14_12_14', 'mask' => '313', 'title' => '1/4 + 1/2 + 1/4', 'icon_class' => 'l_14_12_14'],
        ['cells' => '56_16', 'mask' => '218', 'title' => '5/6 + 1/6', 'icon_class' => 'l_56_16'],
        ['cells' => '16_16_16_16_16_16', 'mask' => '642', 'title' => '1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6', 'icon_class' => 'l_16_16_16_16_16_16'],
        ['cells' => '16_23_16', 'mask' => '319', 'title' => '1/6 + 4/6 + 1/6', 'icon_class' => 'l_16_46_16'],
        ['cells' => '16_16_16_12', 'mask' => '424', 'title' => '1/6 + 1/6 + 1/6 + 1/2', 'icon_class' => 'l_16_16_16_12'],
    ];
    public static $vc_image_allowed_attr = 'image|images|img|button_bg_img|icon_img|spacer_img|thumb_img|banner_image|info_img|btn_img|bg_image_new|video_poster|swatch_trans_bg_img|layer_image';
    public $controls = [
        'add_element',
        'templates',
        'save_backend',
        'preview',
        'frontend',
        'custom_css',
    ];

    public $wpb_js_composer_js_view = [];
    public $wpb_js_composer_automapper = [];

    public function __construct() {

        global $smarty;
        global $globalShortcodeHandler;
        $this->context = Context::getContext();

        self::$sds_action_hooks['wpb_single_image_src'] = [ & $this, 'wpb_single_image_src'];
        self::$sds_action_hooks['wpb_gallery_html'] = ['ComposerBase', 'galleryHTML'];
        self::$sds_action_hooks['delete_image'] = ['Composer', 'delete_uploaded_file'];
        self::$sds_action_hooks['editpost'] = [$this, 'update_content_frontend'];
        self::$sds_action_hooks['wpb_save_css_values'] = [$this, 'updateCSSValues'];
        self::$sds_action_hooks['wpb_get_preview_link'] = [$this, 'getPreviewLink'];
        self::$sds_action_hooks['vcca_ajax_get_products'] = [$this, 'getProductsList'];
        self::$sds_action_hooks['vc_get_autocomplete_suggestion'] = [$this, 'vc_get_autocomplete_suggestion'];

        $this->front_js[] = _EPH_JS_DIR_ . 'composer/composer_front.js';

    }

    public function buildHandler() {

        $arrayExclude = ['vc_single_image', 'vc_gallery', 'vc_images_carousel'];

        $handlers = new HandlerContainer();

        foreach ($this->seetings_maps as $key => $value) {

            if (in_array($key, $arrayExclude)) {
                continue;
            }

            $handlers->add($key, function (ShortcodeInterface $s) {

                $args = [
                    'full_width',
                    'gap',
                    'columns_placement',
                    'full_height',
                    'equal_height',
                    'content_placement',
                    'parallax',
                    'font_color',

                ];

                $key = $s->getName();
                $value = $this->seetings_maps[$key];

                $class = $key . ' wb_' . $value['type'];

                if ($value['type'] == 'row') {
                    $class .= ' vc_row-fluid';
                }

                if ($value['type'] == 'column') {
                    $width = $this->translateColumnWidthToSpan($s->getParameter('width'));
                    $class .= ' ' . $width;
                }

                $el_id = $s->getParameter('el_id');

                if (!empty($el_id)) {
                    $el_id = 'id="' . $el_id . '" ';
                }

                $el_class = $s->getParameter('el_class');

                if (!empty($el_id)) {
                    $class .= ' ' . $el_class;
                }

                $css = $s->getParameter('css');

                if (!empty($css)) {
                    $classCss = explode('{', $css);
                    $css = '<style>' . $css . '</style>';
                    $class .= ' ' . $classCss[0];
                }

                $css_animation = $s->getParameter('css_animation');

                if (!empty($css_animation)) {
                    $css_animation = 'wpb_animate_when_almost_visible wpb_' . $css_animation.' '. $css_animation;
                    $class .= ' ' . $css_animation;
                }

                $attribute = '';
                $option = [];

                foreach ($args as $arg) {
                    $option[$arg] = $s->getParameter($arg);

                    if (!empty($option[$arg])) {
                        $attribute .= 'data-vc-' . $arg . '="' . $option[$arg] . '" ';

                        if ($key == 'vc_row') {
                            $flex_row = false;
                            $full_height = false;

                            switch ($arg) {
                            case 'full_width':
                                $class .= ' data-vc-full-width="true" data-vc-full-width-init="false"';

                                if ('stretch_row_content' === $option[$arg]) {
                                    $attribute .= 'data-vc-stretch-content="true"';
                                } else

                                if ('stretch_row_content_no_spaces' === $option[$arg]) {
                                    $attribute .= 'data-vc-stretch-content="true"';
                                    $class .= ' vc_row-no-padding';
                                }

                                break;
                            case 'full_height':
                                $full_height = true;
                                $class .= ' vc_row-o-full-height';

                                break;
                            case 'equal_height':
                                $flex_row = true;
                                $class .= ' vc_row-o-equal-height';
                                break;
                            case 'content_placement':
                                $flex_row = true;
                                $class .= ' vc_row-o-content-' . $option[$arg];
                                break;
                            case 'columns_placement':
                                $classToAdd = ' vc_row-o-content-' . $option[$arg];
                                break;

                            }

                            if ($full_height && !empty($classToAdd)) {
                                $class .= $classToAdd;
                            }

                        }

                    }

                }

                $output = $css;
                $output .= '<div ' . $el_id . ' class="' . $class . '" ' . $attribute . '>';

                if ($key != 'vc_row') {
                    $output .= '<div class="wpb_wrapper">';
                }

                $output .= $s->getContent();

                if ($key != 'vc_row') {
                    $output .= '</div>';
                }

                $output .= '</div>';
                return $output;
            });

        }

        $handlers->add('vc_single_image', function (ShortcodeInterface $s) {

            $args = [
                'full_width',
                'gap',
                'columns_placement',
                'full_height',
                'equal_height',
                'content_placement',
                'parallax',
                'font_color',

            ];

            $class = 'wpb_single_image wpb_content_element';

            $el_id = $s->getParameter('el_id');

            if (!empty($el_id)) {
                $el_id = 'id="' . $el_id . '" ';
            }

            $el_class = $s->getParameter('el_class');

            if (!empty($el_id)) {
                $class .= ' ' . $el_class;
            }

            $css = $s->getParameter('css');

            if (!empty($css)) {
                $classCss = explode('{', $css);
                $css = '<style>' . $css . '</style>';
                $class .= ' ' . $classCss[0];
            }

            $css_animation = $s->getParameter('css_animation');

            if (!empty($css_animation)) {
                $css_animation = 'wpb_animate_when_almost_visible wpb_' . $css_animation.' '. $css_animation;
                $class .= ' ' . $css_animation;
            }

            $attribute = '';
            $option = [];

            foreach ($args as $arg) {
                $option[$arg] = $s->getParameter($arg);

                if (!empty($option[$arg])) {
                    $attribute .= 'data-vc-' . $arg . '="' . $option[$arg] . '" ';
                }

            }

            $alignement = 'vc_align_left';
            $align_key = $s->getParameter('alignment');

            if (!empty($align_key)) {
                $alignement = 'vc_align_' . $align_key;

            }

            $class .= ' ' . $alignement;

            $image = $s->getParameter('image');

            if (is_string($image)) {
                $image = [$image];
            }

            $imageLinks = Composer::fieldAttachedImages($image);
            $borderClass = 'vc_box_border_grey';
            $border_color = $s->getParameter('border_color');

            if (!empty($border_color)) {
                $borderClass = 'vc_box_border_' . $border_color;
            }

            $output = $css;
            $output .= '<div ' . $el_id . ' class="' . $class . '" ' . $attribute . '><div class="wpb_wrapper">';

            foreach ($imageLinks as $src) {
                $output .= '<img class="' . $borderClass . '" alt="" src="' . $src . '">';
            }

            $output .= '</div></div>';

            return $output;

        });

        $handlers->add('vc_gallery', function (ShortcodeInterface $s) {

            $args = [
                'full_width',
                'gap',
                'columns_placement',
                'full_height',
                'equal_height',
                'content_placement',
                'parallax',
                'font_color',

            ];

            $class = 'wpb_gallery wpb_content_element vc_clearfix';

            $el_id = $s->getParameter('el_id');

            if (!empty($el_id)) {
                $el_id = 'id="' . $el_id . '" ';
            }

            $el_class = $s->getParameter('el_class');

            if (!empty($el_id)) {
                $class .= ' ' . $el_class;
            }

            $css = $s->getParameter('css');

            if (!empty($css)) {
                $classCss = explode('{', $css);
                $css = '<style>' . $css . '</style>';
                $class .= ' ' . $classCss[0];
            }

            $attribute = '';
            $option = [];

            foreach ($args as $arg) {
                $option[$arg] = $s->getParameter($arg);

                if (!empty($option[$arg])) {
                    $attribute .= 'data-vc-' . $arg . '="' . $option[$arg] . '" ';
                }

            }

            $img_size = $s->getParameter('img_size');

            $image = $s->getParameter('image');

            if (is_string($image)) {
                $image = [$image];
            }

            $imageLinks = Composer::fieldAttachedImages($image);

            $type = $s->getParameter('type');
            $interval = $s->getParameter('interval');

            $custom_links_target = $s->getParameter('custom_links_target');
            $eventclick = $s->getParameter('eventclick');

            $output = $css;
            $output .= '<div ' . $el_id . ' class="' . $class . '" ' . $attribute . '><div class="wpb_wrapper">';
            $output .= '<div class="wpb_gallery_slides wpb_flexslider ' . $type . ' flexslider" data-interval="' . $interval . '" data-flex_fx="fade">
                <ul class="slides">';

            foreach ($imageLinks as $src) {
                $output .= '<li>
                        <a class="prettyphoto" href="/content/img/composer/Artistic-Putty-One-Colour-Application---YouTube-1080p-00_01_41_19-Still010.jpg" rel="prettyPhoto[rel-2064136127]">
                            <img class="" alt="" src="' . $src . '">
                        </a>
                    </li>';
                $output .= '<img class="" alt="" src="' . $src . '">';
            }

            $output .= '</ul></div>';
            $output .= '</div></div>';

            return $output;

        });

        $handlers->add('vc_images_carousel', function (ShortcodeInterface $s) {

            $class = 'vc_slide vc_images_carousel';
            $el_class = $s->getParameter('el_class');

            if (!empty($el_class)) {
                $class .= ' ' . $el_class;
            }

            $dataInterval = 'data-interval="' . $s->getParameter('speed') . '"';
            $wrapSize = 'data-wrap="false" style="width: 100%;"';
            $imgSize = null;
            $img_size = $s->getParameter('img_size');
            $tagSlideline = 'style="width: 400px;"';
            $tagvc_item = 'style="width: 50%; height: 205px;"';

            if ($img_size != 'default') {
                $imgSize = $img_size;
                $sliderWidth = $this->getSliderWidth($img_size);
                $wrapSize = 'data-wrap="true" style="width: ' . $sliderWidth . ';"';

            }

            $images = explode(",", $s->getParameter('images'));

            $imageLinks = Composer::fieldAttachedImages($images, $imgSize);

            $custom_links_target = 'target="' . $s->getParameter('custom_links_target') . '"';
            $aClass = 'class="prettyphoto"';
            $custom_links = $s->getParameter('custom_links');

            if (!empty($custom_links)) {
                $aClass = '';
            }

            $eventclick = $s->getParameter('eventclick');

            $slidesPerView = $s->getParameter('slides_per_view');

            $slides_per_view = 'data-per-view="' . $slidesPerView . '"';

            if ($slidesPerView > 1) {
                $class .= ' vc_per-view-more vc_per-view-' . $slidesPerView;
            }

            $dataMode = $s->getParameter('mode');

            if ($dataMode == 'vertical') {
                $class .= ' vc_carousel_' . $dataMode;

                if ($img_size != 'default') {
                    $sliderHeight = $this->getSliderHeight($img_size) + 2;
                    $tagvc_item = 'style="height: ' . $sliderHeight . 'px;"';
                    $heightSlideline = (count($images) + 1) * $sliderHeight;
                    $tagSlideline = 'style="height: ' . $heightSlideline . 'px;"';

                }

            }

            $dataMode = 'data-mode="' . $dataMode . '"';
            $class .= ' vc_build';

            $Idcarousel = 'vc_images-carousel-' . Composer::getCarouselIndex();

            $tag_autoplay = '';
            $tag_autoHigh = 'data-hide-on-end="true"';
            $autoplay = $s->getParameter('autoplay');

            if (!empty($autoplay) && $autoplay == 'yes') {
                $tag_autoplay = 'data-auto-height="yes"';
                $tag_autoHigh = 'data-hide-on-end="true"';
            }

            $partialView = 'data-partial="false"';
            $partial_view = $s->getParameter('partial_view');

            if (!empty($partial_view) && $partial_view == 'true') {
                $partialView = 'data-partial="true"';
            }

            $hide_pagination_control = $s->getParameter('hide_pagination_control');
            $hide_prev_next_buttons = $s->getParameter('hide_prev_next_buttons');

            $output = '<div class="wpb_images_carousel wpb_content_element vc_clearfix">';
            $output .= '<div class="wpb_wrapper">';
            $output .= '<div id="' . $Idcarousel . '" class="' . $class . '" data-ride="vc_carousel" ' . $wrapSize . ' ' . $dataInterval . ' ' . $tag_autoplay . ' ' . $dataMode . ' ' . $partialView . ' ' . $slides_per_view . ' ' . $tag_autoHigh . '>';

            if ($hide_pagination_control !== 'yes') {
                $output .= '<ol class="vc_carousel-indicators">';

                for ($z = 0; $z < count($imageLinks); $z++) {
                    $output .= '<li data-target="#' . $Idcarousel . '" data-slide-to="' . $z . '"></li>';
                }

                $output .= '</ol>';

            }

            $output .= '<div class="vc_carousel-inner">';
            $output .= '<div class="vc_carousel-slideline" ' . $tagSlideline . '>';
            $output .= '<div class="vc_carousel-slideline-inner">';

            foreach ($imageLinks as $src) {
                $output .= '<div class="vc_item" ' . $tagvc_item . '>';
                $output .= '<div class="vc_inner">';
                $output .= '<a ' . $aClass . ' href="' . $src . '" rel="prettyPhoto[rel-2064136127]" ' . $custom_links_target . '>';
                $output .= '<img src="' . $src . '">';
                $output .= '</a>';
                $output .= '</div>';
                $output .= '</div>';
            }

            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';

            if ($hide_prev_next_buttons !== 'yes') {
                $output .= '<a class="vc_left vc_carousel-control" href="#vc_images-carousel-2-1581579735" data-slide="prev">';
                $output .= '<span class="icon-prev"></span>';
                $output .= '</a>';
                $output .= '<a class="vc_right vc_carousel-control" href="#vc_images-carousel-2-1581579735" data-slide="next">';
                $output .= '<span class="icon-next"></span>';
                $output .= '</a>';
            }

            $output .= '</div>';
            $output .= '</div>';
            $output .= '</div>';

            return $output;

        });

        return $handlers;

    }

    protected function getSliderWidth($size) {

        $width = '100%';
        $types = ComposerImageType::getImageTypeByName($size);

        if (isset($types)) {
            $width = $types['width'] . 'px';
        }

        return $width;
    }

    protected function getSliderHeight($size) {

        $width = '100%';
        $types = ComposerImageType::getImageTypeByName($size);

        if (isset($types)) {
            $width = $types['height'];
        }

        return $width;
    }

    public static function getCarouselIndex() {

        return self::$carousel_index++ . '-' . time();
    }

    public static function fieldAttachedImages($att_ids = [], $imageSize = null) {

        $links = [];

        foreach ($att_ids as $th_id) {

            $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
                (new DbQuery())
                    ->select('*')
                    ->from('vc_media')
                    ->where('`id_vc_media` = ' . (int) $th_id)
            );

            if (isset($result['base_64']) && !empty($result['base_64'])) {
                $links[$th_id] = $result['base_64'];

            } else
            if (isset($result['file_name']) && !empty($result['file_name'])) {
                $thumb_src = __EPH_BASE_URI__ . 'content/img/composer/';

                if (!empty($result['subdir'])) {
                    $thumb_src .= $result['subdir'];
                }

                $thumb_src .= $result['file_name'];

                if (!empty($imageSize)) {
                    $path_parts = pathinfo($thumb_src);
                    $thumb_src = $path_parts['dirname'] . DIRECTORY_SEPARATOR . $path_parts['filename'] . '-' . $imageSize . '.' . $path_parts['extension'];

                }

                if (empty($result['base_64'])) {
                    $extension = pathinfo($thumb_src, PATHINFO_EXTENSION);
                    $img = new Imagick(_EPH_ROOT_DIR_ . $thumb_src);
                    $imgBuff = $img->getimageblob();
                    $img->clear();
                    $img = base64_encode($imgBuff);
                    $base64 = 'data:image/' . $extension . ';base64,' . $img;
                    $imageType = new ComposerMedia($result['id_vc_media']);
                    $imageType->file_name = $result['file_name'];
                    $imageType->base_64 = $base64;
                    $imageType->subdir = $result['subdir'];

                    foreach (Language::getIDs(false) as $idLang) {
                        $imageType->legend[$idLang] = pathinfo($thumb_src, PATHINFO_FILENAME);
                    }

                    if ($imageType->update()) {
                        $thumb_src = $base64;
                    }

                }

                $links[$th_id] = $thumb_src;
            }

        }

        return $links;
    }

    protected function translateColumnWidthToSpan($width) {

        if (preg_match('/^(\d{1,2})\/12$/', $width, $match)) {
            $w = 'vc_col-sm-' . $match[1];
        } else {
            $w = 'vc_col-sm-';

            switch ($width) {
            case "1/6":
                $w .= '2';
                break;
            case "1/4":
                $w .= '3';
                break;
            case "1/3":
                $w .= '4';
                break;
            case "1/2":
                $w .= '6';
                break;
            case "2/3":
                $w .= '8';
                break;
            case "3/4":
                $w .= '9';
                break;
            case "5/6":
                $w .= '10';
                break;
            case "1/1":
                $w .= '12';
                break;
            default:
                $w = $width;
            }

        }

        return $w;
    }

    public static function shortcode_unautop($pee) {

        $shortcode_tags = self::$static_shortcode_tags;

        if (empty($shortcode_tags) || !is_array($shortcode_tags)) {
            return $pee;
        }

        if (is_null($pee)) {
            return $pee;
        }

        $tagregexp = join('|', array_map('preg_quote', array_keys($shortcode_tags)));
        $pattern = '/'
        . '<p>' // Opening paragraph
         . '\\s*+' // Optional leading whitespace
         . '(' // 1: The shortcode
         . '\\[' // Opening bracket
         . "($tagregexp)"// 2: Shortcode name
         . '(?![\\w-])' // Not followed by word character or hyphen
        // Unroll the loop: Inside the opening shortcode tag
         . '[^\\]\\/]*' // Not a closing bracket or forward slash
         . '(?:'
        . '\\/(?!\\])' // A forward slash not followed by a closing bracket
         . '[^\\]\\/]*' // Not a closing bracket or forward slash
         . ')*?'
        . '(?:'
        . '\\/\\]' // Self closing tag and closing bracket
         . '|'
        . '\\]' // Closing bracket
         . '(?:' // Unroll the loop: Optionally, anything between the opening and closing shortcode tags
         . '[^\\[]*+' // Not an opening bracket
         . '(?:'
        . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
         . '[^\\[]*+' // Not an opening bracket
         . ')*+'
        . '\\[\\/\\2\\]' // Closing shortcode tag
         . ')?'
        . ')'
        . ')'
        . '\\s*+' // optional trailing whitespace
         . '<\\/p>' // closing paragraph
         . '/s';

        return preg_replace($pattern, '$1', $pee);
    }

    public function init() {

        $this->vcallmod();
        $this->add_custom_param_code();
        $this->setMode();
        $this->vc()->initAdmin();
        $this->mapper()->buildShortCodeTag();
        $this->is_admin() && $this->asAdmin();
    }

    protected function asAdmin() {

        $this->backendEditor()->addHooksSettings();
    }

    public static function getInstance() {

        return new Composer();
    }

    public function updateCSSValues() {

        $post_id = Tools::getValue('post_id');
        $id_lang = Tools::getValue('id_lang');
        $css = Tools::getValue('css');
        $type = Tools::getValue('type');
        $optionname = "_wpb_{$type}_{$post_id}_{$id_lang}_css";
        Configuration::updateValue($optionname, $css, true);
        die();
    }

    public function vc() {

        if (!isset($this->factory['vc'])) {
            $vc = new ComposerBase();
            $vc->setTemplatesEditor(new ComposerTemplatesEditor());
            $vc->setEditForm(new ComposerShortcodeEditForm());
            $this->factory['vc'] = $vc;
        }

        return $this->factory['vc'];
    }

    public function backendEditor() {

        if (!isset($this->factory['backend_editor'])) {
            $this->factory['backend_editor'] = new ComposerBackenEditor();
        }

        return $this->factory['backend_editor'];
    }

    public function mapper() {

        if (!isset($this->factory['mapper'])) {
            $this->factory['mapper'] = new ComposerMap();
        }

        return $this->factory['mapper'];
    }

    public function mode() {

        return $this->mode;
    }

    protected function setMode() {

        if ($this->is_admin()) {

            if ($this->vc_action() === 'vc_inline') {
                $this->mode = 'admin_frontend_editor';
            } else {
                $this->mode = 'admin_page';
            }

        } else

        if (Tools::getValue('vc_editable') === 'true') {
            $this->mode = 'page_editable';
        }

    }

    public function vc_action() {

        if ($vc_action = Tools::getValue('vc_action')) {
            return $vc_action;
        }

        return null;
    }

    public function addDefaultTemplates($data) {

        add_default_templates($data);
    }

    public function loadDefaultTemplates() {

        return load_default_templates();
    }

    public function vcallmod() {
       
        if (!is_object($this->cawobj)) {
            $this->cawobj = new ContentAnyWhere();
        }

        $vccaw = $this->cawobj;
        $GetAllplugins_list = [];

        if ($this->is_admin()) {

            if (Tools::getValue('action') == 'wpb_show_edit_form') {
                $params = Tools::getValue('params');

                if (isset($params['execute_plugin'])) {
                    $GetAllplugins_list[] = ['id' => $params['execute_plugin'], 'name' => $params['execute_plugin']];
                }

            } else {
                $GetAllplugins_list = $vccaw->GetAllFilterPlugins();
            }

        } else {
            $GetAllplugins_list = $vccaw->GetAllPlugins();
        }

        if (!empty($GetAllplugins_list)) {
            
            foreach ($GetAllplugins_list as &$value) {
                

                if (!isset($value['id']) || !isset($value['name'])) {
                    $value = ['id' => $value, 'name' => $value];
                }

                Composer::add_shortcode('vc_' . $value['id'], [$this, 'vcallmodcode']);
                $this->vcmaps_init('vc_' . $value['id'], $value['name'], $vccaw);

            }

        }      

    }
    
   
   

    public function vcmaps_init($base = '', $plugin_name = null, $vccaw = null) {

        $hooks = [];

        if (!is_object($this->cawobj)) {
            $this->cawobj = new ContentAnyWhere();
        }

        $vccaw = $this->cawobj;
        $plug_name = str_replace('vc_', '', $base);

        if (empty($plugin_name)) {
            $plugin_name = $plug_name;
        }

        $allhooks = $vccaw->getPluginHookbyedit($plug_name);

        if (isset($allhooks) && !empty($allhooks)) {

            foreach ($allhooks as $hook) {
                $hooks[$hook['name']] = $hook['name'];
            }

        }

        $icon_url = '/includes/plugins/' . $plug_name . '/logo.png';
        $vc = ephenyx_manager();
        $brands_params = [
            'name'     => $plugin_name,
            'base'     => $base,
            'icon'     => $icon_url,
            'category' => 'Modules',
            'params'   => [
                [
                    "type"       => "dropdown",
                    "heading"    => $this->l("Executed Hook"),
                    "param_name" => "execute_hook",
                    "value"      => $hooks,
                ], [
                    "type"       => "vc_hidden_field",
                    "param_name" => "execute_plugin",
                    "def_value"  => $plug_name,
                    "value"      => $plug_name,
                ],
            ],
        ];
        ephenyx_mapper()->map($brands_params['base'], $brands_params);
    }

    public function vcallmodcode($atts, $content = null) {

        extract(Composer::shortcode_atts([
            'execute_hook'   => '',
            'execute_plugin' => '',
        ], $atts));

        if (!is_object($this->cawobj)) {
            $this->cawobj = new ContentAnyWhere();
        }

        $vccaw = $this->cawobj;
        $results = $vccaw->ModHookExec($execute_plugin, $execute_hook);
        return $results;
    }

    public static function vc_content_filter($content = '') {

        $content = Composer::do_shortcode($content);

        return $content;
    }

    public function add_custom_param_code() {

        Composer::add_shortcode_param('vc_hidden_field', [$this, 'vc_hidden_fields_func']);
        Composer::add_shortcode_param('vc_product_fileds', [$this, 'vc_product_fileds_func']);
        Composer::add_shortcode_param('vc_category_fileds', [$this, 'vc_category_fileds_func']);
        Composer::add_shortcode_param('vc_brands_fileds', [$this, 'vc_brands_fileds_func']);
        Composer::add_shortcode_param('vc_supplier_fileds', [$this, 'vc_supplier_fileds_func']);
    }

    public function vc_hidden_fields_func($settings, $value) {

        
        $outputcontent = '<input type="hidden" name="' . $settings['param_name'] . '" value="' . $settings['def_value'] . '" class="wpb_vc_param_value wpb-textinput ' . $settings['param_name'] . ' ' . $settings['type'] . '_field">';
        return $outputcontent;
    }

    public function is_admin() {

        if (isset($_SERVER['REQUEST_URI'])) {
            $request_uri = $_SERVER['REQUEST_URI'];
        } else

        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $request_uri = $_SERVER['HTTP_X_REWRITE_URL'];
        }

        if (str_contains($request_uri, 'backend/') || str_contains($request_uri, 'admin')) {
            return true;
        }

        return false;
    }

    public static function content_filter($content = '') {

        $content = Composer::do_shortcode($content);

        return $content;
    }

    public static function remove_shortcode($tag) {

        unset(self::$static_shortcode_tags[$tag]);
    }

    public static function add_shortcode($tag, $func) {

        self::$static_shortcode_tags[$tag] = $func;
    }

    public static function recurseShortCode($content) {

        $handlers = self::$staticShortcodeHandler;
        $processor = new Processor(new RegularParser(), $handlers);
        return $processor->process($content);

    }

    public static function do_shortcode($content, $hook_name = '') {

        if (is_null($content)) {

            return $content;
        }

        $shortcode_tags = self::$static_shortcode_tags;

        if (empty($shortcode_tags) || !is_array($shortcode_tags)) {
            return $content;
        }

        $pattern = ephenyx_manager()->get_shortcode_regex();
        self::$sds_current_hook = $hook_name;

        return preg_replace_callback("/$pattern/s", [__CLASS__, 'do_shortcode_tag'], $content);
    }

    public static function do_shortcode_tag($m) {

        $shortcode_tags = self::$static_shortcode_tags;

        if ($m[1] == '[' && $m[6] == ']') {
            return Tools::substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = self::shortcode_parse_atts($m[3]);

        if (isset($m[5])) {
            return $m[1] . call_user_func($shortcode_tags[$tag], $attr, $m[5], $tag, self::$sds_current_hook) . $m[6];
        } else {

            return $m[1] . call_user_func($shortcode_tags[$tag], $attr, null, $tag, self::$sds_current_hook) . $m[6];
        }

    }

    public function get_shortcode_regex() {

        $shortcode_tags = self::$static_shortcode_tags;
        $tagnames = array_keys($shortcode_tags);
        $tagregexp = join('|', array_map('preg_quote', $tagnames));
        return
        '\\[' // Opening bracket
         . '(\\[?)' // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
         . "($tagregexp)"// 2: Shortcode name
         . '(?![\\w-])' // Not followed by word character or hyphen
         . '(' // 3: Unroll the loop: Inside the opening shortcode tag
         . '[^\\]\\/]*' // Not a closing bracket or forward slash
         . '(?:'
        . '\\/(?!\\])' // A forward slash not followed by a closing bracket
         . '[^\\]\\/]*' // Not a closing bracket or forward slash
         . ')*?'
        . ')'
        . '(?:'
        . '(\\/)' // 4: Self closing tag ...
         . '\\]' // ... and closing bracket
         . '|'
        . '\\]' // Closing bracket
         . '(?:'
        . '(' // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
         . '[^\\[]*+' // Not an opening bracket
         . '(?:'
        . '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
         . '[^\\[]*+' // Not an opening bracket
         . ')*+'
        . ')'
        . '\\[\\/\\2\\]' // Closing shortcode tag
         . ')?'
            . ')'
            . '(\\]?)'; // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
    }

    public static function shortcode_parse_atts($text) {

        $atts = [];
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);

        if (preg_match_all($pattern, $text, $match, PREG_SET_ORDER)) {

            foreach ($match as $m) {

                if (!empty($m[1])) {
                    $atts[strtolower($m[1])] = stripcslashes($m[2]);
                } else

                if (!empty($m[3])) {
                    $atts[strtolower($m[3])] = stripcslashes($m[4]);
                } else

                if (!empty($m[5])) {
                    $atts[strtolower($m[5])] = stripcslashes($m[6]);
                } else

                if (isset($m[7]) and strlen($m[7])) {
                    $atts[] = stripcslashes($m[7]);
                } else

                if (isset($m[8])) {
                    $atts[] = stripcslashes($m[8]);
                }

            }

        } else {
            $atts = ltrim($text);
        }

        return $atts;
    }

    public static function vc_remove_element($tag) {

        remove_element($tag);
    }

    public static function add_shortcode_param($name, $form_field_callback, $script_url = null) {

        return ComposerShortCodeParams::addField($name, $form_field_callback, $script_url);

    }

    public static function strip_shortcodes($content) {

        $shortcode_tags = self::$static_shortcode_tags;

        if (empty($shortcode_tags) || !is_array($shortcode_tags)) {
            return $content;
        }

        $pattern = ephenyx_manager()->get_shortcode_regex();
        return preg_replace_callback("/$pattern/s", [__CLASS__, 'strip_shortcode_tag'], $content);
    }

    public static function strip_shortcode_tag($m) {

        if ($m[1] == '[' && $m[6] == ']') {
            return Tools::substr($m[0], 1, -1);
        }

        return $m[1] . $m[6];
    }

    public static function wpautop($pee, $br = true) {

        $pre_tags = [];

        if (trim($pee) === '') {
            return '';
        }

        $pee = $pee . "\n";

        if (Tools::strpos($pee, '<pre') !== false) {
            $pee_parts = explode('</pre>', $pee);
            $last_pee = array_pop($pee_parts);
            $pee = '';
            $i = 0;

            foreach ($pee_parts as $pee_part) {
                $start = Tools::strpos($pee_part, '<pre');

                if ($start === false) {
                    $pee .= $pee_part;
                    continue;
                }

                $name = "<pre wp-pre-tag-$i></pre>";
                $pre_tags[$name] = Tools::substr($pee_part, $start) . '</pre>';
                $pee .= Tools::substr($pee_part, 0, $start) . $name;
                $i++;
            }

            $pee .= $last_pee;
        }

        $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|details|menu|summary)';
        $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
        $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
        $pee = str_replace(["\r\n", "\r"], "\n", $pee); // cross-platform newlines

        if (Tools::strpos($pee, '<option') !== false) {
            $pee = preg_replace('|\s*<option|', '<option', $pee);
            $pee = preg_replace('|</option>\s*|', '</option>', $pee);
        }

        if (Tools::strpos($pee, '</object>') !== false) {
            // no P/BR around param and embed
            $pee = preg_replace('|(<object[^>]*>)\s*|', '$1', $pee);
            $pee = preg_replace('|\s*</object>|', '</object>', $pee);
            $pee = preg_replace('%\s*(</?(?:param|embed)[^>]*>)\s*%', '$1', $pee);
        }

        if (Tools::strpos($pee, '<source') !== false || Tools::strpos($pee, '<track') !== false) {
            // no P/BR around source and track
            $pee = preg_replace('%([<\[](?:audio|video)[^>\]]*[>\]])\s*%', '$1', $pee);
            $pee = preg_replace('%\s*([<\[]/(?:audio|video)[>\]])%', '$1', $pee);
            $pee = preg_replace('%\s*(<(?:source|track)[^>]*>)\s*%', '$1', $pee);
        }

        $pee = preg_replace("/\n\n+/", "\n\n", $pee);
        $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
        $pee = '';

        foreach ($pees as $tinkle) {
            $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        }

        $pee = preg_replace('|<p>\s*</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
        $pee = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
        $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
        $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
        $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);

        if ($br) {
            $pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', [__CLASS__, '_autop_newline_preservation_helper'], $pee);
            $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
            $pee = str_replace('<WPPreserveNewline />', "\n", $pee);
        }

        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
        $pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $pee);
        $pee = preg_replace("|\n</p>$|", '</p>', $pee);

        if (!empty($pre_tags)) {
            $pee = str_replace(array_keys($pre_tags), array_values($pre_tags), $pee);
        }

        return $pee;
    }

    public static function _autop_newline_preservation_helper($matches) {

        return str_replace("\n", "<WPPreserveNewline />", $matches[0]);
    }

    public function esc_attr($string) {

        return Tools::htmlentitiesUTF8($string);
    }

    public function esc_attr__($string) {

        return $this->esc_attr($this->l($string));
    }

    public function esc_attr_e($string, $textdomain = '') {

        echo $this->esc_attr($string);
    }

    public function lcfirst($str) {

        $str[0] = mb_strtolower($str[0]);
        return $str;
    }

    public function vc_studly($value) {

        $value = Tools::ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }

    public function vc_camel_case($value) {

        return $this->lcfirst($this->vc_studly($value));
    }

    public function getControls() {

        $list = [];

        foreach ($this->controls as $control) {
            $method = $this->vc_camel_case('get_control_' . $control);

            if (method_exists($this, $method)) {
                $list[] = [$control, $this->$method() . "\n"];
            }

        }

        return $list;
    }

    public static function renderComposer($params, $smarty) {

        $context = Context::getContext();
        $controller = Tools::getValue('controller');
        $autolaunch = false;
        $target = $params['target'];

        $autolaunch = $params['autolaunch'];
        $file = fopen("testrenderComposer.txt", "w");
        fwrite($file, $controller);

        switch ($controller) {
        case 'adminsuppliers':
            $page_type = 'sup';
            $post_id = Tools::getValue('id_supplier') ? Tools::getValue('id_supplier') : "null";
            break;
        case 'adminmanufacturers':
            $page_type = 'man';
            $post_id = Tools::getValue('id_manufacturer') ? Tools::getValue('id_manufacturer') : "null";
            break;
        case 'admincategories':
            $page_type = 'cat';
            $post_id = Tools::getValue('id_category') ? Tools::getValue('id_category') : "null";
            break;
        case 'adminproducts':
            $page_type = 'prd';
            $post_id = Tools::getValue('id_product') ? Tools::getValue('id_product') : "null";
            break;
        case 'admincms':
            $post_id = Tools::getValue('id_cms') ? Tools::getValue('id_cms') : "null";
            $page_type = 'cms';
            break;
        case 'adminpagecms':
            $post_id = Tools::getValue('id_page_cms') ? Tools::getValue('id_page_cms') : "null";
            $page_type = 'pagecms';
            break;
        case 'admincontentanywhere':
            $post_id = Tools::getValue('id_contentanywhere') ? Tools::getValue('id_contentanywhere') : "null";
            $page_type = 'vccaw';
            break;
        default:
            $post_id = 0;
            $page_type = 'vcdiv';
            break;
        }

        $id_lang = $context->language->id;
        $optname = "_wpb_{$page_type}_{$post_id}_{$id_lang}_css";
        $post_custom_css[$id_lang] = Configuration::get($optname);

        $css_custom_out = (isset($post_custom_css[$id_lang]) && !empty($post_custom_css[$id_lang])) ? $post_custom_css[$id_lang] : '';

        $navBar = new ComposerNavbar();
        ob_start();
        $data = $context->smarty->createTemplate(_EPH_COMPOSER_DIR_ . 'editors/backend_editor.tpl');
        $data->assign(
            [
                'page_type'      => $page_type,
                'post_id'        => $post_id,
                'navBar'         => $navBar->render(),
                'editor'         => ephenyx_manager(),
                'target'         => $target,
                'autolaunch'     => $autolaunch,
                'controller'     => $controller,
                'id_lang'        => $id_lang,
                'css_custom_out' => $css_custom_out,
                'templates'      => Composer::getDefaultTemplates($id_lang),
            ]
        );
        $content = $data->fetch();
        ob_get_clean();

        //$content .= $this->renderEditorFooter();
        return $content;
    }

    public function renderEditorFooter() {

        ob_start();
        $this->init();
        $element_box = new ComposerAddElementBox();
        $editForm = new ComposerShortcodeEditForm();
        $post_settings = new ComposerPostSeetings($this);
        $edit_layout = new ComposerEditLayout();

        $data = $this->context->smarty->createTemplate(_EPH_COMPOSER_DIR_ . 'editors/partials/backend_editor_footer.tpl');
        $data->assign(
            [
                'editor'           => $this,
                'add_element_box'  => $element_box->render($this),
                'editForm'         => $editForm->render(),
                'post_settings'    => $post_settings->render(),
                'edit_layout'      => $edit_layout->render($this),
                'shortCodes'       => ComposerMap::getShortCodes(),
                'map'              => new ComposerMap(),
                'ephenyx_composer' => ephenyx_composer(),
            ]
        );
        return $data->fetch();
        ob_get_clean();

    }

    public function vc_include_template($file, $args) {

        extract($args);
        require $this->vc_path_dir('TEMPLATES_DIR', $file);
    }

    public static function getDefaultTemplates($id_lang) {

        return new PhenyxCollection('ComposerTemplatesEditor', $id_lang);

    }

    public static function getPhenyxImgSizesOption() {

        $db = Db::getInstance();
        $tablename = _DB_PREFIX_ . 'image_type';
        $sizes = $db->executeS("SELECT name FROM {$tablename} ORDER BY name ASC");
        $options = ['Default' => ''];

        if (!empty($sizes)) {

            foreach ($sizes as $size) {
                $options[$size['name']] = $size['name'];
            }

        }

        return $options;
    }

    public static function productIdAutocompleteRender($query) {

        if (!empty($query['value'])) {
            $elemid = $elemName = '';
            $context = Context::getContext();

            switch ($query['type']) {
            case 'product':
                $product = new Product((int) $query['value']);

                if (!empty($product) && isset($product->name)) {
                    $elemid = (int) $query['value'];
                    $elemName = $product->name[$context->language->id];
                }

                break;
            case 'category':
                $cat = new Category((int) $query['value']);

                if (!empty($cat) && isset($cat->name)) {
                    $elemid = (int) $query['value'];
                    $elemName = $cat->name[$context->language->id];
                }

                break;
            case 'manufacturer':
                $man = new Manufacturer((int) $query['value']);

                if (!empty($man) && isset($man->name)) {
                    $elemid = (int) $query['value'];
                    $elemName = $man->name;
                }

                break;
            case 'supplier':
                $sup = new Supplier((int) $query['value']);

                if (!empty($sup) && isset($sup->name)) {
                    $elemid = (int) $query['value'];
                    $elemName = $sup->name;
                }

                break;
            }

            if (!empty($elemid)) {
                return [$elemid, $elemName];
            }

        }

        return false;
    }

    public function vc_post_param($param, $default = null) {

        return Tools::getValue($param) ? Tools::getValue($param) : $default;
    }

    public function vc_asset_url($url) {

        return _SHOP_ROOT_DIR_ . _EPH_THEMES_DIR_ . $url;
    }

    public static function admin_shortcode_atts($pairs, $atts, $shortcode = '') {

        $out = self::shortcode_atts($pairs, $atts, $shortcode);

        if (isset($atts['content'])) {
            $out['content'] = $atts['content'];
        }

        return $out;
    }

    public static function shortcode_atts($pairs, $atts, $shortcode = '') {

        $atts = (array) $atts;

        $out = [];

        foreach ($pairs as $name => $default) {

            if (array_key_exists($name, $atts)) {
                $out[$name] = $atts[$name];
            } else {
                $out[$name] = $default;
            }

        }

        return $out;
    }

    public function generateImageSizesArray() {

        $sizes = array_merge([['name' => 'default']], VcImageType::getImagesTypes());

        if (!empty($sizes)) {

            foreach ($sizes as $size) {

                if (isset($size['width'])) {
                    $this->image_sizes[$size['name']] = "{$size['width']}x{$size['height']}";
                }

                $this->image_sizes_dropdown[$size['name']] = $size['name'];
            }

        }

    }

    public function getLogo() {

        $output = '<a id="vc_logo" class="vc_navbar-brand" title="' . $this->esc_attr('Visual Composer', 'js_composer')
        . '" href="' . $this->esc_attr($this->brand_url) . '" target="_blank">'
        . $this->l('Visual Composer') . '</a>';
        return $output;
    }

    public function getControlCustomCss() {

        return '<li class="vc_pull-right"><a id="vc_post-settings-button" class="vc_icon-btn vc_post-settings" title="'
        . $this->esc_attr('Page settings', 'js_composer') . '">'
        . '<span id="vc_post-css-badge" class="vc_badge vc_badge-custom-css" style="display: none;">' . $this->l('CSS') . '</span></a>'
            . '</li>';
    }

    public function getControlAddElement() {

        return '<li class="vc_show-mobile">'
        . ' <a href="javascript:;" class="vc_icon-btn vc_element-button" data-model-id="vc_element" id="vc_add-new-element" title="'
        . '' . $this->l('Add new element') . '">'
            . ' </a>'
            . '</li>';
    }

    public function getControlTemplates() {

        return '<li><a href="javascript:;" class="vc_icon-btn vc_templates-button vc_navbar-border-right"  id="vc_templates-editor-button" title="'
        . $this->l('Templates') . '"></a></li>';
    }

    public function getControlFrontend() {

        if (!function_exists('enabled_frontend')) {
            return false;
        }

        return '<li class="vc_pull-right">'
        . '<a href="' . ephenyx_frontend_editor()->getInlineUrl() . '" class="vc_btn vc_btn-primary vc_btn-sm vc_navbar-btn" id="wpb-edit-inline">' . __('Frontend', "js_composer") . '</a>'
            . '</li>';
    }

    public function getControlPreview() {

        return '';
    }

    public function getControlSaveBackend() {

        return '<li class="vc_pull-right vc_save-backend">'
        . '<a href="javascript:;" class="vc_btn vc_btn-grey vc_btn-sm vc_navbar-btn vc_control-preview">' . $this->l('Preview') . '</a>'
        . '<a class="vc_btn vc_btn-sm vc_navbar-btn vc_btn-primary vc_control-save" id="wpb-save-post">' . $this->l('Update') . '</a>'
            . '</li>';
    }

    public static function getComposerImageTypes() {

        $images_types = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('*')
                ->from('vc_image_type')
                ->orderBy('`name` ASC')
        );
        $values = [];
        $values[] = [
            'value_key' => '',
            'name'      => '',
        ];

        foreach ($images_types as $type) {
            $values[] = [
                'value_key' => $type['name'],
                'name'      => $type['name'],
            ];

        }

        return $values;
    }

    public static function getTPLPath($template = '', $plugin_name = 'phenyxthememanager') {

        if (Tools::file_exists_cache(_EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/' . $template)) {
            return _EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/' . $template;
        } else if (Tools::file_exists_cache(_EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/views/templates/front/' . $template)) {
            return _EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/views/templates/front/' . $template;
        } else if (Tools::file_exists_cache(_EPH_PLUGIN_DIR_ . $plugin_name . '/views/templates/front/' . $template)) {
            return _EPH_PLUGIN_DIR_ . $plugin_name . '/views/templates/front/' . $template;
        }

        return false;
    }

    public function wpb_single_image_src() {

        if (Tools::getValue('content') && is_numeric(Tools::getValue('content'))) {
            $image_src = '/content/img/composer/' . Tools::get_media_thumbnail_url(Tools::getValue('content'));
            echo Tools::ModifyImageUrl($image_src);
            die();
        }

    }

    public function l($string, $idLang = null, Context $context = null) {

        $class = 'Composer';

        return Translate::getClassTranslation($string, $class);
    }

}
