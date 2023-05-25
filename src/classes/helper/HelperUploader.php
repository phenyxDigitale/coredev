<?php

/**
 * Class HelperUploader
 *
 * @since 1.8.1.0
 */
class HelperUploader extends PhenyxUploader {

    const DEFAULT_TEMPLATE_DIRECTORY = 'helpers/uploader';
    const DEFAULT_TEMPLATE = 'simple.tpl';
    const DEFAULT_AJAX_TEMPLATE = 'ajax.tpl';

    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';

    // @codingStandardsIgnoreStart
    private $_context;
    private $_drop_zone;
    private $_id;
    private $_files;
    private $_name;
    private $_max_files;
    private $_multiple;
    private $_post_max_size;
    protected $_template;
    private $_template_directory;
    private $_title;
    private $_url;
    private $_use_ajax;
    // @codingStandardsIgnoreEnd

    /**
     * @param Context $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setContext($value) {

        $this->_context = $value;

        return $this;
    }

    /**
     * @return Context
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getContext() {

        if (!isset($this->_context)) {
            $this->_context = Context::getContext();
        }

        return $this->_context;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setDropZone($value) {

        $this->_drop_zone = $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getDropZone() {

        if (!isset($this->_drop_zone)) {
            $this->setDropZone("$('#" . $this->getId() . "-add-button')");
        }

        return $this->_drop_zone;
    }

    /**
     * @param int $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setId($value) {

        $this->_id = (string) $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId() {

        if (!isset($this->_id) || trim($this->_id) === '') {
            $this->_id = $this->getName();
        }

        return $this->_id;
    }

    /**
     * @param string[] $value
     *
     * @return $this
     */
    public function setFiles($value) {

        $this->_files = $value;

        return $this;
    }

    /**
     * @return string[]
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getFiles() {

        if (!isset($this->_files)) {
            $this->_files = [];
        }

        return $this->_files;
    }

    /**
     * @param int $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setMaxFiles($value) {

        $this->_max_files = isset($value) ? (int)($value) : $value;

        return $this;
    }

    /**
     * @return int
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getMaxFiles() {

        return $this->_max_files;
    }

    /**
     * @param bool $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setMultiple($value) {

        $this->_multiple = (bool) $value;

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setName($value) {

        $this->_name = (string) $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getName() {

        return $this->_name;
    }

    /**
     * @param int $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setPostMaxSize($value) {

        $this->_post_max_size = $value;
        $this->setMaxSize($value);

        return $this;
    }

    /**
     * @return int
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getPostMaxSize() {

        if (!isset($this->_post_max_size)) {
            $this->_post_max_size = parent::getPostMaxSize();
        }

        return $this->_post_max_size;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setTemplate($value) {

        $this->_template = $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getTemplate() {

        if (!isset($this->_template)) {
            $this->setTemplate(static::DEFAULT_TEMPLATE);
        }

        return $this->_template;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setTemplateDirectory($value) {

        $this->_template_directory = $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getTemplateDirectory() {

        if (!isset($this->_template_directory)) {
            $this->_template_directory = static::DEFAULT_TEMPLATE_DIRECTORY;
        }

        return $this->_normalizeDirectory($this->_template_directory);
    }

    /**
     * @param string $template
     *
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getTemplateFile($template) {

        $file = fopen("testgetTemplateFile.txt","w");
        if (preg_match_all('/((?:^|[A-Z])[a-z]+)/', get_class($this->getContext()->controller), $matches) !== false) {
            $controllerName = strtolower($matches[0][1]);
        }

        if ($this->getContext()->controller instanceof PluginAdminController) {
            fwrite($file,"Yo Plugin!".PHP_EOL);
            fwrite($file,$this->getContext()->controller->getTemplatePath($template). $this->getTemplateDirectory() . $template.PHP_EOL);
            if(file_exists($this->_normalizeDirectory($this->getContext()->controller->getTemplatePath($template)) . $this->getTemplateDirectory() . $template)) {
            return $this->_normalizeDirectory($this->getContext()->controller->getTemplatePath($template)) . $this->getTemplateDirectory() . $template;
            }
        } else
        if ($this->getContext()->controller instanceof AdminController && isset($controllerName)
            && file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)) . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . DIRECTORY_SEPARATOR . $this->getTemplateDirectory() . $template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)) . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . DIRECTORY_SEPARATOR . $this->getTemplateDirectory() . $template;
        } else
        if (file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(1)) . $this->getTemplateDirectory() . $template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(1)) . $this->getTemplateDirectory() . $template;
        } else
        if (file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)) . $this->getTemplateDirectory() . $template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)) . $this->getTemplateDirectory() . $template;
        } else {
            return $this->getTemplateDirectory() . $template;
        }

    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setTitle($value) {

        $this->_title = $value;

        return $this;
    }

    /**
     * @return mixed
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getTitle() {

        return $this->_title;
    }

    /**
     * @param string $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setUrl($value) {

        $this->_url = (string) $value;

        return $this;
    }

    /**
     * @return string
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function getUrl() {

        return $this->_url;
    }

    /**
     * @param bool $value
     *
     * @return $this
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function setUseAjax($value) {

        $this->_use_ajax = (bool) $value;

        return $this;
    }

    /**
     * @return bool
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function isMultiple() {

        return (isset($this->_multiple) && $this->_multiple);
    }

    /**
     * @return mixed
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function render() {

        $adminWebpath = str_ireplace(_SHOP_CORE_DIR_, '', _EPH_ROOT_DIR_);
        $adminWebpath = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $adminWebpath);
        $boTheme = ((Validate::isLoadedObject($this->getContext()->employee)
            && $this->getContext()->employee->bo_theme) ? $this->getContext()->employee->bo_theme : 'default');

        if (!file_exists(_EPH_BO_ALL_THEMES_DIR_ . $boTheme . DIRECTORY_SEPARATOR . 'template')) {
            $boTheme = 'default';
        }

        $this->getContext()->controller->addJs(_EPH_JS_DIR_. 'jquery.iframe-transport.js');
        $this->getContext()->controller->addJs(_EPH_JS_DIR_.'jquery.fileupload.js');
        $this->getContext()->controller->addJs(_EPH_JS_DIR_.'jquery.fileupload-process.js');
        $this->getContext()->controller->addJs(_EPH_JS_DIR_. 'jquery.fileupload-validate.js');
        $this->getContext()->controller->addJs('https://cdn.ephenyx.io/vendor/spin.js');
        $this->getContext()->controller->addJs('https://cdn.ephenyx.io/vendor/ladda.js');

        if ($this->useAjax() && !isset($this->_template)) {
            $this->setTemplate(static::DEFAULT_AJAX_TEMPLATE);
        }

        $template = $this->getContext()->smarty->createTemplate(
            $this->getTemplateFile($this->getTemplate()),
            $this->getContext()->smarty
        );

        $template->assign(
            [
                'id'            => $this->getId(),
                'name'          => $this->getName(),
                'url'           => $this->getUrl(),
                'multiple'      => $this->isMultiple(),
                'files'         => $this->getFiles(),
                'title'         => $this->getTitle(),
                'max_files'     => $this->getMaxFiles(),
                'post_max_size' => $this->getPostMaxSizeBytes(),
                'drop_zone'     => $this->getDropZone(),
            ]
        );

        return $template->fetch();
    }

    /**
     * @return bool
     *
     * @since 1.8.1.0
     * @version 1.8.5.0
     */
    public function useAjax() {

        return (isset($this->_use_ajax) && $this->_use_ajax);
    }

}
