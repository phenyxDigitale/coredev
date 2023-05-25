<?php

/**
 * Class Configuration
 *
 * @since 1.9.1.0
 */
class Configuration extends PhenyxObjectModel {

    // Default configuration consts
    // @since 1.0.1
    
    const ONE_PHONE_AT_LEAST = 'EPH_ONE_PHONE_AT_LEAST';
    const GROUP_FEATURE_ACTIVE = 'EPH_GROUP_FEATURE_ACTIVE';
    const COUNTRY_DEFAULT = 'EPH_COUNTRY_DEFAULT';
    const REWRITING_SETTINGS = 'EPH_REWRITING_SETTINGS';
    const NAVIGATION_PIPE = 'EPH_NAVIGATION_PIPE';
    const SHOP_ENABLE = 'EPH_SHOP_ENABLE';
    const SSL_ENABLED = 'EPH_SSL_ENABLED';
    const SSL_ENABLED_EVERYWHERE = 'EPH_SSL_ENABLED_EVERYWHERE';
    const MAIL_TYPE = 'EPH_MAIL_TYPE';
    const PASSWD_TIME_BACK = 'EPH_PASSWD_TIME_BACK';
    const PASSWD_TIME_FRONT = 'EPH_PASSWD_TIME_FRONT';
    const TIMEZONE = 'EPH_TIMEZONE';
    const SHOW_ALL_PLUGINS = 'EPH_SHOW_ALL_PLUGINS';
    const BACKUP_ALL = 'EPH_BACKUP_ALL';
    const TRACKING_DIRECT_TRAFFIC = 'TRACKING_DIRECT_TRAFFIC';
    const META_KEYWORDS = 'EPH_META_KEYWORDS';
    const CIPHER_ALGORITHM = 'EPH_CIPHER_ALGORITHM';
    const CUSTOMER_SERVICE_FILE_UPLOAD = 'EPH_CUSTOMER_SERVICE_FILE_UPLOAD';
    const CUSTOMER_SERVICE_SIGNATURE = 'EPH_CUSTOMER_SERVICE_SIGNATURE';
    const SMARTY_FORCE_COMPILE = 'EPH_SMARTY_FORCE_COMPILE';
    const STORES_DISPLAY_CMS = 'EPH_STORES_DISPLAY_CMS';
    const STORES_DISPLAY_FOOTER = 'EPH_STORES_DISPLAY_FOOTER';
    const STORES_SIMPLIFIED = 'EPH_STORES_SIMPLIFIED';
    const SHOP_LOGO_WIDTH = 'SHOP_LOGO_WIDTH';
    const SHOP_LOGO_HEIGHT = 'SHOP_LOGO_HEIGHT';
    const EDITORIAL_IMAGE_WIDTH = 'EDITORIAL_IMAGE_WIDTH';
    const EDITORIAL_IMAGE_HEIGHT = 'EDITORIAL_IMAGE_HEIGHT';
    const STATSDATA_CUSTOMER_PAGESVIEWS = 'EPH_STATSDATA_CUSTOMER_PAGESVIEWS';
    const STATSDATA_PAGESVIEWS = 'EPH_STATSDATA_PAGESVIEWS';
    const STATSDATA_PLUGINS = 'EPH_STATSDATA_PLUGINS';
    const GEOLOCATION_ENABLED = 'EPH_GEOLOCATION_ENABLED';
    const ALLOWED_COUNTRIES = 'EPH_ALLOWED_COUNTRIES';
    const GEOLOCATION_BEHAVIOR = 'EPH_GEOLOCATION_BEHAVIOR';
    const LOCALE_LANGUAGE = 'EPH_LOCALE_LANGUAGE';
    const LOCALE_COUNTRY = 'EPH_LOCALE_COUNTRY';
    const ATTACHMENT_MAXIMUM_SIZE = 'EPH_ATTACHMENT_MAXIMUM_SIZE';
    const SMARTY_CACHE = 'EPH_SMARTY_CACHE';
    const DIMENSION_UNIT = 'EPH_DIMENSION_UNIT';
    const GEOLOCATION_WHITELIST = 'EPH_GEOLOCATION_WHITELIST';
    const LOGS_BY_EMAIL = 'EPH_LOGS_BY_EMAIL';
    const COOKIE_CHECKIP = 'EPH_COOKIE_CHECKIP';
    const STORES_CENTER_LAT = 'EPH_STORES_CENTER_LAT';
    const STORES_CENTER_LONG = 'EPH_STORES_CENTER_LONG';
    const CANONICAL_REDIRECT = 'EPH_CANONICAL_REDIRECT';
    const IMG_UPDATE_TIME = 'EPH_IMG_UPDATE_TIME';
    const BACKUP_DROP_TABLE = 'EPH_BACKUP_DROP_TABLE';   
    const IMAGE_QUALITY = 'EPH_IMAGE_QUALITY';
    const PNG_QUALITY = 'EPH_PNG_QUALITY';
    const JPEG_QUALITY = 'EPH_JPEG_QUALITY';
    const COOKIE_LIFETIME_FO = 'EPH_COOKIE_LIFETIME_FO';
    const COOKIE_LIFETIME_BO = 'EPH_COOKIE_LIFETIME_BO';
    const RESTRICT_DELIVERED_COUNTRIES = 'EPH_RESTRICT_DELIVERED_COUNTRIES';
    const SHOW_NEW_CUSTOMERS = 'EPH_SHOW_NEW_CUSTOMERS';
    const SHOW_NEW_MESSAGES = 'EPH_SHOW_NEW_MESSAGES';
    const SHOP_DEFAULT = 'EPH_SHOP_DEFAULT';
    const UNIDENTIFIED_GROUP = 'EPH_UNIDENTIFIED_GROUP';
    const GUEST_GROUP = 'EPH_GUEST_GROUP';
    const CUSTOMER_GROUP = 'EPH_CUSTOMER_GROUP';
    const SMARTY_CONSOLE = 'EPH_SMARTY_CONSOLE';
    const LIMIT_UPLOAD_IMAGE_VALUE = 'EPH_LIMIT_UPLOAD_IMAGE_VALUE';
    const LIMIT_UPLOAD_FILE_VALUE = 'EPH_LIMIT_UPLOAD_FILE_VALUE';
    const TOKEN_ENABLE = 'EPH_TOKEN_ENABLE';
    const STATS_RENDER = 'EPH_STATS_RENDER';
    const STATS_OLD_CONNECT_AUTO_CLEAN = 'EPH_STATS_OLD_CONNECT_AUTO_CLEAN';
    const STATS_GRID_RENDER = 'EPH_STATS_GRID_RENDER';
    const BASE_DISTANCE_UNIT = 'EPH_BASE_DISTANCE_UNIT';
    const SHOP_DOMAIN = 'EPH_SHOP_DOMAIN';
    const SHOP_DOMAIN_SSL = 'EPH_SHOP_DOMAIN_SSL';
    const LANG_DEFAULT = 'EPH_LANG_DEFAULT';
    const ALLOW_HTML_IFRAME = 'EPH_ALLOW_HTML_IFRAME';
    const SHOP_NAME = 'EPH_SHOP_NAME';
    const SHOP_EMAIL = 'EPH_SHOP_EMAIL';
    const MAIL_METHOD = 'EPH_MAIL_METHOD';
    const SHOP_ACTIVITY = 'EPH_SHOP_ACTIVITY';
    const LOGO = 'EPH_LOGO';
    const FAVICON = 'EPH_FAVICON';
    const STORES_ICON = 'EPH_STORES_ICON';
    const MAIL_SERVER = 'EPH_MAIL_SERVER';
    const MAIL_USER = 'EPH_MAIL_USER';
    const MAIL_PASSWD = 'EPH_MAIL_PASSWD';
    const MAIL_SMTP_ENCRYPTION = 'EPH_MAIL_SMTP_ENCRYPTION';
    const MAIL_SMTP_PORT = 'EPH_MAIL_SMTP_PORT';
    const ALLOW_MOBILE_DEVICE = 'EPH_ALLOW_MOBILE_DEVICE';
    const CUSTOMER_CREATION_EMAIL = 'EPH_CUSTOMER_CREATION_EMAIL';
    const SMARTY_CONSOLE_KEY = 'EPH_SMARTY_CONSOLE_KEY';
    const DASHBOARD_USE_PUSH = 'EPH_DASHBOARD_USE_PUSH';
    const DASHBOARD_SIMULATION = 'EPH_DASHBOARD_SIMULATION';
    const USE_HTMLPURIFIER = 'EPH_USE_HTMLPURIFIER';
    const SMARTY_CACHING_TYPE = 'EPH_SMARTY_CACHING_TYPE';
    const SMARTY_CLEAR_CACHE = 'EPH_SMARTY_CLEAR_CACHE';
    const DETECT_LANG = 'EPH_DETECT_LANG';
    const DETECT_COUNTRY = 'EPH_DETECT_COUNTRY';
    const ROUND_TYPE = 'EPH_ROUND_TYPE';
    const LOG_EMAILS = 'EPH_LOG_EMAILS';
    const CUSTOMER_NWSL = 'EPH_CUSTOMER_NWSL';
    const CUSTOMER_OPTIN = 'EPH_CUSTOMER_OPTIN';
    const LOG_PLUGIN_PERFS_MODULO = 'EPH_LOG_PLUGIN_PERFS_MODULO';
    const PAGE_CACHE_CONTROLLERS = 'EPH_PAGE_CACHE_CONTROLLERS';
    const ROUTE_CMS_RULE = 'EPH_ROUTE_cms_rule';
    const DISABLE_OVERRIDES = 'EPH_DISABLE_OVERRIDES';
    const CUSTOMCODE_METAS = 'EPH_CUSTOMCODE_METAS';
    const CUSTOMCODE_CSS = 'EPH_CUSTOMCODE_CSS';
    const CUSTOMCODE_JS = 'EPH_CUSTOMCODE_JS';
    const STORE_REGISTERED = 'EPH_STORE_REGISTERED';
    const EPHENYX_LICENSE_KEY = '_EPHENYX_LICENSE_KEY_';
    // @codingStandardsIgnoreStart
    /**
     * @see PhenyxObjectModel::$definition
     */
    public static $definition = [
        'table'     => 'configuration',
        'primary'   => 'id_configuration',
        'multilang' => true,
        'fields'    => [
            'name'          => ['type' => self::TYPE_STRING, 'validate' => 'isConfigName', 'required' => true, 'size' => 254],
            'value'         => ['type' => self::TYPE_NOTHING],
            'date_add'      => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd'      => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            
            'value_lang'         => ['type' => self::TYPE_NOTHING, 'lang' => true],
            //'date_upd'      => ['type' => self::TYPE_DATE, 'lang' => true, 'validate' => 'isDate'],
        ],
    ];
    /** @var array Configuration cache */
    protected static $_cache = [];
    /** @var array Vars types */
    protected static $types = [];
    /** @var string Key */
    public $name;
    /** @var string Value */
    public $value;
    
