<?php

/**
 * Class PhenyxObjectModel
 *
 * @since 1.9.1.0
 */
abstract class PhenyxObjectModel implements Core_Foundation_Database_EntityInterface {

    /**
     * List of field types
     */
    const TYPE_INT = 1;
    const TYPE_BOOL = 2;
    const TYPE_STRING = 3;
    const TYPE_FLOAT = 4;
    const TYPE_DATE = 5;
    const TYPE_HTML = 6;
    const TYPE_NOTHING = 7;
    const TYPE_SQL = 8;
	const TYPE_JSON = 9;
    const TYPE_SCRIPT = 9;

    /**
     * List of data to format
     */
    const FORMAT_COMMON = 1;
    const FORMAT_LANG = 2;

    /**
     * List of association types
     */
    const HAS_ONE = 1;
    const HAS_MANY = 2;

    // @codingStandardsIgnoreStart
    /** @var int Object ID */
    public $id;

    /** @var int Language ID */
    public $id_lang = null;


    /** @var array|null Holds required fields for each PhenyxObjectModel class */
    protected static $fieldsRequiredDatabase = null;

    /**
     * @deprecated 1.0.0 Define property using $definition['table'] property instead.
     * @var string
     */
    protected $table;

    /**
     * @deprecated 1.0.0 Define property using $definition['table'] property instead.
     * @var string
     */
    protected $identifier;

    /**
     * @deprecated 1.0.0 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsRequired = [];

    /**
     * @deprecated 1.0.0 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsSize = [];

    /**
     * @deprecated 1.0.0 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsValidate = [];

    /**
     * @deprecated 1.0.0 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsRequiredLang = [];

    /**
     * @deprecated 1.0.0 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsSizeLang = [];

    /**
     * @deprecated 1.0.0 Define property using $definition['table'] property instead.
     * @var array
     */
    protected $fieldsValidateLang = [];

    /**
     * @deprecated 1.0.0
     * @var array
     */
    protected $tables = [];

    /** @var array Tables */
    protected $webserviceParameters = [];

    /** @var string Path to image directory. Used for image deletion. */
    protected $image_dir = null;

    /** @var String file type of image files. */
    protected $image_format = 'jpg';

    /**
     * @var array Contains object definition
     * @since 1.5.0.1
     */
    public static $definition = [];

    /**
     * Holds compiled definitions of each PhenyxObjectModel class.
     * Values are assigned during object initialization.
     *
     * @var array
     */
    protected static $loaded_classes = [];

    /** @var array Contains current object definition. */
    protected $def;

    /** @var array|null List of specific fields to update (all fields if null). */
    protected $update_fields = null;

    /** @var Db An instance of the db in order to avoid calling Db::getInstance() thousands of times. */
    protected static $db = false;

    /** @var bool Enables to define an ID before adding object. */
    public $force_id = false;
    
    public $request_admin = false;
    
    public $paramFields = [];

    /**
     * @var bool If true, objects are cached in memory.
     */
    protected static $cache_objects = true;
    // @codingStandardsIgnoreEnd
    
