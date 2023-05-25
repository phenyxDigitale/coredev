<?php

$revslider_fonts = ['queue' => [], 'loaded' => []];
$revslider_is_preview_mode = false;
$revslider_save_post = false;
$revslider_addon_notice_merged = 0;

class RevLoader {

    public static $hook_args;
    public static $hook_values, $filter_values, $hook_register, $hook_deregister;
    public static $admin_scripts = [], $admin_scripts_foot = [], $front_scripts_foot = [], $front_scripts = [], $front_styles = [], $admin_styles = [], $local_scripts = [], $local_scripts_footer = [], $registered_script, $registered_style, $current_filter, $current_action, $blank_scripts = [], $blank_styles = [];
    public $headers, $body;
    public static $prestaDbInstance;

    const TABLE_OPTIONS = 'revslider_options';
    const TABLE_SLIDER = 'revslider_slider';
    const TABLE_SLIDES = 'revslider_slide';
    const TABLE_STATIC_SLIDES = 'revslider_static_slide';
    const TABLE_CSS = 'revslider_css';
    const TABLE_LAYER_ANIMATIONS = 'revslider_layer_animations';
    const TABLE_NAVIGATIONS = 'revslider_navigations';

    public function __construct() {

        $this->headers = '';
        $this->body = '';
    }

    private function streamHeaders($handle, $headers) {

        $this->headers .= $headers;
        return self::strlen($headers);
    }

    function getS3Url() {

        //    return RS_PLUGIN_URL;
        return "https://revsliderapp.s3.us-east-1.amazonaws.com/";
    }

    public static function strlen($str, $encoding = 'UTF-8') {

        if (is_array($str)) {
            return false;
        }

        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');

        if (function_exists('mb_strlen')) {
            return mb_strlen($str, $encoding);
        }

        return strlen($str);
    }

    static function getHtt() {

        $url_custom = Context::getContext()->link->getBaseLink();

        $is_ssl = false;

        if (strpos($url_custom, "https") !== false) {
            $is_ssl = true;
        }

        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 || $is_ssl == true) {
            return 'https:';
        }