    public $value_lang;
    /** @var string Object creation date */
    public $date_add;
    /** @var string Object last modification date */
    public $date_upd;
    
    // @codingStandardsIgnoreEnd

    /**
     * @return bool|null
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function configurationIsLoaded() {

        return isset(static::$_cache['configuration'])
        && is_array(static::$_cache['configuration'])
        && count(static::$_cache['configuration']);
    }

    /**
     * WARNING: For testing only. Do NOT rely on this method, it may be removed at any time.
     *
     * @todo    Delegate static calls from Configuration to an instance of a class to be created.
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function clearConfigurationCacheForTesting() {

        static::$_cache = [];
    }

    /**
     * @param string   $key
     * @param int|null $idLang
     *
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function getGlobalValue($key, $idLang = null) {

        return Configuration::get($key, $idLang);
    }

    /**
     * Get a single configuration value (in one language only)
     *
     * @param string   $key    Key wanted
     * @param int      $idLang Language ID
     * @param int|null $idCompanyGroup
     * @param int|null $idCompany
     *
     * @return string Value
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     * @throws PhenyxException
     */
    public static function get($key, $idLang = null) {

        if (defined('_EPH_DO_NOT_LOAD_CONFIGURATION_') && _EPH_DO_NOT_LOAD_CONFIGURATION_) {
            return false;
        }

        static::validateKey($key);
		

        if (!static::configurationIsLoaded()) {
            Configuration::loadConfiguration();
        }

        $idLang = (int) $idLang;

       
        if (!isset(static::$_cache['configuration'][$idLang])) {
            $idLang = 0;
        }

        
        if (Configuration::hasKey($key, $idLang) && isset(static::$_cache['configuration'][$idLang]['global'][$key])) {
			
            return purifyFetch(static::$_cache['configuration'][$idLang]['global'][$key]);
        } else {
            $value = Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
                (new DbQuery())
                    ->select('`value`')
                    ->from('configuration')
                    ->where('`name` LIKE \'' . $key . '\'')
            );
            static::$_cache['configuration'][$idLang]['global'][$key] = $value;
            return $value;
        }