    public static $debug_list = [];

    
    public static function getRepositoryClassName() {

        return null;
    }

    
    public static function getValidationRules($class = __CLASS__) {

        $object = new $class();

        return [
            'required'     => $object->fieldsRequired,
            'size'         => $object->fieldsSize,
            'validate'     => $object->fieldsValidate,
            'requiredLang' => $object->fieldsRequiredLang,
            'sizeLang'     => $object->fieldsSizeLang,
            'validateLang' => $object->fieldsValidateLang,
        ];
    }

   
    public function __construct($id = null, $idLang = null) {

        $className = get_class($this);

        if (!isset(PhenyxObjectModel::$loaded_classes[$className])) {
            $this->def = PhenyxObjectModel::getDefinition($className);
            
            if (!Validate::isTableOrIdentifier($this->def['primary']) || !Validate::isTableOrIdentifier($this->def['table'])) {
                throw new PhenyxException('Identifier or table format not valid for class ' . $className);
            }

            PhenyxObjectModel::$loaded_classes[$className] = get_object_vars($this);
        } else {

            foreach (PhenyxObjectModel::$loaded_classes[$className] as $key => $value) {
                $this->{$key}

                = $value;
            }

        }

        if ($idLang !== null) {
            $this->id_lang = (Language::getLanguage($idLang) !== false) ? $idLang : Configuration::get(Configuration::LANG_DEFAULT);
        }

        if (_EPH_DEBUG_PROFILING_ || _EPH_ADMIN_DEBUG_PROFILING_) {
            
            $classname = get_class($this);
            
            if (!isset(self::$debug_list[$classname])) {
                self::$debug_list[$classname] = [];
            }
            $class_list = ['PhenyxObjectModel', $classname, $classname . 'Core'];
            $backtrace = debug_backtrace();

            foreach ($backtrace as $trace_id => $row) {

                if (!isset($backtrace[$trace_id]['class']) || !in_array($backtrace[$trace_id]['class'], $class_list)) {
                    break;
                }

            }

            $trace_id--;

            self::$debug_list[$classname][] = [
                'file' => @$backtrace[$trace_id]['file'],
                'line' => @$backtrace[$trace_id]['line'],
            ];
         }        

        if ($id) {
            $entityMapper = Adapter_ServiceLocator::get("Adapter_EntityMapper");
            $entityMapper->load($id, $idLang, $this, $this->def, static::$cache_objects);
        }        
    }

    
    public function &__get($property) {

        
        $camelCaseProperty = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $property))));

        if (property_exists($this, $camelCaseProperty)) {
            return $this->$camelCaseProperty;
        }

        return $this->$property;
    }

    
    public function __set($property, $value) {

        
        $snakeCaseProperty = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $property))));

        if (property_exists($this, $snakeCaseProperty)) {
            $this->$snakeCaseProperty = $value;
        } else {
            $this->$property = $value;
        }

    }
    
    public function getRequest() {
        
        $className = get_class($this);
        $request = Hook::exec('action' . $className . 'getRequestModifier', [], null, true);
        
        if(!empty($request)) {
            return array_shift($request);
        }
        return null;
    }
    
    public function getParamFields() {
        
        $className = get_class($this);
        $fields = Hook::exec('action' . $className . 'getFieldsModifier', [], null, true);
        
        if(is_array($fields) && count($fields)) {
            foreach(array_shift($fields) as $key => $values) {
                if(is_int($key)) {
                    $this->paramFields[] =$values;
                }
            }           
        }        
        
        return $this->paramFields;
    }
    
    public function removeRequestFields($requests) {
        
        $objects = [];
        
        if(is_array($this->paramFields) && count($this->paramFields)) {
            $fields = [];
            foreach ($this->paramFields as $grifField) {
                $fields[] = $grifField['dataIndx'];
            }

            foreach ($requests as $key => $object) {

                foreach ($object as $field => $value) {

                    if (in_array($field, $fields)) {
                        $objects[$key][$field] = $value;
                    }
                }
            }

        }

        
        return $objects;
        
    }


    
    public function getFields() {

        
		$this->validateFields();
        $fields = $this->formatFields(static::FORMAT_COMMON);        

        if (!$fields && isset($this->id) && Validate::isUnsignedId($this->id)) {
            $fields[$this->def['primary']] = $this->id;
        }
        
        return $fields;
    }

    public function getFieldsLang() {

        if (method_exists($this, 'getTranslationsFieldsChild')) {
            return $this->getTranslationsFieldsChild();
        }

        $this->validateFieldsLang();

        $fields = [];

        if ($this->id_lang === null || empty($this->id_lang)) {

            foreach (Language::getIDs(false) as $idLang) {
                $fields[$idLang] = $this->formatFields(static::FORMAT_LANG, $idLang);
                $fields[$idLang]['id_lang'] = $idLang;
            }

        } else {
            $fields = [$this->id_lang => $this->formatFields(static::FORMAT_LANG, $this->id_lang)];
            $fields[$this->id_lang]['id_lang'] = $this->id_lang;
        }

        return $fields;
    }

    protected function formatFields($type, $idLang = null) {

        $fields = [];
		      

        if (isset($this->id)) {
            $fields[$this->def['primary']] = $this->id;
        }

        foreach ($this->def['fields'] as $field => $data) {

            if (($type == static::FORMAT_LANG && empty($data['lang']))
                || ($type == static::FORMAT_COMMON && !empty($data['lang']))) {
                continue;
            }

            if (is_array($this->update_fields)) {

                if (!empty($data['lang']) && (empty($this->update_fields[$field]) || ($type == static::FORMAT_LANG && empty($this->update_fields[$field][$idLang])))) {
                    continue;
                }

            }

            
            $value = $this->$field;

            if ($type == static::FORMAT_LANG && $idLang && is_array($value)) {

                if (!empty($value[$idLang])) {
                    $value = $value[$idLang];
                } else
                if (!empty($data['required'])) {
                    $value = $value[Configuration::get(Configuration::LANG_DEFAULT)];
                } else {
                    $value = '';
                }

            }

            $purify = (isset($data['validate']) && mb_strtolower($data['validate']) == 'iscleanhtml') ? true : false;
            
			
            $fields[$field] = PhenyxObjectModel::formatValue($value, $data['type'], false, $purify, !empty($data['allow_null']));
        }

        return $fields;
    }
    
    public static function formatValue($value, $type, $withQuotes = false, $purify = true, $allowNull = false) {

        if ($allowNull && $value === null) {
            return ['type' => 'sql', 'value' => 'NULL'];
        }

        switch ($type) {
        case self::TYPE_INT:
            return (int) $value;

        case self::TYPE_BOOL:
            return (int) $value;

        case self::TYPE_FLOAT:
			if(is_null($value)) {
				$value = '0.00';
			}
            return (float) str_replace(',', '.', $value);

        case self::TYPE_DATE:

            if (!$value) {
                return '0000-00-00';
            }

            if ($withQuotes) {
                return '\'' . pSQL($value) . '\'';
            }

            return pSQL($value);

        case self::TYPE_HTML:
                
            $value = purifyFetch($value);

            if ($purify) {
                $value = Tools::purifyHTML($value);
            }

            if ($withQuotes) {
                return '\'' . pSQL($value, true) . '\'';
            }

            return pSQL($value, true);
       case self::TYPE_SCRIPT:
            return sSQL($value);    
        case self::TYPE_SQL:

            if ($withQuotes) {
                return '\'' . pSQL($value, true) . '\'';
            }

            return pSQL($value, true);

        case self::TYPE_NOTHING:
            return $value;
		case self::TYPE_JSON:
			return '\'' . $value . '\'';

        case self::TYPE_STRING:
        default:

            if ($withQuotes) {
                return '\'' . pSQL($value) . '\'';
            }

            return pSQL($value);
        }

    }

    
    public function save($nullValues = false, $autoDate = true) {

        return (int) $this->id > 0 ? $this->update($nullValues) : $this->add($autoDate, $nullValues);
    }

    
    public function add($autoDate = true, $nullValues = false) {

        if (isset($this->id) && !$this->force_id) {
            unset($this->id);
        }
        
        Hook::exec('actionObjectAddBefore', ['object' => $this]);
        Hook::exec('actionObject' . get_class($this) . 'AddBefore', ['object' => $this]);

        if ($autoDate && property_exists($this, 'date_add')) {
            $this->date_add = date('Y-m-d H:i:s');
        }

        if ($autoDate && property_exists($this, 'date_upd')) {
            $this->date_upd = date('Y-m-d H:i:s');
        }


        $fields = $this->getFields();

        if (!$result = Db::getInstance()->insert($this->def['table'], $fields, $nullValues)) {
            return false;
        }

        
        $this->id = Db::getInstance()->Insert_ID();        

        if (!$result) {
            return false;
        }

        if (!empty($this->def['multilang'])) {
            
            $fields = $this->getFieldsLang();

            if ($fields && is_array($fields)) {

                foreach ($fields as $field) {

                    foreach (array_keys($field) as $key) {

                        if (!Validate::isTableOrIdentifier($key)) {
                            throw new PhenyxException('key ' . $key . ' is not table or identifier');
                        }

                    }

                    $field[$this->def['primary']] = (int) $this->id;

                    $result &= Db::getInstance()->insert($this->def['table'] . '_lang', $field);

                }

            }

        }
       
        Hook::exec('actionObjectAddAfter', ['object' => $this]);
        Hook::exec('actionObject' . get_class($this) . 'AddAfter', ['object' => $this]);

        return $result;
    }

    
    public function duplicateObject() {

        $definition = PhenyxObjectModel::getDefinition($this);
        
        $res = Db::getInstance()->getRow(
			(new DbQuery())
				->select('*')
				->from(bqSQL($definition['table']))
				->where('`' . bqSQL($definition['primary']) . '` = ' . (int) $this->id)
		);

        if (!$res) {
            return false;
        }

        unset($res[$definition['primary']]);

        foreach ($res as $field => &$value) {

            if (isset($definition['fields'][$field])) {
                $value = PhenyxObjectModel::formatValue($value, $definition['fields'][$field]['type'], false, true, !empty($definition['fields'][$field]['allow_null']));
            }

        }

        if (!Db::getInstance()->insert($definition['table'], $res)) {
            return false;
        }

        $objectId = Db::getInstance()->Insert_ID();

        if (isset($definition['multilang']) && $definition['multilang']) {
            
            $result = Db::getInstance()->executeS(
                (new DbQuery())
				->select('*')
				->from(bqSQL($definition['table']) . '_lang`')
				->where('`' . bqSQL($definition['primary']) . '` = ' . (int) $this->id)
            );
           
            if (!$result) {
                return false;
            }

            foreach ($result as &$row) {

                foreach ($row as $field => &$value) {

                    if (isset($definition['fields'][$field])) {
                        $value = PhenyxObjectModel::formatValue($value, $definition['fields'][$field]['type'], false, true, !empty($definition['fields'][$field]['allow_null']));
                    }

                }

            }

            foreach ($result as $row2) {
                $row2[$definition['primary']] = (int) $objectId;

                if (!Db::getInstance()->insert($definition['table'] . '_lang', $row2)) {
                    return false;
                }

            }

        }

        $objectDuplicated = new $definition['classname']((int) $objectId);

        return $objectDuplicated;
    }
    
    public function update($nullValues = false) {

       
        Hook::exec('actionObjectUpdateBefore', ['object' => $this]);
        Hook::exec('actionObject' . get_class($this) . 'UpdateBefore', ['object' => $this]);

        $this->clearCache();
        
        if (property_exists($this, 'date_upd')) {
            $this->date_upd = date('Y-m-d H:i:s');

            if (isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields)) {
                $this->update_fields['date_upd'] = true;
            }

        }

        if (property_exists($this, 'date_add') && $this->date_add == null) {
            $this->date_add = date('Y-m-d H:i:s');

            if (isset($this->update_fields) && is_array($this->update_fields) && count($this->update_fields)) {
                $this->update_fields['date_add'] = true;
            }

        }      

        if (!$result = Db::getInstance()->update($this->def['table'], $this->getFields(), '`' . pSQL($this->def['primary']) . '` = ' . (int) $this->id, 0, $nullValues)) {
            return false;
        }

        if (isset($this->def['multilang']) && $this->def['multilang']) {
            $fields = $this->getFieldsLang();

            if (is_array($fields)) {

                foreach ($fields as $field) {

                    foreach (array_keys($field) as $key) {

                        if (!Validate::isTableOrIdentifier($key)) {
                            throw new PhenyxException('key ' . $key . ' is not a valid table or identifier');
                        }

                    }

                    $where = pSQL($this->def['primary']) . ' = ' . (int) $this->id . ' AND id_lang = ' . (int) $field['id_lang'];
                    if(Db::getInstance()->getValue(
                        (new DbQuery())
				        ->select('COUNT(*)')
				        ->from(pSQL($this->def['table']).'_lang')
				        ->where($where)
                    )) {
                        $result &= Db::getInstance()->update($this->def['table'] . '_lang', $field, $where);
                    } else {
                        $result &= Db::getInstance()->insert($this->def['table'] . '_lang', $field, $nullValues);
                    }

                }

            }

        }
       
        Hook::exec('actionObjectUpdateAfter', ['object' => $this]);
        Hook::exec('actionObject' . get_class($this) . 'UpdateAfter', ['object' => $this]);

        return $result;
    }

    public function delete() {
        
        Hook::exec('actionObjectDeleteBefore', ['object' => $this]);
        Hook::exec('actionObject' . get_class($this) . 'DeleteBefore', ['object' => $this]);

        $this->clearCache();
        $result = true;
        

        $result &= Db::getInstance()->delete($this->def['table'], '`' . bqSQL($this->def['primary']) . '` = ' . (int) $this->id);

        if (!$result) {
            return false;
        }
        
        if (!empty($this->def['multilang'])) {
            $result &= Db::getInstance()->delete($this->def['table'] . '_lang', '`' . bqSQL($this->def['primary']) . '` = ' . (int) $this->id);
        }
       
        Hook::exec('actionObjectDeleteAfter', ['object' => $this]);
        Hook::exec('actionObject' . get_class($this) . 'DeleteAfter', ['object' => $this]);

        return $result;
    }

    public function deleteSelection($ids) {

        $result = true;

        foreach ($ids as $id) {
            $this->id = (int) $id;
            $result = $result && $this->delete();
        }

        return $result;
    }

    public function toggleStatus() {

        
        if (!property_exists($this, 'active')) {
            throw new PhenyxException('property "active" is missing in object ' . get_class($this));
        }
        
        $this->setFieldsToUpdate(['active' => true]);

        $this->active = !(int) $this->active;

        return $this->update(false);
    }

    protected function getTranslationsFields($fieldsArray) {

        $fields = [];

        if ($this->id_lang == null) {

            foreach (Language::getIDs(false) as $id_lang) {
                $this->makeTranslationFields($fields, $fieldsArray, $id_lang);
            }

        } else {
            $this->makeTranslationFields($fields, $fieldsArray, $this->id_lang);
        }

        return $fields;
    }

    protected function makeTranslationFields(&$fields, &$fieldsArray, $idLanguage) {

        $fields[$idLanguage]['id_lang'] = $idLanguage;
        $fields[$idLanguage][$this->def['primary']] = (int) $this->id;

        foreach ($fieldsArray as $k => $field) {
            $html = false;
            $fieldName = $field;

            if (is_array($field)) {
                $fieldName = $k;
                $html = (isset($field['html'])) ? $field['html'] : false;
            }

            if (!Validate::isTableOrIdentifier($fieldName)) {
                throw new PhenyxException('identifier is not table or identifier : ' . $fieldName);
            }

            if ((!$this->id_lang && isset($this->{$fieldName}

                [$idLanguage]) && !empty($this->{$fieldName}

                [$idLanguage]))
                || ($this->id_lang && isset($this->$fieldName) && !empty($this->$fieldName))) {
                $fields[$idLanguage][$fieldName] = $this->id_lang ? pSQL($this->$fieldName, $html) : pSQL($this->{$fieldName}

                    [$idLanguage], $html);
            } else
            if (in_array($fieldName, $this->fieldsRequiredLang)) {
                $fields[$idLanguage][$fieldName] = pSQL($this->id_lang ? $this->$fieldName : $this->{$fieldName}

                    [Configuration::get(Configuration::LANG_DEFAULT)], $html);
            } else {
                $fields[$idLanguage][$fieldName] = '';
            }

        }

    }

    
    public function validateFields($die = true, $errorReturn = false) {

       
        foreach ($this->def['fields'] as $field => $data) {
            
            if (!empty($data['lang'])) {
                continue;
            }
            if (is_array($this->update_fields) && empty($this->update_fields[$field])) {
                continue;
            }
            
            $message = $this->validateField($field, $this->$field);

            if ($message !== true) {

                $return = [
        			'success' => false,
            		'message' => $message,
        		];

                die(Tools::jsonEncode($return));
            }

        }
        
        return true;
    }

    public function validateFieldsLang($die = true, $errorReturn = false) {

        $idLangDefault = Configuration::get(Configuration::LANG_DEFAULT);

        foreach ($this->def['fields'] as $field => $data) {

            if (empty($data['lang'])) {
                continue;
            }

            $values = $this->$field;

            if (!is_array($values)) {
                $values = [$this->id_lang => $values];
            }

            if (!isset($values[$idLangDefault])) {
                $values[$idLangDefault] = '';
            }

            foreach ($values as $idLang => $value) {

                if (is_array($this->update_fields) && empty($this->update_fields[$field][$idLang])) {
                    continue;
                }

                $message = $this->validateField($field, $value, $idLang);

                if ($message !== true) {

                    $return = [
        				'success' => false,
            			'message' => $message,
        			];

                	die(Tools::jsonEncode($return));
                }

            }

        }

        return true;
    }

    public function validateField($field, $value, $idLang = null, $skip = [], $humanErrors = false) {

        static $psLangDefault = null;
        static $psAllowHtmlIframe = null;

        if ($psLangDefault === null) {
            $psLangDefault = Configuration::get(Configuration::LANG_DEFAULT);
        }

        if ($psAllowHtmlIframe === null) {
            $psAllowHtmlIframe = (int) Configuration::get(Configuration::ALLOW_HTML_IFRAME);
        }

        $this->cacheFieldsRequiredDatabase();
        $data = $this->def['fields'][$field];
        
        $requiredFields = (isset(static::$fieldsRequiredDatabase[get_class($this)])) ? static::$fieldsRequiredDatabase[get_class($this)] : [];

        if (!$idLang || $idLang == $psLangDefault) {

            if (!in_array('required', $skip) && (!empty($data['required']) || in_array($field, $requiredFields))) {

                if (Tools::isEmpty($value)) {

                    if ($humanErrors) {
                       $message = sprintf(Tools::displayError('The %s field is required.'), $this->displayFieldName($field, get_class($this)));
                    } else {
                        $message = 'Property ' . get_class($this) . '->' . $field . ' is empty';
                    }
					
					$return = [
        				'success' => false,
            			'message' => $message,
        			];

                	die(Tools::jsonEncode($return));

                }

            }

        }

        if (!$value && !empty($data['default'])) {
            $value = $data['default'];
            $this->$field = $value;
        }

        if (!in_array('values', $skip) && !empty($data['values']) && is_array($data['values']) && !in_array($value, $data['values'])) {
            return 'Property ' . get_class($this) . '->' . $field . ' has bad value (allowed values are: ' . implode(', ', $data['values']) . ')';
        }

        if (!in_array('size', $skip) && !empty($data['size'])) {
            $size = $data['size'];

            if (!is_array($data['size'])) {
                $size = ['min' => 0, 'max' => $data['size']];
            }
			$length = 0;
			if(!is_null($value) && is_string($value)) {
				$length = mb_strlen($value);
			}            

            if ($length < $size['min'] || $length > $size['max']) {

                if ($humanErrors) {

                    if (isset($data['lang']) && $data['lang']) {
                        $language = new Language((int) $idLang);
                        $message =  sprintf(Tools::displayError('The field %1$s (%2$s) is too long (%3$d chars max, html chars including).'), $this->displayFieldName($field, get_class($this)), $language->name, $size['max']);
                    } else {
                        $message = sprintf(Tools::displayError('The %1$s field is too long (%2$d chars max).'), $this->displayFieldName($field, get_class($this)), $size['max']);
                    }
					
					$return = [
        				'success' => false,
            			'message' => $message,
        			];

                	die(Tools::jsonEncode($return));

                } else {
                   $message = 'Property ' . get_class($this) . '->' . $field . ' length (' . $length . ') must be between ' . $size['min'] . ' and ' . $size['max'];
					
					$return = [
        				'success' => false,
            			'message' => $message,
        			];

                	die(Tools::jsonEncode($return));
                }

            }

        }

        if (!in_array('validate', $skip) && !empty($data['validate'])) {

            if (!method_exists('Validate', $data['validate'])) {
                throw new PhenyxException('Validation function not found. ' . $data['validate']);
            }

            if (!empty($value)) {
                $res = true;

                if (mb_strtolower($data['validate']) == 'iscleanhtml') {

                    if (!call_user_func(['Validate', $data['validate']], $value, $psAllowHtmlIframe)) {
                        $res = false;
                    }

                } else {

                    if (!call_user_func(['Validate', $data['validate']], $value)) {
                        $res = false;
                    }

                }

                if (!$res) {

                    if ($humanErrors) {
                        $message = sprintf(Tools::displayError('The %s field is invalid.'), $this->displayFieldName($field, get_class($this)));
                    } else {
                        $message = 'Property ' . get_class($this) . '->' . $field . ' is not valid';
                    }
					
					$return = [
        				'success' => false,
            			'message' => $message,
        			];

                	die(Tools::jsonEncode($return));

                }

            }

        }

        return true;
    }

    public static function displayFieldName($field, $class = __CLASS__, $htmlentities = true, Context $context = null) {

        global $_FIELDS;

        if (!isset($context)) {
            $context = Context::getContext();
        }

        if ($_FIELDS === null && file_exists(_EPH_TRANSLATIONS_DIR_ . $context->language->iso_code . '/fields.php')) {
            include_once _EPH_TRANSLATIONS_DIR_ . $context->language->iso_code . '/fields.php';
        }

        $key = $class . '_' . md5($field);

        return ((is_array($_FIELDS) && array_key_exists($key, $_FIELDS)) ? ($htmlentities ? htmlentities($_FIELDS[$key], ENT_QUOTES, 'utf-8') : $_FIELDS[$key]) : $field);
    }

    public function validateControler($htmlentities = true) {

        Tools::displayAsDeprecated();

        return $this->validateController($htmlentities);
    }

    public function validateController($htmlentities = true) {

        $this->cacheFieldsRequiredDatabase();
        $errors = [];
        $requiredFieldsDatabase = (isset(static::$fieldsRequiredDatabase[get_class($this)])) ? static::$fieldsRequiredDatabase[get_class($this)] : [];

        foreach ($this->def['fields'] as $field => $data) {
            $value = Tools::getValue($field, $this->{$field});

            if (in_array($field, $requiredFieldsDatabase)) {
                $data['required'] = true;
            }

            if (isset($data['required']) && $data['required'] && empty($value) && $value !== '0') {

                if (!$this->id || $field != 'passwd') {
                    $errors[$field] = '<b>' . static::displayFieldName($field, get_class($this), $htmlentities) . '</b> ' . Tools::displayError('is required.');
                }

            }

            if (isset($data['size']) && !empty($value) && is_string($value) && mb_strlen($value) > $data['size']) {
                $errors[$field] = sprintf(
                    Tools::displayError('%1$s is too long. Maximum length: %2$d'),
                    static::displayFieldName($field, get_class($this), $htmlentities),
                    $data['size']
                );
            }

            if (!empty($value) || $value === '0' || ($field == 'postcode' && $value == '0')) {
                $validationError = false;

                if (isset($data['validate'])) {
                    $dataValidate = $data['validate'];

                    if (!Validate::$dataValidate($value) && (!empty($value) || $data['required'])) {
                        $errors[$field] = '<b>' . static::displayFieldName($field, get_class($this), $htmlentities) .
                        '</b> ' . Tools::displayError('is invalid.');
                        $validationError = true;
                    }

                }

                if (!$validationError) {

                    if (isset($data['copy_post']) && !$data['copy_post']) {
                        continue;
                    }

                    if ($field == 'passwd') {

                        if ($value = Tools::getValue($field)) {
                            $this->{$field}

                            = Tools::hash($value);
                        }

                    } else {
                        $this->{$field}

                        = $value;
                    }

                }

            }

        }

        return $errors;
    }

    public function validateFieldsRequiredDatabase($htmlentities = true) {

        $this->cacheFieldsRequiredDatabase();
        $errors = [];
        $requiredFields = (isset(static::$fieldsRequiredDatabase[get_class($this)])) ? static::$fieldsRequiredDatabase[get_class($this)] : [];

        foreach ($this->def['fields'] as $field => $data) {

            if (!in_array($field, $requiredFields)) {
                continue;
            }

            if (!method_exists('Validate', $data['validate'])) {
                throw new PhenyxException('Validation function not found. ' . $data['validate']);
            }

            $value = Tools::getValue($field);

            if (empty($value)) {
                $errors[$field] = sprintf(Tools::displayError('The field %s is required.'), static::displayFieldName($field, get_class($this), $htmlentities));
            }

        }

        return $errors;
    }

    public function getFieldsRequiredDatabase($all = false) {

        return Db::getInstance()->executeS(
			(new DbQuery())
				->select('id_required_field, object_name, field_name')
				->from('required_field')
				->where((!$all ? 'object_name = \'' . pSQL(get_class($this)) . '\'' : '1'))
		);
    }

    public function cacheFieldsRequiredDatabase($all = true) {

        if (!is_array(static::$fieldsRequiredDatabase)) {
            $fields = $this->getfieldsRequiredDatabase((bool) $all);

            if ($fields) {

                foreach ($fields as $row) {
                    static::$fieldsRequiredDatabase[$row['object_name']][(int) $row['id_required_field']] = pSQL($row['field_name']);
                }

            } else {
                static::$fieldsRequiredDatabase = [];
            }

        }

    }

    public function addFieldsRequiredDatabase($fields) {

        if (!is_array($fields)) {
            return false;
        }
        $result = Db::getInstance()->execute(
			(new DbQuery())
				->type('DELETE')
				->from('required_field')
				->where('object_name = \'' . get_class($this) . '\'')
		);
        if(!$result) {
            return false;
        }

        foreach ($fields as $field) {

            if (!Db::getInstance()->insert('required_field', ['object_name' => get_class($this), 'field_name' => pSQL($field)])) {
                return false;
            }

        }

        return true;
    }

    public function clearCache($all = false) {

        if ($all) {
            Cache::clean('PhenyxObjectModel_' . $this->def['classname'] . '_*');
        } else
        if ($this->id) {
            Cache::clean('PhenyxObjectModel_' . $this->def['classname'] . '_' . (int) $this->id . '_*');
        }

    }
    
    public function deleteImage($forceDelete = false) {

        if (!$this->id) {
            return false;
        }

        if ($forceDelete) {

            if ($this->image_dir) {

                if (file_exists($this->image_dir . $this->id . '.' . $this->image_format)
                    && !unlink($this->image_dir . $this->id . '.' . $this->image_format)) {
                    return false;
                }

            }

            if (file_exists(_EPH_TMP_IMG_DIR_ . $this->def['table'] . '_' . $this->id . '.' . $this->image_format)
                && !unlink(_EPH_TMP_IMG_DIR_ . $this->def['table'] . '_' . $this->id . '.' . $this->image_format)) {
                return false;
            }

            if (file_exists(_EPH_TMP_IMG_DIR_ . $this->def['table'] . '_mini_' . $this->id . '.' . $this->image_format)
                && !unlink(_EPH_TMP_IMG_DIR_ . $this->def['table'] . '_mini_' . $this->id . '.' . $this->image_format)) {
                return false;
            }

            $types = ImageType::getImagesTypes();

            foreach ($types as $imageType) {

                if (file_exists($this->image_dir . $this->id . '-' . stripslashes($imageType['name']) . '.' . $this->image_format)
                    && !unlink($this->image_dir . $this->id . '-' . stripslashes($imageType['name']) . '.' . $this->image_format)) {
                    return false;
                }

            }

        }

        return true;
    }

    public static function existsInDatabase($idEntity, $table) {

        $row = Db::getInstance()->getRow(
			(new DbQuery())
				->select('`id_' . bqSQL($table) . '` as id')
				->from(bqSQL($table), 'e')
				->where('e.`id_' . bqSQL($table) . '` = ' . (int) $idEntity)
		);
    
        return isset($row['id']);
    }

    public static function isCurrentlyUsed($table = null, $hasActiveColumn = false) {

        if ($table === null) {
            $table = static::$definition['table'];
        }

        $query = new DbQuery();
        $query->select('`id_' . bqSQL($table) . '`');
        $query->from($table);

        if ($hasActiveColumn) {
            $query->where('`active` = 1');
        }

        return (bool) Db::getInstance()->getValue($query);
    }

    public function hydrate(array $data, $idLang = null) {

        $this->id_lang = $idLang;

        if (isset($data[$this->def['primary']])) {
            $this->id = $data[$this->def['primary']];
        }

        foreach ($data as $key => $value) {

            if (property_exists($this, $key)) {
                $this->$key = $value;
            }

        }

    }

    public function hydrateMultilang(array $data) {

        foreach ($data as $row) {

            if (isset($row[$this->def['primary']])) {
                $this->id = $row[$this->def['primary']];
            }

            foreach ($row as $key => $value) {

                if (array_key_exists($key, $this)) {

                    if (!empty($this->def['fields'][$key]['lang']) && !empty($row['id_lang'])) {
                        // Multilang

                        if (!is_array($this->$key)) {
                            $this->$key = [];
                        }

                        $this->$key[(int) $row['id_lang']] = $value;
                    } else {
                        // Normal

                        if (array_key_exists($key, $this)) {
                            $this->$key = $value;
                        }

                    }

                }

            }

        }

    }

    public static function hydrateCollection($class, array $datas, $idLang = null) {

        if (!class_exists($class)) {
            throw new PhenyxException("Class '$class' not found");
        }

        $collection = [];
        $rows = [];

        if ($datas) {
            $definition = PhenyxObjectModel::getDefinition($class);

            if (!array_key_exists($definition['primary'], $datas[0])) {
                throw new PhenyxException("Identifier '{$definition['primary']}' not found for class '$class'");
            }

            foreach ($datas as $row) {
                // Get object common properties
                $id = $row[$definition['primary']];

                if (!isset($rows[$id])) {
                    $rows[$id] = $row;
                }

                // Get object lang properties

                if (isset($row['id_lang']) && !$idLang) {

                    foreach ($definition['fields'] as $field => $data) {

                        if (!empty($data['lang'])) {

                            if (!is_array($rows[$id][$field])) {
                                $rows[$id][$field] = [];
                            }

                            $rows[$id][$field][$row['id_lang']] = $row[$field];
                        }

                    }

                }

            }

        }

        foreach ($rows as $row) {
            $obj = new $class();
            $obj->hydrate($row, $idLang);
            $collection[] = $obj;
        }

        return $collection;
    }

    public static function getDefinition($class, $field = null) {

        if (is_object($class)) {
            $class = get_class($class);
        }

        if ($field === null) {
            $cacheId = 'PhenyxObjectModel_def_' . $class;
        }

        if ($field !== null || !Cache::isStored($cacheId)) {
            $reflection = new ReflectionClass($class);

            if (!$reflection->hasProperty('definition')) {
                return false;
            }

            $definition = $reflection->getStaticPropertyValue('definition');

            $definition['classname'] = $class;

            if (!empty($definition['multilang'])) {
                $definition['associations'][PhenyxCollection::LANG_ALIAS] = [
                    'type'          => static::HAS_MANY,
                    'field'         => $definition['primary'],
                    'foreign_field' => $definition['primary'],
                ];
            }

            if ($field) {
                return isset($definition['fields'][$field]) ? $definition['fields'][$field] : null;
            }

            Cache::store($cacheId, $definition);

            return $definition;
        }

        return Cache::retrieve($cacheId);
    }

    public function getFieldByLang($fieldName, $idLang = null) {

        $definition = PhenyxObjectModel::getDefinition($this);
        
        if ($definition && isset($definition['fields'][$fieldName])) {
            $field = $definition['fields'][$fieldName];
            
            if (isset($field['lang']) && $field['lang']) {

                if (is_array($this->{$fieldName})) {
                    return $this->{$fieldName}

                    [$idLang ?: Context::getContext()->language->id];
                }

            }

            return $this->{$fieldName};
        } else {
            throw new PhenyxException('Could not load field from definition.');
        }

    }

    public function setFieldsToUpdate(array $fields) {

        $this->update_fields = $fields;
    }

    public static function enableCache() {

        PhenyxObjectModel::$cache_objects = true;
    }

    public static function disableCache() {

        PhenyxObjectModel::$cache_objects = false;
    }
    
    public static function updateMultiTable($classname, $data, $where = '', $specific_where = '')  {
        
        $def = PhenyxObjectModel::getDefinition($classname);
        $update_data = array();
        foreach ($data as $field => $value) {
            if (!isset($def['fields'][$field])) {
                continue;
            }

            if ($value === null && !empty($def['fields'][$field]['allow_null'])) {
                $update_data[] = "a.$field = NULL";
            } else {
                $update_data[] = "a.$field = '$value'";
            }
        }

        $sql = 'UPDATE '._DB_PREFIX_.$def['table'].' a
				SET '.implode(', ', $update_data).
                (!empty($where) ? ' WHERE '.$where : '');

        return Db::getInstance()->execute($sql);
    }


    public static function createDatabase($className = null) {

        $success = true;

        if (empty($className)) {
            $className = get_called_class();
        }

        $definition = static::getDefinition($className);
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . bqSQL($definition['table']) . '` (';
        $sql .= '`' . $definition['primary'] . '` INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,';

        foreach ($definition['fields'] as $fieldName => $field) {

            if ($fieldName === $definition['primary']) {
                continue;
            }

            if (isset($field['lang']) && $field['lang']) {
                continue;
            }

            if (empty($field['db_type'])) {

                switch ($field['type']) {
                case '1':
                    $field['db_type'] = 'INT(11) UNSIGNED';
                    break;
                case '2':
                    $field['db_type'] .= 'TINYINT(1)';
                    break;
                case '3':
                    (isset($field['size']) && $field['size'] > 256)
                    ? $field['db_type'] = 'VARCHAR(256)'
                    : $field['db_type'] = 'VARCHAR(512)';
                    break;
                case '4':
                    $field['db_type'] = 'DECIMAL(20,6)';
                    break;
                case '5':
                    $field['db_type'] = 'DATETIME';
                    break;
                case '6':
                    $field['db_type'] = 'TEXT';
                    break;
                }

            }

            $sql .= '`' . $fieldName . '` ' . $field['db_type'];

            if (isset($field['required'])) {
                $sql .= ' NOT NULL';
            }

            if (isset($field['default'])) {
                $sql .= ' DEFAULT \'' . $field['default'] . '\'';
            }

            $sql .= ',';
        }

        $sql = trim($sql, ',');
        $sql .= ')';

        try {
            $success &= Db::getInstance()->execute($sql);
        } catch (\PhenyxDatabaseExceptionException $exception) {
            static::dropDatabase($className);

            return false;
        }

        if (isset($definition['multilang']) && $definition['multilang']) {
            $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . bqSQL($definition['table']) . '_lang` (';
            $sql .= '`' . $definition['primary'] . '` INT(11) UNSIGNED NOT NULL,';

            foreach ($definition['fields'] as $fieldName => $field) {

                if ($fieldName === $definition['primary'] || !(isset($field['lang']) && $field['lang'])) {
                    continue;
                }

                $sql .= '`' . $fieldName . '` ' . $field['db_type'];

                if (isset($field['required'])) {
                    $sql .= ' NOT NULL';
                }

                if (isset($field['default'])) {
                    $sql .= ' DEFAULT \'' . $field['default'] . '\'';
                }

                $sql .= ',';
            }

            $sql .= '`id_lang` INT(11) NOT NULL,';


            $sql .= 'PRIMARY KEY (`' . bqSQL($definition['primary']) . '`, `id_lang`)';

            $sql .= ')';

            try {
                $success &= Db::getInstance()->execute($sql);
            } catch (\PhenyxDatabaseExceptionException $exception) {
                static::dropDatabase($className);

                return false;
            }

        }       

        return $success;
    }

    
    public static function dropDatabase($className = null) {

        $success = true;

        if (empty($className)) {
            $className = get_called_class();
        }

        $definition = \PhenyxObjectModel::getDefinition($className);

        $success &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . bqSQL($definition['table']) . '`');

        if (isset($definition['multilang']) && $definition['multilang']) {
            $success &= Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . bqSQL($definition['table']) . '_lang`');
        }

        return $success;
    }

    public static function getDatabaseColumns($className = null) {

        if (empty($className)) {
            $className = get_called_class();
        }

        $definition = \PhenyxObjectModel::getDefinition($className);

        $sql = 'SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=\'' . _DB_NAME_ . '\' AND TABLE_NAME=\'' . _DB_PREFIX_ . pSQL($definition['table']) . '\'';

        return Db::getInstance()->executeS($sql);
    }

    public static function createColumn($name, $columnDefinition, $className = null) {

        if (empty($className)) {
            $className = get_called_class();
        }

        $definition = static::getDefinition($className);
        $sql = 'ALTER TABLE `' . _DB_PREFIX_ . bqSQL($definition['table']) . '`';
        $sql .= ' ADD COLUMN `' . bqSQL($name) . '` ' . bqSQL($columnDefinition['db_type']) . '';

        if ($name === $definition['primary']) {
            $sql .= ' INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT';
        } else {

            if (isset($columnDefinition['required']) && $columnDefinition['required']) {
                $sql .= ' NOT NULL';
            }

            if (isset($columnDefinition['default'])) {
                $sql .= ' DEFAULT "' . pSQL($columnDefinition['default']) . '"';
            }

        }

        return (bool) Db::getInstance()->execute($sql);
    }

    public static function createMissingColumns($className = null) {

        if (empty($className)) {
            $className = get_called_class();
        }

        $success = true;

        $definition = static::getDefinition($className);
        $columns = static::getDatabaseColumns();

        foreach ($definition['fields'] as $columnName => $columnDefinition) {
            //column exists in database
            $exists = false;

            foreach ($columns as $column) {

                if ($column['COLUMN_NAME'] === $columnName) {
                    $exists = true;
                    break;
                }

            }

            if (!$exists) {
                $success &= static::createColumn($columnName, $columnDefinition);
            }

        }

        return $success;
    }
    
    public function l($string, $idLang = null, Context $context = null) {

       
       $class = get_class($this);
		if (strtolower(substr($class, -4)) == 'core') {
            $class = substr($class, 0, -4);
        }
        
       return Translate::getClassTranslation($string, $class);
    }
	
	

}
