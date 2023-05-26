<?php

use vierbergenlars\SemVer\version;

/**
 * Class PluginCore
 *
 * @since 2.1.0.0
 *
 * @property $confirmUninstall
 */
abstract class Plugin {

    const CACHE_FILE_TAB_PLUGINS_LIST = '/app/xml/tab_plugins_list.xml';
    // @codingStandardsIgnoreStart
    /** @var array used by AdminTab to determine which lang file to use (admin.php or plugin lang file) */
    public static $classInPlugin = [];
    /** @var bool Define if we will log plugins performances for this session */
    public static $_log_plugins_perfs = null;
    /** @var array $hosted_plugins_blacklist */
    public static $hosted_plugins_blacklist = ['autoupgrade'];
    /** @var bool Random session for plugins perfs logs */
    public static $_log_plugins_perfs_session = null;
    /** @var array Array cache filled with plugins informations */
    protected static $plugins_cache;
    /** @var array Array cache filled with plugins instances */
    protected static $_INSTANCE = [];
    /** @var bool Config xml generation mode */
    protected static $_generate_config_xml_mode = false;
    /** @var array Array filled with cache translations */
    protected static $l_cache = [];
    /** @var array Array filled with cache permissions (plugins / employee profiles) */
    protected static $cache_permissions = [];
    /** @var bool $update_translations_after_install */
    protected static $update_translations_after_install = true;
    /** @var bool $_batch_mode */
    protected static $_batch_mode = false;
    /** @var array $_defered_clearCache */
    protected static $_defered_clearCache = [];
    /** @var array $_defered_func_call */
    protected static $_defered_func_call = [];
    /** @var int Plugin ID */
    public $id = null;
    /** @var string $version Version */
    public $version;
    
    public $is_configurable;
    
    public $confirmUninstall;

    public $removable = true;

    public $has_table = false;
    /** @var string $database_version */
    public $database_version;
    /** @var string Registered Version in database */
    public $registered_version;
    /** @var array filled with known compliant PrestaShop versions */
    public $eph_versions_compliancy = [];
    /**
     * @var string Filled with known compliant thirty bees versions
     *             This string contains a SemVer 1.0.0 range
     */
    public $has_reset;
    /** @var array filled with plugins needed for install */
    public $dependencies = [];
    /** @var string Unique name */
    public $name;
    /** @var string Human name */
    public $displayName;
    /** @var string A little description of the plugin */
    public $description;
    /** @var string author of the plugin */
    public $author;
    /** @var string URI author of the plugin */
    public $author_uri = '';
    /** @var string Plugin key */
    public $plugin_key = '';
    /** @var string $description_full */
    public $description_full;
    /** @var string $additional_description */
    public $additional_description;
    /** @var string $compatibility */
    public $compatibility;
    /** @var int $nb_rates */
    public $nb_rates;
    /** @var float $avg_rate */
    public $avg_rate;
    /** @var array $badges */
    public $badges;
    
    public $need_config = false;
    
    public $config_controller = null;
    /** @var string Admin tab corresponding to the plugin */
    public $tab = null;
    /** @var bool Status */
    public $active = false;
    /** @var bool Is the plugin certified */
    public $trusted = true;
    /** @var string Fill it if the plugin is installed but not yet set up */
    public $warning;
    /** @var int $enable_device */
    public $enable_device = 7;
    /** @var array to store the limited country */
    public $limited_countries = [];
    /** @var array names of the controllers */
    public $controllers = [];
    /** @var bool If true, allow push */
    public $allow_push;
    /** @var int $push_time_limit */
    public $push_time_limit = 180;
    
    public $is_eu_compatible;
    /**
     * @var bool $bootstrap
     *
     * Indicates whether the plugin's configuration page supports bootstrap
     */
    public $bootstrap = false;
    /** @var array current language translations */
    protected $_lang = [];
    /** @var string Plugin web path (eg. '/shop/plugins/pluginname/') */
    protected $_path = null;
    /** @var string Plugin local path (eg. '/home/prestashop/plugins/pluginname/') */
    protected $local_path = null;
    /** @var array Array filled with plugin errors */
    protected $_errors = [];
    /** @var array Array  array filled with plugin success */
    protected $_confirmations = [];
    /** @var string Main table used for plugins installed */
    protected $table = 'plugin';
    /** @var string Identifier of the main table */
    protected $identifier = 'id_plugin';
    /** @var Context */
    //protected $context;
    /** @var Smarty_Data */
    protected $smarty;
    /** @var Smarty_Internal_Template|null */
    protected $current_subtemplate = null;
    /** @var bool $installed */
    public $installed;

    public $favorite = false;
    
    public $context;
    // @codingStandardsIgnoreEnd

