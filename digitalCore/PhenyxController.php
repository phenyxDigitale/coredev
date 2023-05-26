<?php

namespace phenyxDigitale\digitalCore;

/**
 * Class PhenyxController
 *
 * @since 1.6.6.3
 */
class PhenyxController {

    public static $php_errors = [];

    public $css_files = [];

    public $js_footers = [];

    public $js_files = [];

    public $index_js_files = [];

    public $index_js_def = [];

    public $push_js_files = [];

    public $push_css_files = [];

    public $ajax = false;

    public $ajaxLayout = false;
    /** @var string Controller type. Possible values: 'front', 'pluginfront', 'admin', 'pluginadmin' */
    public $controller_type;

    public $php_self;

    public $link_rewrite;

    protected $context;

    protected $display_header;

    protected $display_header_javascript;

    protected $template;

    protected $display_footer;

    protected $content_only = false;

    protected $json = false;

    protected $status = '';

    protected $redirect_after = null;
    
    protected $total_filesize = 0;
    
    protected $total_query_time = 0;
    
    protected $total_global_var_size = 0;
    
    protected $total_plugins_time = 0;
    
    protected $total_plugins_memory = 0;
    
    protected $global_var_size = [];

    protected $total_cache_size;

    protected $plugins_perfs = [];
    
    protected $hooks_perfs = [];

    protected $array_queries = [];

    protected $profiler = [];

    public $content_ajax = '';

    public function __construct() {
        
         if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            $this->profiler[] = $this->stamp('config');
         }


        if (is_null($this->display_header)) {
            $this->display_header = true;
        }

        if (is_null($this->display_header_javascript)) {
            $this->display_header_javascript = true;
        }

        if (is_null($this->display_footer)) {
            $this->display_footer = true;
        }

        $this->context = Context::getContext();
        $this->context->controller = $this;

        $this->ajax = Tools::getValue('ajax') || Tools::isSubmit('ajax');