        return 'http:';
    }

    static function get_plugin_url($link = '') {

        $url = Context::getContext()->link->getBaseLink() . "includes/plugins/revslider/";
        $double_http = self::getHtt() . '//' . self::getHtt() . '//';
        $url = str_replace($double_http, self::getHtt() . '//', $url);
        return $url;
    }

    private function streamBody($handle, $data) {

        $data_length = strlen($data);
        $this->body .= $data;
        // Upon event of this function returning less than strlen( $data ) curl will error with CURLE_WRITE_ERROR.
        return $data_length;
    }

    public static function shouldDecode($headers) {

        if (is_array($headers)) {

            if (array_key_exists('content-encoding', $headers) && !empty($headers['content-encoding'])) {
                return true;
            }

        } else

        if (is_string($headers)) {
            return (stripos($headers, 'content-encoding:') !== false);
        }

        return false;
    }

    public static function decompress($compressed, $length = null) {

        if (empty($compressed)) {
            return $compressed;
        }

        if (false !== ($decompressed = @gzinflate($compressed))) {
            return $decompressed;
        }

        if (false !== ($decompressed = self::compatibleGzinflate($compressed))) {
            return $decompressed;
        }

        if (false !== ($decompressed = @gzuncompress($compressed))) {
            return $decompressed;
        }

        if (function_exists('gzdecode')) {
            $decompressed = @gzdecode($compressed);

            if (false !== $decompressed) {
                return $decompressed;
            }

        }

        return $compressed;
    }

    public static function compatibleGzinflate($gzData) {

        // Compressed data might contain a full header, if so strip it for gzinflate().

        if (substr($gzData, 0, 3) == "\x1f\x8b\x08") {
            $i = 10;
            $flg = ord(substr($gzData, 3, 1));

            if ($flg > 0) {

                if ($flg & 4) {
                    list($xlen) = unpack('v', substr($gzData, $i, 2));
                    $i = $i + 2 + $xlen;
                }

                if ($flg & 8) {
                    $i = strpos($gzData, "\0", $i) + 1;
                }

                if ($flg & 16) {
                    $i = strpos($gzData, "\0", $i) + 1;
                }

                if ($flg & 2) {
                    $i = $i + 2;
                }

            }

            $decompressed = @gzinflate(substr($gzData, $i, -8));

            if (false !== $decompressed) {
                return $decompressed;
            }

        }

        // Compressed data from java.util.zip.Deflater amongst others.
        $decompressed = @gzinflate(substr($gzData, 2));

        if (false !== $decompressed) {
            return $decompressed;
        }

        return false;
    }

    public static function getIsset($variable) {

        return isset($variable);
    }

    public static function getHooks() {

        $all_hooks = [];
        $default_hooks = [
            ''                              => 'Select Hook',
            'displayBanner'                 => 'displayBanner',
            'displayTop'                    => 'displayTop',
            'displayTopColumn'              => 'displayTopColumn',
            'displayHome'                   => 'displayHome',
            'displayFullWidthTop'           => 'displayFullWidthTop',
            'displayFullWidthTop2'          => 'displayFullWidthTop2',
            'displayFullWidthTop'           => 'displayFullWidthTop',
            'displayLeftColumn'             => 'displayLeftColumn',
            'displayRightColumn'            => 'displayRightColumn',
            'displayFooter'                 => 'displayFooter',
            'displayLeftColumnProduct'      => 'displayLeftColumnProduct',
            'displayRightColumnProduct'     => 'displayRightColumnProduct',
            'displayFooterProduct'          => 'displayFooterProduct',
            'displayMyAccountBlock'         => 'displayMyAccountBlock',
            'displayMyAccountBlockfooter'   => 'displayMyAccountBlockfooter',
            'displayProductButtons'         => 'displayProductButtons',
            'displayCarrierList'            => 'displayCarrierList',
            'displayBeforeCarrier'          => 'displayBeforeCarrier',
            'displayPaymentTop'             => 'displayPaymentTop',
            'displayPaymentReturn'          => 'displayPaymentReturn',
            'displayOrderConfirmation'      => 'displayOrderConfirmation',
            'displayShoppingCart'           => 'displayShoppingCart',
            'displayShoppingCartFooter'     => 'displayShoppingCartFooter',
            'dislayMyAccountBlock'          => 'dislayMyAccountBlock',
            'displayCustomerAccountFormTop' => 'displayCustomerAccountFormTop',
        ];

        $existing_custom_hooks = RevLoader::get_option('revslider-custom-hooks');
        $existing_custom_hooks = Tools::jsonDecode($existing_custom_hooks, true);

        if (isset($existing_custom_hooks)) {

            if (is_array($existing_custom_hooks) && !empty($existing_custom_hooks)) {
                $all_hooks = array_merge($default_hooks, $existing_custom_hooks);
            } else {
                $all_hooks = $default_hooks;
            }

        } else {
            $all_hooks = $default_hooks;
        }

        $all_hooks['customhookname'] = 'Custom Hook Name';
        return $all_hooks;
    }

    static function wp_remote_fopen($Url) {

        $UserAgentList = [];
        $UserAgentList[] = "Mozilla/4.0 (compatible; MSIE 6.0; X11; Linux i686; en) Opera 8.01";
        $UserAgentList[] = "Mozilla/5.0 (compatible; Konqueror/3.3; Linux) (KHTML, like Gecko)";
        $UserAgentList[] = "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2";
        $UserAgentList[] = "Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9.2.25) Gecko/20111212 Firefox/3.6.25";
        $UserAgentList[] = "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/534.52.7 (KHTML, like Gecko) Version/5.1.2 Safari/534.52.7";
        $UserAgentList[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.2; Win64; x64; SV1; .NET CLR 2.0.50727)";
        $UserAgentList[] = "Mozilla/5.0 (Windows NT 6.1; rv:8.0.1) Gecko/20100101 Firefox/8.0.1";
        $UserAgentList[] = "Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.63 Safari/535.7";

        $hcurl = curl_init();
        curl_setopt($hcurl, CURLOPT_URL, $Url);
        curl_setopt($hcurl, CURLOPT_USERAGENT, $UserAgentList[array_rand($UserAgentList)]);
        curl_setopt($hcurl, CURLOPT_TIMEOUT, 120);
        curl_setopt($hcurl, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($hcurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($hcurl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($hcurl);
        curl_close($hcurl);
        return $result;
    }

    function check_slider_else_redirect($slider_id) {

        $is_exist = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('id_revslider_slider')
                ->from('revslider_slider')
                ->where('`id_revslider_slider` = ' . (int) $slider_id)
        );

        if (empty($is_exist) || $is_exist == null || $is_exist == false) {
            die('404 url not found');
        }

    }

    public static function esc_attr($value, $ext = '') {

        return $value;
    }

    static function is_ssl() {

        // Config

        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            return true;
        }

        return false;
    }

    function check_slide_else_redirect($slide_id) {

        if ($slide_id == 'new') {
            return;
        }

        //static slider will search in slider table

        if (strpos($slide_id, "static_") !== false) {
            $slider_id = str_replace("static_", "", $slide_id);
            $slider_id = (int) $slider_id;

            $is_exist = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
                (new DbQuery())
                    ->select('id_revslider_slide')
                    ->from('revslider_slide')
                    ->where('`id_revslider_slide` = ' . (int) $slide_id)
            );

            if (empty($is_exist) || $is_exist == null || $is_exist == false) {
                die('404 url not found');
            }

            return;
        }

        $is_exist = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('id_revslider_slide')
                ->from('revslider_slide')
                ->where('`id_revslider_slide` = ' . (int) $slide_id)
        );

        if (empty($is_exist) || $is_exist == null || $is_exist == false) {
            die('404 url not found');
        }

    }

    static function get_transient($option_name) {

        $main_opt_name = "_trns_{$option_name}";
        $return = false;
        $result = Db::getInstance()->getRow(
            (new DbQuery())
                ->select('*')
                ->from('revslider_options')
                ->where('`option_name` = "' . $main_opt_name . '"')
        );

        $return_temp = null;

        if (isset($result['option_value'])) {
            $return_temp = (array) json_decode(stripslashes($result['option_value']));

            if ($result && is_array($result) && $return_temp != null) {

                if ($return_temp['reset_time'] >= time()) {
                    $return = $return_temp['data'];
                }

            }

        }

        return $return;
    }

    static function set_transient($option_name, $option_value, $reset_time = 1200) {

        $main_opt_name = "_trns_{$option_name}";

        $serialized_data = [];
        $serialized_data['reset_time'] = time() + $reset_time;
        $serialized_data['data'] = $option_value;

        $serialized_data = addslashes(json_encode($serialized_data));

        $is_exist = Db::getInstance()->getRow(
            (new DbQuery())
                ->select('*')
                ->from('revslider_options')
                ->where('`option_name` = "' . $main_opt_name . '"')
        );

        if (!empty($is_exist)) {
            $result_temp = (array) json_decode($is_exist['option_value']);
        } else {
            $result_temp = [];
        }

        if ($is_exist && isset($result_temp['reset_time']) && $result_temp['reset_time'] < time()) {
            Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "revslider_options` SET `option_value`='" . $serialized_data . "' WHERE `option_name`='{$main_opt_name}';");
        } else

        if (!$is_exist) {
            Db::getInstance()->execute("INSERT INTO `" . _DB_PREFIX_ . "revslider_options` (`option_id`, `option_name`, `option_value`) VALUES (NULL, '" . $main_opt_name . "', '" . $serialized_data . "');");
        }

    }

    static function get_transient_addon($option_name) {

        $main_opt_name_and_time = "_trns_{$option_name}_time";
        $reset_time = RevLoader::get_option($main_opt_name_and_time);

        if ($reset_time && $reset_time >= time()) {
            $main_opt_name = "_trns_{$option_name}";
            return RevLoader::get_option($main_opt_name);
        } else {
            return false;
        }

    }

    static function set_transient_addon($option_name, $option_value, $reset_time = 1200) {

        $main_opt_name = "_trns_{$option_name}";
        RevLoader::update_option($main_opt_name, $option_value);
        $main_opt_name_and_time = "_trns_{$option_name}_time";
        $reset_time = time() + $reset_time;
        RevLoader::update_option($main_opt_name_and_time, $reset_time);

        return true;
    }

    static function delete_transient($option_name) {

        $main_opt_name = "_trns_{$option_name}";
        RevLoader::update_option($main_opt_name, '');
        $main_opt_name_and_time = "_trns_{$option_name}_time";
        RevLoader::update_option($main_opt_name_and_time, '');

        return true;
    }

    static function date_i18n($date) {

        return date($date);

    }

    function get_version_from_file($file_path) {

        $fp = fopen($file_path, 'r');
        // Pull only the first 8kiB of the file in.
        $file_data = fread($fp, 8192);
        // PHP will close file handle, but we are good citizens.
        fclose($fp);
        // Make sure we catch CR-only line endings.
        $file_data = str_replace("\r", "\n", $file_data);

        if (preg_match('/^[ \t\/*#@]*' . preg_quote('Version', '/') . ':(.*)$/mi', $file_data, $match) && $match[1]) {
            return $match[1];
        }

    }

    static function content_url($link = '') {

        $url = RS_PLUGIN_URL;
        return $url;
    }

    function uploads_url($src = '') {

        return './uploads/' . $src;
    }

    function uploads_real_url($src = '') {

        return './uploads/' . $src;
    }

    function generate_svg_url($url = '') {

        //$url = "/plugins/revslider-whiteboard-addon/public/assets/svg/busy-icons-svg/character07.svg";
        $url_array = explode("/", $url);
        $found_svg = false;
        $relative_url = '';

        foreach ($url_array as $url_part) {

            if ($url_part == 'svg') {
                $found_svg = true;
            }

            if ($found_svg) {
                $relative_url = $relative_url . '/' . $url_part;
            }

        }

        // var_dump($relative_url);die();
        return get_svg_url() . $relative_url;
    }

    function getRevSliderClass() {

        return new RevSlider();
    }

    function getPermissionByComma() {

        return "read_themes, write_themes,read_script_tags, write_script_tags,read_content, write_content";
    }

    function get_object_taxonomies($object, $output = 'names') {

        return null;
    }

    static function selected($selected, $current = true, $echo = true) {

        return self::__checked_selected_helper($selected, $current, $echo, 'selected');
    }

    static function __checked_selected_helper($helper, $current, $echo, $type) {

        if ((string) $helper === (string) $current) {
            $result = " $type='$type'";
        } else {
            $result = '';
        }

        if ($echo) {
            echo $result;
        } else {
            return $result;
        }

    }

    static function maybe_unserialize($original) {

        if (self::is_serialized($original)) // don't attempt to unserialize data that wasn't serialized going in
        {
            return @unserialize($original);
        }

        return $original;
    }

    static function is_serialized($data, $strict = true) {

        // if it isn't a string, it isn't serialized.

        if (!is_string($data)) {
            return false;
        }

        $data = trim($data);

        if ('N;' == $data) {
            return true;
        }

        if (strlen($data) < 4) {
            return false;
        }

        if (':' !== $data[1]) {
            return false;
        }

        if ($strict) {
            $lastc = substr($data, -1);

            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }

        } else {
            $semicolon = strpos($data, ';');
            $brace = strpos($data, '}');
            // Either ; or } must exist.

            if (false === $semicolon && false === $brace) {
                return false;
            }

            // But neither must be in the first X characters.

            if (false !== $semicolon && $semicolon < 3) {
                return false;
            }

            if (false !== $brace && $brace < 4) {
                return false;
            }

        }

        $token = $data[0];

        switch ($token) {
        case 's':

            if ($strict) {

                if ('"' !== substr($data, -2, 1)) {
                    return false;
                }

            } else
            if (false === strpos($data, '"')) {
                return false;
            }

        // or else fall through
        case 'a':
        case 'O':
            return (bool) preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'b':
        case 'i':
        case 'd':
            $end = $strict ? '$' : '';
            return (bool) preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }

        return false;
    }

    static function checked($checked, $current = true, $echo = true) {

        return self::__checked_selected_helper($checked, $current, $echo, 'checked');
    }

    static function esc_js($value) {

        return $value;
    }

    static function delete_files($dir) {

        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {

            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!self::delete_files($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

    function esc_sql($data) {

        return Revloader::_escape($data);
    }

    public function _real_escape($string) {

        //  return $this->sdsdb->escape($string);
        return $this->escape($string);
    }

    public static function escape($text, $extra = false) {

        if (is_int($text) || is_float($text)) {
            return $text;
        }

        $text = str_replace("'", "''", $text);
        return addcslashes($text, "\000\n\r\\\032");
    }

    public static function _escape($data) {

        if (is_array($data)) {

            foreach ($data as $k => $v) {

                if (is_array($v))
                //$data[$k] = DB::connection()->getPdo()->quote($v);
                {
                    $data[$k] = $v;
                } else {
                    $data[$k] = self::escape($v);
                }

            }

        } else {
            //  $data = $this->sdsdb->escape( $data );
            $data = self::escape($data);
        }

        return $data;
    }

    static function esc_html($value, $txd = '') {

        return $value;
    }

    public static function wp_create_nonce($pure_string = '') {

        // RevLoader::createNonce($pure_string);
        $token = rand(10, 100);
        return $token;
    }

    public static function do_action($tag, $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = '', $arg5 = '') {

        if (isset(self::$hook_values[$tag])) {
            self::$current_action = $tag;

            $params = [$arg1, $arg2, $arg3, $arg4, $arg5];
            // var_dump(self::$hook_values[$tag]);

            foreach (self::$hook_values[$tag] as $hook) {

                if ($hook['type'] == 'class') {
                    call_user_func_array([$hook['class'], $hook['function_name']], $params);
                } else {
                    call_user_func_array($hook['function_name'], $params);
                }

            }

            self::$current_action = null;
        } else {
            return true;
        }

    }

    public static function admin_url() {

        return '';
    }

    static function is_admin() {

        $controller_name = Tools::getValue('controller');

        if (isset(Context::getContext()->employee) && Context::getContext()->employee->id > 0 && $controller_name != 'AdminRevolutionsliderAjax') {

            return true;
        } else {

            return false;
        }

    }

    public static function __($text, $textdomain = '') {

        return $text;
    }

    static function _x($string, $text_domain = '') {

        return $string;
    }

    static function _e($string, $text_domain = '') {

        echo $string;
    }

    public static function get_intermediate_image_sizes() {

        $image_sizes = ['thumbnail', 'medium', 'medium_large', 'large', 'custom-size']; // Standard sizes

        /**
         * Filters the list of intermediate image sizes.
         *
         * @since 2.5.0
         *
         * @param array $image_sizes An array of intermediate image sizes. Defaults
         *                           are 'thumbnail', 'medium', 'medium_large', 'large'.
         */
        return $image_sizes;
    }

    public static function apply_filters($tag, $value, $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = '', $arg5 = '') {

        if (isset(self::$filter_values[$tag])) {
            self::$current_filter = $tag;
            $filtered_value = null;
            $params = [$value, $arg1, $arg2, $arg3, $arg4, $arg5];

            $filter_tag_values = self::$filter_values[$tag];

            foreach ($filter_tag_values as $filter) {

                if ($filter['type'] == 'class') {
                    $return_data = call_user_func_array([$filter['class'], $filter['function_name']], $params);
                } else {
                    $return_data = call_user_func_array($filter['function_name'], $params);
                }

                //get the filtered value weather string or array. sometimes returns only string
                $filtered_value = $return_data;
                //if array then reassign the value

                if (is_array($return_data)) {

                    if (count($return_data) == 1 || empty($return_data)) {

                        if (!empty($return_data)) {
                            $array_value[key($return_data)] = $return_data[key($return_data)];
                        } else {
                            $array_value = [];
                        }

                    } else {
                        $array_value = $return_data;
                    }

                    $filtered_value = $array_value;
                }

            }

            self::$current_filter = null;
            return $filtered_value;
        } else {
            return $value;
        }

    }

    public static function add_filter($tag, $function, $priority = 10, $accepted_args = 1) {

        if (is_array($function)) {
            $function_info['class'] = $function[0];
            $function_info['type'] = 'class';
            $function_info['function_name'] = $function[1];
        } else {
            $function_info['type'] = 'noclass';
            $function_info['function_name'] = $function;
        }

        self::$filter_values[$tag][] = $function_info;
        return true;
    }

    public static function add_action($tag, $function, $priority = 10, $accepted_args = 1) {

        if ($tag == 'plugins_loaded') {
            $params = [];
            call_user_func_array($function, $params);
        } else {

            if (is_array($function)) {
                $function_info['class'] = $function[0];
                $function_info['type'] = 'class';
                $function_info['function_name'] = $function[1];
            } else {
                $function_info['type'] = 'noclass';
                $function_info['function_name'] = $function;
            }

            self::$hook_values[$tag][] = $function_info;
        }

        return true;
    }

    public static function wp_convert_hr_to_bytes($size) {

        $size = strtolower($size);
        $bytes = (int) $size;

        if (strpos($size, 'k') !== false) {
            $bytes = (int) ($size) * 1024;
        } else

        if (strpos($size, 'm') !== false) {
            $bytes = (int) ($size) * 1024 * 1024;
        } else

        if (strpos($size, 'g') !== false) {
            $bytes = (int) ($size) * 1024 * 1024 * 1024;
        }

        return $bytes;
    }

    public static function wp_upload_dir() {

        //return './uploads/';
        $upload_dir['basedir'] = RS_PLUGIN_PATH . 'uploads';
        $upload_dir['baseurl'] = RS_PLUGIN_URL . 'uploads';
        return $upload_dir;
    }

    public static function register_activation_hook($file_dir, $activation_name) {

        $filename = basename($file_dir);
        $filename_arr = explode('.php', $filename);
        //var_dump($filename_arr);die();
        $file_location = $filename_arr[0] . '/' . $filename;
        //RevLoader::$hook_register[$file_location]=$activation_name;
        $registered_hooks = RevLoader::get_option('hook_register', []);

        if (empty($registered_hooks)) {
            $registered_hooks = $registered_hooks;
        } else {
            $registered_hooks = json_decode($registered_hooks, true);
        }

        $registered_hooks[$file_location] = $activation_name;

        RevLoader::update_option('hook_register', json_encode($registered_hooks));

        //  var_dump(RevLoader::$hook_register);die();
        return true;
    }

    public static function register_deactivation_hook($file_dir, $deactivation_name) {

        $filename = basename($file_dir);
        $filename_arr = explode('.php', $filename);
        //var_dump($filename_arr);die();
        $file_location = $filename_arr[0] . '/' . $filename;
        //  RevLoader::$hook_deregister[$file_location]=$deactivation_name;
        $deregistered_hooks = RevLoader::get_option('hook_deregister', []);

        if (empty($deregistered_hooks)) {
            $deregistered_hooks = $deregistered_hooks;
        } else {
            $deregistered_hooks = json_decode($deregistered_hooks, true);
        }

        $deregistered_hooks[$file_location] = $deactivation_name;

        RevLoader::update_option('hook_deregister', json_encode($deregistered_hooks));
        return true;
    }

    public static function load_plugin_textdomain() {

        return true;
    }

    public static function get_option($key, $default = false) {

        $value = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('option_value')
                ->from('revslider_options')
                ->where('`option_name` = "' . $key . '"')
        );

        if ($value !== false) {

            $value = RevLoader::maybe_unserialize($value);

            if (is_array($value) || is_object($value)) {
                return $value;
            }

            if ($key == 'rs-templates-new' || $key == 'rs-library' || $key == 'rs-templates' || $key == 'revslider_servers') {
                //$value = stripslashes($value);
                $value = json_decode($value, true);
                return $value;
            }

            if ($key == 'revslider-addons') {
                $value = stripslashes($value);
                $json_ob = json_decode($value);
                return $json_ob;
            }

            if ($key == 'revslider-nav-preset-default') {
                $json_ob = json_decode($value);
                return $json_ob;
            }

            return $value;
        } else {
            return $default;
        }

    }

    public static function update_option($key, $value) {

        $is_exist = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('option_id')
                ->from('revslider_options')
                ->where('`option_name` = "' . $key . '"')
        );

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
            $value = addslashes($value);
        } else {
            $value = addslashes($value);
        }

        if (!empty($is_exist)) {

            Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "revslider_options` SET `option_value`='{$value}' WHERE `option_name`='{$key}'");
        } else {
            Db::getInstance()->execute("INSERT INTO `" . _DB_PREFIX_ . "revslider_options` (`option_name`, `option_value`) VALUES ('{$key}', '{$value}');");
        }

        return true;
    }

    public static function update_option_test($key, $value) {

        $is_exist = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('option_id')
                ->from('revslider_options')
                ->where('`option_name` = "' . $key . '"')
        );

        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
            $value = addslashes($value);
        }

        if (!empty($is_exist)) {

            Db::getInstance()->execute("UPDATE `" . _DB_PREFIX_ . "revslider_options` SET `option_value`='{$value}' WHERE `option_name`='{$key}'");
        } else {
            Db::getInstance()->execute("INSERT INTO `" . _DB_PREFIX_ . "revslider_options` (`option_name`, `option_value`) VALUES ('{$key}', '{$value}');");
        }

        return true;
    }

    static function wp_is_mobile() {

        if (empty($_SERVER['HTTP_USER_AGENT'])) {
            $is_mobile = false;
        } else

        if (
            strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false// many mobile devices (all iPhone, iPad, etc.)
             || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false
        ) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }

        return $is_mobile;
    }

    static function home_url() {

        return '';
    }

    static function is_wp_error() {

        return false;
    }

    public static function wp_image_editor_supports() {

        return true;
    }

    public static function wp_die() {

        die();
    }

    public static function esc_url($url) {

        return $url;
    }

    public static function sanitize_title($title) {

        $raw_title = $title;
        $title = strtolower($title);
        $title = str_replace(' ', '-', $title);
        $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
        return $title;
    }

    public static function size_format($bytes, $decimals = 0) {

        $quant = [
            'TB' => TB_IN_BYTES,
            'GB' => GB_IN_BYTES,
            'MB' => MB_IN_BYTES,
            'KB' => KB_IN_BYTES,
            'B'  => 1,
        ];

        if (0 === $bytes) {
            return RevLoader::number_format_i18n(0, $decimals) . ' B';
        }

        foreach ($quant as $unit => $mag) {

            if (doubleval($bytes) >= $mag) {
                return RevLoader::number_format_i18n($bytes / $mag, $decimals) . ' ' . $unit;
            }

        }

        return false;
    }

    public static function number_format_i18n($number, $decimals = 0) {

        global $wp_locale;

        if (isset($wp_locale)) {
            $formatted = number_format(
                $number,
                self::abs_int($decimals),
                $wp_locale->number_format['decimal_point'],
                $wp_locale->number_format['thousands_sep']
            );
        } else {
            $formatted = number_format($number, self::abs_int($decimals));
        }

        return RevLoader::apply_filters('number_format_i18n', $formatted);
    }

    public static function abs_int($maybe_int) {

        return abs((int) ($maybe_int));
    }

    public static function wp_is_writable($path) {

        if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
            return self::win_is_writable($path);
        } else {
            return @is_writable($path);
        }

    }

    public static function win_is_writable($path) {

        if ($path[strlen($path) - 1] == '/') {
            // if it looks like a directory, check a random file within the directory
            return self::win_is_writable($path . uniqid(mt_rand()) . '.tmp');
        } else

        if (is_dir($path)) {
            // If it's a directory (and not a file) check a random file within the directory
            return self::win_is_writable($path . '/' . uniqid(mt_rand()) . '.tmp');
        }

        // check tmp file for read/write capabilities
        $should_delete_tmp_file = !file_exists($path);
        $f = @fopen($path, 'a');

        if ($f === false) {
            return false;
        }

        fclose($f);

        if ($should_delete_tmp_file) {
            unlink($path);
        }

        return true;
    }

    static function wp_enqueue_script($scriptName, $src = '', $deps = [], $ver = '1.0', $in_footer = false) {

        if (isset(self::$registered_script[$scriptName])) {
            $src = self::$registered_script[$scriptName]['src'];
            $deps = self::$registered_script[$scriptName]['deps'];
            $in_footer = self::$registered_script[$scriptName]['in_footer'];
        }

        self::enqueue_script($scriptName, $src, $deps, $ver, $in_footer);
    }

    static function wp_enqueue_style($handle, $noscript = false, $ver = '', $media = 'all', $src = '', $deps = []) {

        if (isset(self::$registered_style[$handle])) {
            $src = self::$registered_style[$handle]['src'];
            $deps = self::$registered_style[$handle]['deps'];
        }

        self::enqueue_style($handle, $noscript, $ver, $media, $src, $deps);
    }

    public static function wp_head() {

        RevLoader::do_action('wp_head');
    }

    static function wp_enqueue_scripts() {

        self::do_action('wp_enqueue_scripts');
    }

    public static function wp_localize_script($handle, $varName, $value, $toFooter = false) {

        if ($toFooter != true) {
            self::$local_scripts[$varName] = $value;
        } else {
            self::$local_scripts_footer[$varName] = $value;
        }

    }

    static function rev_front_print_head_scripts() {

        foreach (self::$front_scripts as $key => $script_src) {

            if ($script_src != null && $script_src != '') {
                echo '<script src="' . $script_src . '"></script>';
            } else {
                //this is just for own purpose
                self::$blank_scripts[] = $key;
            }

        }

        self::footer_local_scripts();

    }

    static function wp_print_head_scripts() {

        foreach (self::forced_predefined_scripts() as $script_src) {
            echo '<script src="' . $script_src . '"></script>';
        }

        foreach (self::$admin_scripts as $key => $script_src) {

            if ($script_src != null && $script_src != '') {
                echo '<script src="' . $script_src . '"></script>' . PHP_EOL;
            } else {
                //this is just for own purpose
                self::$blank_scripts[] = $key;
            }

        }

        self::header_local_scripts();
    }

    static function rev_front_print_footer_scripts() {

        foreach (self::$front_scripts_foot as $key => $script_src) {

            if ($script_src != null && $script_src != '') {
                echo '<script src="' . $script_src . '"></script>';
            } else {
                self::$blank_scripts[] = $key;
                //this is just for own purpose
            }

        }

        self::footer_local_scripts();

    }

    static function wp_print_footer_scripts() {

        foreach (self::$admin_scripts_foot as $key => $script_src) {

            if ($script_src != null && $script_src != '') {
                echo '<script src="' . $script_src . '"></script>';
            } else {
                self::$blank_scripts[] = $key;
                //this is just for own purpose
            }

        }

        self::footer_local_scripts();
    }

    public static function rev_front_print_styles() {

        foreach (self::$front_styles as $key => $style_src) {

            if ($style_src != null && $style_src != '') {
                echo '<link rel="stylesheet" href="' . $style_src . '" type="text/css" />';
            } else {
                //this is just for own purpose
                self::$blank_styles[] = $key;
            }

        }

    }

    static function wp_print_styles() {

        foreach (self::forced_predefined_styles() as $style_src) {
            echo '<link rel="stylesheet" href="' . $style_src . '" type="text/css" />
            ';
        }

        foreach (self::$admin_styles as $key => $style_src) {

            if ($style_src != null && $style_src != '') {
                echo '<link rel="stylesheet" href="' . $style_src . '" type="text/css" />
            ';
            } else {
                //this is just for own purpose
                self::$blank_styles[] = $key;
            }

        }

    }

    public static function enqueue_style($styleName, $noscript, $ver = '1.0', $media = 'all', $src = '', $deps = []) {

        foreach ($deps as $depnd) {
            self::wp_enqueue_style($depnd);
        }

        if (self::is_admin()) {
            self::$admin_styles[$styleName] = $src;
        } else {
            self::$front_styles[$styleName] = $src;
        }

    }

    public static function enqueue_script($scriptName, $src = '', $deps = [], $ver = '1.0', $in_footer = false) {

        if (is_array($deps)) {

            foreach ($deps as $depnd) {
                self::wp_enqueue_script($depnd);
            }

        }

        //           var_dump(self::$registered_script); var_dump($deps);die();

        if ($in_footer == false) {

            if (self::is_admin()) {
                self::$admin_scripts[$scriptName] = $src;
            } else {
                self::$front_scripts[$scriptName] = $src;
            }

        } else {

            if (self::is_admin()) {
                self::$admin_scripts_foot[$scriptName] = $src;
            } else {
                self::$front_scripts_foot[$scriptName] = $src;
            }

        }

    }

    static function wp_register_script($name, $src, $deps = [], $ver = '1.0', $in_footer = false) {

        self::$registered_script[$name]['src'] = $src;
        self::$registered_script[$name]['deps'] = $deps;
        self::$registered_script[$name]['in_footer'] = $in_footer;
    }

    static function wp_register_style($name, $src, $deps = [], $ver = '', $media = 'all', $noscript = false) {

        self::$registered_style[$name]['src'] = $src;
        self::$registered_style[$name]['deps'] = $deps;
    }

    public static function footer_local_scripts() {

        $allLocalScripts = "<script type='text/javascript'>";

        foreach (self::$local_scripts_footer as $var_name => $scripts_each) {

            if (is_array($scripts_each)) {
                $value = json_encode($scripts_each);
            } else {
                $value = '"' . $scripts_each . '"';
            }

            $allLocalScripts .= "var " . $var_name . "= " . $value . ";";
        }

        $allLocalScripts .= "</script>";

        echo $allLocalScripts;
    }

    public static function header_local_scripts() {

        $allLocalScripts = "<script type='text/javascript'>";

        foreach (self::$local_scripts as $var_name => $scripts_each) {

            if (is_array($scripts_each)) {
                $value = json_encode($scripts_each);
            } else {
                $value = '"' . $scripts_each . '"';
            }

            $allLocalScripts .= "var " . $var_name . "= " . $value . ";";
        }

        $allLocalScripts .= "</script>";

        echo $allLocalScripts;
    }

    public static function forced_predefined_scripts() {

        if (Context::getContext()->employee->id > 0) {

            return [];
        }

        $scripts = [
            //  RS_PLUGIN_URL . 'admin/assets/default/js/jquery.js',
            // RS_PLUGIN_URL . 'admin/assets/default/js/jquery-ui.js',
            RS_PLUGIN_URL . 'admin/assets/default/js/iris.min.js',
            RS_PLUGIN_URL . 'admin/assets/default/js/wp-color-picker.js',
            RS_PLUGIN_URL . 'admin/assets/default/js/wpdialogs.js',
            RS_PLUGIN_URL . 'admin/assets/default/js/thickbox.js',
            RS_PLUGIN_URL . 'admin/assets/default/js/media-upload.js',
            RS_PLUGIN_URL . 'admin/assets/default/js/underscore.min.js',
        ];

        return $scripts;
    }

    public static function forced_predefined_styles() {

        return [

        ];
    }

    static function is_ajax() {

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'revslider_ajax_action') {
            return true;
        } else {
            return false;
        }

    }

    function unzip_file($path) {

        $zip = new ZipArchive;
        $res = $zip->open($path);

        if ($res === TRUE) {
            $zip->extractTo($this->download_path);
            $zip->close();
            $this->import_zip = true;
            return true;
        } else {
            return ['success' => false, 'error' => 'File unzip file'];
        }

    }

    static function is_rtl() {

        return true;
    }

    static function sanitize_text_field($text) {

        return $text;
    }

    static function _isCurl() {

        return function_exists('curl_version');
    }