    /**
     * Constructor
     *
     * @param string  $name Plugin unique name
     * @param Context $context
     *
     * @since 2.1.0.0
     * @throws PhenyxException
     */
    public function __construct($name = null, Context $context = null) {

        if (isset($this->eph_versions_compliancy) && !isset($this->eph_versions_compliancy['min'])) {
            $this->eph_versions_compliancy['min'] = '1.4.0.0';
        }

        if (isset($this->eph_versions_compliancy) && !isset($this->eph_versions_compliancy['max'])) {
            $this->eph_versions_compliancy['max'] = _EPH_VERSION_;
        }

        if (strlen($this->eph_versions_compliancy['min']) == 3) {
            $this->eph_versions_compliancy['min'] .= '.0.0';
        }

        if (strlen($this->eph_versions_compliancy['max']) == 3) {
            $this->eph_versions_compliancy['max'] .= '.999.999';
        }

        $this->context = $context ? $context : Context::getContext();

        if (is_object($this->context->smarty)) {
            $this->smarty = $this->context->smarty->createData($this->context->smarty);
        }

        if ($this->name === null) {
            $this->name = $this->id;
        }

        if ($this->name != null) {

            if (static::$plugins_cache == null && !is_array(static::$plugins_cache)) {

                static::$plugins_cache = [];

                $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
                    (new DbQuery())
                        ->select('p.`id_plugin`, p.`name`, p.`active`')
                        ->from('plugin', 'p')
                );

                foreach ($result as $row) {
                    static::$plugins_cache[$row['name']] = $row;
                    static::$plugins_cache[$row['name']]['active'] = $row['active'];
                }

            }

            if (isset(static::$plugins_cache[$this->name])) {

                if (isset(static::$plugins_cache[$this->name]['id_plugin'])) {
                    $this->id = static::$plugins_cache[$this->name]['id_plugin'];
                }

                foreach (static::$plugins_cache[$this->name] as $key => $value) {

                    if (property_exists($this, $key)) {
                        $this->{$key}

                        = $value;
                    }

                }

                $this->_path = __EPH_BASE_URI__ . 'includes/plugins/' . $this->name . '/';
            }

            if (method_exists($this, 'reset')) {
                $this->has_reset = true;
            } else {
                $this->has_reset = false;
            }

            if ($this->context->controller instanceof Controller) {
                static::$plugins_cache = null;
            }

            $this->local_path = _EPH_PLUGIN_DIR_ . $this->name . '/';
        }

    }

    public function installsql($file) {

        if (!file_exists($this->local_path . 'sql/' . $file)) {
            return false;
        } else
        if (!$sql = Tools::file_get_contents($this->local_path . 'sql/' . $file)) {
            return false;
        }

        $replace = [
            'PREFIX_'        => _DB_PREFIX_,
            'ENGINE_DEFAULT' => _MYSQL_ENGINE_,
        ];
        $sql = strtr($sql, $replace);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            Db::getInstance()->execute(trim($query));
        }

        return true;
    }

    public function uninstallsql($file) {

        if (!file_exists($this->local_path . 'sql/' . $file)) {
            return false;
        } else
        if (!$sql = Tools::file_get_contents($this->local_path . 'sql/' . $file)) {
            return false;
        }

        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query) {
            Db::getInstance()->execute(trim($query));
        }
        
        return true;

    }
    
   

    public static function getBatchMode() {

        return static::$_batch_mode;

    }

    public static function setBatchMode($value) {

        // @codingStandardsIgnoreStart
        static::$_batch_mode = (bool) $value;
        // @codingStandardsIgnoreEnd
    }

    public static function processDeferedFuncCall() {

        static::setBatchMode(false);
        // @codingStandardsIgnoreStart

        foreach (static::$_defered_func_call as $funcCall) {
            call_user_func_array($funcCall[0], $funcCall[1]);
        }

        static::$_defered_func_call = [];
        // @codingStandardsIgnoreEnd
    }

    public static function processDeferedClearCache() {

        static::setBatchMode(false);

        // @codingStandardsIgnoreStart

        foreach (static::$_defered_clearCache as $clearCacheArray) {
            static::_deferedClearCache($clearCacheArray[0], $clearCacheArray[1], $clearCacheArray[2]);
        }

        static::$_defered_clearCache = [];
        // @codingStandardsIgnoreEnd
    }

    public static function _deferedClearCache($templatePath, $cacheId, $compileId) {

        Tools::enableCache();
        $numberOfTemplateCleared = Tools::clearCache(Context::getContext()->smarty, $templatePath, $cacheId, $compileId);
        Tools::restoreCacheSettings();

        return $numberOfTemplateCleared;
    }

    public static function updateTranslationsAfterInstall($update = true) {

        // @codingStandardsIgnoreStart
        Plugin::$update_translations_after_install = (bool) $update;
        // @codingStandardsIgnoreEnd
    }

    public static function getInstanceByName($pluginName, $full = true) {

        if (!Validate::isPluginName($pluginName)) {

            if (_EPH_MODE_DEV_) {
                die(Tools::displayError(Tools::safeOutput($pluginName) . ' is not a valid plugin name.'));
            }

            return false;
        }

        if (!isset(static::$_INSTANCE[$pluginName])) {

            if (!Tools::file_exists_no_cache(_EPH_PLUGIN_DIR_ . $pluginName . '/' . $pluginName . '.php')) {
                return false;
            }

            return Plugin::coreLoadPlugin($pluginName, $full);
        }

        return static::$_INSTANCE[$pluginName];
    }

    protected static function coreLoadPlugin($pluginName, $full) {

        if (Plugin::$_log_plugins_perfs === null) {
            $modulo = _EPH_DEBUG_PROFILING_ ? 1 : Configuration::get('EPH_log_plugins_perfs_MODULO');
            Plugin::$_log_plugins_perfs = ($modulo && mt_rand(0, $modulo - 1) == 0);

            if (Plugin::$_log_plugins_perfs) {
                Plugin::$_log_plugins_perfs_session = mt_rand();
            }

        }

        if (Plugin::$_log_plugins_perfs) {
            $timeStart = microtime(true);
            $memoryStart = memory_get_usage(true);
        }

        include_once _EPH_PLUGIN_DIR_ . $pluginName . '/' . $pluginName . '.php';

        $r = false;

        if (Tools::file_exists_no_cache(_EPH_OVERRIDE_DIR_ . 'plugins/' . $pluginName . '/' . $pluginName . '.php')) {
            include_once _EPH_OVERRIDE_DIR_ . 'plugins/' . $pluginName . '/' . $pluginName . '.php';
            $override = $pluginName . 'Override';

            if (class_exists($override, false)) {
                $r = static::$_INSTANCE[$pluginName] = Adapter_ServiceLocator::get($override);
            }

        }

        if (!$r && class_exists($pluginName, false)) {
            $r = static::$_INSTANCE[$pluginName] = Adapter_ServiceLocator::get($pluginName);
        }

        // @codingStandardsIgnoreStart

        if (Plugin::$_log_plugins_perfs) {
            // @codingStandardsIgnoreEnd
            $timeEnd = microtime(true);
            $memoryEnd = memory_get_usage(true);

            Db::getInstance()->insert(
                'plugins_perfs',
                [
                    'session'      => (int) Plugin::$_log_plugins_perfs_session,
                    'plugin'       => pSQL($pluginName),
                    'method'       => '__construct',
                    'time_start'   => pSQL($timeStart),
                    'time_end'     => pSQL($timeEnd),
                    'memory_start' => (int) $memoryStart,
                    'memory_end'   => (int) $memoryEnd,
                ]
            );
        }

        return $r;
    }

    public static function enableByName($name) {

        if (!is_array($name)) {
            $name = [$name];
        }

        $res = true;

        foreach ($name as $n) {

            if (Validate::isPluginName($n)) {
                $res &= Plugin::getInstanceByName($n)->enable();
            }

        }

        return $res;
    }

    public static function disableByName($name) {

        if (!is_array($name)) {
            $name = [$name];
        }

        $res = true;

        foreach ($name as $n) {

            if (Validate::isPluginName($n)) {
                Plugin::getInstanceByName($n)->disable();
            }

        }

        return $res;
    }

    public static function getPluginNameFromClass($currentClass) {

        if (!isset(static::$classInPlugin[$currentClass]) && class_exists($currentClass)) {
            global $_PLUGINS;
            $_PLUGIN = [];
            $reflectionClass = new ReflectionClass($currentClass);
            $filePath = realpath($reflectionClass->getFileName());
            $realpathPluginDir = realpath(_EPH_PLUGIN_DIR_);

            if (substr(realpath($filePath), 0, strlen($realpathPluginDir)) == $realpathPluginDir) {

                if (basename(dirname(dirname($filePath))) == 'controllers') {
                    static::$classInPlugin[$currentClass] = basename(dirname(dirname(dirname($filePath))));
                } else {

                    static::$classInPlugin[$currentClass] = substr(dirname($filePath), strlen($realpathPluginDir) + 1);
                }

                $file = _EPH_PLUGIN_DIR_ . static::$classInPlugin[$currentClass] . '/' . Context::getContext()->language->iso_code . '.php';

                if (file_exists($file) && include_once ($file)) {
                    $_PLUGINS = !empty($_PLUGINS) ? array_merge($_PLUGINS, $_PLUGIN) : $_PLUGIN;
                }

            } else {
                static::$classInPlugin[$currentClass] = false;
            }

        }

        return static::$classInPlugin[$currentClass];
    }

    public static function getInstanceById($idPlugin) {

        static $id2name = null;

        if (is_null($id2name)) {
            $id2name = [];

            if ($results = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
                (new DbQuery())
                ->select('`id_plugin`, `name`')
                ->from('plugin')
            )) {

                foreach ($results as $row) {
                    $id2name[$row['id_plugin']] = $row['name'];
                }

            }

        }

        if (isset($id2name[$idPlugin])) {
            return Plugin::getInstanceByName($id2name[$idPlugin]);
        }

        return false;
    }

    public static function getPluginName($plugin) {

        $iso = substr(Context::getContext()->language->iso_code, 0, 2);

        $configFile = _EPH_PLUGIN_DIR_ . $plugin . '/config_' . $iso . '.xml';

        if ($iso == 'en' || !file_exists($configFile)) {
            $configFile = _EPH_PLUGIN_DIR_ . $plugin . '/config.xml';

            if (!file_exists($configFile)) {
                return 'Plugin ' . ucfirst($plugin);
            }

        }

        libxml_use_internal_errors(true);
        $xmlPlugin = @simplexml_load_file($configFile);

        if (!$xmlPlugin) {
            return 'Plugin ' . ucfirst($plugin);
        }

        if (!empty(libxml_get_errors())) {
            libxml_clear_errors();

            return 'Plugin ' . ucfirst($plugin);
        }

        libxml_clear_errors();

        global $_PLUGINS;
        $file = _EPH_PLUGIN_DIR_ . $plugin . '/' . Context::getContext()->language->iso_code . '.php';

        if (file_exists($file) && include_once ($file)) {

            if (isset($_PLUGIN) && is_array($_PLUGIN)) {
                $_PLUGINS = !empty($_PLUGINS) ? array_merge($_PLUGINS, $_PLUGIN) : $_PLUGIN;
            }

        }

        return Translate::getPluginTranslation((string) $xmlPlugin->name, Plugin::configXmlStringFormat($xmlPlugin->displayName), (string) $xmlPlugin->name);
    }

    public static function configXmlStringFormat($string) {

        return Tools::htmlentitiesDecodeUTF8($string);
    }

    public static function getPluginsOnDisk($useConfig = false, $loggedOnAddons = false, $idEmployee = false) {

        global $_PLUGINS;

        $pluginList = [];
        $pluginNameList = [];
        $pluginsNameToCursor = [];
        $errors = [];

        $pluginsDir = Plugin::getPluginsDirOnDisk();

        $pluginsInstalled = [];
        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('m.id_plugin, m.`name`, m.`version`, m.active, m.enable_device, mp.`interest`')
                ->from('plugin', 'm')
                ->leftJoin('plugin_preference', 'mp', 'mp.`plugin` = m.`name` AND mp.`id_employee` = ' . (int) $idEmployee)
        );

        foreach ($result as $row) {
            $pluginsInstalled[$row['name']] = $row;
        }

        foreach ($pluginsDir as $plugin) {

            if (Plugin::useTooMuchMemory()) {
                $errors[] = Tools::displayError('All plugins cannot be loaded due to memory limit restrictions, please increase your memory_limit value on your server configuration');
                break;
            }


            
                if (!class_exists($plugin, false)) {

                    $filePath = _EPH_PLUGIN_DIR_ . $plugin . '/' . $plugin . '.php';
                    $file = trim(file_get_contents(_EPH_PLUGIN_DIR_ . $plugin . '/' . $plugin . '.php'));

                    if (substr($file, 0, 5) == '<?php') {
                        $file = substr($file, 5);
                    }

                    if (substr($file, -2) == '?>') {
                        $file = substr($file, 0, -2);
                    }

                    $file = preg_replace('/\n[\s\t]*?use\s.*?;/', '', $file);

                    if (eval('if (false){   ' . $file . "\n" . ' }') !== false) {
                        require_once _EPH_PLUGIN_DIR_ . $plugin . '/' . $plugin . '.php';
                    } else {
                        $errors[] = sprintf(Tools::displayError('%1$s (parse error in %2$s)'), $plugin, substr($filePath, strlen(_EPH_ROOT_DIR_)));
                    }

                }

                if (class_exists($plugin, false)) {

                    $tmpPlugin = Adapter_ServiceLocator::get($plugin);

                    $item = [
                        'id'                     => is_null($tmpPlugin->id) ? 0 : $tmpPlugin->id,
                        'warning'                => $tmpPlugin->warning,
                        'name'                   => $tmpPlugin->name,
                        'version'                => $tmpPlugin->version,
                        'tab'                    => $tmpPlugin->tab,
                        'displayName'            => $tmpPlugin->displayName,
                        'description'            => !is_null($tmpPlugin->description) ? stripslashes($tmpPlugin->description) : '',
                        'author'                 => $tmpPlugin->author,
                        'author_uri'             => (isset($tmpPlugin->author_uri) && $tmpPlugin->author_uri) ? $tmpPlugin->author_uri : false,
                        'limited_countries'      => $tmpPlugin->limited_countries,
                        'parent_class'           => get_parent_class($plugin),
                        'is_configurable'        => $tmpPlugin->is_configurable = method_exists($tmpPlugin, 'getContent') ? 1 : 0,
                        'config_controller'      => $tmpPlugin->config_controller,
                        'active'                 => $tmpPlugin->active,
                        'trusted'                => true,
                        'currencies'             => isset($tmpPlugin->currencies) ? $tmpPlugin->currencies : null,
                        'currencies_mode'        => isset($tmpPlugin->currencies_mode) ? $tmpPlugin->currencies_mode : null,
                        'confirmUninstall'       => isset($tmpPlugin->confirmUninstall) ? html_entity_decode($tmpPlugin->confirmUninstall) : null,
                        'description_full'       => isset($tmpPlugin->description_full) ? stripslashes($tmpPlugin->description_full) : null,
                        'additional_description' => isset($tmpPlugin->additional_description) ? stripslashes($tmpPlugin->additional_description) : null,
                        'compatibility'          => isset($tmpPlugin->compatibility) ? (array) $tmpPlugin->compatibility : null,
                        'nb_rates'               => isset($tmpPlugin->nb_rates) ? (array) $tmpPlugin->nb_rates : null,
                        'avg_rate'               => isset($tmpPlugin->avg_rate) ? (array) $tmpPlugin->avg_rate : null,
                        'badges'                 => isset($tmpPlugin->badges) ? (array) $tmpPlugin->badges : null,
                        'url'                    => isset($tmpPlugin->url) ? $tmpPlugin->url : null,
                        'onclick_option'         => method_exists($plugin, 'onclickOption') ? true : false,
                    ];

                    if ($item['onclick_option']) {
                        $href = Context::getContext()->link->getAdminLink('Plugin', true) . '&plugin_name=' . $tmpPlugin->name . '&tab_plugin=' . $tmpPlugin->tab;
                        $item['onclick_option_content'] = [];
                        $optionTab = ['desactive', 'reset', 'configure', 'delete'];

                        foreach ($optionTab as $opt) {
                            $item['onclick_option_content'][$opt] = $tmpPlugin->onclickOption($opt, $href);
                        }

                    }

                    $item = (object) $item;
                    $pluginList[] = $item;
                    $pluginsNameToCursor[mb_strtolower($item->name)] = $item;

                   

                    unset($tmpPlugin);
                } else {
                    $errors[] = sprintf(Tools::displayError('%1$s (class missing in %2$s)'), $plugin, substr($filePath, strlen(_EPH_ROOT_DIR_)));
                }

           

        }

        if (!empty($pluginNameList)) {
            $list = [Context::getContext()->company->id];
            $results = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
                (new DbQuery())
                    ->select('m.`id_plugin`, m.`name`, m.active, COUNT(m.`id_plugin`) AS `total`')
                    ->from('plugin', 'm')
                    ->where('LOWER(m.`name`) IN (' . mb_strtolower(implode(',', $pluginNameList)) . ')')
            );

            foreach ($results as $result) {

                if (isset($pluginsNameToCursor[mb_strtolower($result['name'])])) {
                    $pluginCursor = $pluginsNameToCursor[mb_strtolower($result['name'])];
                    $pluginCursor->id = (int) $result['id_plugin'];
                    $pluginCursor->active = $result['active'];
                }

            }

        }

        $languageCode = str_replace('_', '-', mb_strtolower(Context::getContext()->language->language_code));

        foreach ($pluginList as $key => &$plugin) {
            require_once _EPH_PLUGIN_DIR_ . $plugin->name . '/' . $plugin->name . '.php';
            $tmpPlugin = Adapter_ServiceLocator::get($plugin->name);

            if (isset($pluginsInstalled[$plugin->name])) {

                if (method_exists($tmpPlugin, 'reset')) {
                    $plugin->has_reset = true;
                } else {
                    $plugin->has_reset = false;
                }

                $plugin->removable = $tmpPlugin->removable;
                $plugin->config_controller = $tmpPlugin->config_controller;
                $plugin->id = $pluginsInstalled[$plugin->name]['id_plugin'];
                $plugin->installed = true;
                $plugin->database_version = $pluginsInstalled[$plugin->name]['version'];
                $plugin->interest = $pluginsInstalled[$plugin->name]['interest'];
                $plugin->enable_device = $pluginsInstalled[$plugin->name]['enable_device'];
                $plugin->active = $pluginsInstalled[$plugin->name]['active'];
            } else {
                $plugin->removable = true;
                $plugin->installed = false;
                $plugin->database_version = 0;
                $plugin->interest = 0;
            }

        }

        if ($errors) {

            if (!isset(Context::getContext()->controller) && !Context::getContext()->controller->controller_name) {
                echo '<div class="alert error"><h3>' . Tools::displayError('The following plugin(s) could not be loaded') . ':</h3><ol>';

                foreach ($errors as $error) {
                    echo '<li>' . $error . '</li>';
                }

                echo '</ol></div>';
            } else {

                foreach ($errors as $error) {
                    Context::getContext()->controller->errors[] = $error;
                }

            }

        }

        return $pluginList;
    }

    public static function getPluginsDirOnDisk() {

        $pluginList = [];
        $plugins = scandir(_EPH_PLUGIN_DIR_);

        foreach ($plugins as $name) {

            if (is_file(_EPH_PLUGIN_DIR_ . $name)) {
                continue;
            } else
            if (is_dir(_EPH_PLUGIN_DIR_ . $name . DIRECTORY_SEPARATOR) && file_exists(_EPH_PLUGIN_DIR_ . $name . '/' . $name . '.php')) {

                if (!Validate::isPluginName($name)) {
                    throw new PhenyxException(sprintf('Plugin %s is not a valid plugin name', $name));
                }

                $pluginList[] = $name;
            }

        }

        return $pluginList;
    }

    protected static function useTooMuchMemory() {

        $memoryLimit = Tools::getMemoryLimit();

        if (function_exists('memory_get_usage') && $memoryLimit != '-1') {
            $currentMemory = memory_get_usage(true);
            $memoryThreshold = (int) max($memoryLimit * 0.15, Tools::isX86_64arch() ? 4194304 : 2097152);
            $memoryLeft = $memoryLimit - $currentMemory;

            if ($memoryLeft <= $memoryThreshold) {
                return true;
            }

        }

        return false;
    }

    final public static function isPluginTrusted($pluginName) {

        Tools::displayAsDeprecated();

        return true;
    }

    public static function getNativePluginList() {

        require _EPH_CONFIG_DIR_ . 'default_plugins.php';

        return $_EPH_DEFAULT_PLUGINS_;
    }

    public static function getNonNativePluginList() {

        $query = (new DbQuery())->select('*')->from('plugin');
        $nativePlugins = static::getNativePluginList();

        if ($nativePlugins) {
            $query->where("`name` NOT IN ('" . implode("', '", array_map('pSQL', $nativePlugins)) . "')");
        }

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS($query);
    }

    public static function getPluginsInstalled($position = 0) {

        $sql = (new DbQuery())
            ->select('m.*')
            ->from('plugin', 'm');

        if ($position) {
            $sql->leftJoin('hook_plugin', 'hm', 'm.`id_plugin` = hm.`id_plugin`');
            $sql->leftJoin('hook', 'h', 'h.`id_hook` = hm.`id_hook`');
            $sql->where('k.`position` = 1');
            $sql->groupBy('m.`id_plugin`');
        }

        return Db::getInstance()->executeS($sql);
    }

    final public static function generateTrustedXml() {

        Tools::displayAsDeprecated();

        return true;
    }

    final public static function checkPluginFromAddonsApi($pluginName) {

        return false;
    }

    public static function preCall($pluginName) {

        return true;
    }

    public static function getPaymentPlugins() {

        $context = Context::getContext();

        if (isset($context->cart)) {
            $billing = new Address((int) $context->cart->id_address_invoice);
        }

        $useGroups = Group::isFeatureActive();

        $frontend = true;
        $groups = [];

        if (isset($context->employee)) {
            $frontend = false;
        } else
        if (isset($context->user) && $useGroups) {
            $groups = $context->user->getGroups();

            if (!count($groups)) {
                $groups = [Configuration::get('EPH_UNIDENTIFIED_GROUP')];
            }

        }

        $frontend = false;
        $groups = $context->user->getGroups();

        if (!count($groups)) {
            $groups = [Configuration::get('EPH_UNIDENTIFIED_GROUP')];
        }

        $hookPayment = 'Payment';

        if (Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
            ->select('`id_hook`')
            ->from('hook')
            ->where('`name` = \'displayPayment\'')
        )) {
            $hookPayment = 'displayPayment';
        }

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('DISTINCT m.`id_plugin`, h.`id_hook`, m.`name`, hm.`position`')
                ->from('plugin', 'm')
                ->join($frontend ? 'LEFT JOIN `' . _DB_PREFIX_ . 'plugin_country` mc ON (m.`id_plugin` = mc.`id_plugin`)' : '')
                ->join($frontend && $useGroups ? 'INNER JOIN `' . _DB_PREFIX_ . 'plugin_group` mg ON (m.`id_plugin` = mg.`id_plugin`' . ')' : '')
                ->join($frontend && isset($context->user) && $useGroups ? 'INNER JOIN `' . _DB_PREFIX_ . 'customer_group` cg on (cg.`id_group` = mg.`id_group`AND cg.`id_customer` = ' . (int) $context->user->id . ')' : '')
                ->leftJoin('hook_plugin', 'hm', 'hm.`id_plugin` = m.`id_plugin`')
                ->leftJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`')
                ->where('h.`name` = \'' . pSQL($hookPayment) . '\'')
                ->where((isset($billing) && $frontend ? 'mc.`id_country` = ' . (int) $billing->id_country : ''))
                ->where((count($groups) && $frontend && $useGroups) ? 'mg.`id_group` IN (' . implode(', ', $groups) . ')' : '')
                ->groupBy('hm.`id_hook`, hm.`id_plugin`')
                ->orderBy('hm.`position`, m.`name` DESC')
        );
    }

    public static function findTranslation($name, $string, $source) {

        return Translate::getPluginTranslation($name, $string, $source);
    }

    public static function isEnabled($pluginName) {

        if (!Cache::isStored('Plugin::isEnabled' . $pluginName)) {
            $active = false;
            $idPlugin = Plugin::getPluginIdByName($pluginName);

            if (Db::getInstance()->getValue(
                (new DbQuery())
                ->select('`id_plugin`')
                ->from('plugin')
                ->where('`id_plugin` = ' . (int) $idPlugin)
                ->where('`active` = 1')
            )) {
                $active = true;
            }

            Cache::store('Plugin::isEnabled' . $pluginName, (bool) $active);

            return (bool) $active;
        }

        return Cache::retrieve('Plugin::isEnabled' . $pluginName);
    }

    public static function getAuthorizedPlugins($groupId) {

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('m.`id_plugin`, m.`name`')
                ->from('plugin_group', 'mg')
                ->leftJoin('plugin', 'm', 'm.`id_plugin` = mg.`id_plugin`')
                ->where('mg.`id_group` = ' . (int) $groupId)
        );
    }

    public function install() {

        Hook::exec('actionPluginInstallBefore', ['object' => $this]);

        if (!Validate::isPluginName($this->name)) {
            $return = [
                'success' => false,
                'message' => Tools::displayError('Unable to install the plugin (Plugin name is not valid).'),
            ];
            die(Tools::jsonEncode($return));
        }

        if (!defined('EPH_INSTALLATION_IN_PROGRESS') || !EPH_INSTALLATION_IN_PROGRESS) {

            if (!$this->checkCompliancy()) {
                $return = [
                    'success' => false,
                    'message' => Tools::displayError('The version of your plugin is not compliant with your Ephenyx version.'),
                ];
                die(Tools::jsonEncode($return));
            }

            if (count($this->dependencies) > 0) {

                foreach ($this->dependencies as $dependency) {

                    if (!Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
                        (new DbQuery())
                        ->select('`id_plugin`')
                        ->from('plugin')
                        ->where('LOWER(`name`) = \'' . pSQL(mb_strtolower($dependency)) . '\'')
                    )) {
                        $error = Tools::displayError('Before installing this plugin, you have to install this/these plugin(s) first:') . '<br />';

                        foreach ($this->dependencies as $d) {
                            $error .= '- ' . $d . '<br />';
                        }

                        $this->_errors[] = $error;

                        $return = [
                            'success' => false,
                            'message' => $error,
                        ];
                        die(Tools::jsonEncode($return));
                    }

                }

            }

            $result = Plugin::isInstalled($this->name);

            if ($result) {

                $return = [
                    'success' => false,
                    'message' => Tools::displayError('This plugin has already been installed.'),
                ];
                die(Tools::jsonEncode($return));

            }

            if (function_exists('opcache_invalidate') && file_exists(_EPH_PLUGIN_DIR_ . $this->name)) {

                foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(_EPH_PLUGIN_DIR_ . $this->name)) as $file) {

                    if (substr($file->getFilename(), -4) !== '.php' || $file->isLink()) {
                        continue;
                    }

                    opcache_invalidate($file->getPathname());
                }

            }

            try {
                $this->installOverrides();
            } catch (Exception $e) {
                $this->_errors[] = sprintf(Tools::displayError('Unable to install override: %s'), $e->getMessage());
                $this->uninstallOverrides();

                return false;
            }
            
            try {
                $this->installOverrplaces();
            } catch (Exception $e) {
                $this->_errors[] = sprintf(Tools::displayError('Unable to install Overplacement: %s'), $e->getMessage());
                $this->uninstallOverrplaces();

                return false;
            }

        }

        if (!$this->installControllers()) {
            return false;
        }

        $result = Db::getInstance()->insert($this->table, ['name' => $this->name, 'active' => 1, 'version' => $this->version]);

        if (!$result) {
            $this->_errors[] = Tools::displayError('Technical error: thirty bees could not install this plugin.');

            return false;
        }

        $this->id = Db::getInstance()->Insert_ID();

        $this->enable();

        Db::getInstance()->execute(
            '
            INSERT INTO `' . _DB_PREFIX_ . 'plugin_access` (`id_profile`, `id_plugin`, `view`, `configure`, `uninstall`) (
                SELECT id_profile, ' . (int) $this->id . ', 1, 1, 1
                FROM ' . _DB_PREFIX_ . 'employee_access a
                WHERE id_employee_menu = (
                    SELECT `id_employee_menu` FROM ' . _DB_PREFIX_ . 'employee_menu
                    WHERE class_name = \'AdminPlugins\' LIMIT 1)
                AND a.`view` = 1)'
        );

        Db::getInstance()->execute(
            '
            INSERT INTO `' . _DB_PREFIX_ . 'plugin_access` (`id_profile`, `id_plugin`, `view`, `configure`, `uninstall`) (
                SELECT id_profile, ' . (int) $this->id . ', 1, 0, 0
                FROM ' . _DB_PREFIX_ . 'employee_access a
                WHERE id_employee_menu = (
                    SELECT `id_employee_menu` FROM ' . _DB_PREFIX_ . 'employee_menu
                    WHERE class_name = \'AdminPlugins\' LIMIT 1)
                AND a.`view` = 0)'
        );

        Group::addRestrictionsForPlugin($this->id);
        Hook::exec('actionPluginInstallAfter', ['object' => $this]);

        if (!defined('EPH_INSTALLATION_IN_PROGRESS') || !EPH_INSTALLATION_IN_PROGRESS) {

            if (Plugin::$update_translations_after_install) {
                $this->updatePluginTranslations();
            }

        }

        return true;
    }

    public function checkCompliancy() {

        if (version_compare(_EPH_VERSION_, $this->eph_versions_compliancy['min'], '<')) {
            return false;
        }

        if (version_compare('1.6.1.20', $this->eph_versions_compliancy['max'], '>')) {
            return false;
        }

        return true;
    }

    public static function isInstalled($pluginName) {

        return (bool) Plugin::getPluginIdByName($pluginName);
    }

    public static function isActive($pluginName) {

        return (bool) Plugin::getActivePluginIdByName($pluginName);
    }

    public static function getPluginIdByName($name) {
       
        $cacheId = 'Plugin::getPluginIdByName_' . pSQL($name);

        if (!Cache::isStored($cacheId)) {
            
            $result = (int) Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
                (new DbQuery())
                    ->select('`id_plugin`')
                    ->from('plugin')
                    ->where('`name` = \'' . pSQL($name) . '\'')
            );
            Cache::store($cacheId, $result);
            return $result;
        }
        
        return Cache::retrieve($cacheId);
    }

    public static function getActivePluginIdByName($name) {

        $cacheId = 'Plugin::getActivePluginIdByName_' . pSQL($name);

        if (!Cache::isStored($cacheId)) {
            $result = (int) Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(

                (new DbQuery())
                    ->select('`id_plugin`')
                    ->from('plugin')
                    ->where('`name` = \'' . pSQL($name) . '\'')
                    ->where('`active` = 1')
            );
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    public function installOverrides() {

        if (!is_dir($this->getLocalPath() . 'override')) {
            return true;
        }

        $result = true;

        foreach (Tools::scandir($this->getLocalPath() . 'override', 'php', '', true) as $file) {
            $class = basename($file, '.php');

            if (PhenyxAutoload::getInstance()->getClassPath($class . 'Core') || Plugin::getPluginIdByName($class)) {
                $result &= $this->addOverride($class);
            }

        }

        return $result;
    }
    
    public function installOverrplaces() {

        if (!is_dir($this->getLocalPath() . 'overplace')) {
            return true;
        }

        $result = true;

        foreach (Tools::scandir($this->getLocalPath() . 'overplace', 'php', '', true) as $file) {
            $class = basename($file, '.php');

            if (PhenyxAutoload::getInstance()->getClassPath($class . 'Core') || Plugin::getPluginIdByName($class)) {
                $result &= $this->addOverPlace($class);
            }

        }

        return $result;
    }
    
    public function uninstallOverrplaces() {

        if (!is_dir($this->getLocalPath() . 'overplace')) {
            return true;
        }
        
        $result = true;

        foreach (Tools::scandir($this->getLocalPath() . 'overplace'. DIRECTORY_SEPARATOR . 'save', 'php', '', true) as $file) {
            $class = basename($file, '.php');
            if($class == 'index') {
                continue;
            }
            
            $origPath = $path = PhenyxAutoload::getInstance()->getClassPath($class . 'Core');
           
            $savePlace = $this->getLocalPath() . 'overplace' . DIRECTORY_SEPARATOR . 'save'. DIRECTORY_SEPARATOR .str_replace('includes/', '', $path);
           
            $result &= copy($savePlace, _EPH_ROOT_DIR_.DIRECTORY_SEPARATOR.$origPath);

        }

        return $result;
    }

    public function getLocalPath() {

        return $this->local_path;
    }
    
     public function addOverPlace($classname) {
         
          

        $origPath = $path = PhenyxAutoload::getInstance()->getClassPath($classname . 'Core');
       
        if (!$path) {
            return false;
        } 
        
        $savePlace = $this->getLocalPath() . 'overplace' . DIRECTORY_SEPARATOR . 'save'. DIRECTORY_SEPARATOR .str_replace('includes/', '', $path);
         
        $pathOverride = $this->getLocalPath() . 'overplace' . DIRECTORY_SEPARATOR . str_replace('includes/', '', $path);
         
        

        if (!file_exists($pathOverride)) {
            return false;
        } else {
           
            if(!copy(_EPH_ROOT_DIR_.DIRECTORY_SEPARATOR.$origPath, $savePlace)) {
                return false;
            }
            
             if(!copy($pathOverride, _EPH_ROOT_DIR_.DIRECTORY_SEPARATOR.$origPath)) {
                return false;
            };
        }

       

        Tools::generateIndex();
        

        return true;
    }

    public function addOverride($classname) {

        $origPath = $path = PhenyxAutoload::getInstance()->getClassPath($classname . 'Core');

        if (!$path) {
            $path = 'includes/plugins' . DIRECTORY_SEPARATOR . $classname . DIRECTORY_SEPARATOR . $classname . '.php';
        }

        $pathOverride = $this->getLocalPath() . 'override' . DIRECTORY_SEPARATOR . $path;

        if (!file_exists($pathOverride)) {
            return false;
        } else {
            file_put_contents($pathOverride, preg_replace('#(\r\n|\r)#ism', "\n", file_get_contents($pathOverride)));
        }

        $patternEscapeCom = '#(^\s*?\/\/.*?\n|\/\*(?!\n\s+\* plugin:.*?\* date:.*?\* version:.*?\*\/).*?\*\/)#ism';

        if ($file = PhenyxAutoload::getInstance()->getClassPath($classname)) {

            $overridePath = _EPH_ROOT_DIR_ . '/' . $file;

            if ((!file_exists($overridePath) && !is_writable(dirname($overridePath))) || (file_exists($overridePath) && !is_writable($overridePath))) {
                throw new Exception(sprintf(Tools::displayError('file (%s) not writable'), $overridePath));
            }

            do {
                $uniq = uniqid();
            } while (class_exists($classname . 'OverrideOriginal_remove', false));

            $overrideFile = file($overridePath);

            if (empty($overrideFile)) {

                $overrideFile = [
                    "<?php\n",
                    "class {$classname} extends {$classname}Core\n",
                    "{\n",
                    "}\n",
                ];
            }

            $overrideFile = array_diff($overrideFile, ["\n"]);
            eval(preg_replace(['#^\s*<\?(?:php)?#', '#class\s+' . $classname . '\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i'], [' ', 'class ' . $classname . 'OverrideOriginal' . $uniq], implode('', $overrideFile)));
            $overrideClass = new ReflectionClass($classname . 'OverrideOriginal' . $uniq);

            $pluginFile = file($pathOverride);
            $pluginFile = array_diff($pluginFile, ["\n"]);
            eval(preg_replace(['#^\s*<\?(?:php)?#', '#class\s+' . $classname . '(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'], [' ', 'class ' . $classname . 'Override' . $uniq], implode('', $pluginFile)));
            $pluginClass = new ReflectionClass($classname . 'Override' . $uniq);

            foreach ($pluginClass->getMethods() as $method) {

                if ($overrideClass->hasMethod($method->getName())) {
                    $methodOverride = $overrideClass->getMethod($method->getName());

                    if (preg_match('/plugin: (.*)/ism', $overrideFile[$methodOverride->getStartLine() - 5], $name) && preg_match('/date: (.*)/ism', $overrideFile[$methodOverride->getStartLine() - 4], $date) && preg_match('/version: ([0-9.]+)/ism', $overrideFile[$methodOverride->getStartLine() - 3], $version)) {

                        if ($name[1] !== $this->name || $version[1] !== $this->version) {
                            throw new Exception(sprintf(Tools::displayError('The method %1$s in the class %2$s is already overridden by the plugin %3$s version %4$s at %5$s.'), $method->getName(), $classname, $name[1], $version[1], $date[1]));
                        }

                        continue;
                    }

                    throw new Exception(sprintf(Tools::displayError('The method %1$s in the class %2$s is already overridden.'), $method->getName(), $classname));
                }

                $pluginFile = preg_replace('/((:?public|private|protected)\s+(static\s+)?function\s+(?:\b' . $method->getName() . '\b))/ism', "/*\n    * plugin: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1", $pluginFile);

                if ($pluginFile === null) {
                    throw new Exception(sprintf(Tools::displayError('Failed to override method %1$s in class %2$s.'), $method->getName(), $classname));
                }

            }

            foreach ($pluginClass->getProperties() as $property) {

                if ($overrideClass->hasProperty($property->getName())) {
                    throw new Exception(sprintf(Tools::displayError('The property %1$s in the class %2$s is already defined.'), $property->getName(), $classname));
                }

                $pluginFile = preg_replace('/((?:public|private|protected)\s)\s*(static\s)?\s*(\$\b' . $property->getName() . '\b)/ism', "/*\n    * plugin: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1$2$3", $pluginFile);

                if ($pluginFile === null) {
                    throw new Exception(sprintf(Tools::displayError('Failed to override property %1$s in class %2$s.'), $property->getName(), $classname));
                }

            }

            foreach ($pluginClass->getConstants() as $constant => $value) {

                if ($overrideClass->hasConstant($constant)) {
                    throw new Exception(sprintf(Tools::displayError('The constant %1$s in the class %2$s is already defined.'), $constant, $classname));
                }

                $pluginFile = preg_replace('/(const\s)\s*(\b' . $constant . '\b)/ism', "/*\n    * plugin: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1$2", $pluginFile);

                if ($pluginFile === null) {
                    throw new Exception(sprintf(Tools::displayError('Failed to override constant %1$s in class %2$s.'), $constant, $classname));
                }

            }

            $copyFrom = array_slice($pluginFile, $pluginClass->getStartLine() + 1, $pluginClass->getEndLine() - $pluginClass->getStartLine() - 2);
            array_splice($overrideFile, $overrideClass->getEndLine() - 1, 0, $copyFrom);
            $code = implode('', $overrideFile);

            file_put_contents($overridePath, preg_replace($patternEscapeCom, '', $code));

        } else {

            $overrideSrc = $pathOverride;

            $overrideDest = _EPH_ROOT_DIR_ . DIRECTORY_SEPARATOR . 'override' . DIRECTORY_SEPARATOR . $path;
            $dirName = dirname($overrideDest);

            if (!$origPath && !is_dir($dirName)) {
                $definedUmask = defined('_EPH_UMASK_') ? _EPH_UMASK_ : 0000;
                $oldumask = umask($definedUmask);
                @mkdir($dirName, 0777);
                umask($oldumask);
            }

            if (!is_writable($dirName)) {
                throw new Exception(sprintf(Tools::displayError('directory (%s) not writable'), $dirName));
            }

            $pluginFile = file($overrideSrc);
            $pluginFile = array_diff($pluginFile, ["\n"]);

            if ($origPath) {

                do {
                    $uniq = uniqid();
                } while (class_exists($classname . 'OverrideOriginal_remove', false));

                eval(preg_replace(['#^\s*<\?(?:php)?#', '#class\s+' . $classname . '(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'], [' ', 'class ' . $classname . 'Override' . $uniq], implode('', $pluginFile)));
                $pluginClass = new ReflectionClass($classname . 'Override' . $uniq);

                foreach ($pluginClass->getMethods() as $method) {
                    $pluginFile = preg_replace('/((:?public|private|protected)\s+(static\s+)?function\s+(?:\b' . $method->getName() . '\b))/ism', "/*\n    * plugin: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1", $pluginFile);

                    if ($pluginFile === null) {
                        throw new Exception(sprintf(Tools::displayError('Failed to override method %1$s in class %2$s.'), $method->getName(), $classname));
                    }

                }

                foreach ($pluginClass->getProperties() as $property) {
                    $pluginFile = preg_replace('/((?:public|private|protected)\s)\s*(static\s)?\s*(\$\b' . $property->getName() . '\b)/ism', "/*\n    * plugin: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1$2$3", $pluginFile);

                    if ($pluginFile === null) {
                        throw new Exception(sprintf(Tools::displayError('Failed to override property %1$s in class %2$s.'), $property->getName(), $classname));
                    }

                }

                foreach ($pluginClass->getConstants() as $constant => $value) {
                    $pluginFile = preg_replace('/(const\s)\s*(\b' . $constant . '\b)/ism', "/*\n    * plugin: " . $this->name . "\n    * date: " . date('Y-m-d H:i:s') . "\n    * version: " . $this->version . "\n    */\n    $1$2", $pluginFile);

                    if ($pluginFile === null) {
                        throw new Exception(sprintf(Tools::displayError('Failed to override constant %1$s in class %2$s.'), $constant, $classname));
                    }

                }

            }

            file_put_contents($overrideDest, preg_replace($patternEscapeCom, '', $pluginFile));

            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($overrideDest);
            }

            Tools::generateIndex();
        }

        return true;
    }

    public function uninstallOverrides() {

        if (!is_dir($this->getLocalPath() . 'override')) {
            return true;
        }

        $result = true;

        foreach (Tools::scandir($this->getLocalPath() . 'override', 'php', '', true) as $file) {
            $class = basename($file, '.php');

            if (PhenyxAutoload::getInstance()->getClassPath($class . 'Core') || Plugin::getPluginIdByName($class)) {
                $result &= $this->removeOverride($class);
            }

        }

        return $result;
    }

    public function removeOverride($classname) {

        $origPath = $path = PhenyxAutoload::getInstance()->getClassPath($classname . 'Core');

        if ($origPath && !$file = PhenyxAutoload::getInstance()->getClassPath($classname)) {
            return true;
        } else
        if (!$origPath && Plugin::getPluginIdByName($classname)) {
            $path = 'includes/plugins' . DIRECTORY_SEPARATOR . $classname . DIRECTORY_SEPARATOR . $classname . '.php';
        }

        if ($origPath) {
            $overridePath = _EPH_ROOT_DIR_ . '/' . $file;
        } else {
            $overridePath = _EPH_OVERRIDE_DIR_ . $path;
        }

        if (!is_file($overridePath) || !is_writable($overridePath)) {
            return false;
        }

        file_put_contents($overridePath, preg_replace('#(\r\n|\r)#ism', "\n", file_get_contents($overridePath)));

        if ($origPath) {

            do {
                $uniq = uniqid();
            } while (class_exists($classname . 'OverrideOriginal_remove', false));

            $overrideFile = file($overridePath);

            eval(preg_replace(['#^\s*<\?(?:php)?#', '#class\s+' . $classname . '\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i'], [' ', 'class ' . $classname . 'OverrideOriginal_remove' . $uniq], implode('', $overrideFile)));
            $overrideClass = new ReflectionClass($classname . 'OverrideOriginal_remove' . $uniq);

            $pluginFile = file($this->getLocalPath() . 'override/' . $path);
            eval(preg_replace(['#^\s*<\?(?:php)?#', '#class\s+' . $classname . '(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'], [' ', 'class ' . $classname . 'Override_remove' . $uniq], implode('', $pluginFile)));
            $pluginClass = new ReflectionClass($classname . 'Override_remove' . $uniq);

            foreach ($pluginClass->getMethods() as $method) {

                if (!$overrideClass->hasMethod($method->getName())) {
                    continue;
                }

                $method = $overrideClass->getMethod($method->getName());
                $length = $method->getEndLine() - $method->getStartLine() + 1;

                $pluginMethod = $pluginClass->getMethod($method->getName());

                $overrideFileOrig = $overrideFile;

                $origContent = preg_replace('/\s/', '', implode('', array_splice($overrideFile, $method->getStartLine() - 1, $length, array_pad([], $length, '#--remove--#'))));
                $pluginContent = preg_replace('/\s/', '', implode('', array_splice($pluginFile, $pluginMethod->getStartLine() - 1, $length, array_pad([], $length, '#--remove--#'))));

                $replace = true;

                if (preg_match('/\* plugin: (' . $this->name . ')/ism', $overrideFile[$method->getStartLine() - 5])) {
                    $overrideFile[$method->getStartLine() - 6] = $overrideFile[$method->getStartLine() - 5] = $overrideFile[$method->getStartLine() - 4] = $overrideFile[$method->getStartLine() - 3] = $overrideFile[$method->getStartLine() - 2] = '#--remove--#';
                    $replace = false;
                }

                if (md5($pluginContent) != md5($origContent) && $replace) {
                    $overrideFile = $overrideFileOrig;
                }

            }

            foreach ($pluginClass->getProperties() as $property) {

                if (!$overrideClass->hasProperty($property->getName())) {
                    continue;
                }

                foreach ($overrideFile as $lineNumber => &$lineContent) {

                    if (preg_match('/(public|private|protected)\s+(static\s+)?(\$)?' . $property->getName() . '/i', $lineContent)) {

                        if (preg_match('/\* plugin: (' . $this->name . ')/ism', $overrideFile[$lineNumber - 4])) {
                            $overrideFile[$lineNumber - 5] = $overrideFile[$lineNumber - 4] = $overrideFile[$lineNumber - 3] = $overrideFile[$lineNumber - 2] = $overrideFile[$lineNumber - 1] = '#--remove--#';
                        }

                        $lineContent = '#--remove--#';
                        break;
                    }

                }

            }

            foreach ($pluginClass->getConstants() as $constant => $value) {

                if (!$overrideClass->hasConstant($constant)) {
                    continue;
                }

                foreach ($overrideFile as $lineNumber => &$lineContent) {

                    if (preg_match('/(const)\s+(static\s+)?(\$)?' . $constant . '/i', $lineContent)) {

                        if (preg_match('/\* plugin: (' . $this->name . ')/ism', $overrideFile[$lineNumber - 4])) {
                            $overrideFile[$lineNumber - 5] = $overrideFile[$lineNumber - 4] = $overrideFile[$lineNumber - 3] = $overrideFile[$lineNumber - 2] = $overrideFile[$lineNumber - 1] = '#--remove--#';
                        }

                        $lineContent = '#--remove--#';
                        break;
                    }

                }

            }

            $count = count($overrideFile);

            for ($i = 0; $i < $count; ++$i) {

                if (preg_match('/(^\s*\/\/.*)/i', $overrideFile[$i])) {
                    $overrideFile[$i] = '#--remove--#';
                } else
                if (preg_match('/(^\s*\/\*)/i', $overrideFile[$i])) {

                    if (!preg_match('/(^\s*\* plugin:)/i', $overrideFile[$i + 1])
                        && !preg_match('/(^\s*\* date:)/i', $overrideFile[$i + 2])
                        && !preg_match('/(^\s*\* version:)/i', $overrideFile[$i + 3])
                        && !preg_match('/(^\s*\*\/)/i', $overrideFile[$i + 4])
                    ) {

                        for (; $overrideFile[$i] && !preg_match('/(.*?\*\/)/i', $overrideFile[$i]); ++$i) {
                            $overrideFile[$i] = '#--remove--#';
                        }

                        $overrideFile[$i] = '#--remove--#';
                    }

                }

            }

            $code = '';

            foreach ($overrideFile as $line) {

                if ($line == '#--remove--#') {
                    continue;
                }

                $code .= $line;
            }

            $toDelete = preg_match('/<\?(?:php)?\s+(?:abstract|interface)?\s*?class\s+' . $classname . '\s+extends\s+' . $classname . 'Core\s*?[{]\s*?[}]/ism', $code);
        }

        if (!isset($toDelete) || $toDelete) {
            unlink($overridePath);
        } else {
            file_put_contents($overridePath, $code);

            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($overridePath);
            }

        }

        Tools::generateIndex();

        return true;
    }

    protected function installControllers() {

        $themes = Theme::getThemes();
        $themeMetaValue = [];

        foreach ($this->controllers as $controller) {
            $page = 'plugin-' . $this->name . '-' . $controller;
            $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
                (new DbQuery())
                    ->select('*')
                    ->from('meta')
                    ->where('`page` = \'' . pSQL($page) . '\'')
            );

            if ((int) $result > 0) {
                continue;
            }

            $meta = new Meta();
            $meta->page = $page;
            $meta->plugin = $this->name;
            $meta->configurable = 1;
            $meta->save();

            if ((int) $meta->id > 0) {

                foreach ($themes as $theme) {

                    $themeMetaValue[] = [
                        'id_theme'     => $theme->id,
                        'id_meta'      => $meta->id,
                        'left_column'  => (int) $theme->default_left_column,
                        'right_column' => (int) $theme->default_right_column,
                    ];
                }

            } else {
                $this->_errors[] = sprintf(Tools::displayError('Unable to install controller: %s'), $controller);
            }

        }

        if (count($themeMetaValue) > 0) {
            return Db::getInstance()->insert('theme_meta', $themeMetaValue);
        }

        return true;
    }

    public function enable($forceAll = false) {

        if (!$this->id) {
            return false;
        }

        Db::getInstance(_EPH_USE_SQL_SLAVE_)->execute('UPDATE `' . _DB_PREFIX_ . 'plugin` SET active = 1 WHERE `id_plugin` = ' . $this->id);

        return true;
    }

    public function updatePluginTranslations() {

        Language::updatePluginsTranslations([$this->name]);
    }

    public function disable($forceAll = false) {

        Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'plugin` SET `active` = 0 WHERE `id_plugin` = ' . $this->id);
        $this->inActivateTab();
    }

    public function uninstall() {

        if (!Validate::isUnsignedId($this->id)) {
            $this->_errors[] = Tools::displayError('The plugin is not installed.');

            return false;
        }

        if (!$this->uninstallOverrides()) {
            return false;
        }
        
        if(!$this->uninstallOverrplaces()) {
            return false;
        }

        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('`id_hook`')
                ->from('hook_plugin')
                ->where('`id_plugin` = ' . (int) $this->id)
        );

        foreach ($result as $row) {
            $this->unregisterHook((int) $row['id_hook']);
            $this->unregisterExceptions((int) $row['id_hook']);
        }

        foreach ($this->controllers as $controller) {
            $pageName = 'plugin-' . $this->name . '-' . $controller;
            $meta = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
                (new DbQuery())
                    ->select('`id_meta`')
                    ->from('meta')
                    ->where('`page` = \'' . pSQL($pageName) . '\'')
            );

            if ((int) $meta > 0) {
                Db::getInstance()->delete('theme_meta', '`id_meta` = ' . (int) $meta);
                Db::getInstance()->delete('meta_lang', '`id_meta` = ' . (int) $meta);
                Db::getInstance()->delete('meta', '`id_meta` = ' . (int) $meta);
            }

        }
        
        $this->uninstallTab();

        $this->disable(true);

        Db::getInstance()->delete('plugin_access', '`id_plugin` = ' . (int) $this->id);

        Group::truncateRestrictionsByPlugin($this->id);

        if (Db::getInstance()->delete('plugin', '`id_plugin` = ' . (int) $this->id)) {
            Cache::clean('Plugin::getPluginIdByName_' . pSQL($this->name));

            return true;
        }

        return false;
    }
    
    public function uninstallTab() {
        
        $tabs = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
            ->select('`id_employee_menu`')
            ->from('employee_menu')
            ->where('`plugin` = \'' . pSQL($this->name) . '\'')
        );
        
        if(is_array($tabs) && count($tabs)) {
            foreach($tabs as $tab) {
                $menu = New EmployeeMenu($tab['id_employee_menu']);
                $menu->delete();
            }
        }        
    }
    
    public function inActivateTab() {
        
        $tabs = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
            ->select('`id_employee_menu`')
            ->from('employee_menu')
            ->where('`plugin` = \'' . pSQL($this->name) . '\'')
        );
        
        if(is_array($tabs) && count($tabs)) {
            foreach($tabs as $tab) {
                $menu = New EmployeeMenu($tab['id_employee_menu']);
                $menu->active = false;
                $menu->update();
            }
        }        
    }

    public function unregisterHook($hookId) {

        if (!is_numeric($hookId)) {
            $hookName = (string) $hookId;

            $hookId = Hook::getIdByName($hookName);

            if (!$hookId) {
                return false;
            }

        } else {
            $hookName = Hook::getNameById((int) $hookId);
        }

        Hook::exec('actionPluginUnRegisterHookBefore', ['object' => $this, 'hook_name' => $hookName]);

        $result = Db::getInstance()->delete(
            'hook_plugin',
            '`id_plugin` = ' . (int) $this->id . ' AND `id_hook` = ' . (int) $hookId
        );

        $this->cleanPositions($hookId);

        $hook = new Hook($hookId);
        $hook->plugins = $hook->getPlugins(true);
        $hook->available_plugins = $hook->getPossiblePluginList(true);
        $hook->update();

        Hook::exec('actionPluginUnRegisterHookAfter', ['object' => $this, 'hook_name' => $hookName]);

        return $result;
    }

    public function cleanPositions($idHook) {

        $results = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('`id_plugin`')
                ->from('hook_plugin')
                ->where('`id_hook` = ' . (int) $idHook)
                ->orderBy('`position`')
        );
        $position = 1;

        foreach ($results as $row) {

            Db::getInstance()->update(
                'hook_plugin',
                [
                    'position' => $position,
                ],
                '`id_hook` = ' . (int) $idHook . ' AND `id_plugin` = ' . $row['id_plugin']
            );
            $position++;
        }

        return true;
    }

    public function unregisterExceptions($hookId, $companyList = null) {

        return Db::getInstance()->delete(
            'hook_plugin_exceptions',
            '`id_plugin` = ' . (int) $this->id . ' AND `id_hook` = ' . (int) $hookId
        );
    }

    public function enableDevice($device) {

        Db::getInstance()->update(
            'plugin',
            [
                'enable_device' => ['type' => 'sql', 'value' => '`enable_device` + ' . (int) $device],
            ],
            '(`enable_device` &~ ' . (int) $device . ' OR `enable_device` = 0) AND `id_plugin` = ' . (int) $this->id
        );

        return true;
    }

    public function disableDevice($device) {

        Db::getInstance()->update(
            'plugin',
            [
                'enable_device' => ['type' => 'sql', 'value' => '`enable_device` - ' . (int) $device],
            ],
            'enable_device & ' . (int) $device . ' AND id_plugin=' . (int) $this->id
        );

        return true;
    }

    public function displayFlags($languages, $defaultLanguage, $ids, $id, $return = false, $useVarsInsteadOfIds = false) {

        if (count($languages) == 1) {
            return false;
        }

        $output = '
        <div class="displayed_flag">
            <img src="../img/l/' . $defaultLanguage . '.jpg" class="pointer" id="language_current_' . $id . '" onclick="toggleLanguageFlags(this);" alt="" />
        </div>
        <div id="languages_' . $id . '" class="language_flags">
            ' . $this->l('Choose language:') . '<br /><br />';

        foreach ($languages as $language) {

            if ($useVarsInsteadOfIds) {
                $output .= '<img src="../img/l/' . (int) $language['id_lang'] . '.jpg" class="pointer" alt="' . $language['name'] . '" title="' . $language['name'] . '" onclick="changeLanguage(\'' . $id . '\', ' . $ids . ', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" /> ';
            } else {
                $output .= '<img src="../img/l/' . (int) $language['id_lang'] . '.jpg" class="pointer" alt="' . $language['name'] . '" title="' . $language['name'] . '" onclick="changeLanguage(\'' . $id . '\', \'' . $ids . '\', ' . $language['id_lang'] . ', \'' . $language['iso_code'] . '\');" /> ';
            }

        }

        $output .= '</div>';

        if ($return) {
            return $output;
        }

        echo $output;
    }

    public function l($string, $specific = false) {

        if (static::$_generate_config_xml_mode) {
            return $string;
        }

        return Translate::getPluginTranslation($this, $string, ($specific) ? $specific : $this->name);
    }

    public function registerHook($hookName, $companyList = null) {

        $return = true;

        if (is_array($hookName)) {
            $hookNames = $hookName;
        } else {
            $hookNames = [$hookName];
        }

        foreach ($hookNames as $hookName) {

            if (!Validate::isHookName($hookName)) {
                throw new PhenyxException('Invalid hook name');
            }

            if (!isset($this->id) || !is_numeric($this->id)) {
                return false;
            }

            if ($alias = Hook::getRetroHookName($hookName)) {
                $hookName = $alias;
            }

            Hook::exec('actionPluginRegisterHookBefore', ['object' => $this, 'hook_name' => $hookName]);

            $idHook = Hook::getIdByName($hookName);

            if (!$idHook) {
                $newHook = new Hook();
                $newHook->name = pSQL($hookName);
                $newHook->title = pSQL($hookName);
                $newHook->live_edit = (bool) preg_match('/^display/i', $newHook->name);
                $newHook->position = 1;
                $newHook->available_plugins = [];
                $newHook->add();
                $idHook = $newHook->id;

                if (!$idHook) {
                    return false;
                }

            }

            $sql = 'SELECT hm.`id_plugin`
                    FROM `' . _DB_PREFIX_ . 'hook_plugin` hm, `' . _DB_PREFIX_ . 'hook` h
                    WHERE hm.`id_plugin` = ' . (int) $this->id . ' AND h.`id_hook` = ' . $idHook . '
                    AND h.`id_hook` = hm.`id_hook`';

            if (Db::getInstance()->getRow($sql)) {
                continue;
            }

            $sql = 'SELECT MAX(`position`) AS position
                    FROM `' . _DB_PREFIX_ . 'hook_plugin`
                    WHERE `id_hook` = ' . (int) $idHook;

            if (!$position = Db::getInstance()->getValue($sql)) {
                $position = 0;
            }

            $return &= Db::getInstance()->insert(
                'hook_plugin',
                [
                    'id_plugin' => (int) $this->id,
                    'id_hook'   => (int) $idHook,
                    'position'  => (int) ($position + 1),
                ]
            );

            $hook = new Hook($idHook);
            $hook->getPlugins(true);
            $hook->getPossiblePluginList(true);
            Hook::exec('actionPluginRegisterHookAfter', ['object' => $this, 'hook_name' => $hookName]);
        }

        return $return;
    }

    public function editExceptions($idHook, $excepts) {

        $result = true;

        foreach ($excepts as $companyId => $except) {
            $companyList = [$companyId];
            $this->unregisterExceptions($idHook, $companyList);
            $result &= $this->registerExceptions($idHook, $except, $companyList);

        }

        return $result;
    }

    public function registerExceptions($idHook, $excepts, $companyList = null) {

        foreach ($excepts as $except) {

            if (!$except) {
                continue;
            }

            $insertException = [
                'id_plugin' => (int) $this->id,
                'id_hook'   => (int) $idHook,
                'file_name' => pSQL($except),
            ];
            $result = Db::getInstance()->insert('hook_plugin_exceptions', $insertException);

            if (!$result) {
                return false;
            }

        }

        return true;
    }

    public function updatePosition($idHook, $way, $position = null) {

       
        $order = $way ? 'ASC' : 'DESC';  
        $res = Db::getInstance()->executeS(
			(new DbQuery())
			->select('`id_plugin`, `position`, `id_hook`')
			->from('hook_plugin')
            ->where('`id_hook` = '.$idHook)
			->orderBy('position ' . $order)
		);

        foreach ($res as $key => $values) {

            if ((int) $values[$this->identifier] == (int) $this->id) {
                $k = $key;
                break;
            }

        }

        if (!isset($k) || !isset($res[$k]) || !isset($res[$k + 1])) {
            return false;
        }

        $from = $res[$k];
        $to = $res[$k + 1];

        if (isset($position) && !empty($position)) {
            $to['position'] = (int) $position;
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin`
                SET `position`= position ' . ($way ? '-1' : '+1') . '
                WHERE position between ' . (int) (min([$from['position'], $to['position']])) . ' AND ' . max([$from['position'], $to['position']]) . '
                AND `id_hook` = ' . (int) $from['id_hook'];

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        $sql = 'UPDATE `' . _DB_PREFIX_ . 'hook_plugin`
                SET `position`=' . (int) $to['position'] . '
                WHERE `' . pSQL($this->identifier) . '` = ' . (int) $from[$this->identifier] . '
                AND `id_hook` = ' . (int) $to['id_hook'];

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        return true;
    }

    public function displayError($error) {

        $output = '
        <div class="bootstrap">
        <div class="plugin_error alert alert-danger" >
            <button type="button" class="close" data-dismiss="alert">&times;</button>';

        if (is_array($error)) {
            $output .= '<ul>';

            foreach ($error as $msg) {
                $output .= '<li>' . $msg . '</li>';
            }

            $output .= '</ul>';
        } else {
            $output .= $error;
        }

        $output .= '</div></div>';

        $this->error = true;

        return $output;
    }

    public function displayWarning($warning) {

        $output = '
        <div class="bootstrap">
        <div class="plugin_warning alert alert-warning" >
            <button type="button" class="close" data-dismiss="alert">&times;</button>';

        if (is_array($warning)) {
            $output .= '<ul>';

            foreach ($warning as $msg) {
                $output .= '<li>' . $msg . '</li>';
            }

            $output .= '</ul>';
        } else {
            $output .= $warning;
        }

        // Close div openned previously
        $output .= '</div></div>';

        return $output;
    }

    public function displayConfirmation($string) {

        $output = '
        <div class="bootstrap">
        <div class="plugin_confirmation conf confirm alert alert-success">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            ' . $string . '
        </div>
        </div>';

        return $output;
    }

    public function getExceptions($idHook, $dispatch = false) {

        return Plugin::getExceptionsStatic($this->id, $idHook, $dispatch);
    }

    public static function getExceptionsStatic($id_plugin, $id_hook, $dispatch = false) {

        $cache_id = 'exceptionsCache';

        if (!Cache::isStored($cache_id)) {
            $exceptions_cache = [];
            $dbSlave = Db::getInstance(_EPH_USE_SQL_SLAVE_);
            $result = $dbSlave->executeS(
                (new DbQuery())
                    ->select('*')
                    ->from('hook_plugin_exceptions')
            );

            foreach ($result as $row) {

                if (!$row['file_name']) {
                    continue;
                }

                $key = $row['id_hook'] . '-' . $row['id_plugin'];

                if (!isset($exceptions_cache[$key])) {
                    $exceptions_cache[$key] = [];
                }

                $exceptions_cache[$key][] = $row['file_name'];
            }

            Cache::store($cache_id, $exceptions_cache);
        } else {
            $exceptions_cache = Cache::retrieve($cache_id);
        }

        $key = $id_hook . '-' . $id_plugin;
        $array_return = [];

        if ($dispatch) {

            foreach ([Context::getContext()->company->id] as $company_id) {

                if (isset($exceptions_cache[$key], $exceptions_cache[$key][$company_id])) {
                    $array_return[$company_id] = $exceptions_cache[$key][$company_id];
                }

            }

        } else {

            foreach ([Context::getContext()->company->id] as $company_id) {

                if (isset($exceptions_cache[$key], $exceptions_cache[$key][$company_id])) {

                    foreach ($exceptions_cache[$key][$company_id] as $file) {

                        if (!in_array($file, $array_return)) {
                            $array_return[] = $file;
                        }

                    }

                }

            }

        }

        return $array_return;
    }

    public function isEnabledForShopContext() {

        return (bool) Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('COUNT(*) n')
                ->from('plugin')
                ->where('`id_plugin` = ' . (int) $this->id)
                ->where('`active` = 1')
                ->groupBy('`id_plugin`')
        );
    }

    public function isRegisteredInHook($hook) {

        if (!$this->id) {
            return false;
        }

        return Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('COUNT(*)')
                ->FROM('hook_plugin', 'hm')
                ->leftJoin('hook', 'h', 'h.`id_hook` = hm.`id_hook`')
                ->where('h.`name` = \'' . pSQL($hook) . '\'')
                ->where('hm.`id_plugin` = ' . (int) $this->id)
        );
    }

    public function display($file, $template, $cache_id = null, $compile_id = null) {

        if (($overloaded = Plugin::_isTemplateOverloadedStatic(basename($file, '.php'), $template)) === null) {
            return Tools::displayError('No template found for plugin') . ' ' . basename($file, '.php');
        } else {

            if (Tools::getIsset('live_edit') || Tools::getIsset('live_configurator_token')) {
                $cache_id = null;
            }

            $this->context->smarty->assign(
                [
                    'plugin_dir'          => $this->context->link->getBaseFrontLink() . 'includes/plugins/' . basename($file, '.php') . '/',
                    'plugin_template_dir' => ($overloaded ? _THEME_DIR_ : __EPH_BASE_URI__) . 'includes/plugins/' . basename($file, '.php') . '/',
                    'allow_push'          => $this->allow_push,
                ]
            );

            if ($cache_id !== null) {
                Tools::enableCache();
            }

            $result = $this->getCurrentSubTemplate($template, $cache_id, $compile_id)->fetch();

            if ($cache_id !== null) {
                Tools::restoreCacheSettings();
            }

            $this->resetCurrentSubTemplate($template, $cache_id, $compile_id);

            if ($result && _EPH_MODE_DEV_ && !Validate::isJSON($result)) {
                $tpl_path = $this->getTemplatePath($template);
                $result = '<!-- START ' . $tpl_path . ' -->' . $result . '<!-- END ' . $tpl_path . ' -->';
            }

            return $result;
        }

    }

    protected static function _isTemplateOverloadedStatic($plugin_name, $template) {

        if (file_exists(_EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/' . $template)) {
            return _EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/' . $template;
        } else
        if (file_exists(_EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/views/templates/hook/' . $template)) {
            return _EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/views/templates/hook/' . $template;
        } else
        if (file_exists(_EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/views/templates/front/' . $template)) {
            return _EPH_THEME_DIR_ . 'plugins/' . $plugin_name . '/views/templates/front/' . $template;
        } else
        if (file_exists(_EPH_PLUGIN_DIR_ . $plugin_name . '/views/templates/hook/' . $template)) {
            return false;
        } else
        if (file_exists(_EPH_PLUGIN_DIR_ . $plugin_name . '/views/templates/front/' . $template)) {
            return false;
        } else
        if (file_exists(_EPH_PLUGIN_DIR_ . $plugin_name . '/' . $template)) {
            return false;
        }

        return null;
    }

    protected function getCurrentSubTemplate($template, $cache_id = null, $compile_id = null) {

        if (!isset($this->current_subtemplate[$template . '_' . $cache_id . '_' . $compile_id])) {
            $this->current_subtemplate[$template . '_' . $cache_id . '_' . $compile_id] = $this->context->smarty->createTemplate(
                $this->getTemplatePath($template),
                $cache_id,
                $compile_id,
                $this->smarty
            );
        }

        return $this->current_subtemplate[$template . '_' . $cache_id . '_' . $compile_id];
    }

    public function getTemplatePath($template) {

        $overloaded = $this->_isTemplateOverloaded($template);

        if ($overloaded === null) {
            return null;
        }

        if ($overloaded) {
            return $overloaded;
        } else
        if (file_exists(_EPH_PLUGIN_DIR_ . $this->name . '/views/templates/hook/' . $template)) {
            return _EPH_PLUGIN_DIR_ . $this->name . '/views/templates/hook/' . $template;
        } else
        if (file_exists(_EPH_PLUGIN_DIR_ . $this->name . '/views/templates/front/' . $template)) {
            return _EPH_PLUGIN_DIR_ . $this->name . '/views/templates/front/' . $template;
        } else
        if (file_exists(_EPH_PLUGIN_DIR_ . $this->name . '/' . $template)) {
            return _EPH_PLUGIN_DIR_ . $this->name . '/' . $template;
        } else {
            return null;
        }

    }

    protected function _isTemplateOverloaded($template) {

        return Plugin::_isTemplateOverloadedStatic($this->name, $template);
    }

    protected function resetCurrentSubTemplate($template, $cache_id, $compile_id) {

        $this->current_subtemplate[$template . '_' . $cache_id . '_' . $compile_id] = null;
    }

    public function isCached($template, $cacheId = null, $compileId = null) {

        if (Tools::getIsset('live_edit') || Tools::getIsset('live_configurator_token')) {
            return false;
        }

        Tools::enableCache();
        $new_tpl = $this->getTemplatePath($template);
        $is_cached = $this->getCurrentSubTemplate($template, $cacheId, $compileId)->isCached($new_tpl, $cacheId, $compileId);
        Tools::restoreCacheSettings();

        return $is_cached;
    }

    public function isHookableOn($hook_name) {

        $retro_hook_name = Hook::getRetroHookName($hook_name);

        return (is_callable([$this, 'hook' . ucfirst($hook_name)]) || is_callable([$this, 'hook' . ucfirst($retro_hook_name)]));
    }

    public function getPermission($variable, $employee = null) {

        return Plugin::getPermissionStatic($this->id, $variable, $employee);
    }

    public static function getPermissionStatic($idPlugin, $variable, $employee = null) {

        if (!in_array($variable, ['view', 'configure', 'uninstall'])) {
            return false;
        }

        if (!$employee) {
            $employee = Context::getContext()->employee;
        }

        if ($employee->id_profile == _EPH_ADMIN_PROFILE_) {
            return true;
        }

        if (!isset(static::$cache_permissions[$employee->id_profile])) {
            static::$cache_permissions[$employee->id_profile] = [];
            $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS('SELECT `id_plugin`, `view`, `configure`, `uninstall` FROM `' . _DB_PREFIX_ . 'plugin_access` WHERE `id_profile` = ' . (int) $employee->id_profile);

            foreach ($result as $row) {
                static::$cache_permissions[$employee->id_profile][$row['id_plugin']]['view'] = $row['view'];
                static::$cache_permissions[$employee->id_profile][$row['id_plugin']]['configure'] = $row['configure'];
                static::$cache_permissions[$employee->id_profile][$row['id_plugin']]['uninstall'] = $row['uninstall'];
            }

        }

        if (!isset(static::$cache_permissions[$employee->id_profile][$idPlugin])) {
            throw new PhenyxException('No access reference in table plugin_access for id_plugin ' . $idPlugin . '.');
        }

        return (bool) static::$cache_permissions[$employee->id_profile][$idPlugin][$variable];
    }

    public function getErrors() {

        return $this->_errors;
    }

    public function getConfirmations() {

        return $this->_confirmations;
    }

    public function getPathUri() {

        return $this->_path;
    }

    public function getPosition($id_hook) {

        if (isset(Hook::$preloadPluginsFromHooks)) {

            if (isset(Hook::$preloadPluginsFromHooks[$id_hook])) {

                if (isset(Hook::$preloadPluginsFromHooks[$id_hook]['plugin_position'][$this->id])) {
                    return Hook::$preloadPluginsFromHooks[$id_hook]['plugin_position'][$this->id];
                } else {
                    return 0;
                }

            }

        }

        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getRow(
            (new DbQuery())
                ->select('`position`')
                ->from('hook_plugin')
                ->where('`id_hook` = ' . (int) $id_hook)
                ->where('`id_plugin` = ' . (int) $this->id)
        );

        return $result['position'];
    }

    public function adminDisplayWarning($msg) {

        if (!($this->context->controller instanceof AdminController)) {
            return;
        }

        $this->context->controller->warnings[] = $msg;
    }

    public function getPossibleHooksList() {

        $hooks_list = Hook::getHooks();
        $possible_hooks_list = [];

        foreach ($hooks_list as &$current_hook) {
            $hook_name = $current_hook['name'];
            $retro_hook_name = Hook::getRetroHookName($hook_name);

            if (is_callable([$this, 'hook' . ucfirst($hook_name)]) || is_callable([$this, 'hook' . ucfirst($retro_hook_name)])) {
                $possible_hooks_list[] = [
                    'id_hook' => $current_hook['id_hook'],
                    'name'    => $hook_name,
                    'title'   => $current_hook['title'],
                ];
            }

        }

        return $possible_hooks_list;
    }

    protected function getCacheId($name = null) {

        $cache_array = [];
        $cache_array[] = $name !== null ? $name : $this->name;

        if (Configuration::get(Configuration::SSL_ENABLED)) {
            $cache_array[] = (int) Tools::usingSecureMode();
        }

        if (Group::isFeatureActive() && isset($this->context->user)) {
            $cache_array[] = (int) Group::getCurrent()->id;
            $cache_array[] = implode('_', User::getGroupsStatic($this->context->user->id));
        }

        if (Language::isMultiLanguageActivated()) {
            $cache_array[] = (int) $this->context->language->id;
        }

        

        $cache_array[] = (int) $this->context->country->id;

        return implode('|', $cache_array);
    }

    protected function _getApplicableTemplateDir($template) {

        return $this->_isTemplateOverloaded($template) ? _EPH_THEME_DIR_ : _EPH_PLUGIN_DIR_ . $this->name . '/';
    }

    protected function _clearCache($template, $cacheId = null, $compileId = null) {

        static $ps_smarty_clear_cache = null;

        if ($ps_smarty_clear_cache === null) {
            $ps_smarty_clear_cache = Configuration::get('EPH_SMARTY_CLEAR_CACHE');
        }

        if (static::$_batch_mode) {

            if ($ps_smarty_clear_cache == 'never') {
                return 0;
            }

            if ($cacheId === null) {

                $cacheId = $this->name;
            }

            $key = $template . '-' . $cacheId . '-' . $compileId;

            if (!isset(static::$_defered_clearCache[$key])) {
                static::$_defered_clearCache[$key] = [$this->getTemplatePath($template), $cacheId, $compileId];
            }

        } else {

            if ($ps_smarty_clear_cache == 'never') {
                return 0;
            }

            if ($cacheId === null) {
                $cacheId = $this->name;
            }

            Tools::enableCache();
            $number_of_template_cleared = Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath($template), $cacheId, $compileId);
            Tools::restoreCacheSettings();

            return $number_of_template_cleared;
        }

        return false;
    }

    protected function _generateConfigXml() {

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        $pluginXML = $xml->createElement('plugin');
        $xml->appendChild($pluginXML);

        $authorUri = '';

        if (isset($this->author_uri)) {
            $authorUri = $this->author_uri;
        }

        $confirmUninstall = '';

        if (isset($this->confirmUninstall)) {
            $confirmUninstall = $this->confirmUninstall;
        }

        $isConfigurable = 0;

        if (isset($this->is_configurable)) {
            $isConfigurable = (int) $this->is_configurable;
        }

        $limitedCountries = '';

        if (count($this->limited_countries) == 1) {
            $limitedCountries = $this->limited_countries[0];
        }

        foreach ([
            'name'              => $this->name,
            'displayName'       => $this->displayName,
            'version'           => $this->version,
            'description'       => $this->description,
            'author'            => $this->author,
            'author_uri'        => $authorUri,
            'tab'               => $this->tab,
            'confirmUninstall'  => $confirmUninstall,
            'is_configurable'   => $isConfigurable,
            'limited_countries' => $limitedCountries,
            
        ] as $node => $value) {

            if (!is_null($value) && is_string($value) && strlen($value)) {
                $element = $xml->createElement($node);
                $element->appendChild($xml->createCDATASection($value));
                $pluginXML->appendChild($element);
            } else {
                $element = null;
            }

            
        }

        if (is_writable(_EPH_PLUGIN_DIR_ . $this->name . '/')) {
            $iso = substr(Context::getContext()->language->iso_code, 0, 2);
            $file = _EPH_PLUGIN_DIR_ . $this->name . '/' . ($iso == 'en' ? 'config.xml' : 'config_' . $iso . '.xml');
            @unlink($file);
            @file_put_contents($file, $xml->saveXml());
            @chmod($file, 0664);
        }

    }

    public function isMobileDevice() {

        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }

    protected function adminDisplayInformation($msg) {

        if (!($this->context->controller instanceof AdminController)) {
            return;
        }

        $this->context->controller->informations[] = $msg;
    }
    
    

}

function ps_plugin_version_sort($a, $b) {

    return version_compare($a['version'], $b['version']);
}