        return false;
    }

    /**
     * Load all configuration data
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function loadConfiguration() {

        return static::loadConfigurationFromDB(Db::getInstance(_EPH_USE_SQL_SLAVE_));
    }

    /**
     * Load all configuration data, using an existing database connection.
     *
     * @param Db $connection Database connection to be used for data retrieval.
     *
     * @since   1.0.7
     * @version 1.0.7 Initial version
     */
    public static function loadConfigurationFromDB($connection) {

        static::$_cache['configuration'] = [];

        $rows = $connection->executeS(
            (new DbQuery())
                ->select('c.`name`, cl.`id_lang`, IFNULL(cl.`value_lang`, c.`value`) AS `value`')
                ->from('configuration', 'c')
                ->leftJoin('configuration_lang', 'cl', 'c.`id_configuration` = cl.`id_configuration`')
        );

        if (!is_array($rows)) {
            return;
        }

        foreach ($rows as $row) {
            $lang = ($row['id_lang']) ? $row['id_lang'] : 0;
            static::$types[$row['name']] = ($lang) ? 'lang' : 'normal';

            if (!isset(static::$_cache['configuration'][$lang])) {
                static::$_cache['configuration'][$lang] = [
                    'global' => [],
                ];
            }

            static::$_cache['configuration'][$lang]['global'][$row['name']] = $row['value'];

        }

    }

    
    public static function hasKey($key, $idLang = null) {

        return (bool) Db::getInstance(_EPH_USE_SQL_SLAVE_)->getValue(
            (new DbQuery())
                ->select('`id_configuration`')
                ->from('configuration')
                ->where('`name` = \''.$key.'\'')
        );
    }

    public static function getInt($key) {

        $resultsArray = [];

        foreach (Language::getIDs() as $idLang) {
            $resultsArray[$idLang] = Configuration::get($key, $idLang);
        }

        return $resultsArray;
    }

    public static function getMultiShopValues($key, $idLang = null) {        

        return Configuration::get($key, $idLang, null);
    }

    public static function getMultiple($keys, $idLang = null) {

        if (!is_array($keys)) {
            throw new PhenyxException('keys var is not an array');
        }

        $idLang = (int) $idLang;

       
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = Configuration::get($key, $idLang);
        }

        return $results;
    }

    public static function updateGlobalValue($key, $values, $html = false) {

        return Configuration::updateValue($key, $values, $html, 0, 0);
    }
    
    public static function updateValue($key, $values, $html = false, $script = false) {


        static::validateKey($key);

        

        if (!is_array($values)) {
            $values = [$values];
        }
        
        if ($html) {

            foreach ($values as &$value) {
                $value = Tools::purifyHTML($value);
            }

            unset($value);
        }

        if(!$script) {
            foreach ($values as &$value) {
                $value = pSQL($value, $html);
            }
        }

        $result = true;
        $idConfig = Configuration::getIdByName($key);
        $configuration = new Configuration($idConfig);

        foreach ($values as $lang => $value) {

            if (Configuration::hasKey($key, $lang)) {
                if (!$lang) {
                    $configuration->value = $value;
                    
                   
                } else {
                    $configuration->value = null;
                    $configuration->value_lang[$lang] = $value;
                   
                    
                }
                try {
				    $configuration->update(true);
				} catch (Exception $e) {
				    
				}

                
            } else {
               
                $configuration->name = $key;
                $configuration->value = $lang ? null : $value;
                $configuration->date_add = date('Y-m-d H:i:s');
                $configuration->date_upd = date('Y-m-d H:i:s');
                if ($lang) {
                    $configuration->value_lang[$lang] = $value;
                }
                
                try {
				    $configuration->add();
				} catch (Exception $e) {
				    
				}
                
                

            }

        }

        Configuration::set($key, $values);

        return $result;
    }
   
    public static function getIdByName($key) {

        static::validateKey($key);

        

        $sql = 'SELECT `id_configuration`
                FROM `' . _DB_PREFIX_ . 'configuration`
                WHERE name = \'' . $key . '\'';

        return (int) Db::getInstance()->getValue($sql);
    }

    public static function set($key, $values) {

        static::validateKey($key);

        

        if (!is_array($values)) {
            $values = [$values];
        }

        foreach ($values as $lang => $value) {

            static::$_cache['configuration'][$lang]['global'][$key] = $value;

        }

    }

    public static function deleteByName($key) {

        static::validateKey($key);

        $result = Db::getInstance()->execute(
            '
        DELETE FROM `configuration_lang`
        WHERE `id_configuration` IN (
            SELECT `id_configuration`
            FROM `' . _DB_PREFIX_ . 'configuration`
            WHERE `name` = "' . $key . '"
        )'
        );

        $result2 = Db::getInstance()->delete('configuration', '`name` = "' . $key . '"');

        static::$_cache['configuration'] = null;

        return ($result && $result2);
    }

    public static function deleteFromContext($key) {


        $id = Configuration::getIdByName($key);
        Db::getInstance()->delete(
            'configuration',
            '`id_configuration` = ' . (int) $id
        );
        Db::getInstance()->delete(
            'configuration_lang',
            '`id_configuration` = ' . (int) $id
        );

        static::$_cache['configuration'] = null;
    }

    public static function isLangKey($key) {

        static::validateKey($key);

        return (isset(static::$types[$key]) && static::$types[$key] == 'lang') ? true : false;
    }

    

    protected static function validateKey($key) {

        if (!Validate::isConfigName($key)) {
            $e = new PhenyxException(sprintf(
                Tools::displayError('[%s] is not a valid configuration key'),
                Tools::htmlentitiesUTF8($key)
            ));
            die($e->displayMessage());
        }

    }
    
}