//    static function loadAssetsOnly(){
    //        new RevSliderAdmin('false');
    //    }
    static function wp_remote_post($url, $args) {

        $args['method'] = 'POST';
        $revLoader = new RevLoader();
        return $revLoader->getHttpCurl($url, $args);
    }

    static function wp_remote_get($url, $args = []) {

        $revLoader = new RevLoader();
        return $revLoader->getHttpCurl($url, $args);
    }

    static function get_bloginfo($parms) {

        if ($parms == 'version') {
            return '';
        } else

        if ($parms == 'url') {
            return RS_PLUGIN_URL;
        } else {
            return true;
        }

    }

    static function wp_remote_retrieve_response_code($response) {

        if (!isset($response['info']['http_code']) || !is_array($response['info'])) {
            return '';
        }

        return $response['info']['http_code'];
    }

    static function wp_remote_retrieve_body($response) {

        if (!isset($response['body'])) {
            return '';
        }

        return $response['body'];
    }

    public function getHttpCurl($url, $args) {

        global $wp_version;

        if (function_exists('curl_init')) {
            $defaults = [
                'method'      => 'GET',
                'timeout'     => 300,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => [
                    'Authorization'   => 'Basic ',
                    'Content-Type'    => 'application/x-www-form-urlencoded;charset=UTF-8',
                    'Accept-Encoding' => 'x-gzip,gzip,deflate',
                ],
                'body'        => [],
                'cookies'     => [],
                'user-agent'  => 'php',
                'header'      => false,
                'sslverify'   => true,
            ];

            $args = $this->smart_merge_attrs($defaults, $args);

            $curl_timeout = ceil($args['timeout']);
            $curl = curl_init();

            if ($args['httpversion'] == '1.0') {
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
            } else {
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            }

            curl_setopt($curl, CURLOPT_USERAGENT, $args['user-agent']);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);

            $ssl_verify = $args['sslverify'];
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl_verify);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, ($ssl_verify === true) ? 2 : false);

            if ($ssl_verify) {
                curl_setopt($curl, CURLOPT_CAINFO, RS_PLUGIN_PATH . '/admin/views/ssl/ca-bundle.crt');
            }

            curl_setopt($curl, CURLOPT_HEADER, $args['header']);

            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);

            if (defined('CURLOPT_PROTOCOLS')) {

                curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            }

            $http_headers = [];

            foreach ($args['headers'] as $key => $value) {
                $http_headers[] = "{$key}: {$value}";
            }

            if (is_array($args['body']) || is_object($args['body'])) {
                $args['body'] = http_build_query($args['body']);
            }

            $http_headers[] = 'Content-Length: ' . strlen($args['body']);

            curl_setopt($curl, CURLOPT_HTTPHEADER, $http_headers);

            switch ($args['method']) {
            case 'HEAD':
                curl_setopt($curl, CURLOPT_NOBODY, true);
                break;
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $args['body']);
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $args['body']);
                break;
            default:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $args['method']);

                if (!is_null($args['body'])) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $args['body']);
                }

                break;
            }

            curl_setopt($curl, CURLOPT_HEADERFUNCTION, [$this, 'streamHeaders']);
            curl_setopt($curl, CURLOPT_WRITEFUNCTION, [$this, 'streamBody']);

            curl_exec($curl);

            $responseBody = $this->body;
            $responseHeader = $this->headers;

            if (self::shouldDecode($responseHeader) === true) {
                $responseBody = self::decompress($responseBody);
            }

            $this->body = '';
            $this->headers = '';

            $error = curl_error($curl);
            $errorcode = curl_errno($curl);
            $info = curl_getinfo($curl);
            curl_close($curl);
            $info_as_response = $info;
            $info_as_response['code'] = $info['http_code'];
            $info_as_response['message'] = 'OK';
            $response = ['body' => $responseBody, 'headers' => $responseHeader, 'info' => $info, 'response' => $info_as_response, 'error' => $error, 'errno' => $errorcode];
            return $response;
        }

        return false;
    }

    function smart_merge_attrs($pairs, $atts) {

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

    static function wp_is_stream($path) {

        $wrappers = stream_get_wrappers();
        $wrappers_re = '(' . join('|', $wrappers) . ')';
        return preg_match("!^$wrappers_re://!", $path) === 1;
    }

    static function wp_mkdir_p($target) {

        $wrapper = null;

        // Strip the protocol.

        if (self::wp_is_stream($target)) {
            list($wrapper, $target) = explode('://', $target, 2);
        }

        // From php.net/mkdir user contributed notes.
        $target = str_replace('//', '/', $target);

        // Put the wrapper back on the target.

        if ($wrapper !== null) {
            $target = $wrapper . '://' . $target;
        }

        /*
                                            * Safe mode fails with a trailing slash under certain PHP versions.
                                            * Use rtrim() instead of untrailingslashit to avoid formatting.php dependency.
        */
        $target = rtrim($target, '/');

        if (empty($target)) {
            $target = '/';
        }

        if (file_exists($target)) {
            return @is_dir($target);
        }

        // We need to find the permissions of the parent folder that exists and inherit that.
        $target_parent = dirname($target);

        while ('.' != $target_parent && !is_dir($target_parent)) {
            $target_parent = dirname($target_parent);
        }

        // Get the permission bits.

        if ($stat = @stat($target_parent)) {
            $dir_perms = $stat['mode'] & 0007777;
        } else {
            $dir_perms = 0777;
        }

        if (@mkdir($target, $dir_perms, true)) {

            /*
                                                        * If a umask is set that modifies $dir_perms, we'll have to re-set
                                                        * the $dir_perms correctly with chmod()
            */

            if ($dir_perms != ($dir_perms & ~umask())) {
                $folder_parts = explode('/', substr($target, strlen($target_parent) + 1));

                for ($i = 1, $c = count($folder_parts); $i <= $c; $i++) {
                    @chmod($target_parent . '/' . implode(
                        '/',
                        array_slice($folder_parts, 0, $i)
                    ), $dir_perms);
                }

            }

            return true;
        }

        return false;
    }

    static function getAjaxUrl() {

        $context = \Context::getContext();
        $ajaxUrl = $context->link->getAdminLink('AdminRevolutionsliderAjax');
        return $ajaxUrl;
    }

    static function url() {

        return self::get_plugin_url();
    }

    static function getCustomAdminRUL() {

        $link = new Link();
        return Context::getContext()->link->getBaseAdminLink();
    }

    static function customBaseURL() {

        return Context::getContext()->link->getBaseFrontLink();

    }

    public static function load_addon() {

        $addon_folder_name = "revslider-paintbrush-addon/revslider-paintbrush-addon.php";
        //$addon_folder_name = "revslider-bubblemorph-addon/revslider-bubblemorph-addon.php";
        //if(get_option($addon_folder_name)=='active'){
        $addon_file_path = RS_PLUGIN_PATH . 'addons/' . $addon_folder_name;

        if (file_exists($addon_file_path)) {
            require_once $addon_file_path;
            // }
        }

    }

    public static function loadAllAddons() {

        $allowed_addons_default = [];
        $addons = self::get_option('revslider-addons', $allowed_addons_default);

        if (!is_array($addons) && !is_object($addons)) {
            $addons = json_decode($addons, true);
        }

        foreach ($addons as $addon => $addon_value) {
            $addon_folder_name = $addon . '/' . $addon . '.php';

            if (RevLoader::get_option($addon) == true) {
                $addon_file_path = RS_PLUGIN_PATH . 'addons/' . $addon . '/' . $addon . '.php';

                if (file_exists($addon_file_path)) {
                    require_once $addon_file_path;
                }

            }

        }

    }

    public static function plugin_dir_path($filepath) {

        $filename = basename($filepath);
        $file_dir = str_replace($filename, '', $filepath);
        return $file_dir;
    }

    public static function plugins_url($file, $filepath) {

        $addon_folder_name = basename($filepath, ".php");
        $addon_url = RS_PLUGIN_ADDONS_URL . $addon_folder_name . '/';
        return $addon_url;
    }

    public static function values_weather() {

        $revslider_weather_addon_values = [];
        parse_str(RevLoader::get_option('revslider_weather_addon'), $revslider_weather_addon_values);
        $return = json_encode($revslider_weather_addon_values);
        return ["message" => "Weather Settings Loaded", "data" => $return];
    }

    //for maintanence addons settigs
    /**
     * Saves Values for this Add-On
     */
    public static function save_maintenance_overwrite() {

        if (isset($_REQUEST['data']['revslider_maintenance_form'])) {
            RevLoader::update_option("revslider_maintenance_addon", $_REQUEST['data']['revslider_maintenance_form']);
            return 1;
        } else {
            return 0;
        }

    }

    /**
     * Load Values for this Add-On
     */
    public static function values_maintenance_overwrite() {

        $revslider_maintenance_addon_values = [];
        parse_str(RevLoader::get_option('revslider_maintenance_addon'), $revslider_maintenance_addon_values);
        $return = json_encode($revslider_maintenance_addon_values);
        return ["message" => "Data found", "data" => $return];
    }

    /**
     * Change Enable Status of this Add-On
     */
    public static function change_addon_status_overwrite($enabled) {

        RevLoader::update_option("revslider_maintenance_enabled", $enabled);
    }

    /**
     * Change Enable Status of this Add-On
     */
    public static function change_backup_addon_status($enabled) {

        RevLoader::update_option("revslider_backup_enabled", $enabled);
    }

    /**
     * fetch all slide revisions by slide_id
     * @since: 1.0.0
     */
    public static function fetch_slide_backups_overwrite($slide_id, $basic = false) {

        global $wpdb;

        if (strpos($slide_id, 'static_') !== false) {
            $slide = new RevSliderSlide();
            $slide_id = $slide->get_static_slide_id(str_replace('static_', '', $slide_id));
            $where = [$slide_id];
            $where[] = 'true';
        } else {
            $where = [$slide_id];
            $where[] = 'false';
        }

        if ($basic) {

            $value = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
                Db::getInstance()->prepare("SELECT `id`, `slide_id`, `slider_id`, `created` FROM " . _DB_PREFIX_ . 'revslider_backup_slides' . " WHERE slide_id = %s AND static = %s ORDER BY `created` ASC", $where)
            );

            if (!empty($record)) {

                $f = new RevSliderFunction();

                foreach ($record as $k => $rec) {
                    $record[$k]['created'] = $f->convert_post_date($rec['created'], true);
                }

            }

        } else {

            $record = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(Db::getInstance()->prepare("SELECT * FROM " . _DB_PREFIX_ . "revslider_backup_slides WHERE slide_id = %s AND static = %s", $where), true);
        }

        return $record;
    }

    /**
     * fetch backup by backup_id
     * @since: 1.0.0
     */
    public static function fetch_backup($backup_id) {

        global $wpdb;

        $record = Db::getInstance()->executeS(Db::getInstance()->prepare("SELECT * FROM " . _DB_PREFIX_ . 'revslider_backup_slides' . " WHERE id = %s", [$backup_id]), true);

        if (!empty($record)) {
            $record = $record[0];
        }

        return $record;

    }

    /**
     *
     * get data array from the database
     *
     */
    private static function fetch($tableName, $where = "", $orderField = "", $groupByField = "", $sqlAddon = "") {

        global $wpdb;

        $query = "select * from $tableName";

        if ($where) {
            $query .= " where $where";
        }

        if ($orderField) {
            $query .= " order by $orderField";
        }

        if ($groupByField) {
            $query .= " group by $groupByField";
        }

        if ($sqlAddon) {
            $query .= " " . $sqlAddon;
        }

        $response = Db::getInstance()->executeS($query, true);

        return ($response);
    }

    /**
     *
     * fetch only one item. if not found - throw error
     */
    public static function fetchSingle($tableName, $where = "", $orderField = "", $groupByField = "", $sqlAddon = "") {

        $response = self::fetch($tableName, $where, $orderField, $groupByField, $sqlAddon);

        if (empty($response)) {
            self::throwError("Record not found");
        }

        $record = $response[0];
        return ($record);
    }

    public static function getDataByID($slideid) {

        global $wpdb;
        $return = false;

        $record = self::fetchSingle(_DB_PREFIX_ . 'revslider_slide', Db::getInstance()->prepare("id_revslider_slide = %d", [$slideid]));
        $return = $record;

        return $return;
    }

    /**
     * restore slide backup
     * @since: 1.0.0
     */
    public static function restore_slide_backup($backup_id, $slide_id, $session_id) {

        global $wpdb;
        $backup = self::fetch_backup($backup_id);

        $current = self::getDataByID($slide_id);
        /*
                                     * process potential older backups previous to 6.0
        */

        if (!empty($backup) && isset($backup['settings'])) {

            $legacy = false;
            $settings = json_decode($backup['settings'], true);

            if (empty($settings)) {
                $legacy = true;
                $settings = ['version', RS_REVISION];
            } else

            if (isset($settings['version']) && version_compare($settings['version'], '6.0.0', '<')) {
                $legacy = true;
                $settings['version'] = RS_REVISION;
            }

            if ($legacy) {

                $slide = new RevSliderSlide();
                $slide->init_by_data($backup);

                $update = new RevSliderPluginUpdate();
                $slide = $update->migrate_slide_to_6_0($slide);

                $layers = json_decode($backup['layers'], true);

                foreach ($layers as $key => $layer) {
                    $layers[$key] = $update->migrate_layer_to_6_0($layer, false, $slide);
                }

                $backup['params'] = json_encode($slide);
                $backup['layers'] = json_encode($layers);
                $backup['settings'] = json_encode($settings);

            }

        }

        if (!empty($backup) && !empty($current)) {

            $update_id = $current['id'];
            unset($current['id']);
            $slider = new RevSliderSlide($update_id);
            $slider->params = $backup['params'];
            $slider->layers = $backup['layers'];
            $slider->settings = $backup['settings'];
            $slider->update();

            //now change the backup date to current date, to set it to the last version
            $backup['created'] = date('Y-m-d H:i:s');
            $update_id = $backup['id'];
            unset($backup['id']);

            $return1 = $wpdb->update(_DB_PREFIX_ . 'revslider_backup_slides', $backup, ['id' => $update_id]);

            return true;
        }

        return false;
    }

    /**
     *
     * throw error
     */
    public static function throwError($message, $code = -1) {

        $f = new RevSliderFunction();
        $f->throw_error($message, $code);

    }

    /**
     * check if a new backup should be created
     * @since: 1.0.0
     */
    public static function check_add_new_backup($ajax_data, $slide_class) {

        global $wpdb;

        $record = Db::getInstance()->executeS(Db::getInstance()->prepare("SELECT * FROM " . _DB_PREFIX_ . "revslider_slide WHERE id_revslider_slide = %s", [$slide_class->get_id()]), true);

        if (!empty($record)) {
            self::add_new_backup($record[0], RevLoader::esc_attr($ajax_data['session_id']));
        }

    }

    /**
     * add new slide backup
     * @since: 1.0.0
     */
    public static function add_new_backup($slide, $session_id, $static = 'false') {

        global $wpdb;

        $slide['slide_id'] = $slide['id'];
        unset($slide['id']);

        $slide['created'] = date('Y-m-d H:i:s');
        $slide['session'] = $session_id;
        $slide['static'] = $static;

        //check if session_id exists, if yes then update
        $row = Db::getInstance()->executeS(Db::getInstance()->prepare("SELECT id FROM " . _DB_PREFIX_ . "revslider_backup_slides WHERE session = %s AND slide_id = %s", [$session_id, $slide['slide_id']]), true);

        if (!empty($row) && isset($row[0]) && !empty($row[0])) {
            $wpdb->update(_DB_PREFIX_ . "revslider_backup_slides", $slide, ['id' => $row[0]['id']]);
        } else {
            Db::getInstance()->insert("revslider_backup_slides", $slide);
        }

        $cur = self::check_backup_num($slide['slide_id']);

        if ($cur > 11) {
            $early = self::get_oldest_backup($slide['slide_id']);

            if ($early !== false) {
                self::delete_backup($early['id']);
            }

        }

    }

    /**
     * get oldest backup of a slide
     * @since: 1.0.0
     */
    public static function get_oldest_backup($slide_id) {

        global $wpdb;

        $early = Db::getInstance()->executeS(Db::getInstance()->prepare("SELECT id FROM " . _DB_PREFIX_ . "revslider_backup_slides WHERE slide_id = %s ORDER BY `created` ASC LIMIT 0,1", [$slide_id]), true);

        if (!empty($early)) {
            return $early[0];
        } else {
            return false;
        }

    }

    /**
     * check for the number of backups for a slide
     * @since: 1.0.0
     */
    public static function check_backup_num($slide_id) {

        global $wpdb;

        $cur = Db::getInstance()->executeS(Db::getInstance()->prepare("SELECT COUNT(*) AS `row` FROM " . _DB_PREFIX_ . "revslider_backup_slides WHERE slide_id = %s GROUP BY `slide_id`", [$slide_id]), true);

        if (!empty($cur)) {
            return $cur[0]['row'];
        } else {
            return 0;
        }

    }

    /**
     * delete a backup of a slide
     * @since: 1.0.0
     */
    public static function delete_backup($id) {

        global $wpdb;

        Db::getInstance()->execute(Db::getInstance()->prepare("DELETE FROM " . _DB_PREFIX_ . "revslider_backup_slides WHERE id = %s", [$id]));

    }

    /**
     * delete all backup of a slide
     * @since: 1.0.0
     */
    public function delete_backup_full($id) {

        global $wpdb;

        Db::getInstance()->execute(Db::getInstance()->prepare("DELETE FROM " . _DB_PREFIX_ . "revslider_backup_slides WHERE slide_id = %s", [$id]));

    }

    /**
     * delete all backup of a slide
     * @since: 1.0.0
     */
    public function delete_backup_full_slider($id) {

        global $wpdb;

        Db::getInstance()->execute(Db::getInstance()->prepare("DELETE FROM " . _DB_PREFIX_ . "revslider_backup_slides WHERE slider_id = %s", [$id]));

    }
    
    public static function update_addon_json() {

        if (RevLoader::_isCurl()) {

            $url = 'http://revapi.smartdatasoft.net/v6/call/json.php?type=addons';
            $response = RevLoader::wp_remote_post($url, [
                'user-agent' => 'php/;' . RevLoader::get_bloginfo('url'),
                'body'       => '',
                'timeout'    => 400,
            ]);

            if ($response['info']['http_code'] == '200') {
                $res = $response['body'];
                $addons = mb_convert_encoding($res, 'UTF-8');
                $results = (array) json_decode($addons);
                
                $new_counter = count($results);
                RevLoader::update_option('rs-addons-counter', $new_counter);
            }

            
            RevLoader::update_option('revslider-addons', $addons);
           

        }

    }


}