        if (!headers_sent()
            && isset($_SERVER['HTTP_USER_AGENT'])
            && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false
                || strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false)
        ) {
            header('X-UA-Compatible: IE=edge,chrome=1');
        }
        
        if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            $this->profiler[] = $this->stamp('__construct');
        }

    }

    public function getController($className, $auth = false, $ssl = false) {

        return new $className($auth, $ssl);
    }

    public static function myErrorHandler($errno, $errstr, $errfile, $errline) {

        if (error_reporting() === 0) {
            return false;
        }

        switch ($errno) {
        case E_USER_ERROR:
        case E_ERROR:
            die('Fatal error: ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
            break;
        case E_USER_WARNING:
        case E_WARNING:
            $type = 'Warning';
            break;
        case E_USER_NOTICE:
        case E_NOTICE:
            $type = 'Notice';
            break;
        default:
            $type = 'Unknown error';
            break;
        }

        static::$php_errors[] = [
            'type'    => $type,
            'errline' => (int) $errline,
            'errfile' => str_replace('\\', '\\\\', $errfile), // Hack for Windows paths
            'errno'   => (int) $errno,
            'errstr'  => $errstr,
        ];

        Context::getContext()->smarty->assign('php_errors', static::$php_errors);

        return true;
    }

    public function &__get($property) {

        $camelCaseProperty = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $property))));

        if (property_exists($this, $camelCaseProperty)) {
            return $this->$camelCaseProperty;
        }

        return $this->$property;
    }

    public function __set($property, $value) {

        $blacklist = [
            '_select',
            '_join',
            '_where',
            '_group',
            '_having',
            '_conf',
            '_lang',
        ];

        // Property to camelCase for backwards compatibility
        $snakeCaseProperty = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $property))));

        if (!in_array($property, $blacklist) && property_exists($this, $snakeCaseProperty)) {
            $this->$snakeCaseProperty = $value;
        } else {
            $this->$property = $value;
        }

    }

    public function run() {

        $this->init();
        if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            $this->profiler[] = $this->stamp('init');
        }

        if ($this->checkAccess()) {

            if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className))) {
                $this->setMedia();
            }

            $this->postProcess();

            if (!empty($this->redirect_after)) {
                $this->redirect();
            }

            if (!$this->content_only && ($this->display_header || (isset($this->className) && $this->className))) {
                $this->initHeader();
            }

            if ($this->viewAccess()) {
                $this->initContent();
            } else {
                $this->errors[] = Tools::displayError('Access denied.');
            }

            if (!$this->content_only && ($this->display_footer || (isset($this->className) && $this->className))) {
                $this->initFooter();
            }

            if ($this->ajax) {
                $action = Tools::toCamelCase(Tools::getValue('action'), true);

                if (!empty($action) && method_exists($this, 'displayAjax' . $action)) {
                    $this->{'displayAjax' . $action}

                    ();
                } else

                if (method_exists($this, 'displayAjax')) {
                    $this->displayAjax();
                }

            } else {
                $this->display();
            }

        } else {
            $this->initCursedPage();

            if (isset($this->layout)) {
                $this->smartyOutputContent($this->layout);
            }

        }

    }

    public function init() {

        if (_EPH_MODE_DEV_ && $this->controller_type == 'admin') {
            set_error_handler([__CLASS__, 'myErrorHandler']);
        }

        if (!defined('_EPH_BASE_URL_')) {
            define('_EPH_BASE_URL_', Tools::getDomain(true));
        }

        if (!defined('_EPH_BASE_URL_SSL_')) {
            define('_EPH_BASE_URL_SSL_', Tools::getDomainSsl(true));
        }

    }

    abstract public function checkAccess();

    abstract public function setMedia();

    abstract public function postProcess();

    abstract protected function redirect();

    abstract public function initHeader();

    abstract public function viewAccess();

    abstract public function initContent();

    abstract public function initFooter();

    abstract public function display();

    abstract public function initCursedPage();

    protected function smartyOutputContent($content) {

        $this->context->cookie->write();
        $html = '';
        $jsTag = 'js_def';
        $this->context->smarty->assign($jsTag, $jsTag);

        if (is_array($content)) {

            foreach ($content as $tpl) {
                $html .= $this->context->smarty->fetch($tpl);
            }

        } else {
            $html = $this->context->smarty->fetch($content);
        }

        $html = trim($html);

        if (!empty($html)) {

            $domAvailable = extension_loaded('dom') ? true : false;
            $defer = (bool) Configuration::get('EPH_JS_DEFER');

            if ($defer && $domAvailable) {
                $html = Media::deferInlineScripts($html);
            }

            $html = trim(str_replace(['</body>', '</html>'], '', $html)) . "\n";

            $this->context->smarty->assign(
                [
                    $jsTag      => Media::getJsDef(),
                    'js_files'  => $defer ? array_unique($this->js_files) : [],
                    'js_inline' => ($defer && $domAvailable) ? Media::getInlineScript() : [],
                ]
            );
            $javascript = $this->context->smarty->fetch(_EPH_ALL_THEMES_DIR_ . 'javascript.tpl');

            if ($defer && (!isset($this->ajax) || !$this->ajax)) {
                echo $html . $javascript;
            } else
            if ($defer && $this->ajax) {

                die(Tools::jsonEncode(['html', $html . $javascript]));

            } else {
                echo preg_replace('/(?<!\$)' . $jsTag . '/', $javascript, $html);
            }

            echo ((!Tools::getIsset($this->ajax) || !$this->ajax) ? '</body></html>' : '');

        } else {
            echo $html;
        }

    }

    protected function ajaxOutputContent($content) {

        $this->context->cookie->write();
        $html = '';
        $jsTag = 'js_def';
        $this->context->smarty->assign($jsTag, $jsTag);

        if (is_array($content)) {

            foreach ($content as $tpl) {
                $html .= $this->context->smarty->fetch($tpl);
            }

        } else {
            $html = $this->context->smarty->fetch($content);
        }

        $html = trim($html);

        $domAvailable = extension_loaded('dom') ? true : false;
        $defer = (bool) Configuration::get('EPH_JS_DEFER');

        $html = trim(str_replace(['</body>', '</html>'], '', $html)) . "\n";
        $this->ajax_head = str_replace(['<head>', '</head>'], '', Media::deferTagOutput('head', $html));
        $page = Media::deferIdOutput('page', $html);
        $this->context->smarty->assign(
            [
                $jsTag      => Media::getJsDef(),
                'js_files'  => $defer ? array_unique($this->js_files) : [],
                'js_inline' => [],
            ]
        );
        $javascript = $this->context->smarty->fetch(_EPH_ALL_THEMES_DIR_ . 'javascript.tpl');

        if ($defer) {
            $templ = $page . $javascript;
            $return = [
                'historyState' => $this->historyState,
                'page_title'   => $this->page_title,
                'ajax_head'    => $this->ajax_head,
                'html'         => $templ,
            ];

        } else {
            $templ = preg_replace('/(?<!\$)' . $jsTag . '/', $javascript, $page);
            $return = [
                'historyState' => $this->historyState,
                'page_title'   => $this->page_title,
                'ajax_head'    => $this->ajax_head,
                'html'         => $templ,
            ];
        }

        die(Tools::jsonEncode($return));

    }

    public function displayHeader($display = true) {

        $this->display_header = $display;
    }

    public function displayHeaderJavaScript($display = true) {

        $this->display_header_javascript = $display;
    }

    public function displayFooter($display = true) {

        $this->display_footer = $display;
    }

    public function setTemplate($template) {

        $this->template = $template;
    }

    public function setRedirectAfter($url) {

        $this->redirect_after = $url;
    }

    public function removeCSS($cssUri, $cssMediaType = 'all', $checkPath = true) {

        if (!is_array($cssUri)) {
            $cssUri = [$cssUri];
        }

        foreach ($cssUri as $cssFile => $media) {

            if (is_string($cssFile) && strlen($cssFile) > 1) {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($cssFile, $media);
                } else {
                    $cssPath = [$cssFile => $media];
                }

            } else {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($media, $cssMediaType);
                } else {
                    $cssPath = [$media => $cssMediaType];
                }

            }

            if ($cssPath && isset($this->css_files[key($cssPath)]) && ($this->css_files[key($cssPath)] == reset($cssPath))) {
                unset($this->css_files[key($cssPath)]);
            }

        }

    }

    public function removeJS($jsUri, $checkPath = true) {

        if (is_array($jsUri)) {

            foreach ($jsUri as $jsFile) {
                $jsPath = $jsFile;

                if ($checkPath) {
                    $jsPath = Media::getJSPath($jsFile);
                }

                if ($jsPath && in_array($jsPath, $this->js_files)) {
                    unset($this->js_files[array_search($jsPath, $this->js_files)]);
                }

            }

        } else {
            $jsPath = $jsUri;

            if ($checkPath) {
                $jsPath = Media::getJSPath($jsUri);
            }

            if ($jsPath) {
                unset($this->js_files[array_search($jsPath, $this->js_files)]);
            }

        }

    }

    public function addJquery($version = null, $folder = null, $minifier = true) {

        $this->addJS(Media::getJqueryPath($version, $folder, $minifier), false);
    }

    public function addJS($jsUri, $checkPath = true) {

        if (is_array($jsUri)) {

            foreach ($jsUri as $jsFile) {
                $jsFile = explode('?', $jsFile);
                $version = '';

                if (isset($jsFile[1]) && $jsFile[1]) {
                    $version = $jsFile[1];
                }

                $jsPath = $jsFile = $jsFile[0];

                if ($checkPath) {
                    $jsPath = Media::getJSPath($jsFile);
                }

                // $key = is_array($js_path) ? key($js_path) : $js_path;

                if ($jsPath && !in_array($jsPath, $this->js_files)) {
                    $this->js_files[] = $jsPath . ($version ? '?' . $version : '');
                }

            }

        } else {
            $jsUri = explode('?', $jsUri);
            $version = '';

            if (isset($jsUri[1]) && $jsUri[1]) {
                $version = $jsUri[1];
            }

            $jsPath = $jsUri = $jsUri[0];

            if ($checkPath) {
                $jsPath = Media::getJSPath($jsUri);
            }

            if ($jsPath && !in_array($jsPath, $this->js_files)) {
                $this->js_files[] = $jsPath . ($version ? '?' . $version : '');
            }

        }

    }

    public function addFooterJS($jsUri, $checkPath = true) {

        if (is_array($jsUri)) {

            foreach ($jsUri as $jsFile) {
                $jsFile = explode('?', $jsFile);
                $version = '';

                if (isset($jsFile[1]) && $jsFile[1]) {
                    $version = $jsFile[1];
                }

                $jsPath = $jsFile = $jsFile[0];

                if ($checkPath) {
                    $jsPath = Media::getJSPath($jsFile);
                }

                // $key = is_array($js_path) ? key($js_path) : $js_path;

                if ($jsPath && !in_array($jsPath, $this->js_footers)) {
                    $this->js_footers[] = $jsPath . ($version ? '?' . $version : '');
                }

            }

        } else {
            $jsUri = explode('?', $jsUri);
            $version = '';

            if (isset($jsUri[1]) && $jsUri[1]) {
                $version = $jsUri[1];
            }

            $jsPath = $jsUri = $jsUri[0];

            if ($checkPath) {
                $jsPath = Media::getJSPath($jsUri);
            }

            if ($jsPath && !in_array($jsPath, $this->js_footers)) {
                $this->js_footers[] = $jsPath . ($version ? '?' . $version : '');
            }

        }

    }

    public function addJqueryUI($component, $theme = 'base', $checkDependencies = true) {

        if (!is_array($component)) {
            $component = [$component];
        }

        foreach ($component as $ui) {
            $uiPath = Media::getJqueryUIPath($ui, $theme, $checkDependencies);
            $this->addCSS($uiPath['css'], 'all', false);
            $this->addJS($uiPath['js'], false);
        }

    }

    public function addCSS($cssUri, $cssMediaType = 'all', $offset = null, $checkPath = true) {

        if (!is_array($cssUri)) {
            $cssUri = [$cssUri];
        }

        foreach ($cssUri as $cssFile => $media) {

            if (is_string($cssFile) && strlen($cssFile) > 1) {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($cssFile, $media);
                } else {
                    $cssPath = [$cssFile => $media];
                }

            } else {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($media, $cssMediaType);
                } else {
                    $cssPath = [$media => is_string($cssMediaType) ? $cssMediaType : 'all'];
                }

            }

            $key = is_array($cssPath) ? key($cssPath) : $cssPath;

            if ($cssPath && (!isset($this->css_files[$key]) || ($this->css_files[$key] != reset($cssPath)))) {
                $size = count($this->css_files);

                if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset)) {
                    $offset = $size;
                }

                $this->css_files = array_merge(array_slice($this->css_files, 0, $offset), $cssPath, array_slice($this->css_files, $offset));
            }

        }

    }

    public function pushCSS($cssUri, $cssMediaType = 'all', $offset = null, $checkPath = true) {

        if (!is_array($cssUri)) {
            $cssUri = [$cssUri];
        }

        $result = [];

        foreach ($cssUri as $cssFile => $media) {

            if (is_string($cssFile) && strlen($cssFile) > 1) {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($cssFile, $media);
                } else {
                    $cssPath = [$cssFile => $media];
                }

            } else {

                if ($checkPath) {
                    $cssPath = Media::getCSSPath($media, $cssMediaType);
                } else {

                    $cssPath = [$media => is_string($cssMediaType) ? $cssMediaType : 'all'];
                }

            }

            $key = is_array($cssPath) ? key($cssPath) : $cssPath;

            if ($cssPath) {
                $size = count($this->push_css_files);

                if ($offset === null || $offset > $size || $offset < 0 || !is_numeric($offset)) {
                    $offset = $size;
                }

                $this->push_css_files = array_merge(array_slice($this->push_css_files, 0, $offset), $cssPath, array_slice($this->push_css_files, $offset));
            }

        }

        return $this->push_css_files;

    }

    public function pushJS($jsUri, $checkPath = true) {

        $this->push_js_files = [];

        if (is_array($jsUri)) {

            foreach ($jsUri as $jsFile) {
                $jsFile = explode('?', $jsFile);
                $version = '';

                if (isset($jsFile[1]) && $jsFile[1]) {
                    $version = $jsFile[1];
                }

                $jsPath = $jsFile = $jsFile[0];

                if ($checkPath) {
                    $jsPath = Media::getJSPath($jsFile);
                }

                // $key = is_array($js_path) ? key($js_path) : $js_path;

                if ($jsPath && !in_array($jsPath, $this->push_js_files)) {
                    $this->push_js_files[] = $jsPath . ($version ? '?' . $version : '');
                }

            }

        } else {
            $jsUri = explode('?', $jsUri);
            $version = '';

            if (isset($jsUri[1]) && $jsUri[1]) {
                $version = $jsUri[1];
            }

            $jsPath = $jsUri = $jsUri[0];

            if ($checkPath) {
                $jsPath = Media::getJSPath($jsUri);
            }

            if ($jsPath && !in_array($jsPath, $this->push_js_files)) {
                $this->push_js_files[] = $jsPath . ($version ? '?' . $version : '');
            }

        }

        return $this->push_js_files;

    }

    public function addJqueryPlugin($name, $folder = null, $css = true) {

        if (!is_array($name)) {
            $name = [$name];
        }

        if (is_array($name)) {

            foreach ($name as $plugin) {
                $pluginPath = Media::getJqueryPluginPath($plugin, $folder);

                if (!empty($pluginPath['js'])) {
                    $this->addJS($pluginPath['js'], false);
                }

                if ($css && !empty($pluginPath['css'])) {
                    $this->addCSS(key($pluginPath['css']), 'all', null, false);
                }

            }

        }

    }

    public function isXmlHttpRequest() {

        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    protected function isCached($template, $cacheId = null, $compileId = null) {

        Tools::enableCache();
        $res = $this->context->smarty->isCached($template, $cacheId, $compileId);
        Tools::restoreCacheSettings();

        return $res;
    }

    protected function ajaxDie($value = null, $controller = null, $method = null) {

        if ($controller === null) {
            $controller = get_class($this);
        }

        if ($method === null) {
            $bt = debug_backtrace();
            $method = $bt[1]['function'];
        }

        Hook::exec('actionBeforeAjaxDie', ['controller' => $controller, 'method' => $method, 'value' => $value]);
        Hook::exec('actionBeforeAjaxDie' . $controller . $method, ['value' => $value]);

        die($value);
    }
    
    private function getMemoryColor($n) {

        $n /= 1048576;

        if ($n > 3) {
            return '<span style="color:red">' . sprintf('%0.2f', $n) . '</span>';
        } else
        if ($n > 1) {
            return '<span style="color:#EF8B00">' . sprintf('%0.2f', $n) . '</span>';
        } else
        if (round($n, 2) > 0) {
            return '<span style="color:green">' . sprintf('%0.2f', $n) . '</span>';
        }

        return '<span style="color:green">-</span>';
    }

    private function getPeakMemoryColor($n) {

        $n /= 1048576;

        if ($n > 16) {
            return '<span style="color:red">' . sprintf('%0.1f', $n) . '</span>';
        }

        if ($n > 12) {
            return '<span style="color:#EF8B00">' . sprintf('%0.1f', $n) . '</span>';
        }

        return '<span style="color:green">' . sprintf('%0.1f', $n) . '</span>';
    }

    private function displaySQLQueries($n) {

        if ($n > 150) {
            return '<span style="color:red">' . $n . ' queries</span>';
        }

        if ($n > 100) {
            return '<span style="color:#EF8B00">' . $n . ' queries</span>';
        }

        return '<span style="color:green">' . $n . ' quer' . ($n == 1 ? 'y' : 'ies') . '</span>';
    }

    private function displayRowsBrowsed($n) {

        if ($n > 400) {
            return '<span style="color:red">' . $n . ' rows browsed</span>';
        }

        if ($n > 100) {
            return '<span style="color:#EF8B00">' . $n . '  rows browsed</span>';
        }

        return '<span style="color:green">' . $n . ' row' . ($n == 1 ? '' : 's') . ' browsed</span>';
    }

    private function getPhpVersionColor($version) {

        if (version_compare($version, '5.3') < 0) {
            return '<span style="color:red">' . $version . ' (Upgrade strongly recommended)</span>';
        } else
        if (version_compare($version, '5.4') < 0) {
            return '<span style="color:#EF8B00">' . $version . ' (Consider upgrading)</span>';
        }

        return '<span style="color:green">' . $version . ' (OK)</span>';
    }

    private function getMySQLVersionColor($version) {

        if (version_compare($version, '5.5') < 0) {
            return '<span style="color:red">' . $version . ' (Upgrade strongly recommended)</span>';
        } else
        if (version_compare($version, '5.6') < 0) {
            return '<span style="color:#EF8B00">' . $version . ' (Consider upgrading)</span>';
        }

        return '<span style="color:green">' . $version . ' (OK)</span>';
    }

    private function getLoadTimeColor($n, $kikoo = false) {

        if ($n > 1.6) {
            return '<span style="color:red">' . round($n * 1000) . '</span>' . ($kikoo ? ' ms - You\'d better run your shop on a toaster' : '');
        } else
        if ($n > 0.8) {
            return '<span style="color:#EF8B00">' . round($n * 1000) . '</span>' . ($kikoo ? ' ms - OK... for a shared hosting' : '');
        } else
        if ($n > 0) {
            return '<span style="color:green">' . round($n * 1000) . '</span>' . ($kikoo ? ' ms - Unicorn powered webserver!' : '');
        }

        return '<span style="color:green">-</span>' . ($kikoo ? ' ms - Faster than light' : '');
    }

    private function getTotalQueriyingTimeColor($n) {

        if ($n >= 100) {
            return '<span style="color:red">' . $n . '</span>';
        } else
        if ($n >= 50) {
            return '<span style="color:#EF8B00">' . $n . '</span>';
        }

        return '<span style="color:green">' . $n . '</span>';
    }

    private function getNbQueriesColor($n) {

        if ($n >= 100) {
            return '<span style="color:red">' . $n . '</span>';
        } else
        if ($n >= 50) {
            return '<span style="color:#EF8B00">' . $n . '</span>';
        }

        return '<span style="color:green">' . $n . '</span>';
    }

    private function getTimeColor($n) {

        if ($n > 4) {
            return 'style="color:red"';
        }

        if ($n > 2) {
            return 'style="color:#EF8B00"';
        }

        return 'style="color:green"';
    }

    private function getQueryColor($n) {

        if ($n > 5) {
            return 'style="color:red"';
        }

        if ($n > 2) {
            return 'style="color:#EF8B00"';
        }

        return 'style="color:green"';
    }

    private function getTableColor($n) {

        if ($n > 30) {
            return 'style="color:red"';
        }

        if ($n > 20) {
            return 'style="color:#EF8B00"';
        }

        return 'style="color:green"';
    }

    private function getObjectModelColor($n) {

        if ($n > 50) {
            return 'style="color:red"';
        }

        if ($n > 10) {
            return 'style="color:#EF8B00"';
        }

        return 'style="color:green"';
    }

    protected function stamp($block) {

        return ['block' => $block, 'memory_usage' => memory_get_usage(), 'peak_memory_usage' => memory_get_peak_usage(), 'time' => microtime(true)];
    }
    
    private function getVarSize($var) {

        $start_memory = memory_get_usage();
        try {
            $tmp = json_decode(json_encode($var));
        } catch (Exception $e) {
            $tmp = $this->getVarData($var);
        }

        $size = memory_get_usage() - $start_memory;
        return $size;
    }

    private function getVarData($var) {

        if (is_object($var)) {
            return $var;
        }

        return (string) $var;
    }

    protected function processProfilingData() {

        global $start_time;

        // Including a lot of files uses memory

        foreach (get_included_files() as $file) {
            $this->total_filesize += filesize($file);
        }

        // Sum querying time

        foreach (Db::getInstance()->queries as $data) {
            $this->total_query_time += $data['time'];
        }

        foreach ($GLOBALS as $key => $value) {

            if ($key != 'GLOBALS') {
                $this->total_global_var_size += ($size = $this->getVarSize($value));

                if ($size > 1024) {
                    $this->global_var_size[$key] = round($size / 1024);
                }

            }

        }

        arsort($this->global_var_size);

        $cache = Cache::retrieveAll();
        $this->total_cache_size = $this->getVarSize($cache);

        // Retrieve plugin perfs
        $result = Db::getInstance()->ExecuteS('
            SELECT *
            FROM ' . _DB_PREFIX_ . 'plugins_perfs
            WHERE session = ' . (int) Plugin::$_log_plugins_perfs_session . '
            AND time_start >= ' . (float) $start_time . '
            AND time_end <= ' . (float) $this->profiler[count($this->profiler) - 1]['time']
        );

        foreach ($result as $row) {
            $tmp_time = $row['time_end'] - $row['time_start'];
            $tmp_memory = $row['memory_end'] - $row['memory_start'];
            $this->total_plugins_time += $tmp_time;
            $this->total_plugins_memory += $tmp_memory;

            if (!isset($this->plugins_perfs[$row['plugin']])) {
                $this->plugins_perfs[$row['plugin']] = ['time' => 0, 'memory' => 0, 'methods' => []];
            }

            $this->plugins_perfs[$row['plugin']]['time'] += $tmp_time;
            $this->plugins_perfs[$row['plugin']]['methods'][$row['method']]['time'] = $tmp_time;
            $this->plugins_perfs[$row['plugin']]['memory'] += $tmp_memory;
            $this->plugins_perfs[$row['plugin']]['methods'][$row['method']]['memory'] = $tmp_memory;

            if (!isset($this->hooks_perfs[$row['method']])) {
                $this->hooks_perfs[$row['method']] = ['time' => 0, 'memory' => 0, 'plugins' => []];
            }

            $this->hooks_perfs[$row['method']]['time'] += $tmp_time;
            $this->hooks_perfs[$row['method']]['plugins'][$row['plugin']]['time'] = $tmp_time;
            $this->hooks_perfs[$row['method']]['memory'] += $tmp_memory;
            $this->hooks_perfs[$row['method']]['plugins'][$row['plugin']]['memory'] = $tmp_memory;
        }

        uasort($this->plugins_perfs, 'phenyxshop_querytime_sort');
        uasort($this->hooks_perfs, 'phenyxshop_querytime_sort');

        $queries = Db::getInstance()->queries;
        uasort($queries, 'phenyxshop_querytime_sort');

        foreach ($queries as $data) {
            $query_row = [
                'time'     => $data['time'],
                'query'    => $data['query'],
                'location' => str_replace('\\', '/', substr($data['stack'][0]['file'], strlen(_EPH_ROOT_DIR_))) . ':' . $data['stack'][0]['line'],
                'filesort' => false,
                'rows'     => 1,
                'group_by' => false,
                'stack'    => [],
            ];

            if (preg_match('/^\s*select\s+/i', $data['query'])) {
                $explain = Db::getInstance()->executeS('explain ' . $data['query']);

                if (stristr($explain[0]['Extra'], 'filesort')) {
                    $query_row['filesort'] = true;
                }

                foreach ($explain as $row) {
                    $query_row['rows'] *= $row['rows'];
                }

                if (stristr($data['query'], 'group by') && !preg_match('/(avg|count|min|max|group_concat|sum)\s*\(/i', $data['query'])) {
                    $query_row['group_by'] = true;
                }

            }

            array_shift($data['stack']);

            foreach ($data['stack'] as $call) {
                $query_row['stack'][] = str_replace('\\', '/', substr($call['file'], strlen(_EPH_ROOT_DIR_))) . ':' . $call['line'];
            }

            $this->array_queries[] = $query_row;
        }

        uasort(PhenyxObjectModel::$debug_list, function ($b, $a) {

            if (count($a) < count($b)) {
                return 1;
            }

            return -1;
        });
        arsort(Db::getInstance()->tables);
        arsort(Db::getInstance()->uniqQueries);
    }

    protected function displayProfilingLinks() {

        $this->content_ajax .= '
        <div id="profiling_link" class="subTabs col-lg-12">
            <ul>
                <li><a href="#stopwatch">Stopwatch SQL</a></li>
                <li><a href="#sql_doubles">Doubles</a></li>
                <li><a href="#stress_tables">Tables stress</a></li>
                ' . (isset(PhenyxObjectModel::$debug_list) ? '<li><a href="#objectModels">ObjectModel instances</a></li>' : '') . '
                <li><a href="#includedFiles">Included Files</a></li>
            </ul>
        <div id="tabs-profilling-content" class="tabs-controller-content">';
    }

    protected function displayProfilingStyle() {

        $this->content_ajax .= '

        <script type="text/javascript" src="https://cdn.rawgit.com/drvic10k/bootstrap-sortable/1.11.2/Scripts/bootstrap-sortable.js"></script>';
    }

    protected function displayProfilingSummary() {

        global $start_time;

        $this->content_ajax .= '
        <div class="col-4">
            <table class="table table-condensed">
                <tr><td>Load time</td><td>' . $this->getLoadTimeColor($this->profiler[count($this->profiler) - 1]['time'] - $start_time, true) . '</td></tr>
                <tr><td>Querying time</td><td>' . $this->getTotalQueriyingTimeColor(round(1000 * $this->total_query_time)) . ' ms</span>
                <tr><td>Queries</td><td>' . $this->getNbQueriesColor(count($this->array_queries)) . '</td></tr>
                <tr><td>Memory peak usage</td><td>' . $this->getPeakMemoryColor($this->profiler[count($this->profiler) - 1]['peak_memory_usage']) . ' Mb</td></tr>
                <tr><td>Included files</td><td>' . count(get_included_files()) . ' files - ' . $this->getMemoryColor($this->total_filesize) . ' Mb</td></tr>
                <tr><td>ephenyx cache</td><td>' . $this->getMemoryColor($this->total_cache_size) . ' Mb</td></tr>
                <tr><td><a href="javascript:void(0);" onclick="$(\'.global_vars_detail\').toggle();">Global vars</a></td><td>' . $this->getMemoryColor($this->total_global_var_size) . ' Mb</td></tr>';

        foreach ($this->global_var_size as $global => $size) {
            $this->content_ajax .= '<tr class="global_vars_detail" style="display:none"><td>- global $' . $global . '</td><td>' . $size . 'k</td></tr>';
        }

        $this->content_ajax .= '
            </table>
        </div>';
    }

    protected function displayProfilingConfiguration() {

        $this->content_ajax .= '
        <div class="col-4">
            <table class="table table-condensed">
                <tr><td>ephenyx version</td><td>' . _EPH_VERSION_ . '</td></tr>
                <tr><td>PhenyxShop (emulated) version</td><td>' . _EPH_VERSION_ . '</td></tr>
                <tr><td>PHP version</td><td>' . $this->getPhpVersionColor(phpversion()) . '</td></tr>
                <tr><td>MySQL version</td><td>' . $this->getMySQLVersionColor(Db::getInstance()->getVersion()) . '</td></tr>
                <tr><td>Memory limit</td><td>' . ini_get('memory_limit') . '</td></tr>
                <tr><td>Max execution time</td><td>' . ini_get('max_execution_time') . 's</td></tr>
                <tr><td>Smarty cache</td><td><span style="color:' . (Configuration::get('EPH_SMARTY_CACHE') ? 'green">enabled' : 'red">disabled') . '</td></tr>
                <tr><td>Smarty Compilation</td><td><span style="color:' . (Configuration::get('EPH_SMARTY_FORCE_COMPILE') == 0 ? 'green">never recompile' : (Configuration::get('EPH_SMARTY_FORCE_COMPILE') == 1 ? '#EF8B00">auto' : 'red">force compile')) . '</td></tr>
            </table>
        </div>';
    }

    protected function displayProfilingRun() {

        global $start_time;

        $this->content_ajax .= '
        <div class="col-4">
            <table class="table table-condensed">
                <tr><th>&nbsp;</th><th>Time</th><th>Cumulated Time</th><th>Memory Usage</th><th>Memory Peak Usage</th></tr>';
        $last = ['time' => $start_time, 'memory_usage' => 0];

        foreach ($this->profiler as $row) {

            if ($row['block'] == 'checkAccess' && $row['time'] == $last['time']) {
                continue;
            }

            $this->content_ajax .= '<tr>
                <td>' . $row['block'] . '</td>
                <td>' . $this->getLoadTimeColor($row['time'] - $last['time']) . ' ms</td>
                <td>' . $this->getLoadTimeColor($row['time'] - $start_time) . ' ms</td>
                <td>' . $this->getMemoryColor($row['memory_usage'] - $last['memory_usage']) . ' Mb</td>
                <td>' . $this->getMemoryColor($row['peak_memory_usage']) . ' Mb</td>
            </tr>';
            $last = $row;
        }

        $this->content_ajax .= '
            </table>
        </div>';
    }

    protected function displayProfilingHooks() {

        $count_hooks = count($this->hooks_perfs);

        $this->content_ajax .= '
        <div class="col-lg-6">
            <table class="table table-condensed">
                <tr>
                    <th>Hook</th>
                    <th>Time</th>
                    <th>Memory Usage</th>
                </tr>';

        foreach ($this->hooks_perfs as $hook => $hooks_perfs) {
            $this->content_ajax .= '
                <tr>
                    <td>
                        <a href="javascript:void(0);" onclick="$(\'.' . $hook . '_plugins_details\').toggle();">' . $hook . '</a>
                    </td>
                    <td>
                        ' . $this->getLoadTimeColor($hooks_perfs['time']) . ' ms
                    </td>
                    <td>
                        ' . $this->getMemoryColor($hooks_perfs['memory']) . ' Mb
                    </td>
                </tr>';

            foreach ($hooks_perfs['plugins'] as $plugin => $perfs) {
                $this->content_ajax .= '
                <tr class="' . $hook . '_plugins_details" style="background-color:#EFEFEF;display:none">
                    <td>
                        =&gt; ' . $plugin . '
                    </td>
                    <td>
                        ' . $this->getLoadTimeColor($perfs['time']) . ' ms
                    </td>
                    <td>
                        ' . $this->getMemoryColor($perfs['memory']) . ' Mb
                    </td>
                </tr>';
            }

        }

        $this->content_ajax .= '  <tr>
                    <th><b>' . ($count_hooks == 1 ? '1 hook' : (int) $count_hooks . ' hooks') . '</b></th>
                    <th>' . $this->getLoadTimeColor($this->total_plugins_time) . ' ms</th>
                    <th>' . $this->getMemoryColor($this->total_plugins_memory) . ' Mb</th>
                </tr>
            </table>
        </div>';
    }

    protected function displayProfilingPlugins() {

        $count_plugins = count($this->plugins_perfs);

        $this->content_ajax .= '
        <div class="col-lg-6">
            <table class="table table-condensed">
                <tr>
                    <th>Plugin</th>

                    <th>Time</th>
                    <th>Memory Usage</th>
                </tr>';

        foreach ($this->plugins_perfs as $plugin => $plugins_perfs) {
            $this->content_ajax .= '
                <tr>
                    <td>
                        <a href="javascript:void(0);" onclick="$(\'.' . $plugin . '_hooks_details\').toggle();">' . $plugin . '</a>
                    </td>
                    <td>
                        ' . $this->getLoadTimeColor($plugins_perfs['time']) . ' ms
                    </td>
                    <td>
                        ' . $this->getMemoryColor($plugins_perfs['memory']) . ' Mb
                    </td>
                </tr>';

            foreach ($plugins_perfs['methods'] as $hook => $perfs) {
                $this->content_ajax .= '
                <tr class="' . $plugin . '_hooks_details" style="background-color:#EFEFEF;display:none">
                    <td>
                        =&gt; ' . $hook . '
                    </td>
                    <td>
                        ' . $this->getLoadTimeColor($perfs['time']) . ' ms
                    </td>
                    <td>
                        ' . $this->getMemoryColor($perfs['memory']) . ' Mb
                    </td>
                </tr>';
            }

        }

        $this->content_ajax .= '  <tr>
                    <th><b>' . ($count_plugins == 1 ? '1 plugin' : (int) $count_plugins . ' plugins') . '</b></th>
                    <th>' . $this->getLoadTimeColor($this->total_plugins_time) . ' ms</th>
                    <th>' . $this->getMemoryColor($this->total_plugins_memory) . ' Mb</th>
                </tr>
            </table>
        </div>';
    }

    protected function displayProfilingStopwatch() {

        $this->content_ajax .= '
        <div id="stopwatch">
            <h2><a name="stopwatch">Stopwatch SQL - ' . count($this->array_queries) . ' queries</a></h2>
            <table class="table table-condensed table-bordered sortable col-lg-12">
                <thead>
                    <tr>
                        <th style="width:50%">Query</th>
                        <th style="width:10%">Time (ms)</th>
                        <th style="width:10%">Rows</th>
                        <th style="width:5%">Filesort</th>
                        <th style="width:5%">Group By</th>
                        <th style="width:20%">Location</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($this->array_queries as $data) {
            $callstack = implode('<br>', $data['stack']);
            $callstack_md5 = md5($callstack);

            $this->content_ajax .= '
                <tr>
                    <td class="pre" style="width:50%; display:table-cell">' . preg_replace("/(^[\s]*)/m", "", htmlspecialchars($data['query'], ENT_NOQUOTES, 'utf-8', false)) . '</td>
                    <td style="width:10%"><span ' . $this->getTimeColor($data['time'] * 1000) . '>' . (round($data['time'] * 1000, 1) < 0.1 ? '< 1' : round($data['time'] * 1000, 1)) . '</span></td>
                    <td>' . (int) $data['rows'] . '</td>
                    <td>' . ($data['filesort'] ? '<span style="color:red">Yes</span>' : '') . '</td>
                    <td>' . ($data['group_by'] ? '<span style="color:red">Yes</span>' : '') . '</td>
                    <td>
                        <a href="javascript:void(0);" onclick="$(\'#callstack_' . $callstack_md5 . '\').toggle();">' . $data['location'] . '</a>
                        <div id="callstack_' . $callstack_md5 . '" style="display:none">' . implode('<br>', $data['stack']) . '</div>
                    </td>
                </tr>';
        }

        $this->content_ajax .= '</table>
        </div>';
    }

    protected function displayProfilingDoubles() {

        $this->content_ajax .= '<div id="sql_doubles">
        <h2><a name="doubles">Doubles</a></h2>
            <table class="table table-condensed">';

        foreach (Db::getInstance()->uniqQueries as $q => $nb) {

            if ($nb > 1) {
                $this->content_ajax .= '<tr><td><span ' . $this->getQueryColor($nb) . '>' . $nb . '</span></td><td class="pre"><pre>' . $q . '</pre></td></tr>';
            }

        }

        $this->content_ajax .= '</table>
        </div>';
    }

    protected function displayProfilingTableStress() {

        $this->content_ajax .= '<div id="stress_tables">
        <h2><a name="tables">Tables stress</a></h2>
        <table class="table table-condensed">';

        foreach (Db::getInstance()->tables as $table => $nb) {
            $this->content_ajax .= '<tr><td><span ' . $this->getTableColor($nb) . '>' . $nb . '</span> ' . $table . '</td></tr>';
        }

        $this->content_ajax .= '</table>
        </div>';
    }

    protected function displayProfilingObjectModel() {

        $this->content_ajax .= '
        <div id="objectModels">
            <h2><a name="objectModels">ObjectModel instances</a></h2>
            <table class="table table-condensed">
                <tr><th>Name</th><th>Instances</th><th>Source</th></tr>';

        foreach (PhenyxObjectModel::$debug_list as $class => $info) {
            $this->content_ajax .= '<tr>
                    <td>' . $class . '</td>
                    <td><span ' . $this->getObjectModelColor(count($info)) . '>' . count($info) . '</span></td>
                    <td>';

            foreach ($info as $trace) {
                $this->content_ajax .= str_replace([_EPH_ROOT_DIR_, '\\'], ['', '/'], $trace['file']) . ' [' . $trace['line'] . ']<br />';
            }

            $this->content_ajax .= '  </td>
                </tr>';
        }

        $this->content_ajax .= '</table>
        </div>';
    }

    protected function displayProfilingFiles() {

        $i = 0;

        $this->content_ajax .= '<div id="includedFiles">
        <h2><a name="includedFiles">Included Files</a></h2>
        <table class="table table-condensed">
            <tr><th>#</th><th>Filename</th></tr>';

        foreach (get_included_files() as $file) {
            $file = str_replace('\\', '/', str_replace(_EPH_ROOT_DIR_, '', $file));

            if (strpos($file, '/tools/profiling/') === 0) {
                continue;
            }

            $this->content_ajax .= '<tr><td>' . (++$i) . '</td><td>' . $file . '</td></tr>';
        }

        $this->content_ajax .= '</table>
        </div>';
    }
    
    public function displayProfiling() {

        $this->profiler[] = $this->stamp('display');
        // Process all profiling data
        $this->processProfilingData();

        // Add some specific style for profiling information
        //$this->displayProfilingStyle();

       $this->content_ajax .= '<div id="phenyxshop_profiling" class="bootstrap">';

        $this->content_ajax .= 'Summary'.'<div class="row">';
        $this->displayProfilingSummary();
        $this->displayProfilingConfiguration();
        $this->displayProfilingRun();
        $this->content_ajax .= '</div><div class="row">';
        $this->displayProfilingHooks();
        $this->displayProfilingPlugins();
        $this->displayProfilingLinks();

        $this->displayProfilingStopwatch();
        $this->displayProfilingDoubles();
        $this->displayProfilingTableStress();

        if (isset(PhenyxObjectModel::$debug_list)) {
            $this->displayProfilingObjectModel();
        }

        $this->displayProfilingFiles();
        
        $this->content_ajax .= '</div>';

        
       return $this->content_ajax;

    }


}
