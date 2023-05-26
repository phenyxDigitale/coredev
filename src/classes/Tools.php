<?php

use CommerceGuys\Intl\Currency\CurrencyRepository;
use CommerceGuys\Intl\Formatter\NumberFormatter;
use CommerceGuys\Intl\NumberFormat\NumberFormatRepository;
use PHPSQLParser\PHPSQLParser;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

/**
 * Class ToolsCore
 *
 * @since 1.9.1.0
 */
class Tools {

    /**
     * Bootstring parameter values
     *
     */
    const PUNYCODE_BASE = 36;
    const PUNYCODE_TMIN = 1;
    const PUNYCODE_TMAX = 26;
    const PUNYCODE_SKEW = 38;
    const PUNYCODE_DAMP = 700;
    const PUNYCODE_INITIAL_BIAS = 72;
    const PUNYCODE_INITIAL_N = 128;
    const PUNYCODE_PREFIX = 'xn--';
    const PUNYCODE_DELIMITER = '-';

    // @codingStandardsIgnoreStart
    public static $round_mode = null;
    protected static $file_exists_cache = [];
    protected static $_forceCompile;
    protected static $_caching;
    protected static $_user_plateform;
    protected static $_user_browser;
    protected static $_cache_nb_media_servers = null;
    protected static $is_addons_up = true;
    
    public static function checkLicense($purchaseKey, $website) {

		
        $context = Context::getContext();
        
        $key = Configuration::get(Configuration::EPHENYX_LICENSE_KEY);
        

		if ($key == $purchaseKey && $context->company->domain_ssl == $website) {
		      return true;
		}

		return false;

	}

    
    public static function passwdGen($length = 8, $flag = 'ALPHANUMERIC') {

        $length = (int) $length;

        if ($length <= 0) {
            return false;
        }

        switch ($flag) {
        case 'NUMERIC':
            $str = '0123456789';
            break;
        case 'NO_NUMERIC':
            $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'RANDOM':
            $numBytes = (int) ceil($length * 0.75);
            $bytes = static::getBytes($numBytes);

            return substr(rtrim(base64_encode($bytes), '='), 0, $length);
        case 'ALPHANUMERIC':
        default:
            $str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        }

        $bytes = Tools::getBytes($length);
        $position = 0;
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $position = ($position + ord($bytes[$i])) % strlen($str);
            $result .= $str[$position];
        }

        return $result;
    }

    
    public static function getBytes($length) {

        $length = (int) $length;

        if ($length <= 0) {
            return false;
        }

        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length, $cryptoStrong);

            if ($cryptoStrong === true) {
                return $bytes;
            }

        }

        if (function_exists('mcrypt_create_iv')) {
            $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);

            if ($bytes !== false && strlen($bytes) === $length) {
                return $bytes;
            }

        }

        // Else try to get $length bytes of entropy.
        // Thanks to Zend

        $result = '';
        $entropy = '';
        $msecPerRound = 400;
        $bitsPerRound = 2;
        $total = $length;
        $hashLength = 20;

        while (strlen($result) < $length) {
            $bytes = ($total > $hashLength) ? $hashLength : $total;
            $total -= $bytes;

            for ($i = 1; $i < 3; $i++) {
                $t1 = microtime(true);
                $seed = mt_rand();

                for ($j = 1; $j < 50; $j++) {
                    $seed = sha1($seed);
                }

                $t2 = microtime(true);
                $entropy .= $t1 . $t2;
            }

            $div = (int) (($t2 - $t1) * 1000000);

            if ($div <= 0) {
                $div = 400;
            }

            $rounds = (int) ($msecPerRound * 50 / $div);
            $iter = $bytes * (int) (ceil(8 / $bitsPerRound));

            for ($i = 0; $i < $iter; $i++) {
                $t1 = microtime();
                $seed = sha1(mt_rand());

                for ($j = 0; $j < $rounds; $j++) {
                    $seed = sha1($seed);
                }

                $t2 = microtime();
                $entropy .= $t1 . $t2;
            }

            $result .= sha1($entropy, true);
        }

        return substr($result, 0, $length);
    }

    public static function redirect($url, $baseUri = __EPH_BASE_URI__, Link $link = null, $headers = null) {
        
        if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            return Profiling::redirect($url, $baseUri, $link, $headers);
        }

        if (!$link) {
            $link = Context::getContext()->link;
        }

        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false && $link) {

            if (strpos($url, $baseUri) === 0) {
                $url = substr($url, strlen($baseUri));
            }

            if (strpos($url, 'index.php?controller=') !== false && strpos($url, 'index.php/') == 0) {
                $url = substr($url, strlen('index.php?controller='));

                if (Configuration::get(Configuration::REWRITING_SETTINGS)) {
                    $url = Tools::strReplaceFirst('&', '?', $url);
                }

            }

            $explode = explode('?', $url);
            // don't use ssl if url is home page
            // used when logout for example
            $useSsl = !empty($url);
            $url = $link->getPageLink($explode[0], $useSsl);

            if (isset($explode[1])) {
                $url .= '?' . $explode[1];
            }

        }

        // Send additional headers

        if ($headers) {

            if (!is_array($headers)) {
                $headers = [$headers];
            }

            foreach ($headers as $header) {
                header($header);
            }

        }

        header('Location: ' . $url);
        exit;
    }

    public static function strReplaceFirst($search, $replace, $subject, $cur = 0) {

         if(!is_null($subject)) {
			return (strpos($subject, $search, $cur)) ? substr_replace($subject, $replace, (int) strpos($subject, $search, $cur), strlen($search)) : $subject;
		}
		
		return $subject;
    }
    
    public static function str_replace_first($search, $replace, $subject) {
    	$search = '/'.preg_quote($search, '/').'/';
    	return preg_replace($search, $replace, $subject, 1);
	}
    
    public static function display_gallery_page($files_array, $pageno = 1, $path = '', $resultspp = 4096, $display = true) {
		if($path == 'undefined') {
            $path = '';
        }
		$composer = false; 
        if (str_contains($path, 'composer')) {
            $composer = true; 
        }
    	$pagination = array();
    	$pagination['resultspp'] = $resultspp;
    	$pagination['startres'] = 1 + (($pageno - 1) * $pagination['resultspp']);
    	$pagination['endres'] = $pagination['startres'] + $pagination['resultspp'] - 1;
    	$pagination['counter'] = 1;
    	$pagination['totalres'] = count($files_array);
    	$pagination['totalpages'] = ceil($pagination['totalres'] / $pagination['resultspp']);

    	$imagepath = '/content/img/';
    	$array_count = 0;
    	$output = '';

    	foreach ($files_array as $file_name) {
        	$file_path = $imagepath.$path.$file_name;
            if($composer) {
                $dataComposer = ComposerMedia::getIdMediaByName($file_name);
            }

        	if (($pagination['counter'] >= $pagination['startres']) && ($pagination['counter'] <= $pagination['endres'])) {
            	$addcbr = '';

            	if (($pagination['counter'] > ($pagination['endres'] - 5)) && ($pagination['counter'] > ($pagination['totalres'] - 5))) {
                	$addcbr = ' br';
            	}

            	$file = array();    // ???
            	$file['name'] = $file_name;

            	$files_array[$array_count] = array($file);
            	$array_count++;

            	if (preg_match('/\.(jpg|jpe|jpeg|png|gif|bmp)$/', $file_name)) {
                    if($composer) {
                        $output .= '<a href="'.$file_path.'" title="'.$file_name.'" data-gallery="gallery" data-image="'.$file_name.'" data-id="'.$dataComposer['id_vc_media'].'" data-field_id="'.$dataComposer['id_vc_media'].'" data-image-folder="'.$dataComposer['subdir'].'">';
                    } else {
                        $output .= '<a href="'.$file_path.'" title="'.$file_name.'" data-gallery="gallery" data-id="">';
                    }
                	
                	$output .= "<div class=\"thumb$addcbr\" style=\"background-image:url('$file_path')\"></div>";
                	$output .= '<label class="file-name">'.$file_name.'</label>';
                	$output .= '</a>';
            	} else {
                	$output .= '<a href="'.$file_path.'" title="'.$file_name.'" data-folder="folder">';
                	$output .= "<div class=\"thumb folder$addcbr\"></div>";
                	$output .= '<label class="file-name">'.$file_name.'</label>';
                	$output .= '</a>';
            	}
        	}
        	$pagination['counter']++;
    	}

    	if ($display == true) {
        	echo $output;
    	} else {
        	return $output;
    	}
	}
	
	public static function display_gallery_pagination($url = '', $totalresults = 0, $pageno = 1, $resultspp = 4096, $display = true) {
		
    	$configp = array();
    	$configp['results_per_page'] = $resultspp;
    	$configp['total_no_results'] = $totalresults;
    	$configp['page_url'] = $url;
    	$configp['current_page_segment'] = 4;
    	$configp['url'] = $url;
    	$configp['pageno'] = $pageno;

    	$output = Tools::get_html($configp);

    	if ($display == true) {
        	echo $output;
    	} else {
        	return $output;
    	}
	}
	
	public static function get_html($pconfig) {
    
		$links_html = '';

    // $pageAddress = $pconfig['url'];
    	$resultspp = $pconfig['results_per_page'];
    	$current_page = $pconfig['pageno'];
    	$start_res = $current_page * $resultspp;
    // $endRes = $start_res + $resultspp;

    	$tot_pages = $pconfig['total_no_results'] / $resultspp;

    	$round_pages = ceil($tot_pages);

    	$links_html .= '<ul>';

    	if ($current_page > 1) {
        	if ($tot_pages > 1) {
            	$links_html .= '<li id="gliFirst"><a data-target-page="1" href="#">&lt; First</a></li>';
        	}
        	$links_html .= '<li id="gliPrev"><a data-target-page="prev" href="#">Prev</a></li>';
    	} else {
        	if ($tot_pages > 1) {
            	$links_html .= '<li class="disabled" id="gliFirst"><a data-target-page="1" href="#">&lt; First</a></li>';
        	}
        	$links_html .= '<li class="disabled" id="gliPrev"><a data-target-page="prev" href="#">Prev</a></li>';
    	}

    // $pageLimit = 9;

    	if (($current_page - 3) > 0) {
        	$start_page = $current_page - 3;
    	} else {
        	$start_page = 1;
        	$end_add = 1 - ($current_page - 3);
    	}

    	$end_page = $round_pages;
    	$start_add = 0;

    	if (($start_page + $start_add) > 0) {
        	$start_page = $start_page - $start_add;
    	} else {
        	$start_page = 1;
    	}

    	if ($start_page <= 0) {
        	$start_page = 1;
    	}

    	for ($i = $start_page; $i <= $end_page; $i++) {
        	if ($i == $current_page) {
            	$links_html .= '<li class="disabled" id="gli$i"><a href="#" data-target-page="$i">$i</a></span></li>';
        	} else {
            	$links_html .= '<li id="gli$i"><a href="#" data-target-page="$i">$i</a></li>';
			}
    	}

    	if ($current_page < $round_pages) {
        // $nextPage = $current_page + 1;
        	$links_html .= '<li id="gliNext"><a href="#" data-target-page="next">Next</a></li>';

        	if ($tot_pages > 1) {
            	$links_html .= '<li id="gliLast"><a href="#" data-target-page="$round_pages">Last &gt;</a></li>';
        	}
    	} else {
        	$links_html .= '<li id="gliNext" class="disabled"><a href="#" data-target-page="next">Next</a></li>';

        	if ($tot_pages > 1) {
            	$links_html .= '<li id="gliLast" class="disabled"><a href="#" data-target-page="$round_pages">Last &gt;</a><li>';
        	}
    	}
		//if ($round_pages > 9) {}
    	$links_html .= '</ul>';
    	return $links_html;
	}

    
    public static function redirectLink($url) {
        
        if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            return Profiling::redirectLink($url);
        }

        if (!preg_match('@^https?://@i', $url)) {

            if (strpos($url, __EPH_BASE_URI__) !== false && strpos($url, __EPH_BASE_URI__) == 0) {
                $url = substr($url, strlen(__EPH_BASE_URI__));
            }

            if (strpos($url, 'index.php?controller=') !== false && strpos($url, 'index.php/') == 0) {
                $url = substr($url, strlen('index.php?controller='));
            }

            $explode = explode('?', $url);
            $url = Context::getContext()->link->getPageLink($explode[0]);

            if (isset($explode[1])) {
                $url .= '?' . $explode[1];
            }

        }

        header('Location: ' . $url);
        exit;
    }

    public static function redirectAdmin($url) {

        if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            return Profiling::redirectAdmin($url);
        }
        header('Location: ' . $url);
        exit;
    }

    public static function getProtocol() {

        $protocol = (Configuration::get(Configuration::SSL_ENABLED) || (!empty($_SERVER['HTTPS'])
            && mb_strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';

        return $protocol;
    }

    public static function strtolower($str) {

        if (is_array($str)) {
            return false;
        }

        return mb_strtolower($str, 'utf-8');
    }


    public static function getRemoteAddr() {

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }

        if (array_key_exists('X-Forwarded-For', $headers)) {
            $_SERVER['HTTP_X_FORWARDED_FOR'] = $headers['X-Forwarded-For'];
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR'])
            || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR']))
            || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR'])))
        ) {

            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                return $ips[0];
            } else {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

        } else {
            return $_SERVER['REMOTE_ADDR'];
        }

    }

    public static function getCurrentUrlProtocolPrefix() {

        if (Tools::usingSecureMode()) {
            return 'https://';
        } else {
            return 'http://';
        }

    }

    public static function usingSecureMode() {

        if (isset($_SERVER['HTTPS'])) {
            return in_array(mb_strtolower($_SERVER['HTTPS']), [1, 'on']);
        }

        // $_SERVER['SSL'] exists only in some specific configuration

        if (isset($_SERVER['SSL'])) {
            return in_array(mb_strtolower($_SERVER['SSL']), [1, 'on']);
        }

        // $_SERVER['REDIRECT_HTTPS'] exists only in some specific configuration

        if (isset($_SERVER['REDIRECT_HTTPS'])) {
            return in_array(mb_strtolower($_SERVER['REDIRECT_HTTPS']), [1, 'on']);
        }

        if (isset($_SERVER['HTTP_SSL'])) {
            return in_array(mb_strtolower($_SERVER['HTTP_SSL']), [1, 'on']);
        }

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
            return mb_strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https';
        }

        return false;
    }

    public static function secureReferrer($referrer) {

        if (preg_match('/^http[s]?:\/\/' . Tools::getServerName() . '(:' . _EPH_SSL_PORT_ . ')?\/.*$/Ui', $referrer)) {
            return $referrer;
        }

        return __EPH_BASE_URI__;
    }
    
    public static function getServerName() {

        if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER']) {
            return $_SERVER['HTTP_X_FORWARDED_SERVER'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    public static function getAllValues() {

        return $_POST + $_GET;
    }

    public static function getIsset($key) {

        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }

        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    public static function setCookieLanguage(Cookie $cookie = null) {

        if (!$cookie) {
            $cookie = Context::getContext()->cookie;
        }

        /* If language does not exist or is disabled, erase it */

        if ($cookie->id_lang) {
            $lang = new Language((int) $cookie->id_lang);

            if (!Validate::isLoadedObject($lang) || !$lang->active ) {
                $cookie->id_lang = null;
            }

        }

        if (!Configuration::get('EPH_DETECT_LANG')) {
            unset($cookie->detect_language);
        }

        /* Automatically detect language if not already defined, detect_language is set in Cookie::update */

        if (!Tools::getValue('isolang') && !Tools::getValue('id_lang') && (!$cookie->id_lang || isset($cookie->detect_language))
            && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])
        ) {
            $array = explode(',', mb_strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
            $string = $array[0];

            if (Validate::isLanguageCode($string)) {
                $lang = Language::getLanguageByIETFCode($string);

                if (Validate::isLoadedObject($lang) && $lang->active ) {
                    Context::getContext()->language = $lang;
                    $cookie->id_lang = (int) $lang->id;
                }

            }

        }

        if (isset($cookie->detect_language)) {
            unset($cookie->detect_language);
        }

        /* If language file not present, you must use default language file */

        if (!$cookie->id_lang || !Validate::isUnsignedId($cookie->id_lang)) {
            $cookie->id_lang = (int) Context::getContext()->language->id;
        }

        $iso = Language::getIsoById((int) $cookie->id_lang);
        @include_once _EPH_THEME_DIR_ . 'lang/' . $iso . '.php';

        return $iso;
    }

    public static function getValue($key, $defaultValue = false) {

        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }

        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));

        if (is_string($ret)) {
            return stripslashes(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret))));
        }

        return $ret;
    }

    public static function switchLanguage(Context $context = null) {

        if (!$context) {
            $context = Context::getContext();
        }

        
        if (!isset($context->cookie)) {
            return;
        }

        if (($iso = Tools::getValue('isolang')) && Validate::isLanguageIsoCode($iso) && ($idLang = (int) Language::getIdByIso($iso))) {
            $_GET['id_lang'] = $idLang;
        }

       
        $cookieIdLang = $context->cookie->id_lang;
        $configurationIdLang = $context->language->id;

        if ((($idLang = (int) Tools::getValue('id_lang')) && Validate::isUnsignedId($idLang) && $cookieIdLang != (int) $idLang)
            || (($idLang == $configurationIdLang) && Validate::isUnsignedId($idLang) && $idLang != $cookieIdLang)
        ) {
            $context->cookie->id_lang = $idLang;
            $language = new Language($idLang);

            if (Validate::isLoadedObject($language) && $language->active) {
                $context->language = $language;
            }

            $params = $_GET;

            if (Configuration::get(Configuration::REWRITING_SETTINGS) || !Language::isMultiLanguageActivated()) {
                unset($params['id_lang']);
            }

        }

    }

    public static function getCountry($address = null) {

        $idCountry = (int) Tools::getValue('id_country');

        if ($idCountry && Validate::isInt($idCountry)) {
            return (int) $idCountry;
        } else
        if (!$idCountry && isset($address) && isset($address->id_country) && $address->id_country) {

            $idCountry = (int) $address->id_country;
        } else
        if (Configuration::get('EPH_DETECT_COUNTRY') && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            preg_match('#(?<=-)\w\w|\w\w(?!-)#', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $array);

            if (is_array($array) && isset($array[0]) && Validate::isLanguageIsoCode($array[0])) {
                $idCountry = (int) Country::getByIso($array[0], true);
            }

        }

        if (!isset($idCountry) || !$idCountry) {
            $idCountry = (int) Configuration::get('EPH_COUNTRY_DEFAULT');
        }

        return (int) $idCountry;
    }

    public static function isSubmit($submit) {

        return (
            isset($_POST[$submit]) || isset($_POST[$submit . '_x']) || isset($_POST[$submit . '_y'])
            || isset($_GET[$submit]) || isset($_GET[$submit . '_x']) || isset($_GET[$submit . '_y'])
        );
    }

    public static function array_replace() {

        if (!function_exists('array_replace')) {
            $args = func_get_args();
            $numArgs = func_num_args();
            $res = [];

            for ($i = 0; $i < $numArgs; $i++) {

                if (is_array($args[$i])) {

                    foreach ($args[$i] as $key => $val) {
                        $res[$key] = $val;
                    }

                } else {
                    trigger_error(__FUNCTION__ . '(): Argument #' . ($i + 1) . ' is not an array', E_USER_WARNING);

                    return null;
                }

            }

            return $res;
        } else {
            return call_user_func_array('array_replace', func_get_args());
        }

    }

    public static function dateFormat($params, $smarty) {

        return Tools::displayDate($params['date'], null, (isset($params['full']) ? $params['full'] : false));
    }

   
    public static function displayDate($date, $idLang = null, $full = false, $separator = null) {

        if ($idLang !== null) {
            Tools::displayParameterAsDeprecated('id_lang');
        }

        if ($separator !== null) {
            Tools::displayParameterAsDeprecated('separator');
        }

        if (!$date || !($time = strtotime($date))) {
            return $date;
        }

        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }

        if (!Validate::isDate($date) || !Validate::isBool($full)) {
            throw new PhenyxException('Invalid date');
        }

        $context = Context::getContext();
        $dateFormat = ($full ? $context->language->date_format_full : $context->language->date_format_lite);

        return date($dateFormat, $time);
    }

    
    public static function displayParameterAsDeprecated($parameter) {

        $backtrace = debug_backtrace();
        $callee = next($backtrace);
        $error = 'Parameter <b>' . $parameter . '</b> in function <b>' . (isset($callee['function']) ? $callee['function'] : '') . '()</b> is deprecated in <b>' . $callee['file'] . '</b> on line <b>' . (isset($callee['line']) ? $callee['line'] : '(undefined)') . '</b><br />';
        $message = 'The parameter ' . $parameter . ' in function ' . $callee['function'] . ' (Line ' . (isset($callee['line']) ? $callee['line'] : 'undefined') . ') is deprecated and will be removed in the next major version.';
        $class = isset($callee['class']) ? $callee['class'] : null;

        Tools::throwDeprecated($error, $message, $class);
    }

    protected static function throwDeprecated($error, $message, $class) {

        if (_EPH_DISPLAY_COMPATIBILITY_WARNING_) {
            trigger_error($error, E_USER_WARNING);
            Logger::addLog($message, 3, $class);
        }

    }

    public static function htmlentitiesDecodeUTF8($string) {

        if (is_array($string)) {
            $string = array_map(['Tools', 'htmlentitiesDecodeUTF8'], $string);

            return (string) array_shift($string);
        }

        return html_entity_decode((string) $string, ENT_QUOTES, 'utf-8');
    }

    
    public static function safePostVars() {

        if (!isset($_POST) || !is_array($_POST)) {
            $_POST = [];
        } else {
            $_POST = array_map(['Tools', 'htmlentitiesUTF8'], $_POST);
        }

    }

    
    public static function deleteDirectory($dirname, $deleteSelf = true) {

        $dirname = rtrim($dirname, '/') . '/';

        if (file_exists($dirname)) {

            if ($files = scandir($dirname)) {

                foreach ($files as $file) {

                    if ($file != '.' && $file != '..' && $file != '.svn') {

                        if (is_dir($dirname . $file)) {
                            Tools::deleteDirectory($dirname . $file, true);
                        } else
                        if (file_exists($dirname . $file)) {
                            @chmod($dirname . $file, 0777); // NT ?
                            unlink($dirname . $file);
                        }

                    }

                }

                if ($deleteSelf && file_exists($dirname)) {

                    if (!rmdir($dirname)) {
                        @chmod($dirname, 0777); // NT ?

                        return false;
                    }

                }

                return true;
            }

        }

        return false;
    }

   
    public static function deleteFile($file, $excludeFiles = []) {

        if (isset($excludeFiles) && !is_array($excludeFiles)) {
            $excludeFiles = [$excludeFiles];
        }

        if (file_exists($file) && is_file($file) && array_search(basename($file), $excludeFiles) === false) {
            @chmod($file, 0777); // NT ?
            unlink($file);
        }

    }

    public static function fd($object, $type = 'log') {

        $types = ['log', 'debug', 'info', 'warn', 'error', 'assert'];

        if (!in_array($type, $types)) {
            $type = 'log';
        }

        echo '
            <script type="text/javascript">
                console.' . $type . '(' . json_encode($object) . ');
            </script>
        ';
    }

    public static function d($object, $kill = true) {

        return (Tools::dieObject($object, $kill));
    }

    public static function dieObject($object, $kill = true) {

        echo '<xmp style="text-align: left;">';
        print_r($object);
        echo '</xmp><br />';

        if ($kill) {
            die('END');
        }

        return $object;
    }

    
    public static function debug_backtrace($start = 0, $limit = null) {

        $backtrace = debug_backtrace();
        array_shift($backtrace);

        for ($i = 0; $i < $start; ++$i) {
            array_shift($backtrace);
        }

        echo '
        <div style="margin:10px;padding:10px;border:1px solid #666666">
            <ul>';
        $i = 0;

        foreach ($backtrace as $id => $trace) {

            if ((int) $limit && (++$i > $limit)) {
                break;
            }

            $relativeFile = (isset($trace['file'])) ? 'in /' . ltrim(str_replace([_EPH_ROOT_DIR_, '\\'], ['', '/'], $trace['file']), '/') : '';
            $currentLine = (isset($trace['line'])) ? ':' . $trace['line'] : '';

            echo '<li>
                <b>' . ((isset($trace['class'])) ? $trace['class'] : '') . ((isset($trace['type'])) ? $trace['type'] : '') . $trace['function'] . '</b>
                ' . $relativeFile . $currentLine . '
            </li>';
        }

        echo '</ul>
        </div>';
    }

   
    public static function p($object) {

        return (Tools::dieObject($object, false));
    }

   
    public static function error_log($object) {

       
        return error_log(print_r($object, true));
    }


    public static function displayAsDeprecated($message = null) {

        $backtrace = debug_backtrace();
        $callee = next($backtrace);
        $class = isset($callee['class']) ? $callee['class'] : null;

        if ($message === null) {
            $message = 'The function ' . $callee['function'] . ' (Line ' . $callee['line'] . ') is deprecated and will be removed in the next major version.';
        }

        $error = 'Function <b>' . $callee['function'] . '()</b> is deprecated in <b>' . $callee['file'] . '</b> on line <b>' . $callee['line'] . '</b><br />';

        Tools::throwDeprecated($error, $message, $class);
    }

    public static function hash($password) {

        return password_hash($password, PASSWORD_BCRYPT);
    }

    public static function encryptIV($data) {

        return md5(_COOKIE_IV_ . $data);
    }

    public static function getToken($page = true, Context $context = null) {

        if (!$context) {
            $context = Context::getContext();
        }

        if ($page === true) {
            return (Tools::encrypt($context->user->id . $context->user->passwd . $_SERVER['SCRIPT_NAME']));
        } else {
            return (Tools::encrypt($context->user->id . $context->user->passwd . $page));
        }

    }

    public static function encrypt($passwd) {

        return md5(_COOKIE_KEY_ . $passwd);
    }

    public static function getAdminTokenLite($tab, Context $context = null) {

        if (!$context) {
            $context = Context::getContext();
        }

        return Tools::getAdminToken($tab . (int) EmployeeMenu::getIdFromClassName($tab) . (int) $context->employee->id);
    }

    
    public static function getAdminToken($string) {

        return !empty($string) ? Tools::encrypt($string) : false;
    }

    public static function getAdminTokenLiteSmarty($params, $smarty) {

        $context = Context::getContext();

        return Tools::getAdminToken($params['tab'] . (int) EmployeeMenu::getIdFromClassName($params['tab']) . (int) $context->employee->id);
    }

    public static function getAdminImageUrl($image = null, $entities = false) {

        return Tools::getAdminUrl(basename(_EPH_IMG_DIR_) . '/' . $image, $entities);
    }

    
    public static function getAdminUrl($url = null, $entities = false) {

        $link = Tools::getHttpHost(true) . __EPH_BASE_URI__;

        if (isset($url)) {
            $link .= ($entities ? Tools::htmlentitiesUTF8($url) : $url);
        }

        return $link;
    }

    
    public static function getHttpHost($http = false, $entities = false, $ignore_port = false) {

        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);

        if ($ignore_port && $pos = strpos($host, ':')) {
            $host = substr($host, 0, $pos);
        }

        if ($entities) {
            $host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
        }

        if ($http) {
            $host = (Configuration::get(Configuration::SSL_ENABLED) ? 'https://' : 'http://') . $host;
        }

        return $host;
    }

    
    public static function htmlentitiesUTF8($string, $type = ENT_QUOTES) {

        if (is_array($string)) {
            return array_map(['Tools', 'htmlentitiesUTF8'], $string);
        }

        return htmlentities((string) $string, $type, 'utf-8');
    }

    public static function safeOutput($string, $html = false) {

        if (!$html) {
            $string = strip_tags($string);
        }

        return @Tools::htmlentitiesUTF8($string, ENT_QUOTES);
    }

    public static function displayError($string = 'Fatal error', $htmlentities = true, Context $context = null) {

        global $_ERRORS;

        if (is_null($context)) {
            $context = Context::getContext();
        }

        @include_once _EPH_TRANSLATIONS_DIR_ . $context->language->iso_code . '/errors.php';

        if (defined('_EPH_MODE_DEV_') && _EPH_MODE_DEV_ && $string == 'Fatal error') {
            return ('<pre>' . print_r(debug_backtrace(), true) . '</pre>');
        }

        if (!is_array($_ERRORS)) {
            return $htmlentities ? Tools::htmlentitiesUTF8($string) : $string;
        }

        $key = md5(str_replace('\'', '\\\'', $string));
        $str = (isset($_ERRORS) && is_array($_ERRORS) && array_key_exists($key, $_ERRORS)) ? $_ERRORS[$key] : $string;

        return $htmlentities ? Tools::htmlentitiesUTF8(stripslashes($str)) : $str;
    }

    
    public static function link_rewrite($str, $utf8Decode = null) {

        if ($utf8Decode !== null) {
            Tools::displayParameterAsDeprecated('utf8_decode');
        }

        return Tools::str2url($str);
    }

    public static function str2url($str) {

        static $arrayStr = [];
        static $allowAccentedChars = null;
        static $hasMbStrtolower = null;

        if ($hasMbStrtolower === null) {
            $hasMbStrtolower = function_exists('mb_strtolower');
        }

        if (isset($arrayStr[$str])) {
            return $arrayStr[$str];
        }

        if (!is_string($str)) {
            return false;
        }

        if ($str == '') {
            return '';
        }

        if ($allowAccentedChars === null) {
            $allowAccentedChars = Configuration::get('EPH_ALLOW_ACCENTED_CHARS_URL');
        }

        $returnStr = trim($str);

        if ($hasMbStrtolower) {
            $returnStr = mb_strtolower($returnStr, 'utf-8');
        }

        if (!$allowAccentedChars) {
            $returnStr = Tools::replaceAccentedChars($returnStr);
        }

        // Remove all non-whitelist chars.

        if ($allowAccentedChars) {
            $returnStr = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-\p{L}]/u', '', $returnStr);
        } else {
            $returnStr = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]\-]/', '', $returnStr);
        }

        $returnStr = preg_replace('/[\s\'\:\/\[\]\-]+/', ' ', $returnStr);
        $returnStr = str_replace([' ', '/'], '-', $returnStr);

        // If it was not possible to lowercase the string with mb_strtolower, we do it after the transformations.
        // This way we lose fewer special chars.

        if (!$hasMbStrtolower) {
            $returnStr = mb_strtolower($returnStr);
        }

        $arrayStr[$str] = $returnStr;

        return $returnStr;
    }

    
    public static function replaceAccentedChars($str) {

        /* One source among others:
                                    http://www.tachyonsoft.com/uc0000.htm
                                    http://www.tachyonsoft.com/uc0001.htm
                                    http://www.tachyonsoft.com/uc0004.htm
        */
        $patterns = [

            /* Lowercase */
            /* a  */
            '/[\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}\x{0101}\x{0103}\x{0105}\x{0430}\x{00C0}-\x{00C3}\x{1EA0}-\x{1EB7}]/u',
            /* b  */
            '/[\x{0431}]/u',
            /* c  */
            '/[\x{00E7}\x{0107}\x{0109}\x{010D}\x{0446}]/u',
            /* d  */
            '/[\x{010F}\x{0111}\x{0434}\x{0110}\x{00F0}]/u',
            /* e  */
            '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}\x{0435}\x{044D}\x{00C8}-\x{00CA}\x{1EB8}-\x{1EC7}]/u',
            /* f  */
            '/[\x{0444}]/u',
            /* g  */
            '/[\x{011F}\x{0121}\x{0123}\x{0433}\x{0491}]/u',
            /* h  */
            '/[\x{0125}\x{0127}]/u',
            /* i  */
            '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}\x{0438}\x{0456}\x{00CC}\x{00CD}\x{1EC8}-\x{1ECB}\x{0128}]/u',
            /* j  */
            '/[\x{0135}\x{0439}]/u',
            /* k  */
            '/[\x{0137}\x{0138}\x{043A}]/u',
            /* l  */
            '/[\x{013A}\x{013C}\x{013E}\x{0140}\x{0142}\x{043B}]/u',
            /* m  */
            '/[\x{043C}]/u',
            /* n  */
            '/[\x{00F1}\x{0144}\x{0146}\x{0148}\x{0149}\x{014B}\x{043D}]/u',
            /* o  */
            '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{014D}\x{014F}\x{0151}\x{043E}\x{00D2}-\x{00D5}\x{01A0}\x{01A1}\x{1ECC}-\x{1EE3}]/u',
            /* p  */
            '/[\x{043F}]/u',
            /* r  */
            '/[\x{0155}\x{0157}\x{0159}\x{0440}]/u',
            /* s  */
            '/[\x{015B}\x{015D}\x{015F}\x{0161}\x{0441}]/u',
            /* ss */
            '/[\x{00DF}]/u',
            /* t  */
            '/[\x{0163}\x{0165}\x{0167}\x{0442}]/u',
            /* u  */
            '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{0169}\x{016B}\x{016D}\x{016F}\x{0171}\x{0173}\x{0443}\x{00D9}-\x{00DA}\x{0168}\x{01AF}\x{01B0}\x{1EE4}-\x{1EF1}]/u',
            /* v  */
            '/[\x{0432}]/u',
            /* w  */
            '/[\x{0175}]/u',
            /* y  */
            '/[\x{00FF}\x{0177}\x{00FD}\x{044B}\x{1EF2}-\x{1EF9}\x{00DD}]/u',
            /* z  */
            '/[\x{017A}\x{017C}\x{017E}\x{0437}]/u',
            /* ae */
            '/[\x{00E6}]/u',
            /* ch */
            '/[\x{0447}]/u',
            /* kh */
            '/[\x{0445}]/u',
            /* oe */
            '/[\x{0153}]/u',
            /* sh */
            '/[\x{0448}]/u',
            /* shh*/
            '/[\x{0449}]/u',
            /* ya */
            '/[\x{044F}]/u',
            /* ye */
            '/[\x{0454}]/u',
            /* yi */
            '/[\x{0457}]/u',
            /* yo */
            '/[\x{0451}]/u',
            /* yu */
            '/[\x{044E}]/u',
            /* zh */
            '/[\x{0436}]/u',

            /* Uppercase */
            /* A  */
            '/[\x{0100}\x{0102}\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}\x{0410}]/u',
            /* B  */
            '/[\x{0411}]/u',
            /* C  */
            '/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}\x{0426}]/u',
            /* D  */
            '/[\x{010E}\x{0110}\x{0414}\x{00D0}]/u',
            /* E  */
            '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}\x{0415}\x{042D}]/u',
            /* F  */
            '/[\x{0424}]/u',
            /* G  */
            '/[\x{011C}\x{011E}\x{0120}\x{0122}\x{0413}\x{0490}]/u',
            /* H  */
            '/[\x{0124}\x{0126}]/u',
            /* I  */
            '/[\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}\x{0418}\x{0406}]/u',
            /* J  */
            '/[\x{0134}\x{0419}]/u',
            /* K  */
            '/[\x{0136}\x{041A}]/u',
            /* L  */
            '/[\x{0139}\x{013B}\x{013D}\x{0139}\x{0141}\x{041B}]/u',
            /* M  */
            '/[\x{041C}]/u',
            /* N  */
            '/[\x{00D1}\x{0143}\x{0145}\x{0147}\x{014A}\x{041D}]/u',
            /* O  */
            '/[\x{00D3}\x{014C}\x{014E}\x{0150}\x{041E}]/u',
            /* P  */
            '/[\x{041F}]/u',
            /* R  */
            '/[\x{0154}\x{0156}\x{0158}\x{0420}]/u',
            /* S  */
            '/[\x{015A}\x{015C}\x{015E}\x{0160}\x{0421}]/u',
            /* T  */
            '/[\x{0162}\x{0164}\x{0166}\x{0422}]/u',
            /* U  */
            '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{0168}\x{016A}\x{016C}\x{016E}\x{0170}\x{0172}\x{0423}]/u',
            /* V  */
            '/[\x{0412}]/u',
            /* W  */
            '/[\x{0174}]/u',
            /* Y  */
            '/[\x{0176}\x{042B}]/u',
            /* Z  */
            '/[\x{0179}\x{017B}\x{017D}\x{0417}]/u',
            /* AE */
            '/[\x{00C6}]/u',
            /* CH */
            '/[\x{0427}]/u',
            /* KH */
            '/[\x{0425}]/u',
            /* OE */

            '/[\x{0152}]/u',
            /* SH */
            '/[\x{0428}]/u',
            /* SHH*/
            '/[\x{0429}]/u',
            /* YA */
            '/[\x{042F}]/u',
            /* YE */
            '/[\x{0404}]/u',
            /* YI */
            '/[\x{0407}]/u',
            /* YO */
            '/[\x{0401}]/u',
            /* YU */
            '/[\x{042E}]/u',
            /* ZH */
            '/[\x{0416}]/u',
        ];

        // ö to oe
        // å to aa
        // ä to ae

        $replacements = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 'ss', 't', 'u', 'v', 'w', 'y', 'z', 'ae', 'ch', 'kh', 'oe', 'sh', 'shh', 'ya', 'ye', 'yi', 'yo', 'yu', 'zh',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'Z', 'AE', 'CH', 'KH', 'OE', 'SH', 'SHH', 'YA', 'YE', 'YI', 'YO', 'YU', 'ZH',
        ];

        return preg_replace($patterns, $replacements, $str);
    }

    public static function truncate($str, $maxLength, $suffix = '...') {

        if (mb_strlen($str) <= $maxLength) {
            return $str;
        }

        $str = utf8_decode($str);

        return (utf8_encode(substr($str, 0, $maxLength - mb_strlen($suffix)) . $suffix));
    }
    
    public static function strlen($str, $encoding = 'UTF-8') {

        if (is_array($str)) {
            return false;
        }

        return mb_strlen($str, $encoding);
    }

    public static function truncateString($text, $length = 120, $options = []) {

        $default = [
            'ellipsis' => '...', 'exact' => true, 'html' => true,
        ];

        $options = array_merge($default, $options);
        extract($options);
        /**
         * @var string $ellipsis
         * @var bool   $exact
         * @var bool   $html
         */

        if ($html) {

            if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }

            $totalLength = mb_strlen(strip_tags($ellipsis));
            $openTags = [];
            $truncate = '';
            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);

            foreach ($tags as $tag) {

                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {

                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($openTags, $tag[2]);
                    } else
                    if (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $closeTag)) {
                        $pos = array_search($closeTag[1], $openTags);

                        if ($pos !== false) {
                            array_splice($openTags, $pos, 1);
                        }

                    }

                }

                $truncate .= $tag[1];
                $contentLength = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));

                if ($contentLength + $totalLength > $length) {
                    $left = $length - $totalLength;
                    $entitiesLength = 0;

                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {

                        foreach ($entities[0] as $entity) {

                            if ($entity[1] + 1 - $entitiesLength <= $left) {
                                $left--;
                                $entitiesLength += mb_strlen($entity[0]);
                            } else {
                                break;
                            }

                        }

                    }

                    $truncate .= mb_substr($tag[3], 0, $left + $entitiesLength);
                    break;
                } else {
                    $truncate .= $tag[3];
                    $totalLength += $contentLength;
                }

                if ($totalLength >= $length) {
                    break;
                }

            }

        } else {

            if (mb_strlen($text) <= $length) {
                return $text;
            }

            $truncate = mb_substr($text, 0, $length - mb_strlen($ellipsis));
        }

        if (!$exact) {
            $spacepos = mb_strrpos($truncate, ' ');

            if ($html) {
                $truncateCheck = mb_substr($truncate, 0, $spacepos);
                $lastOpenTag = mb_strrpos($truncateCheck, '<');
                $lastCloseTag = mb_strrpos($truncateCheck, '>');

                if ($lastOpenTag > $lastCloseTag) {
                    preg_match_all('/<[\w]+[^>]*>/s', $truncate, $lastTagMatches);
                    $lastTag = array_pop($lastTagMatches[0]);
                    $spacepos = mb_strrpos($truncate, $lastTag) + mb_strlen($lastTag);
                }

                $bits = mb_substr($truncate, $spacepos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $droppedTags, PREG_SET_ORDER);

                if (!empty($droppedTags)) {

                    if (!empty($openTags)) {

                        foreach ($droppedTags as $closing_tag) {

                            if (!in_array($closing_tag[1], $openTags)) {
                                array_unshift($openTags, $closing_tag[1]);
                            }

                        }

                    } else {

                        foreach ($droppedTags as $closing_tag) {
                            $openTags[] = $closing_tag[1];
                        }

                    }

                }

            }

            $truncate = mb_substr($truncate, 0, $spacepos);
        }

        $truncate .= $ellipsis;

        if ($html) {

            foreach ($openTags as $tag) {
                $truncate .= '</' . $tag . '>';
            }

        }

        return $truncate;
    }

    public static function substr($str, $start, $length = false, $encoding = 'utf-8') {

        if (is_array($str)) {
            return false;
        }

        return mb_substr($str, (int) $start, ($length === false ? mb_strlen($str) : (int) $length), $encoding);
    }
    
    public static function strrpos($str, $find, $offset = 0, $encoding = 'utf-8') {

        return mb_strrpos($str, $find, $offset, $encoding);
    }

    public static function normalizeDirectory($directory) {

        return rtrim($directory, '/\\') . DIRECTORY_SEPARATOR;
    }

    public static function dateYears() {

        $tab = [];

        for ($i = date('Y'); $i >= 1900; $i--) {
            $tab[] = $i;
        }

        return $tab;
    }

    public static function dateDays() {

        $tab = [];

        for ($i = 1; $i != 32; $i++) {
            $tab[] = $i;
        }

        return $tab;
    }

    public static function dateMonths() {

        $tab = [];

        for ($i = 1; $i != 13; $i++) {
            $tab[$i] = date('F', mktime(0, 0, 0, $i, date('m'), date('Y')));
        }

        return $tab;
    }

    public static function dateFrom($date) {

        $tab = explode(' ', $date);

        if (!isset($tab[1])) {
            $date .= ' ' . Tools::hourGenerate(0, 0, 0);
        }

        return $date;
    }

    public static function hourGenerate($hours, $minutes, $seconds) {

        return implode(':', [$hours, $minutes, $seconds]);
    }

    public static function dateTo($date) {

        $tab = explode(' ', $date);

        if (!isset($tab[1])) {
            $date .= ' ' . Tools::hourGenerate(23, 59, 59);
        }

        return $date;
    }

    public static function stripslashes($string) {

        if (_EPH_MAGIC_QUOTES_GPC_) {
            $string = stripslashes($string);
        }

        return $string;
    }

    public static function strpos($str, $find, $offset = 0, $encoding = 'UTF-8') {

        return mb_strpos($str, $find, $offset, $encoding);
    }

    public static function ucwords($str) {

        if (function_exists('mb_convert_case')) {
            return mb_convert_case($str, MB_CASE_TITLE);
        }

        return ucwords(mb_strtolower($str));
    }

    
    public static function iconv($from, $to, $string) {

        if (function_exists('iconv')) {
            return iconv($from, $to . '//TRANSLIT', str_replace('¥', '&yen;', str_replace('£', '&pound;', str_replace('€', '&euro;', $string))));
        }

        return html_entity_decode(htmlentities($string, ENT_NOQUOTES, $from), ENT_NOQUOTES, $to);
    }

    public static function isEmpty($field) {

        return ($field === '' || $field === null);
    }

    public static function file_exists_no_cache($filename) {

        clearstatcache(true, $filename);

        return file_exists($filename);
    }

    public static function file_get_contents($url, $useIncludePath = false, $streamContext = null, $curlTimeout = 5) {

        if ($streamContext == null && preg_match('/^https?:\/\//', $url)) {
            $streamContext = @stream_context_create(['http' => ['timeout' => $curlTimeout]]);
        }

        if (is_resource($streamContext)) {
            $opts = stream_context_get_options($streamContext);
        }

        // Remove the Content-Length header -- let cURL/fopen handle it

        if (!empty($opts['http']['header'])) {
            $headers = explode("\r\n", $opts['http']['header']);

            foreach ($headers as $index => $header) {

                if (substr(strtolower($header), 0, 14) === 'content-length') {
                    unset($headers[$index]);
                }

            }

            $opts['http']['header'] = implode("\r\n", $headers);
            stream_context_set_option($streamContext, ['http' => $opts['http']]);
        }

        if (!preg_match('/^https?:\/\//', $url)) {
            return @file_get_contents($url, $useIncludePath, $streamContext);
        } else
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curlTimeout);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

            if (!empty($opts['http']['header'])) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, explode("\r\n", $opts['http']['header']));
            }

            if ($streamContext != null) {

                if (isset($opts['http']['method']) && mb_strtolower($opts['http']['method']) == 'post') {
                    curl_setopt($curl, CURLOPT_POST, true);

                    if (isset($opts['http']['content'])) {
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $opts['http']['content']);
                    }

                }

            }

            $content = curl_exec($curl);
            curl_close($curl);

            return $content;
        } else
        if (ini_get('allow_url_fopen')) {
            return @file_get_contents($url, $useIncludePath, $streamContext);
        } else {
            return false;
        }

    }

    
    public static function simplexml_load_file($url, $class_name = null) {

        $cache_id = 'Tools::simplexml_load_file' . $url;

        if (!Cache::isStored($cache_id)) {
            $guzzle = new \GuzzleHttp\Client([
                'verify'  => DIGITAL_CORE_DIR . '/vendor/cacert.pem',
                'timeout' => 20,
            ]);
            try {
                $result = @simplexml_load_string((string) $guzzle->get($url)->getBody(), $class_name);
            } catch (Exception $e) {
                return null;
            }

            Cache::store($cache_id, $result);

            return $result;
        }

        return Cache::retrieve($cache_id);
    }

    public static function copy($source, $destination, $streamContext = null) {

        if ($streamContext) {
            Tools::displayParameterAsDeprecated('streamContext');
        }

        if (!preg_match('/^https?:\/\//', $source)) {
            return @copy($source, $destination);
        }

        $timeout = ini_get('max_execution_time');

        if (!$timeout || $timeout > 600) {
            $timeout = 600;
        }

        $timeout -= 5; // Room for other processing.

        $guzzle = new \GuzzleHttp\Client([
            'verify'  => __DIR__ . '/../cacert.pem',
            'timeout' => $timeout,
        ]);

        try {
            $guzzle->get($source, ['sink' => $destination]);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    public static function toCamelCase($str, $catapitaliseFirstChar = false) {

        $str = mb_strtolower($str);

        if ($catapitaliseFirstChar) {
            $str = Tools::ucfirst($str);
        }

        return preg_replace_callback('/_+([a-z])/', function ($c) {

            return strtoupper($c[1]);
        }, $str);
    }

    public static function ucfirst($str) {

        return ucfirst($str);
    }

    public static function strtoupper($str) {

        if (is_array($str)) {
            return false;
        }

        return mb_strtoupper($str, 'utf-8');
    }

    public static function toUnderscoreCase($string) {
        
        return mb_strtolower(trim(preg_replace('/([A-Z][a-z])/', '_$1', $string), '_'));
    }

    
    public static function getBrightness($hex) {

        if (mb_strtolower($hex) == 'transparent') {
            return '129';
        }

        $hex = str_replace('#', '', $hex);

        if (mb_strlen($hex) == 3) {
            $hex .= $hex;
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    }

    public static function parserSQL($sql) {

        if (strlen($sql) > 0) {
            $parser = new PHPSQLParser($sql);

            return $parser->parsed;
        }

        return false;
    }

    public static function getMediaServer($filename) {        

        return Tools::usingSecureMode() ? Tools::getDomainSSL() : Tools::getDomain();
    }

    public static function getDomainSsl($http = false, $entities = false) {

        $domain = Tools::getHttpHost();

        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }

        if ($http) {
            $domain = (Configuration::get(Configuration::SSL_ENABLED) ? 'https://' : 'http://') . $domain;
        }

        return $domain;
    }

    public static function getDomain($http = false, $entities = false) {

        $domain = Tools::getHttpHost();

        if ($entities) {
            $domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
        }

        if ($http) {
            $domain = 'http://' . $domain;
        }

        return $domain;
    }

     public static function generateHtaccess($path = null, $rewrite_settings = null, $cache_control = null, $specific = '', $disable_multiviews = null, $medias = false, $disable_modsec = null) {

        if (defined('EPH_INSTALLATION_IN_PROGRESS') && $rewrite_settings === null) {
            return true;
        }
        
        // Default values for parameters

        if (is_null($path)) {
            $path = _EPH_ROOT_DIR_ . '/.htaccess';
        }

        if (is_null($cache_control)) {
            $cache_control = (int) Configuration::get('EPH_HTACCESS_CACHE_CONTROL');
        }

        if (is_null($disable_multiviews)) {
            $disable_multiviews = (int) Configuration::get('EPH_HTACCESS_DISABLE_MULTIVIEWS');
        }

        if ($disable_modsec === null) {
            $disable_modsec = (int) Configuration::get('EPH_HTACCESS_DISABLE_MODSEC');
        }

        // Check current content of .htaccess and save all code outside of ephenyx comments
        $specific_before = $specific_after = '';

        if (file_exists($path)) {

            if (static::isSubmit('htaccess')) {
                $content = static::getValue('htaccess');
            } else {
                $content = file_get_contents($path);
            }

            if (preg_match('#^(.*)\# ~~start~~.*\# ~~end~~[^\n]*(.*)$#s', $content, $m)) {
                $specific_before = $m[1];
                $specific_after = $m[2];
            } else {
                // For retrocompatibility

                if (preg_match('#\# http://www\.erphenyx\.com - http://www\.ephenyx\.com/forums\s*(.*)<IfModule mod_rewrite\.c>#si', $content, $m)) {
                    $specific_before = $m[1];
                } else {
                    $specific_before = $content;
                }

            }

        }

        // Write .htaccess data

        if (!$write_fd = @fopen($path, 'w')) {
            return false;
        }

        if ($specific_before) {
            fwrite($write_fd, trim($specific_before) . "\n\n");
        }

        $domains = [];

        foreach (CompanyUrl::getCompanyUrls() as $company_url) {
            /** @var ShopUrl $company_url */

            if (!isset($domains[$company_url->domain])) {
                $domains[$company_url->domain] = [];
            }

            $domains[$company_url->domain][] = [
                'physical' => $company_url->physical_uri,
                'virtual'  => $company_url->virtual_uri,
                'id_company'  => $company_url->id_company,
            ];

            if ($company_url->domain == $company_url->domain_ssl) {
                continue;
            }

            if (!isset($domains[$company_url->domain_ssl])) {
                $domains[$company_url->domain_ssl] = [];
            }

            $domains[$company_url->domain_ssl][] = [
                'physical' => $company_url->physical_uri,
                'virtual'  => $company_url->virtual_uri,
                'id_company'  => $company_url->id_company,
            ];
        }
       
        // Write data in .htaccess file
        fwrite($write_fd, "# ~~start~~ Do not remove this comment, Ephenyx Shop will keep automatically the code outside this comment when .htaccess will be generated again\n");
        fwrite($write_fd, "# .htaccess automatically generated by Ephenyx Shop e-commerce open-source solution\n");
        fwrite($write_fd, "# http://www.ephenyx.com - http://www.ephenyx.com/forums\n\n");

        fwrite($write_fd, '# Apache 2.2' . "\n");
        fwrite($write_fd, '<IfModule !mod_authz_core.c>' . "\n");
        fwrite($write_fd, '    <Files ~ "(?i)^.*\.(webp)$">' . "\n");
        fwrite($write_fd, '        Allow from all' . "\n");
        fwrite($write_fd, '    </Files>' . "\n");
        fwrite($write_fd, '</IfModule>' . "\n");
        fwrite($write_fd, '# Apache 2.4' . "\n");
        fwrite($write_fd, '<IfModule mod_authz_core.c>' . "\n");
        fwrite($write_fd, '    <Files ~ "(?i)^.*\.(webp)$">' . "\n");
        fwrite($write_fd, '        Require all granted' . "\n");
        fwrite($write_fd, '        allow from all' . "\n");
        fwrite($write_fd, '    </Files>' . "\n");
        fwrite($write_fd, '</IfModule>' . "\n");

        fwrite($write_fd, "\n");
        //Check browser compatibility from .htacces
        fwrite($write_fd, "\n");
        fwrite($write_fd, '<IfModule mod_setenvif.c>' . "\n");
        fwrite($write_fd, 'SetEnvIf Request_URI "\.(jpe?g|png)$" REQUEST_image' . "\n");
        fwrite($write_fd, '</IfModule>' . "\n");
        fwrite($write_fd, "\n");

        fwrite($write_fd, '<IfModule mod_mime.c>' . "\n");
        fwrite($write_fd, 'AddType image/webp .webp' . "\n");
        fwrite($write_fd, '</IfModule>' . "\n");
        fwrite($write_fd, "<IfModule mod_headers.c>" . "\n");
        fwrite($write_fd, 'Header append Vary Accept env=REQUEST_image' . "\n");
        fwrite($write_fd, '</IfModule>' . "\n");


        if ($disable_modsec) {
            fwrite($write_fd, "<IfModule mod_security.c>\nSecFilterEngine Off\nSecFilterScanPOST Off\n</IfModule>\n\n");
        }

        // RewriteEngine
        fwrite($write_fd, "<IfModule mod_rewrite.c>\n");

        // Ensure HTTP_MOD_REWRITE variable is set in environment
        fwrite($write_fd, "<IfModule mod_env.c>\n");
        fwrite($write_fd, "SetEnv HTTP_MOD_REWRITE On\n");
        fwrite($write_fd, "</IfModule>\n\n");

        // Disable multiviews ?

        if ($disable_multiviews) {
            fwrite($write_fd, "\n# Disable Multiviews\nOptions -Multiviews\n\n");
        }

        fwrite($write_fd, "RewriteEngine on\n");
        fwrite($write_fd, 'RewriteCond %{HTTP_ACCEPT} image/webp' . "\n");
        fwrite($write_fd, 'RewriteCond %{DOCUMENT_ROOT}/$1.webp -f' . "\n");
        fwrite($write_fd, 'RewriteRule (.+)\.(jpe?g|png)$ $1.webp [T=image/webp]' . "\n");

        

        fwrite($write_fd, 'RewriteRule ^api$ api/ [L]' . "\n\n");
        fwrite($write_fd, 'RewriteRule ^api/(.*)$ %{ENV:REWRITEBASE}webephenyx/dispatcher.php?url=$1 [QSA,L]' . "\n\n");
		
        $media_domains = '';

        

        if (Configuration::get('EPH_WEBSERVICE_CGI_HOST')) {
            fwrite($write_fd, "RewriteCond %{HTTP:Authorization} ^(.*)\nRewriteRule . - [E=HTTP_AUTHORIZATION:%1]\n\n");
        }

        foreach ($domains as $domain => $list_uri) {
            $physicals = [];

            foreach ($list_uri as $uri) {
                fwrite($write_fd, PHP_EOL . PHP_EOL . '#Domain: ' . $domain . PHP_EOL);

                

                fwrite($write_fd, 'RewriteRule . - [E=REWRITEBASE:' . $uri['physical'] . ']' . "\n");

                // Webservice
                fwrite($write_fd, 'RewriteRule ^api$ api/ [L]' . "\n\n");
                fwrite($write_fd, 'RewriteRule ^api/(.*)$ %{ENV:REWRITEBASE}webephenyx/dispatcher.php?url=$1 [QSA,L]' . "\n\n");
				
                if (!$rewrite_settings) {
                    $rewrite_settings = (int) Configuration::get(Configuration::REWRITING_SETTINGS);
                }

                $domain_rewrite_cond = 'RewriteCond %{HTTP_HOST} ^' . $domain . '$' . "\n";
                // Rewrite virtual multishop uri

                if ($uri['virtual']) {

                    if (!$rewrite_settings) {
                        fwrite($write_fd, $media_domains);
                        fwrite($write_fd, $domain_rewrite_cond);
                        fwrite($write_fd, 'RewriteRule ^' . trim($uri['virtual'], '/') . '/?$ ' . $uri['physical'] . $uri['virtual'] . "index.php [L,R]\n");
                    } else {
                        fwrite($write_fd, $media_domains);
                        fwrite($write_fd, $domain_rewrite_cond);
                        fwrite($write_fd, 'RewriteRule ^' . trim($uri['virtual'], '/') . '$ ' . $uri['physical'] . $uri['virtual'] . " [L,R]\n");
                    }

                    fwrite($write_fd, $media_domains);
                    fwrite($write_fd, $domain_rewrite_cond);
                    fwrite($write_fd, 'RewriteRule ^' . ltrim($uri['virtual'], '/') . '(.*) ' . $uri['physical'] . "$1 [L]\n\n");
                }


                fwrite($write_fd, "# AlphaImageLoader for IE and fancybox\n");

                

                fwrite($write_fd, 'RewriteRule ^images_ie/?([^/]+)\.(jpe?g|png|gif)$ js/jquery/plugins/fancybox/images/$1.$2 [L]' . "\n");
            }

            // Redirections to dispatcher

            if ($rewrite_settings) {
                fwrite($write_fd, "\n# Dispatcher\n");
                fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -s [OR]\n");
                fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -l [OR]\n");
                fwrite($write_fd, "RewriteCond %{REQUEST_FILENAME} -d\n");

                

                fwrite($write_fd, "RewriteRule ^.*$ - [NC,L]\n");

               

                fwrite($write_fd, "RewriteRule ^.*\$ %{ENV:REWRITEBASE}index.php [NC,L]\n");

            }

        }

        fwrite($write_fd, "</IfModule>\n\n");

        fwrite($write_fd, "AddType application/vnd.ms-fontobject .eot\n");
        fwrite($write_fd, "AddType font/ttf .ttf\n");
        fwrite($write_fd, "AddType font/otf .otf\n");
        fwrite($write_fd, "AddType application/font-woff .woff\n");
        fwrite($write_fd, "AddType application/font-woff2 .woff2\n");
        fwrite($write_fd, "<IfModule mod_headers.c>
            <FilesMatch \"\.(ttf|ttc|otf|eot|woff|woff2|svg)$\">
        Header set Access-Control-Allow-Origin \"*\"
    </FilesMatch>
</IfModule>\n\n"
        );

        // Cache control

        if ($cache_control) {
            $cache_control = "<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/gif \"access plus 1 month\"
    ExpiresByType image/jpeg \"access plus 1 month\"
    ExpiresByType image/png \"access plus 1 month\"
    ExpiresByType image/webp \"access plus 1 month\"
    ExpiresByType text/css \"access plus 1 week\"
    ExpiresByType text/javascript \"access plus 1 week\"
    ExpiresByType application/javascript \"access plus 1 week\"
    ExpiresByType application/x-javascript \"access plus 1 week\"
    ExpiresByType image/x-icon \"access plus 1 year\"
    ExpiresByType image/svg+xml \"access plus 1 year\"
    ExpiresByType image/vnd.microsoft.icon \"access plus 1 year\"
    ExpiresByType application/font-woff \"access plus 1 year\"
    ExpiresByType application/x-font-woff \"access plus 1 year\"
    ExpiresByType font/woff2 \"access plus 1 year\"
    ExpiresByType application/vnd.ms-fontobject \"access plus 1 year\"
    ExpiresByType font/opentype \"access plus 1 year\"
    ExpiresByType font/ttf \"access plus 1 year\"
    ExpiresByType font/otf \"access plus 1 year\"
    ExpiresByType application/x-font-ttf \"access plus 1 year\"
    ExpiresByType application/x-font-otf \"access plus 1 year\"
</IfModule>

<IfModule mod_headers.c>
    Header unset Etag
</IfModule>
FileETag none
<IfModule mod_deflate.c>
    <IfModule mod_filter.c>
        AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/x-javascript font/ttf application/x-font-ttf font/otf application/x-font-otf font/opentype
    </IfModule>
</IfModule>\n\n";
            fwrite($write_fd, $cache_control);
        }

        // In case the user hasn't rewrite mod enabled
        fwrite($write_fd, "#If rewrite mod isn't enabled\n");

        // Do not remove ($domains is already iterated upper)
        reset($domains);
        $domain = current($domains);
        fwrite($write_fd, 'ErrorDocument 404 ' . $domain[0]['physical'] . "index.php?controller=404\n\n");

        fwrite($write_fd, "# ~~end~~ Do not remove this comment, Ephenyx Shop will keep automatically the code outside this comment when .htaccess will be generated again");

        if ($specific_after) {
            fwrite($write_fd, "\n\n" . trim($specific_after));
        }

        fclose($write_fd);

        if (!defined('EPH_INSTALLATION_IN_PROGRESS')) {
            Hook::exec('actionHtaccessCreate');
        }

        return true;
    }
	


    public static function generateIndex() {

        if (defined('_DB_PREFIX_') && Configuration::get('EPH_DISABLE_OVERRIDES')) {
            PhenyxAutoload::getInstance()->_include_override_path = false;
        }

        PhenyxAutoload::getInstance()->generateIndex();
    }

    public static function getDefaultIndexContent() {

        // Use a random, existing index.php as template.
        $content = file_get_contents(_EPH_ROOT_DIR_ . '/includes/classes/index.php');

        // Drop the license section, we can't really claim a license for an
        // auto-generated file.
        $replacement = '/* Auto-generated file, don\'t edit. */';
        $content = preg_replace('/\/\*.*\*\//s', $replacement, $content);

        return $content;
    }

    
    public static function jsonDecode($json, $assoc = false) {
		
        if(is_array($json)) {
            return $json;
        }
		if(is_null($assoc)) {
			if(!is_null($json)) {
				return json_decode($json);
			}
		}
		if(!is_null($json)) {
			return json_decode($json, $assoc);
		}
        
    }

    public static function jsonEncode($data, $encodeFlags = null) {

        if(is_null($encodeFlags)) {
			return json_encode($data);
		}
		return json_encode($data, $encodeFlags);
    }

   
    public static function displayFileAsDeprecated() {

        $backtrace = debug_backtrace();
        $callee = current($backtrace);
        $error = 'File <b>' . $callee['file'] . '</b> is deprecated<br />';
        $message = 'The file ' . $callee['file'] . ' is deprecated and will be removed in the next major version.';
        $class = isset($callee['class']) ? $callee['class'] : null;

        Tools::throwDeprecated($error, $message, $class);
    }

    
    public static function enableCache($level = 1, Context $context = null) {

        if (!$context) {
            $context = Context::getContext();
        }

        $smarty = $context->smarty;

        if (!Configuration::get('EPH_SMARTY_CACHE')) {
            return;
        }

        if ($smarty->force_compile == 0 && $smarty->caching == $level) {
            return;
        }

        static::$_forceCompile = (int) $smarty->force_compile;
        static::$_caching = (int) $smarty->caching;
        $smarty->force_compile = 0;
        $smarty->caching = (int) $level;
        $smarty->cache_lifetime = 31536000; // 1 Year
    }

    
    public static function restoreCacheSettings(Context $context = null) {

        if (!$context) {
            $context = Context::getContext();
        }

        if (isset(static::$_forceCompile)) {
            $context->smarty->force_compile = (int) static::$_forceCompile;
        }

        if (isset(static::$_caching)) {
            $context->smarty->caching = (int) static::$_caching;
        }

    }

    public static function isCallable($function) {

        $disabled = explode(',', ini_get('disable_functions'));

        return (!in_array($function, $disabled) && is_callable($function));
    }

    
    public static function pRegexp($s, $delim) {

        $s = str_replace($delim, '\\' . $delim, $s);

        foreach (['?', '[', ']', '(', ')', '{', '}', '-', '.', '+', '*', '^', '$', '`', '"', '%'] as $char) {
            $s = str_replace($char, '\\' . $char, $s);
        }

        return $s;
    }

    
    public static function str_replace_once($needle, $replace, $haystack) {

        $pos = false;

        if ($needle) {
            $pos = strpos($haystack, $needle);
        }

        if ($pos === false) {
            return $haystack;
        }

        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    
    public static function checkPhpVersion() {

        $version = null;

        if (defined('PHP_VERSION')) {
            $version = PHP_VERSION;
        } else {
            $version = phpversion('');
        }

        //Case management system of ubuntu, php version return 5.2.4-2ubuntu5.2

        if (strpos($version, '-') !== false) {
            $version = substr($version, 0, strpos($version, '-'));
        }

        return $version;
    }

    public static function ZipTest($fromFile) {

        $zip = new ZipArchive();

        return ($zip->open($fromFile, ZIPARCHIVE::CHECKCONS) === true);
    }

    
    public static function getSafeModeStatus() {

        Tools::displayAsDeprecated();

        return false;
    }

    public static function ZipExtract($fromFile, $toDir) {

        if (!file_exists($toDir)) {
            mkdir($toDir, 0777);
        }

        $zip = new ZipArchive();

        if ($zip->open($fromFile) === true && $zip->extractTo($toDir) && $zip->close()) {
            return true;
        }

        return false;
    }

    
    public static function chmodr($path, $filemode) {

        if (!is_dir($path)) {
            return @chmod($path, $filemode);
        }

        $dh = opendir($path);

        while (($file = readdir($dh)) !== false) {

            if ($file != '.' && $file != '..') {
                $fullpath = $path . '/' . $file;

                if (is_link($fullpath)) {
                    return false;
                } else
                if (!is_dir($fullpath) && !@chmod($fullpath, $filemode)) {
                    return false;
                } else
                if (!Tools::chmodr($fullpath, $filemode)) {
                    return false;
                }

            }

        }

        closedir($dh);

        if (@chmod($path, $filemode)) {
            return true;
        } else {
            return false;
        }

    }

    public static function display404Error() {

        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        include dirname(__FILE__) . '/../404.php';
        die;
    }

    public static function url($begin, $end) {

        return $begin . ((strpos($begin, '?') !== false) ? '&' : '?') . $end;
    }

    public static function dieOrLog($msg, $die = true) {

        Tools::displayAsDeprecated();

        if ($die || (defined('_EPH_MODE_DEV_') && _EPH_MODE_DEV_)) {
            die($msg);
        }

        return Logger::addLog($msg);
    }

    public static function nl2br($str) {

		if(!is_null($str)) {
			return str_replace(["\r\n", "\r", "\n"], '<br />', $str);
		}
		return $str;
        
    }

    public static function clearSmartyCache() {

        $smarty = Context::getContext()->smarty;
        Tools::clearCache($smarty);
        Tools::clearCompile($smarty);
    }

   
    public static function clearCache($smarty = null, $tpl = false, $cacheId = null, $compileId = null) {

        if ($smarty === null) {
            $smarty = Context::getContext()->smarty;
        }

        if ($smarty === null) {
            return;
        }

        if (!$tpl && $cacheId === null && $compileId === null) {
            return $smarty->clearAllCache();
        }

        return $smarty->clearCache($tpl, $cacheId, $compileId);
    }
    
    public static function getCmsPath($idCms, $path)  {
        
        $context = Context::getContext();
        
        $ajax_mode = Configuration::get('EPH_FRONT_AJAX') ? 1 : 0;        
        $cms_ajax_mode = Configuration::get('EPH_CMS_AJAX') ? 1 : 0;
        
        $idCms = (int) $idCms;
        
        $path = '<span class="navigation_end">'.$path.'</span>';
        
        
        $pipe = Configuration::get('EPH_NAVIGATION_PIPE');
        if (empty($pipe)) {
            $pipe = '>';
        }

        $fullPath = [];
        $finalPath = '';
        $cms = new CMS($idCms, $context->language->id);
        if($cms->level_depth == 1) {
            return $path;
        }
        $level_depth = $cms->level_depth - 1;
        for($i = $level_depth; $i > 0; $i--) {
            $cms = new CMS($cms->id_parent, $context->language->id);
             if($ajax_mode && $cms_ajax_mode) {          
                $fullPath[$i] = '<a href="javascript:void(0)" onClick="openAjaxCms('.$cms->id.')" data-gg="">'.htmlentities($cms->meta_title, ENT_NOQUOTES, 'UTF-8').'</a><span class="navigation-pipe">'.$pipe.'</span>';
             } else {
                 $fullPath[$i] = '<a href="'.$context->link->getCMSLink($cms->id).'" data-gg="">'.htmlentities($cms->meta_title, ENT_NOQUOTES, 'UTF-8').'</a><span class="navigation-pipe">'.$pipe.'</span>';
             }
        }
        ksort($fullPath);
        foreach($fullPath as $key => $value) {
             $finalPath .= $value;
        }
        
         return $finalPath.$path;
        
        
    }
    
    public static function getFormPath($idForm, $path)  {
        
        $context = Context::getContext();
        
        $idForm = (int) $idForm;
        
        $path = '<span class="navigation_end">'.$path.'</span>';        
        
        return $path;
        
        
    }
    
    public static function clearCompile($smarty = null) {

        if ($smarty === null) {
            $smarty = Context::getContext()->smarty;
        }

        if ($smarty === null) {
            return;
        }

        return $smarty->clearCompiledTemplate();
    }
    
    public static function cleanFrontCache() {

		$recursive_directory = [
			'app/cache/smarty/cache',
			'app/cache/smarty/compile',
			'app/cache/purifier/CSS',
			'app/cache/purifier/URI'
		];
		$iterator = new AppendIterator();
        foreach ($recursive_directory as $key => $directory) {
			if(is_dir(_EPH_ROOT_DIR_ . '/' . $directory )) {
				$iterator->append(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(_EPH_ROOT_DIR_ . '/' . $directory . '/')));
			}
        }
		foreach ($iterator as $file) {
            Tools::deleteDirectory($file->getPathname());
        }
		
		mkdir(_EPH_ROOT_DIR_ .'/app/cache/smarty/cache', 0777, true);
		Tools::generateIndexFiles(_EPH_ROOT_DIR_ .'/app/cache/smarty/cache/');
		mkdir(_EPH_ROOT_DIR_ .'/app/cache/smarty/compile', 0777, true);
		Tools::generateIndexFiles(_EPH_ROOT_DIR_ .'/app/cache/smarty/compile/');
		mkdir(_EPH_ROOT_DIR_ .'/app/cache/purifier/CSS', 0777, true);
		Tools::generateIndexFiles(_EPH_ROOT_DIR_ .'/app/cache/purifier/CSS/');
		mkdir(_EPH_ROOT_DIR_ .'/app/cache/purifier/URI', 0777, true);
		Tools::generateIndexFiles(_EPH_ROOT_DIR_ .'/app/cache/purifier/URI/');
        mkdir(_EPH_ROOT_DIR_ .'/content/backoffice/backend/cache', 0777, true);
        
        $files = glob(_EPH_ROOT_DIR_ .'/content/backoffice/backend/cache/*'); 
        foreach($files as $file){ 
            if(is_file($file)) {
                unlink($file); 
            }
        }
        Tools::generateIndexFiles(_EPH_ROOT_DIR_ .'/content/backoffice/backend/cache/');
        $files = glob(_EPH_ROOT_DIR_ .'/content/themes/phenyx-theme-default/cache/*'); 
        foreach($files as $file){ 
            if(is_file($file)) {
                unlink($file); 
            }
        }
        Tools::generateIndexFiles(_EPH_ROOT_DIR_ .'/content/themes/phenyx-theme-default/cache/');
    }
    
    public static function reGenerateilesIndex() {
        
        $recursive_directory = [
            'includes',
            'app', 
	       'content'
        ];

        $iterator = new AppendIterator();

        foreach ($recursive_directory as $key => $directory) {
	       if(is_dir(_EPH_ROOT_DIR_ . '/' . $directory )) {
               $iterator->append(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(_EPH_ROOT_DIR_ . '/' . $directory . '/')));
           }
        }
        
        
        foreach ($iterator as $file) {
    
            if ($file->getFilename() == '..') {
                $filePathTest = $file->getPathname();
                $test = str_replace('..', '', $filePathTest);
                if(!file_exists($test.'index.php')) {
                    $diretory = str_replace(_EPH_ROOT_DIR_, '', $test);
                    $level = substr_count($diretory, '/') -1;
                    Tools::generateIndexFiles($test, $level);
                }
            }    
 
            if($file->getFilename() == 'index.php') {
                $path = '';
                $filePath = $file->getPathname();
                $diretory = str_replace(_EPH_ROOT_DIR_, '', $filePath);
                $level = substr_count($diretory, '/') -1;
                Tools::generateIndexFiles(str_replace('index.php', '', $filePath), $level);
            }	
   
        }

    }
    

    public static function generateIndexFiles($directory, $level = 1) {
		
		$path = '';
        for($i = 0; $i <= $level; $i++) {
            $path .= "../";
        }
        
        $file = fopen($directory."index.php","w");
		fwrite($file,"<?php". "\n\n");
		fwrite($file,"header(\"Expires: Mon, 26 Jul 1997 05:00:00 GMT\");". "\n");
		fwrite($file,"header(\"Last-Modified: \".gmdate(\"D, d M Y H:i:s\").\" GMT\");". "\n\n");
		fwrite($file,"header(\"Cache-Control: no-store, no-cache, must-revalidate\");". "\n");
		fwrite($file,"header(\"Cache-Control: post-check=0, pre-check=0\", false);". "\n");
		fwrite($file,"header(\"Pragma: no-cache\");". "\n\n");
		fwrite($file,"header(\"Location: ".$path."\");". "\n");
		fwrite($file,"exit;");
		fclose($file);
	}

   
    public static function clearColorListCache($id_product = false) {

        // Change template dir if called from the BackOffice
        $current_template_dir = Context::getContext()->smarty->getTemplateDir();
        Context::getContext()->smarty->setTemplateDir(_EPH_THEME_DIR_);
        Tools::clearCache(null, _EPH_THEME_DIR_ . 'product-list-colors.tpl', Product::getColorsListCacheId((int) $id_product, false));
        Context::getContext()->smarty->setTemplateDir($current_template_dir);
    }

   
    public static function getMemoryLimit() {

        $memory_limit = @ini_get('memory_limit');

        return Tools::getOctets($memory_limit);
    }

    public static function getOctets($option) {

        if (preg_match('/[0-9]+k/i', $option)) {
            return 1024 * (int) $option;
        }

        if (preg_match('/[0-9]+m/i', $option)) {
            return 1024 * 1024 * (int) $option;
        }

        if (preg_match('/[0-9]+g/i', $option)) {
            return 1024 * 1024 * 1024 * (int) $option;
        }

        return $option;
    }

    public static function isX86_64arch() {

        return (PHP_INT_MAX == '9223372036854775807');
    }

    public static function isPHPCLI() {

        return (defined('STDIN') || (mb_strtolower(php_sapi_name()) == 'cli' && (!isset($_SERVER['REMOTE_ADDR']) || empty($_SERVER['REMOTE_ADDR']))));
    }

    public static function argvToGET($argc, $argv) {

        if ($argc <= 1) {
            return;
        }

        // get the first argument and parse it like a query string
        parse_str($argv[1], $args);

        if (!is_array($args) || !count($args)) {
            return;
        }

        $_GET = array_merge($args, $_GET);
        $_SERVER['QUERY_STRING'] = $argv[1];
    }

    public static function getMaxUploadSize($max_size = 0) {

        $post_max_size = Tools::convertBytes(ini_get('post_max_size'));
        $upload_max_filesize = Tools::convertBytes(ini_get('upload_max_filesize'));

        if ($max_size > 0) {
            $result = min($post_max_size, $upload_max_filesize, $max_size);
        } else {
            $result = min($post_max_size, $upload_max_filesize);
        }

        return $result;
    }

    public static function convertBytes($value) {

        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = (int) substr($value, 0, $value_length - 1);
            $unit = mb_strtolower(substr($value, $value_length - 1));

            switch ($unit) {
            case 'k':
                $qty *= 1024;
                break;
            case 'm':
                $qty *= 1048576;
                break;
            case 'g':
                $qty *= 1073741824;
                break;
            }

            return $qty;
        }

    }

    public static function recurseCopy($src, $dst, $del = false) {

        if (!file_exists($src)) {
            return false;
        }

        $dir = opendir($src);

        if (!file_exists($dst)) {
            mkdir($dst);
        }

        while (false !== ($file = readdir($dir))) {

            if (($file != '.') && ($file != '..')) {

                if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                    static::recurseCopy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file, $del);
                } else {
                    copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);

                    if ($del && is_writable($src . DIRECTORY_SEPARATOR . $file)) {
                        unlink($src . DIRECTORY_SEPARATOR . $file);
                    }

                }

            }

        }

        closedir($dir);

        if ($del && is_writable($src)) {
            rmdir($src);
        }

    }

    public static function file_exists_cache($filename) {

        if (!isset(static::$file_exists_cache[$filename])) {
            static::$file_exists_cache[$filename] = file_exists($filename);
        }

        return static::$file_exists_cache[$filename];
    }

    public static function scandir($path, $ext = 'php', $dir = '', $recursive = false) {

        $path = rtrim(rtrim($path, '\\'), '/') . '/';
        $real_path = rtrim(rtrim($path . $dir, '\\'), '/') . '/';
        $files = scandir($real_path);

        if (!$files) {
            return [];
        }

        $filtered_files = [];

        $real_ext = false;

        if (!empty($ext)) {
            $real_ext = '.' . $ext;
        }

        $real_ext_length = strlen($real_ext);

        $subdir = ($dir) ? $dir . '/' : '';

        foreach ($files as $file) {

            if (!$real_ext || (strpos($file, $real_ext) && strpos($file, $real_ext) == (strlen($file) - $real_ext_length))) {
                $filtered_files[] = $subdir . $file;
            }

            if ($recursive && $file[0] != '.' && is_dir($real_path . $file)) {

                foreach (Tools::scandir($path, $ext, $subdir . $file, $recursive) as $subfile) {
                    $filtered_files[] = $subfile;
                }

            }

        }

        return $filtered_files;
    }

    public static function version_compare($v1, $v2, $operator = '<') {

        Tools::alignVersionNumber($v1, $v2);

        return version_compare($v1, $v2, $operator);
    }

    public static function alignVersionNumber(&$v1, &$v2) {

        $len1 = count(explode('.', trim($v1, '.')));
        $len2 = count(explode('.', trim($v2, '.')));
        $len = 0;
        $str = '';

        if ($len1 > $len2) {
            $len = $len1 - $len2;
            $str = &$v2;
        } else
        if ($len2 > $len1) {
            $len = $len2 - $len1;
            $str = &$v1;
        }

        for ($len; $len > 0; $len--) {
            $str .= '.0';
        }

    }

    public static function modRewriteActive() {

        return true;
    }

    public static function apacheModExists($name) {

        if (function_exists('apache_get_plugins')) {
            static $apache_plugin_list = null;

            if (!is_array($apache_plugin_list)) {
                $apache_plugin_list = apache_get_plugins();
            }

            // we need strpos (example, evasive can be evasive20)

            foreach ($apache_plugin_list as $plugin) {

                if (strpos($plugin, $name) !== false) {
                    return true;
                }

            }

        }

        return false;
    }

    public static function unSerialize($serialized, $object = false) {

        if (is_string($serialized) && (strpos($serialized, 'O:') === false || !preg_match('/(^|;|{|})O:[0-9]+:"/', $serialized)) && !$object || $object) {
            return @unserialize($serialized);
        }

        return false;
    }

    public static function arrayUnique($array) {

        if (version_compare(phpversion(), '5.2.9', '<')) {
            return array_unique($array);
        } else {
            return array_unique($array, SORT_REGULAR);
        }

    }

    
    public static function cleanNonUnicodeSupport($pattern) {

        if (!defined('PREG_BAD_UTF8_OFFSET')) {
            return $pattern;
        }

        return preg_replace('/\\\[px]\{[a-z]{1,2}\}|(\/[a-z]*)u([a-z]*)$/i', '$1$2', $pattern);
    }

    public static function addonsRequest($request, $params = []) {

        return false;
    }

    public static function fileAttachment($input = 'fileUpload', $return_content = true) {

        $file_attachment = null;

        if (isset($_FILES[$input]['name']) && !empty($_FILES[$input]['name']) && !empty($_FILES[$input]['tmp_name'])) {
            $file_attachment['rename'] = uniqid() . mb_strtolower(substr($_FILES[$input]['name'], -5));

            if ($return_content) {
                $file_attachment['content'] = file_get_contents($_FILES[$input]['tmp_name']);
            }

            $file_attachment['tmp_name'] = $_FILES[$input]['tmp_name'];
            $file_attachment['name'] = $_FILES[$input]['name'];
            $file_attachment['mime'] = $_FILES[$input]['type'];
            $file_attachment['error'] = $_FILES[$input]['error'];
            $file_attachment['size'] = $_FILES[$input]['size'];
        }

        return $file_attachment;
    }

    public static function changeFileMTime($file_name) {

        @touch($file_name);
    }

   
    public static function waitUntilFileIsModified($file_name, $timeout = 180) {

        @ini_set('max_execution_time', $timeout);

        if (($time_limit = ini_get('max_execution_time')) === null) {
            $time_limit = 30;
        }

        $time_limit -= 5;
        $start_time = microtime(true);
        $last_modified = @filemtime($file_name);

        while (true) {

            if (((microtime(true) - $start_time) > $time_limit) || @filemtime($file_name) > $last_modified) {
                break;
            }

            clearstatcache();
            usleep(300);
        }

    }

   
    public static function rtrimString($str, $str_search) {

        $length_str = strlen($str_search);

        if (strlen($str) >= $length_str && substr($str, -$length_str) == $str_search) {
            $str = substr($str, 0, -$length_str);
        }

        return $str;
    }

    
    public static function formatBytes($size, $precision = 2) {

        if (!$size) {
            return '0';
        }

        $base = log($size) / log(1024);
        $suffixes = ['', 'k', 'M', 'G', 'T'];

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

   
    public static function boolVal($value) {

        if (empty($value)) {
            $value = false;
        }

        return (bool) $value;
    }

    
    public static function getUserPlatform() {

        if (isset(static::$_user_plateform)) {
            return static::$_user_plateform;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        static::$_user_plateform = 'unknown';


        if (preg_match('/linux/i', $user_agent)) {
            static::$_user_plateform = 'Linux';
        } else
        if (preg_match('/macintosh|mac os x/i', $user_agent)) {
            static::$_user_plateform = 'Mac';
        } else
        if (preg_match('/windows|win32/i', $user_agent)) {
            static::$_user_plateform = 'Windows';
        }

        return static::$_user_plateform;
    }

    public static function getUserBrowser() {

        if (isset(static::$_user_browser)) {
            return static::$_user_browser;
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        static::$_user_browser = 'unknown';

        if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
            static::$_user_browser = 'Internet Explorer';
        } else
        if (preg_match('/Firefox/i', $user_agent)) {
            static::$_user_browser = 'Mozilla Firefox';
        } else
        if (preg_match('/Chrome/i', $user_agent)) {
            static::$_user_browser = 'Google Chrome';
        } else
        if (preg_match('/Safari/i', $user_agent)) {
            static::$_user_browser = 'Apple Safari';
        } else
        if (preg_match('/Opera/i', $user_agent)) {
            static::$_user_browser = 'Opera';
        } else
        if (preg_match('/Netscape/i', $user_agent)) {
            static::$_user_browser = 'Netscape';
        }

        return static::$_user_browser;
    }

    
    public static function getDescriptionClean($description) {
        
        if(is_null($description)) {
            return $description;
        }

        return strip_tags(stripslashes($description));
    }

    
    public static function purifyHTML($html, $uriUnescape = null, $allowStyle = false) {

        static $use_html_purifier = null;
        static $purifier = null;

        if (defined('EPH_INSTALLATION_IN_PROGRESS') || !Configuration::configurationIsLoaded()) {
            return $html;
        }

        if ($use_html_purifier === null) {
            $use_html_purifier = (bool) Configuration::get('EPH_USE_HTMLPURIFIER');
        }

        if ($use_html_purifier) {

            if ($purifier === null) {
                $config = HTMLPurifier_Config::createDefault();

                $config->set('Attr.EnableID', true);
                $config->set('HTML.Trusted', true);
                $config->set('Cache.SerializerPath', _EPH_CACHE_DIR_ . 'purifier');
                $config->set('Attr.AllowedFrameTargets', ['_blank', '_self', '_parent', '_top']);
                $config->set('Core.NormalizeNewlines', false);

                if (is_array($uriUnescape)) {
                    $config->set('URI.UnescapeCharacters', implode('', $uriUnescape));
                }

                if (Configuration::get('EPH_ALLOW_HTML_IFRAME')) {
                    $config->set('HTML.SafeIframe', true);
                    $config->set('HTML.SafeObject', true);
                    $config->set('URI.SafeIframeRegexp', '/.*/');
                }

                /** @var HTMLPurifier_HTMLDefinition|HTMLPurifier_HTMLPlugin $def */
                // http://developers.whatwg.org/the-video-element.html#the-video-element

                if ($def = $config->getHTMLDefinition(true)) {
                    $def->addElement(
                        'video',
                        'Block',
                        'Optional: (source, Flow) | (Flow, source) | Flow',
                        'Common',
                        [
                            'src'      => 'URI',
                            'type'     => 'Text',
                            'width'    => 'Length',
                            'height'   => 'Length',
                            'poster'   => 'URI',
                            'preload'  => 'Enum#auto,metadata,none',
                            'controls' => 'Bool',
                        ]
                    );
                    $def->addElement(
                        'source',
                        'Block',
                        'Flow',
                        'Common',
                        [
                            'src'  => 'URI',
                            'type' => 'Text',
                        ]
                    );
                    $def->addElement(
                        'meta',
                        'Inline',
                        'Empty',
                        'Common',
                        [
                            'itemprop'  => 'Text',
                            'itemscope' => 'Bool',
                            'itemtype'  => 'URI',
                            'name'      => 'Text',
                            'content'   => 'Text',
                        ]
                    );
                    $def->addElement(
                        'link',
                        'Inline',
                        'Empty',
                        'Common',
                        [
                            'rel'   => 'Text',
                            'href'  => 'Text',
                            'sizes' => 'Text',
                        ]
                    );

                    if ($allowStyle) {
                        $def->addElement('style', 'Block', 'Flow', 'Common', ['type' => 'Text']);
                    }

                }

                $purifier = new HTMLPurifier($config);
            }

            if (_EPH_MAGIC_QUOTES_GPC_) {
                $html = stripslashes($html);
            }

            $html = $purifier->purify($html);

            if (_EPH_MAGIC_QUOTES_GPC_) {
                $html = addslashes($html);
            }

        }

        return $html;
    }

    
    public static function safeDefine($constant, $value) {

        if (!defined($constant)) {
            define($constant, $value);
        }

    }

    
    public static function arrayReplaceRecursive($base, $replacements) {

        if (function_exists('array_replace_recursive')) {
            return array_replace_recursive($base, $replacements);
        }

        foreach (array_slice(func_get_args(), 1) as $replacements) {
            $brefStack = [ & $base];
            $headStack = [$replacements];

            do {
                end($brefStack);

                $bref = &$brefStack[key($brefStack)];
                $head = array_pop($headStack);
                unset($brefStack[key($brefStack)]);

                foreach (array_keys($head) as $key) {

                    if (isset($key, $bref) && is_array($bref[$key]) && is_array($head[$key])) {
                        $brefStack[] = &$bref[$key];
                        $headStack[] = $head[$key];
                    } else {
                        $bref[$key] = $head[$key];
                    }

                }

            } while (count($headStack));

        }

        return $base;
    }

    /**
     * Smarty {implode} plugin
     *
     * Type:     function<br>
     * Name:     implode<br>
     * Purpose:  implode Array
     * Use: {implode value="" separator=""}
     *
     * @link http://www.smarty.net/manual/en/language.function.fetch.php {fetch}
     *       (Smarty online manual)
     *
     * @param array                    $params   parameters
     * @param Smarty_Internal_Template $template template object
     * @return string|null if the assign parameter is passed, Smarty assigns the result to a template variable
     */
    public static function smartyImplode($params, $template) {

        if (!isset($params['value'])) {
            trigger_error("[plugin] implode parameter 'value' cannot be empty", E_USER_NOTICE);
            return;
        }

        if (empty($params['separator'])) {
            $params['separator'] = ',';
        }

        return implode($params['separator'], $params['value']);
    }

    /**
     * Encode table
     *
     * @param array
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     *
     * Copyright (c) 2014 TrueServer B.V.
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is furnished
     * to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in all
     * copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
     * THE SOFTWARE.
     */
    protected static $encodeTable = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l',
        'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
    ];
    /**
     * Decode table
     *
     * @param array
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     */
    protected static $decodeTable = [
        'a' => 0, 'b'  => 1, 'c'  => 2, 'd'  => 3, 'e'  => 4, 'f'  => 5,
        'g' => 6, 'h'  => 7, 'i'  => 8, 'j'  => 9, 'k'  => 10, 'l' => 11,
        'm' => 12, 'n' => 13, 'o' => 14, 'p' => 15, 'q' => 16, 'r' => 17,
        's' => 18, 't' => 19, 'u' => 20, 'v' => 21, 'w' => 22, 'x' => 23,
        'y' => 24, 'z' => 25, '0' => 26, '1' => 27, '2' => 28, '3' => 29,
        '4' => 30, '5' => 31, '6' => 32, '7' => 33, '8' => 34, '9' => 35,
    ];

    /**
     * Convert a UTF-8 email addres to IDN format (domain part only)
     *
     * @param string $email
     *
     * @return string
     */
    public static function convertEmailToIdn($email) {

        if (mb_detect_encoding($email, 'UTF-8', true) && mb_strpos($email, '@') > -1) {
            // Convert to IDN
            list($local, $domain) = explode('@', $email, 2);
            $domain = Tools::utf8ToIdn($domain);
            $email = "$local@$domain";
        }

        return $email;
    }

    /**
     * Convert an IDN email to UTF-8 (domain part only)
     *
     * @param string $email
     *
     * @return string
     */
    public static function convertEmailFromIdn($email) {

        if (mb_strpos($email, '@') > -1) {
            // Convert from IDN if necessary
            list($local, $domain) = explode('@', $email, 2);
            $domain = Tools::idnToUtf8($domain);
            $email = "$local@$domain";
        }

        return $email;
    }

    /**
     * Encode a domain to its Punycode version
     *
     * @param string $input Domain name in Unicode to be encoded
     *
     * @return string Punycode representation in ASCII
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     */
    public static function utf8ToIdn($input) {

        $input = mb_strtolower($input);
        $parts = explode('.', $input);

        foreach ($parts as &$part) {
            $length = strlen($part);

            if ($length < 1) {
                return false;
            }

            $part = static::encodePart($part);
        }

        $output = implode('.', $parts);
        $length = strlen($output);

        if ($length > 255) {
            return false;
        }

        return $output;
    }

    /**
     * Decode a Punycode domain name to its Unicode counterpart
     *
     * @param string $input Domain name in Punycode
     *
     * @return string Unicode domain name
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     */
    public static function idnToUtf8($input) {

        $input = strtolower($input);
        $parts = explode('.', $input);

        foreach ($parts as &$part) {
            $length = strlen($part);

            if ($length > 63 || $length < 1) {
                return false;
            }

            if (strpos($part, static::PUNYCODE_PREFIX) !== 0) {
                continue;
            }

            $part = substr($part, strlen(static::PUNYCODE_PREFIX));
            $part = static::decodePart($part);
        }

        $output = implode('.', $parts);
        $length = strlen($output);

        if ($length > 255) {
            return false;
        }

        return $output;
    }

    /**
     * Encode a part of a domain name, such as tld, to its Punycode version
     *
     * @param string $input Part of a domain name
     *
     * @return string Punycode representation of a domain part
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     */
    protected static function encodePart($input) {

        $codePoints = static::listCodePoints($input);
        $n = static::PUNYCODE_INITIAL_N;
        $bias = static::PUNYCODE_INITIAL_BIAS;
        $delta = 0;
        $h = $b = count($codePoints['basic']);
        $output = '';

        foreach ($codePoints['basic'] as $code) {
            $output .= static::codePointToChar($code);
        }

        if ($input === $output) {
            return $output;
        }

        if ($b > 0) {
            $output .= static::PUNYCODE_DELIMITER;
        }

        $codePoints['nonBasic'] = array_unique($codePoints['nonBasic']);
        sort($codePoints['nonBasic']);
        $i = 0;
        $length = static::strlen($input);

        while ($h < $length) {
            $m = $codePoints['nonBasic'][$i++];
            $delta = $delta + ($m - $n) * ($h + 1);
            $n = $m;

            foreach ($codePoints['all'] as $c) {

                if ($c < $n || $c < static::PUNYCODE_INITIAL_N) {
                    $delta++;
                }

                if ($c === $n) {
                    $q = $delta;

                    for ($k = static::PUNYCODE_BASE;; $k += static::PUNYCODE_BASE) {
                        $t = static::calculateThreshold($k, $bias);

                        if ($q < $t) {
                            break;
                        }

                        $code = $t + (($q - $t) % (static::PUNYCODE_BASE - $t));
                        $output .= static::$encodeTable[$code];
                        $q = ($q - $t) / (static::PUNYCODE_BASE - $t);
                    }

                    $output .= static::$encodeTable[$q];
                    $bias = static::adapt($delta, $h + 1, ($h === $b));
                    $delta = 0;
                    $h++;
                }

            }

            $delta++;
            $n++;
        }

        $out = static::PUNYCODE_PREFIX . $output;
        $length = strlen($out);

        if ($length > 63 || $length < 1) {
            return false;
        }

        return $out;
    }

    /**
     * Decode a part of domain name, such as tld
     *
     * @param string $input Part of a domain name
     *
     * @return string Unicode domain part
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     */
    protected static function decodePart($input) {

        $n = static::PUNYCODE_INITIAL_N;
        $i = 0;
        $bias = static::PUNYCODE_INITIAL_BIAS;
        $output = '';
        $pos = strrpos($input, static::PUNYCODE_DELIMITER);

        if ($pos !== false) {
            $output = substr($input, 0, $pos++);
        } else {
            $pos = 0;
        }

        $outputLength = strlen($output);
        $inputLength = strlen($input);

        while ($pos < $inputLength) {
            $oldi = $i;
            $w = 1;

            for ($k = static::PUNYCODE_BASE;; $k += static::PUNYCODE_BASE) {
                $digit = static::$decodeTable[$input[$pos++]];
                $i = $i + ($digit * $w);
                $t = static::calculateThreshold($k, $bias);

                if ($digit < $t) {
                    break;
                }

                $w = $w * (static::PUNYCODE_BASE - $t);
            }

            $bias = static::adapt($i - $oldi, ++$outputLength, ($oldi === 0));
            $n = $n + (int) ($i / $outputLength);
            $i = $i % ($outputLength);
            $output = static::substr($output, 0, $i) . static::codePointToChar($n) . static::substr($output, $i, $outputLength - 1);
            $i++;
        }

        return $output;
    }

    /**
     * Calculate the bias threshold to fall between TMIN and TMAX
     *
     * @param integer $k
     * @param integer $bias
     *
     * @return integer
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     */
    protected static function calculateThreshold($k, $bias) {

        if ($k <= $bias+static::PUNYCODE_TMIN) {
            return static::PUNYCODE_TMIN;
        } else
        if ($k >= $bias+static::PUNYCODE_TMAX) {
            return static::PUNYCODE_TMAX;
        }

        return $k - $bias;
    }

    /**
     * Bias adaptation
     *
     * @param integer $delta
     * @param integer $numPoints
     * @param boolean $firstTime
     *
     * @return integer
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     */
    protected static function adapt($delta, $numPoints, $firstTime) {

        $delta = (int) (
            ($firstTime)
            ? $delta / static::PUNYCODE_DAMP
            : $delta / 2
        );
        $delta += (int) ($delta / $numPoints);
        $k = 0;

        while ($delta > ((static::PUNYCODE_BASE-static::PUNYCODE_TMIN) * static::PUNYCODE_TMAX) / 2) {
            $delta = (int) ($delta / (static::PUNYCODE_BASE-static::PUNYCODE_TMIN));
            $k = $k+static::PUNYCODE_BASE;
        }

        $k = $k + (int) (((static::PUNYCODE_BASE-static::PUNYCODE_TMIN + 1) * $delta) / ($delta+static::PUNYCODE_SKEW));

        return $k;
    }

    /**
     * List code points for a given input
     *
     * @param string $input
     *
     * @return array Multi-dimension array with basic, non-basic and aggregated code points
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     */
    protected static function listCodePoints($input) {

        $codePoints = [
            'all'      => [],
            'basic'    => [],
            'nonBasic' => [],
        ];
        $length = static::strlen($input);

        for ($i = 0; $i < $length; $i++) {
            $char = static::substr($input, $i, 1);
            $code = static::charToCodePoint($char);

            if ($code < 128) {
                $codePoints['all'][] = $codePoints['basic'][] = $code;
            } else {
                $codePoints['all'][] = $codePoints['nonBasic'][] = $code;
            }

        }

        return $codePoints;
    }

    /**
     * Convert a single or multi-byte character to its code point
     *
     * @param string $char
     * @return integer
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     */
    protected static function charToCodePoint($char) {

        $code = ord($char[0]);

        if ($code < 128) {
            return $code;
        } else
        if ($code < 224) {
            return (($code - 192) * 64) + (ord($char[1]) - 128);
        } else
        if ($code < 240) {
            return (($code - 224) * 4096) + ((ord($char[1]) - 128) * 64) + (ord($char[2]) - 128);
        } else {
            return (($code - 240) * 262144) + ((ord($char[1]) - 128) * 4096) + ((ord($char[2]) - 128) * 64) + (ord($char[3]) - 128);
        }

    }

    /**
     * Convert a code point to its single or multi-byte character
     *
     * @param integer $code
     * @return string
     *
     * @since 1.0.4
     *
     * @copyright 2014 TrueServer B.V. (https://github.com/true/php-punycode)
     *
     */
    protected static function codePointToChar($code) {

        if ($code <= 0x7F) {
            return chr($code);
        } else
        if ($code <= 0x7FF) {
            return chr(($code >> 6) + 192) . chr(($code & 63) + 128);
        } else
        if ($code <= 0xFFFF) {
            return chr(($code >> 12) + 224) . chr((($code >> 6) & 63) + 128) . chr(($code & 63) + 128);
        } else {
            return chr(($code >> 18) + 240) . chr((($code >> 12) & 63) + 128) . chr((($code >> 6) & 63) + 128) . chr(($code & 63) + 128);
        }

    }

    /**
     * Base 64 encode that does not require additional URL Encoding for i.e. cookies
     *
     * This greatly reduces the size of a cookie
     *
     * @param mixed $data
     *
     * @return string
     *
     * @since 1.0.4
     */
    public static function base64UrlEncode($data) {

        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base 64 decode for base64UrlEncoded data
     *
     * @param mixed $data
     *
     * @return string
     *
     * @since 1.0.4
     */
    public static function base64UrlDecode($data) {

        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * Grabs a size tag from a DOMElement (as HTML)
     *
     * @param string $html
     *
     * @return array|false
     *
     * @since 1.0.4
     */
    public static function parseFaviconSizeTag($html) {

        $srcFound = false;
        $favicon = [];
        preg_match('/\{(.*)\}/U', $html, $m);

        if (!$m || count($m) < 2) {
            return false;
        }

        $tags = explode(' ', $m[1]);

        foreach ($tags as $tag) {
            $components = explode('=', $tag);

            if (count($components) === 1) {

                if ($components[0] === 'src') {
                    $srcFound = true;
                }

                continue;
            }

            switch ($components[0]) {
            case 'type':
                $favicon['type'] = $components[1];
                break;
            case 'size':
                $dimension = explode('x', $components[1]);

                if (count($dimension) !== 2) {
                    return false;
                }

                $favicon['width'] = $dimension[0];
                $favicon['height'] = $dimension[1];
                break;
            }

        }

        if ($srcFound && array_key_exists('width', $favicon) && array_key_exists('height', $favicon)) {

            if (!isset($favicon['type'])) {
                $favicon['type'] = 'png';
            }

            return $favicon;
        }

        return false;
    }

    /**
     * Returns current server timezone setting.
     *
     * @return string
     *
     * @since   1.0.7
     * @version 1.0.7 Initial version.
     */
    public static function getTimeZone() {

        $timezone = Configuration::get('EPH_TIMEZONE');

        if (!$timezone) {
            // Fallback use php timezone settings.
            $timezone = date_default_timezone_get();
        }

        return $timezone;
    }

    public static function isWebPSupported() {

        if (Configuration::get('plugin-webpconverter-demo-mode')) {
            return false;
        }

        if (Plugin::isEnabled('webpgenerator')) {

            if (isset($_SERVER["HTTP_ACCEPT"])) {

                if (strpos($_SERVER["HTTP_ACCEPT"], "image/webp") > 0) {
                    return true;
                }

                $agent = $_SERVER['HTTP_USER_AGENT'];

                if (strlen(strstr($agent, 'Firefox')) > 0) {
                    return true;
                }

                if (strlen(strstr($agent, 'Edge')) > 0) {
                    return true;
                }

            }

        }

    }

    public static function isImagickCompatible() {

         try {

            if (!class_exists('Imagick')) {
                return false;
            }

            /**
             * Check if the Imagick::queryFormats method exists
             */

            if (!method_exists(\Imagick::class, 'queryFormats')) {
                return false;
            }

            return in_array('WEBP', \Imagick::queryFormats(), false);
        } catch (Exception $exception) {
            return false;
        }

    }

    

    public static function is_assoc(array $array) {

        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    public static function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds') {

        $sets = [];

        if (strpos($available_sets, 'l') !== false) {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }

        if (strpos($available_sets, 'u') !== false) {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }

        if (strpos($available_sets, 'd') !== false) {
            $sets[] = '23456789';
        }

        if (strpos($available_sets, 's') !== false) {
            $sets[] = '-@_.()';
        }

        $all = '';
        $password = '';

        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all .= $set;
        }

        $all = str_split($all);

        for ($i = 0; $i < $length - count($sets); $i++) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);

        if (!$add_dashes) {
            return $password;
        }

        $dash_len = floor(sqrt($length));
        $dash_str = '';

        while (strlen($password) > $dash_len) {
            $dash_str .= substr($password, 0, $dash_len) . '-';
            $password = substr($password, $dash_len);
        }

        $dash_str .= $password;
        return $dash_str;
    }

   

    public static function sendEmail($postfields) {

        
        $context = Context::getContext();

        $htmlContent = $postfields['htmlContent'];
        $tpl = $context->smarty->createTemplate(_EPH_MAIL_DIR_ . 'header.tpl');
        $tpl->assign([
            'title'        => $postfields['subject'],
            'logoMailLink' => 'https://' . Configuration::get('EPH_SHOP_URL') . '/content/img/' . Configuration::get('EPH_LOGO_MAIL'),
        ]);
        $header = $tpl->fetch();
        $tpl = $context->smarty->createTemplate(_EPH_MAIL_DIR_ . 'footer.tpl');
        $tpl->assign([
            'tag' => Configuration::get('EPH_FOOTER_EMAIL'),
        ]);
        $footer = $tpl->fetch();
        $postfields['htmlContent'] = $header . $htmlContent . $footer;
        $mail_method = Configuration::get('EPH_MAIL_METHOD');
        if ($mail_method == 1) {
            $encrypt = Configuration::get('EPH_MAIL_SMTP_ENCRYPTION');
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = Configuration::get('EPH_MAIL_SERVER');
            $mail->Port = Configuration::get('EPH_MAIL_SMTP_PORT');            
            //$mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
            $mail->Username =  Configuration::get('EPH_MAIL_USER');
            $mail->Password = Configuration::get('EPH_MAIL_PASSWD');
            $mail->setFrom($postfields['sender']['email'], $postfields['sender']['name']);
            foreach($postfields['to'] as $key => $value) {         
                $mail->addAddress($value['email'], $value['name']);
            }
            
            $mail->Subject = $postfields['subject'];
            if($encrypt != 'off') {
                if($encrypt == 'ENCRYPTION_STARTTLS') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                } else {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                }
            } else {
               
            }
            
            $mail->Body = $postfields['htmlContent'];
            $mail->isHTML(true);
            if(isset($postfields['attachment']) && !is_null($postfields['attachment'])) {
                $mail->addAttachment($postfields['attachment']);
            }
            
            if (!$mail->send()) {
                return false;
            } else {
                return true;
            }

            
        } else  if ($mail_method == 2) {
            $api_key = Configuration::get('EPH_SENDINBLUE_API');

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL            => "https://api.sendinblue.com/v3/smtp/email",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => "",
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => "POST",
                CURLOPT_POSTFIELDS     => json_encode(($postfields)),
                CURLOPT_HTTPHEADER     => [
                    "Accept: application/json",
                    "Content-Type: application/json",
                    "api-key: " . $api_key,
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                return false;
            } else {
                return true;
            }
        }

    }
   
    public static function hex2rgb($colour) {

        if ($colour[0] == '#') {
            $colour = substr($colour, 1);
        }

        if (strlen($colour) == 6) {
            list($r, $g, $b) = [$colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]];
        } else
        if (strlen($colour) == 3) {
            list($r, $g, $b) = [$colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]];
        } else {
            return false;
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return ['red' => $r, 'green' => $g, 'blue' => $b];
    }

    public static function convertTime($dec) {

        $seconds = ($dec * 3600);
        $hours = floor($dec);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        return Tools::lz($hours) . ":" . Tools::lz($minutes) . ":" . (int) Tools::lz($seconds);
    }

    public static function convertTimetoHex($hours, $minutes) {

        return $hours + round($minutes / 60, 2);
    }

    public static function lz($num) {

        return (strlen($num) < 2) ? "0{$num}" : $num;
    }

    public static function convertFrenchDate($date) {

        $date = DateTime::createFromFormat('d/m/Y', $date);
        return date_format($date, "Y-m-d");
    }

    public static function encrypt_decrypt($action, $string, $secret_key, $secret_iv) {

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else

        if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public static function skip_accents($str, $charset = 'utf-8') {

        $str = htmlentities($str, ENT_NOQUOTES, $charset);

        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);

        return $str;
    }

    public static function random_float($min, $max) {

        return random_int($min, $max - 1) + (random_int(0, PHP_INT_MAX - 1) / PHP_INT_MAX);
    }

    public static function getMonthById($idMonth) {

        switch ($idMonth) {
        case '01':
            $month = 'Janvier';
            break;
        case '02':
            $month = 'Fevrier';
            break;
        case '03':
            $month = 'Mars';
            break;
        case '04':
            $month = 'Avril';
            break;
        case '05':
            $month = 'Mai';
            break;
        case '06':
            $month = 'Juin';
            break;
        case '07':
            $month = 'Juillet';
            break;
        case '08':
            $month = 'Aout';
            break;
        case '09':
            $month = 'Septembre';
            break;
        case '10':
            $month = 'Octobre';
            break;
        case '11':
            $month = 'Novembre';
            break;
        case '12':
            $month = 'Décembre';
            break;

        }

        return $month;
    }

    public static function str_rsplit($string, $length) {

        // splits a string "starting" at the end, so any left over (small chunk) is at the beginning of the array.

        if (!$length) {return false;}

        if ($length > 0) {return str_split($string, $length);}

        // normal split

        $l = strlen($string);
        $length = min(-$length, $l);
        $mod = $l % $length;

        if (!$mod) {return str_split($string, $length);}

        // even/max-length split

        // split
        return array_merge([substr($string, 0, $mod)], str_split(substr($string, $mod), $length));
    }

    public static function getContentLinkTitle($url) {

        $html = Tools::file_get_contents_curl($url);

        $image_url = [];

        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $metas = $doc->getElementsByTagName('meta');

        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);

            if ($meta->getAttribute('property') == 'og:title') {
                $title = $meta->getAttribute('content');
            }

        }

        if (empty($title)) {
            $nodes = $doc->getElementsByTagName('title');
            $title = $nodes->item(0)->nodeValue;
        }

        return $title;

    }

    public static function getContentLink($url) {

        $html = Tools::file_get_contents_curl($url);

        $image_url = [];

        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $metas = $doc->getElementsByTagName('meta');

        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);

            if ($meta->getAttribute('property') == 'og:title') {
                $title = $meta->getAttribute('content');
            }

            if ($meta->getAttribute('property') == 'og:image') {
                $image_url[0] = $meta->getAttribute('content');
            }

            if ($meta->getAttribute('name') == 'description') {
                $body_content = $meta->getAttribute('content');
            }

        }

        if (empty($title)) {
            $nodes = $doc->getElementsByTagName('title');
            $title = $nodes->item(0)->nodeValue;
        }

        if (empty($image_url[0])) {

            $content = Tools::file_get_html($url);

            foreach ($content->find('img') as $element) {

                if (filter_var($element->src, FILTER_VALIDATE_URL)) {
                    list($width, $height) = getimagesize($element->src);

                    if ($width > 150 || $height > 150) {
                        $image_url[0] = $element->src;
                        break;
                    }

                }

            }

        }

        $image_div = "";

        if (!empty($image_url[0])) {
            $image_div = "<div class='image-extract col-lg-12'>" .
                "<input type='hidden' id='index' value='0'/>" .
                "<img id='image_url' src='" . $image_url[0] . "' />";

            if (count($image_url) > 1) {
                $image_div .= "<div>" .
                "<input type='button' class='btnNav' id='prev-extract' onClick=navigateImage(" . json_encode($image_url) . ",'prev') disabled />" .
                "<input type='button' class='btnNav' id='next-extract' target='_blank' onClick=navigateImage(" . json_encode($image_url) . ",'next') />" .
                    "</div>";
            }

            $image_div .= "</div>";
        }

        $output = $image_div . "<div class='content-extract col-lg-12'>" .
            "<h3><a href='" . $url . "' target='_blank'>" . $title . "</a></h3>" .
            "<div>" . $body_content . "</div>" .
            "</div>";

        return $output;

    }
    
    public static function deleteBulkFiles($file) {
		
		if(file_exists(_EPH_ROOT_DIR_.$file)) {
            unlink(_EPH_ROOT_DIR_.$file);
        }
		return true;
		
	}

	public static function cleanEmptyDirectory() {
		
		
		$recursive_directory = ['includes/classes', 'includes/controllers', 'includes/plugins', 'content/js', 'content/backoffice/backend'];		
       

        $iterator = new AppendIterator();

        foreach ($recursive_directory as $key => $directory) {
            $iterator->append(new DirectoryIterator(_EPH_ROOT_DIR_ . '/' . $directory));
        }
		foreach ($iterator as $file) {
			$fileName = $file->getFilename();
			$filePath = $file->getPathname();
            $path = str_replace($fileName, '', $filePath);
            if(is_dir($path)) {
                Tools::removeEmptyDirs($path);
            }			
		}
        
        $iterator = new AppendIterator();
        $iterator->append(new DirectoryIterator(_EPH_ROOT_DIR_ . '/'));
        
        foreach ($iterator as $file) {
            
            $filePath = $file->getPathname();
            $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

            if ($ext == 'txt') {
                unlink($filePath);
            }
			
        }

		
	}
    
    public static function removeEmptyDirs($path) {

       
        $dirs = glob($path . "*", GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $files = glob($dir . "/*");
            $innerDirs = glob($dir . "/*", GLOB_ONLYDIR);

            if (is_array($files) && count($files) == 1 && basename($files[0]) == 'index.php') {

                 unlink($files[0]);
                 rmdir($dir);

            } elseif (empty($files)) {
                rmdir($dir);
            } else if (is_array($innerDirs) && count($innerDirs) > 0) {
                Tools::removeEmptyDirs($dir.'/');
            }

        }

    }
    
    
	
    public static function generateCurrentJson() {

        
		$recursive_directory = [
            'app/composer',
            'app/xml',
            'content/backoffice',                       
            'content/css', 
            'content/fonts', 
            'content/js', 
            'content/mails', 
            'content/pdf',   
			'includes/classes',		
            'includes/controllers',		
            'includes/plugins',	
            'webephenyx',
		];
       
		
        $iterator = new AppendIterator();

        foreach ($recursive_directory as $key => $directory) {
			if(is_dir(_EPH_ROOT_DIR_ . '/' . $directory )) {
				$iterator->append(new RecursiveIteratorIterator(new RecursiveDirectoryIterator(_EPH_ROOT_DIR_ . '/' . $directory . '/')));
			}
        }
		$iterator->append(new DirectoryIterator(_EPH_ROOT_DIR_ . '/app/'));
        $iterator->append(new DirectoryIterator(_EPH_ROOT_DIR_ . '/'));
        $iterator->append(new DirectoryIterator(_EPH_ROOT_DIR_ . '/content/themes/'));


        foreach ($iterator as $file) {
            $filePath = $file->getPathname();
            $filePath = str_replace(_EPH_ROOT_DIR_, '', $filePath);
			
            if (in_array($file->getFilename(), ['.', '..', '.htaccess', '.user.ini', 'defines.inc.php','settings.inc.php', 'root.css', 'custom.css', 'polygon.css', 'layout.css', 'custom_menu.css', 'root.js', '.php-ini', '.php-version'])) {
                continue;
            }
			
            if (is_dir($file->getPathname())) {
								
                continue;
            }

            $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);

            if ($ext == 'txt') {
                continue;
            }
			if ($ext == 'zip') {
				continue;
			}
			
            if (strpos($filePath, '/uploads/') !== false) {
				continue;
			}
             if (strpos($filePath, '/phenyx-theme-default/cache/') !== false) {
				continue;
			}
            if (strpos($filePath, '/backend/cache/') !== false) {
				continue;
			}
            
            if (strpos($filePath, '/phenyx-theme-default/css/plugins/') !== false) {
				continue;
			}
            if (strpos($filePath, '/phenyx-theme-default/js/plugins/') !== false) {
				continue;
			}
			if (strpos($filePath, '/phenyx-theme-default/img/') !== false) {
				continue;
			}
            if (strpos($filePath, '/phenyx-theme-default/plugins/') !== false) {
				continue;
			}
            
            if (str_contains($filePath, '/config_') && $ext == 'xml') {			
                continue;				
            }

            $md5List[$filePath] = md5_file($file->getPathname());
        }

        return $md5List;

    }

    

    
    public static function buildMaps() {

		$context = Context::getContext();
		
        $map_seeting = [];
		
        $seetings = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('cml.name, c.*, ccl.`name` as `category`, cml.`description`')
                ->from('composer_map', 'c')
                ->leftJoin('composer_map_lang', 'cml', 'cml.`id_composer_map` = c.`id_composer_map` AND cml.`id_lang` = ' . $context->language->id)
                ->leftJoin('composer_category_lang', 'ccl', 'ccl.`id_composer_category` = c.`id_composer_category` AND ccl.`id_lang` = ' . $context->language->id)
                ->where('c.`is_corporate` = 1')
        );
		$excludeField = ['show_settings_on_create', 'content_element', 'is_container'];
		
		foreach ($seetings as &$seeting) {
			foreach ($seeting as $key => $value) {
				if($key == 'show_settings_on_create') {
					if($value == 2) {
						$seeting['show_settings_on_create'] = false;
					} else if($value == 1) {
						$seeting['show_settings_on_create'] = true;
					} else if(empty($value)) {
						unset($seeting['show_settings_on_create']);
					} 
				}
				if($key == 'content_element') {
					if($value == 0) {
						$seeting['content_element'] = false;
					} else if($value == 1) {
						unset($seeting['content_element']);
					} 
				}
				if($key == 'is_container') {
					if($value == 1) {
						$seeting['is_container'] = true;
					} else  {
						unset($seeting['is_container']);
					} 
				}
			}
			
		}

        foreach ($seetings as &$seeting) {
			
			
            foreach ($seeting as $key => $value) {
				if(in_array($key, $excludeField)) {
					continue;
				}
				if (empty($value)) {
					unset($seeting[$key]);
                    
                }
            }
			unset($seeting['id_composer_category']);
			unset($seeting['active']);
			


        }

        foreach ($seetings as &$seeting) {
			
			
            $params = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
                (new DbQuery())
                    ->select('cpt.`value`as `type`, cmpl.heading, cmp.*, cmpl.description, cmpl.param_group as `group`')
                    ->from('composer_map_params', 'cmp')
                    ->leftJoin('composer_map_params_lang', 'cmpl', 'cmpl.`id_composer_map_params` = cmp.`id_composer_map_params` AND cmpl.`id_lang` = ' . $context->language->id)
                    ->leftJoin('composer_param_type', 'cpt', 'cpt.`id_composer_param_type` = cmp.`id_type`')
                    ->where('cmp.`id_composer_map` = ' . $seeting['id_composer_map'])
            );

            foreach ($params as &$param) {

                unset($param['id_type']);
				foreach ($param as $key => $value) {

                    if (empty($value)) {
                        unset($param[$key]);
                    }
					

                }
				if(!empty($param['value'])  && $param['param_name'] != 'content') {
					$param['value'] = Tools::jsonDecode($param['value'], true);
				}
				if($param['param_name'] == 'img_size') {
					$param['values'] = Tools::getComposerImageTypes();
				} else {
					$values = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
						(new DbQuery())
						->select('cv.`value_key`, cvl.`name`')
						->from('composer_value', 'cv')
                		->leftJoin('composer_value_lang', 'cvl', 'cvl.`id_composer_value` = cv.`id_composer_value` AND cvl.`id_lang` = ' . $context->language->id)
                		->where('cv.`id_composer_map_params` = ' . $param['id_composer_map_params'])
					);
					$param['values'] = $values;
				
				}

            }

            if (!empty($params)) {

                foreach ($params as &$param) {

                    if (!empty($param['dependency'])) {
                        $param['dependency'] = Tools::jsonDecode($param['dependency'], true);
                    }

                    if (!empty($param['settings'])) {
                        $param['settings'] = Tools::jsonDecode($param['settings'], true);
                    }

                    unset($param['id_composer_map']);
                    unset($param['id_composer_map_params']);
                    unset($param['position']);

                }

            }

            unset($seeting['id_composer_map']);
            unset($seeting['id_lang']);
            $seeting['params'] = $params;
            $map_seeting[$seeting['base']] = $seeting;
        }
		Configuration::updateValue('_EPH_SEETINGS_MAP_FILE_', Tools::jsonEncode($map_seeting));
        return $map_seeting;

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
			'name' => ''
		];
		
		foreach($images_types as $type) {
			$values[] = [
				'value_key' => $type['name'],
				'name' =>  $type['name']
			];
			
		}

		return $values;
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
			if(isset($result['base_64']) && !empty($result['base_64'])) {
				$links[$th_id] = $result['base_64'];
				
			} else if (isset($result['file_name']) && !empty($result['file_name'])) {
                $thumb_src = __EPH_BASE_URI__.'content/img/composer/';

                if (!empty($result['subdir'])) {
                    $thumb_src .= $result['subdir'];
                }

                $thumb_src .= $result['file_name'];

                if (!empty($imageSize)) {
                    $path_parts = pathinfo($thumb_src);
                    $thumb_src = $path_parts['dirname'] . DIRECTORY_SEPARATOR . $path_parts['filename'] . '-' . $imageSize . '.' . $path_parts['extension'];

                }
				if(empty($result['base_64'])) {
					$extension = pathinfo($thumb_src, PATHINFO_EXTENSION);
					$img = new Imagick(_EPH_ROOT_DIR_.$thumb_src);
					$imgBuff = $img->getimageblob();
					$img->clear(); 
					$img = base64_encode($imgBuff);
					$base64 = 'data:image/'.$extension.';base64,'.$img;
					$imageType = new ComposerMedia($result['id_vc_media']);
					$imageType->file_name = $result['file_name'];
					$imageType->base_64 = $base64;
					$imageType->subdir = $result['subdir'];
					foreach (Language::getIDs(false) as $idLang) {
						$imageType->legend[$idLang] = pathinfo($thumb_src, PATHINFO_FILENAME);
					}
					if($imageType->update()) {
						$thumb_src = $base64;
					}

					
				}

                $links[$th_id] = $thumb_src;
            }

        }

        return $links;
    }
	
	public static function getSliderWidth($size) {

        $width = '100%';
        $types = Tools::getImageTypeByName($size);

        if (isset($types)) {
            $width = $types['width'] . 'px';
        }

        return $width;
    }

	
	public static function getImageTypeByName($name) {
		
		$result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
                (new DbQuery())
                    ->select('*')
                    ->from('vc_image_type')
                    ->where('`name` LIKE  \'' . $name.'\'')
            );


		if (!empty($result)) {
			$image['width'] = $result['width'];
			$image['height'] = $result['height'];

			return $image;
		}

		return false;
	}

	
	public static function get_media_thumbnail_url($id = '') {

        if (isset($id) && !empty($id)) {
            $db = Db::getInstance();
            $tablename = _DB_PREFIX_ .'vc_media';

            $db_results = $db->executeS("SELECT `file_name`, `subdir` FROM eph_vc_media WHERE id_vc_media={$id}", true, false);

            $url = isset($db_results[0]['subdir']) && !empty($db_results[0]['subdir']) ? $db_results[0]['subdir'] . '/' : '';
            return $url .= isset($db_results[0]['file_name']) ? $db_results[0]['file_name'] : '';
        } else {
            return '';
        }

    }
    
    
    public static function ModifyImageUrl($img_src = '') {
        
        $img_pathinfo = pathinfo($img_src);
        $mainstr = $img_pathinfo['basename'];
        $static_url = $img_pathinfo['dirname'] . '/' . $mainstr;
        return '//' . Tools::getMediaServer($static_url) . $static_url;
    }	
	
	public static function getDistantTables($currentTables) {

		$tableToKeep = [];
		$tableToCheck = [];

		foreach ($currentTables as $table) {
			$tableToKeep[] = $table['Tables_in_' . _DB_NAME_];
		}

		$distantTables = Db::getInstance()->executeS('SHOW TABLES');

		foreach ($distantTables as $table) {
			$tableToCheck[] = $table['Tables_in_' . $dbName];
		}

		$tableToDelete = [];

		foreach ($tableToCheck as $table) {

			if (in_array($table, $tableToKeep)) {
				continue;
			}

			$schema = Db::getInstance()->executeS('SHOW CREATE TABLE `' . $table . '`');

			if (count($schema) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table'])) {
				continue;
			}

			$tableToDelete[$table] = 'DROP TABLE IF EXISTS `' . $schema[0]['Table'] . '`;' . PHP_EOL;
		}

		return $tableToDelete;

	}
    
	public static function cleanThemeDirectory() {
		
		$folder = [];
		$plugintochecks = [];
		
		$iterator = new AppendIterator();

		$iterator->append(new DirectoryIterator(_EPH_ROOT_DIR_ . '/includes/plugins'));
		foreach ($iterator as $file) {
			if (in_array($file->getFilename(), ['.', '..', '.htaccess', 'index.php'])) {
                continue;
    		}
			$filePath = $file->getPathname();
			$plugin = str_replace(_EPH_ROOT_DIR_ . '/includes/plugins/', '', $filePath);
			if(file_exists($filePath.'/'.$plugin.'.php')) {
				$folder[] = $plugin;
			} else {
				Tools::deleteDirectory($file->getPathname());
			}
		}
		$plugins = Plugin::getPluginsOnDisk();
		foreach($plugins as $plugin) {
			if($plugin->id > 0) {
				if(in_array($plugin->name, $folder)) {
					$plugintochecks[] = $plugin->name;
				} else {
					$result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
       					(new DbQuery())
           				->select('`id_hook`')
           				->from('hook_plugin')
						->where('`id_plugin` = ' . (int) $plugin->id)
   					);

   					foreach ($result as $row) {
						$plugin->unregisterHook((int) $row['id_hook']);
       					$plugin->unregisterExceptions((int) $row['id_hook']);
   					}
				
					Db::getInstance()->delete('plugin_access', '`id_plugin` = ' . (int) $plugin->id);
   					Group::truncateRestrictionsByPlugin($plugin->id);
					Db::getInstance()->delete('plugin', '`id_plugin` = ' . (int) $plugin->id);      
				}
			} 
		}
		$iterator = new AppendIterator();

		$iterator->append(new DirectoryIterator(_EPH_THEME_DIR_ . 'css/plugins'));
		foreach ($iterator as $file) {
			if (in_array($file->getFilename(), ['.', '..', '.htaccess', 'index.php'])) {
                continue;
    		}
			$filePath = $file->getPathname();
			$filePath = str_replace(_EPH_THEME_DIR_ . 'css/plugins/', '', $filePath);
			if(in_array($filePath, $plugintochecks)) {
				
			} else {
				Tools::deleteDirectory($file->getPathname());
			}	
		}
		$iterator = new AppendIterator();
		$iterator->append(new DirectoryIterator(_EPH_THEME_DIR_ . 'js/plugins'));
		foreach ($iterator as $file) {
			if (in_array($file->getFilename(), ['.', '..', '.htaccess', 'index.php'])) {
                continue;
    		}
			$filePath = $file->getPathname();
			$filePath = str_replace(_EPH_THEME_DIR_ . 'js/plugins/', '', $filePath);
			if(in_array($filePath, $plugintochecks)) {
				
			} else {
				Tools::deleteDirectory($file->getPathname());
			}		
		}
		$iterator = new AppendIterator();
		$iterator->append(new DirectoryIterator(_EPH_THEME_DIR_ . 'plugins'));
		foreach ($iterator as $file) {
			if (in_array($file->getFilename(), ['.', '..', '.htaccess', 'index.php'])) {
                continue;
			}
			$filePath = $file->getPathname();
			$filePath = str_replace(_EPH_THEME_DIR_ . 'plugins/', '', $filePath);
			if(in_array($filePath, $plugintochecks)) {
				
			} else {
				Tools::deleteDirectory($file->getPathname());
			}	 	
		}

	}
	
	public static function singleFontsUrl() {
		 
    	$url = '//fonts.googleapis.com/css?family=';
    	$main_str = '';
    	$subsets_str = '';
    	$subsets = array();
    	$all_fonts = array();
    	$font_types = array('bodyfont','headingfont','additionalfont');
    	if(isset($font_types) && !empty($font_types)){
    		foreach ($font_types as $font_type) {
    			$famil = Configuration::get($font_type.'_family');
    			$all_fonts[$famil]['fonts'] = $famil;
    			$all_fonts[$famil]['variants'] = Configuration::get($font_type.'_variants');
    			$subset = Configuration::get($font_type.'_subsets');
    			if(isset($subset) && !empty($subset)){
    				$subsetarr = @explode(",",$subset);
    				if(isset($subsetarr) && !empty($subsetarr) && is_array($subsetarr)){
    					foreach ($subsetarr as $arr) {
    						$subsets[$arr] = $arr;
    					}
    				}
    			}
    		}
    	}
    	$main = array();
    	if(isset($all_fonts) && !empty($all_fonts)){
    		foreach ($all_fonts as $all_font) {
    			$main[] = $all_font['fonts'].':'.$all_font['variants'];
    		}
    	}
    	if(isset($subsets) && !empty($subsets) && is_array($subsets)){
    		$subsets_str = implode(",",$subsets);
    	}
    	if(isset($main) && !empty($main) && is_array($main)){
    		$main_str = implode("|",$main);
    	}
    	if(isset($main_str) && !empty($main_str)){
    		$url .= $main_str;
    	}
    	if(isset($subsets_str) && !empty($subsets_str)){
    		$url .= '&subset='.$subsets_str;
    	}
    	if(isset($main_str) && !empty($main_str)){
    		return $url;
    	}else{
    		return false;
    	}
    }
	
	public static function getPhenyxFontName($key) {
		
		$name = str_replace(' ', '', Configuration::get($key . '_family'));
		return $name.'_'.Configuration::get($key . '_variants');
	}
	
	public static function GetPhenyxFontsURL($key = "", $var = [], $sub =[], $family = '') {

        if ($key == "") {
            return false;
        }
		

        if (Tools::usingSecureMode()) {
            $link = 'https://ephenyx.io/css?family=';
        } else {
            $link = 'https://ephenyx.io/css?family=';
        }
		
		if(empty($family)) {
			$family = Configuration::get($key . '_family');
        	$variants = Configuration::get($key . '_variants');
        	$subsets = Configuration::get($key . '_subsets');
			if (isset($family) && !empty($family)) {
            	$family = str_replace(" ", "+", $family);
            	$link .= $family;

            	if (isset($variants) && !empty($variants)) {
                	$link .= ':' . $variants;

                	if (isset($subsets) && !empty($subsets)) {
                    	$link .= '&subset=' . $subsets;
                	}

            	}

            	return $link;
			}
        
		}  else {
            $family = str_replace(" ", "+", $family);
            $link .= $family;

            if (is_array($var) && count($var)) {
				foreach($var as $key => $value)
                $link .= ':' . $value;

                if (is_array($sub) && count($sub)) {
					foreach($sub as $key => $value)
                    $link .= '&subset=' . $value;
                }

            }

            return $link;
        }

    }
	
	public static function GetAdminPhenyxFontsURL($key = "", $var = '', $sub ='') {

        if ($key == "") {
            return false;
        }
		

        if (Tools::usingSecureMode()) {
            $link = 'https://ephenyxapi.com/css?family=';
        } else {
            $link = 'https://ephenyxapi.com/css?family=';
        }

        $family = Configuration::get($key . '_family');
        $variants = Configuration::get($key . '_variants');
        $subsets = Configuration::get($key . '_subsets');
		
		

        if (isset($family) && !empty($family)) {
            $family = str_replace(" ", "+", $family);
            $link .= $family;

            if (isset($variants) && !empty($variants)) {
                $link .= ':' . $variants;

                if (isset($subsets) && !empty($subsets)) {
                    $link .= '&subset=' . $subsets;
                }

            }

            return $link;
        } else {
             $family = str_replace(" ", "+", $key);
            $link .= $family;

            if (isset($var) && !empty($var)) {
                $link .= ':' . $variants;

                if (isset($sub) && !empty($sub)) {
                    $link .= '&subset=' . $subsets;
                }

            }

            return $link;
        }

    }
    
    public static function cleanPluginDataBase() {
        
        $plugins = Db::getInstance()->executeS(
            (new DbQuery())
	         ->select('`id_plugin`, name')
            ->from('plugin')
        );
        foreach($plugins as $plugin) {
                
            if(!file_exists(_EPH_PLUGIN_DIR_.$plugin['name'].'/'.$plugin['name'].'.php')) {                    
            
                $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'hook_plugin WHERE id_plugin = '.$plugin['id_plugin'];
                Db::getInstance()->execute($sql);
                $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'hook_plugin_exceptions WHERE id_plugin = '.$plugin['id_plugin'];
                Db::getInstance()->execute($sql);
                $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin WHERE id_plugin = '.$plugin['id_plugin'];
                Db::getInstance()->execute($sql);
                $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_perfs WHERE plugin LIKE \''.$plugin['name'].'\'';
                Db::getInstance()->execute($sql);
                $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_access WHERE id_plugin = '.$plugin['id_plugin'];
                Db::getInstance()->execute($sql);
                $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_country WHERE id_plugin = '.$plugin['id_plugin'];
                Db::getInstance()->execute($sql);
                $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_currency WHERE id_plugin = '.$plugin['id_plugin'];
                Db::getInstance()->execute($sql);
                $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_group WHERE id_plugin = '.$plugin['id_plugin'];
                Db::getInstance()->execute($sql);
                $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_preference WHERE plugin LIKE \''.$plugin['name'].'\'';
               
            }
        
        }
        
        $hooks = Hook::getPluginHook();
        
        foreach($hooks as $hook) {
            $plugins = Db::getInstance()->executeS(
                (new DbQuery())
	            ->select('m.`id_plugin`, m.name')
                ->from('plugin', 'm')
                ->leftJoin('hook_plugin', 'hm', 'hm.`id_plugin` = m.`id_plugin`')
                ->where('hm.`id_hook` = '.$hook['id_hook'])
                ->orderBy('m.`id_plugin` ASC')
            );
            foreach($plugins as $plugin) {
                
                if(!file_exists(_EPH_PLUGIN_DIR_.$plugin['name'].'/'.$plugin['name'].'.php')) {                    
            
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'hook_plugin WHERE id_plugin = '.$plugin['id_plugin'];
                    Db::getInstance()->execute($sql);
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'hook_plugin_exceptions WHERE id_plugin = '.$plugin['id_plugin'];
                    Db::getInstance()->execute($sql);
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin WHERE id_plugin = '.$plugin['id_plugin'];
                    Db::getInstance()->execute($sql);
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugins_perfs WHERE plugin LIKE \''.$plugin['name'].'\'';
                    Db::getInstance()->execute($sql);
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_access WHERE id_plugin = '.$plugin['id_plugin'];
                    Db::getInstance()->execute($sql);
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_carrier WHERE id_plugin = '.$plugin['id_plugin'];
                    Db::getInstance()->execute($sql);
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_country WHERE id_plugin = '.$plugin['id_plugin'];
                    Db::getInstance()->execute($sql);
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_currency WHERE id_plugin = '.$plugin['id_plugin'];
                    Db::getInstance()->execute($sql);
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_group WHERE id_plugin = '.$plugin['id_plugin'];
                    Db::getInstance()->execute($sql);
                    $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'plugin_preference WHERE plugin LIKE \''.$plugin['name'].'\'';
                    
                }
        
            }
        }
    }
    
    public static function buildIncrementSelect($objet, $idLang, $fieldName, $rootName, $idParent = null) {
        
        
        $classObject = get_class($objet);        
        $classVars = get_class_vars($classObject);
        $primary = $classVars['definition']['primary'];
        $table = $classVars['definition']['table'];
       
        $root = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
 			(new DbQuery())
     		->select('`'.$primary.'`')
     		->from($table)
	 		->where('id_parent = 0' )
 		);
        
        $object_array = [];
        
        $select = '';	

       	
		$select .= '<option value="'.$root.'">'.$rootName.'</option>';
        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
 			(new DbQuery())
     		->select('a.`'.$primary.'`,a.id_parent, b.`'.$fieldName.'`')
     		->from($table, 'a')
     		->leftJoin($table.'_lang', 'b', 'a.`'.$primary.'` = b.`'.$primary.'` AND b.`id_lang`  = ' . (int) $idLang)
	 		->where('id_parent = '.$root )
     		->orderBy('a.`position` ASC')
 		);

        if (is_array($result)) {
			foreach($result as &$row) {
                $row['children'] = $classObject::getChlidren($row[$primary]);
				$object_array[$row[$primary]] = $row;
			}			
			foreach($object_array as $key => $value) {
				$select .= '<option value="'.$value[$primary].'" ';

				if($value[$primary] == $idParent) {
					$select .= 'selected="selected"';
				}
				$select .= '>'.$value[$fieldName].'</option>';
				foreach($value['children'] as $child) {
					$select .= '<option value="'.$child[$primary].'" ';
					if($child[$primary] == $idParent) {
						$select .= 'selected="selected"';
					}
					$select .= '>'.$value[$fieldName].' > '.$child[$fieldName].'</option>';
					if(is_array($child['children']) && count($child['children']))
                        foreach($child['children'] as $key => $value) {
			                foreach($value['children'] as $child) {
				                $select .= '<option value="'.$child[$primary].'" ';
				                if($child[$primary] == $idParent) {
					               $select .= 'selected="selected"';
				                }
				                $select .= '>'.$value[$fieldName].' > '.$child[$fieldName].'</option>';       
			                 }	
		              }
				}
			 }
		}
		
		return $select;
        
    }
    
    public static function get_media_alt($id = '') {

        if (isset($id) && !empty($id)) {
            $db = Db::getInstance();
            $context = Context::getContext();
            $id_lang = (int) Context::getContext()->language->id;
            $db_results = $db->getRow("SELECT `legend`  
            FROM eph_vc_media vm 
            INNER JOIN `eph_vc_media_lang` vml ON `vml`.`id_vc_media` = `vml`.`id_vc_media` 
            WHERE vm.id_vc_media={$id} AND `vml`.id_lang = " . $id_lang, true, false);
            return isset($db_results['legend']) ? $db_results['legend'] : '';
        } else {
            return '';
        }

    }
    
    public static function getAutoCompleteCity() {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
 			(new DbQuery())
     		->select('`post_code`, `city`')
     		->from('post_code')
     		->orderBy('city')
 		);
       


    }
    
    
    




}

/**
 * Compare 2 prices to sort products
 *
 * @param float $a
 * @param float $b
 *
 * @return int
 *
 * @since 1.9.1.0
 * @version 1.8.1.0 Initial version
 *
 * @todo    : move into class
 */
/* Externalized because of a bug in PHP 5.1.6 when inside an object */
function cmpPriceAsc($a, $b) {

    if ((float) $a['price_tmp'] < (float) $b['price_tmp']) {
        return (-1);
    } else
    if ((float) $a['price_tmp'] > (float) $b['price_tmp']) {
        return (1);
    }

    return 0;
}

/**
 * @param $a
 * @param $b
 *
 * @return int
 *
 * @since 1.9.1.0
 * @version 1.8.1.0 Initial version
 *
 * @todo    : move into class
 */
function cmpPriceDesc($a, $b) {

    if ((float) $a['price_tmp'] < (float) $b['price_tmp']) {
        return 1;
    } else
    if ((float) $a['price_tmp'] > (float) $b['price_tmp']) {
        return -1;
    }

    return 0;
}
