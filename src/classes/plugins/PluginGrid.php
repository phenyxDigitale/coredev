<?php

/**
 * Class PluginGridCore
 *
 * @since 1.9.1.0
 */
abstract class PluginGrid extends Plugin {

    // @codingStandardsIgnoreStart
    protected $_employee;
    /** @var array of strings graph data */
    protected $_values = [];
    
    protected $_script;
    /** @var int total number of values **/
    protected $_totalCount = 0;
    /**@var string graph titles */
    protected $_title;
    /**@var int start */
    protected $_start;
    /**@var int limit */
    protected $_limit;
    /**@var string column name on which to sort */
    protected $_sort = null;
    /**@var string sort direction DESC/ASC */
    protected $_direction = null;
    /** @var PluginGridEngine grid engine */
    protected $_render;
    
    protected $_id_lang;
    
    protected $_columns;
    // @codingStandardsIgnoreEnd

    /**
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    abstract protected function getData();

    /**
     * @param int $idEmployee
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setEmployee($idEmployee) {

        $this->_employee = new User($idEmployee);
    }

    /**
     * @param int $idLang
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function setLang($idLang) {

        $this->_id_lang = $idLang;
    }

    /**
     * @param $render
     * @param $type
     * @param $width
     * @param $height
     * @param $start
     * @param $limit
     * @param $sort
     * @param $dir
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function create($render, $action) {

        if (!Validate::isPluginName($render)) {
            die(Tools::displayError());
        }

        if (!file_exists($file = _EPH_ROOT_DIR_ . '/includes/plugins/' . $render . '/' . $render . '.php')) {
            die(Tools::displayError());
        }

        require_once $file;
        $this->_render = new $render();
        $this->_sort = $this->default_sort_column;
        $this->_direction = $this->default_sort_direction;
        if($action == 'getStatisticFields') {
            die(Tools::jsonEncode($this->_columns));
        }
        if($action == 'getStatisticRequest') {
            $this->getData();
        }

       
    }
    
    public function generateParaGridScript() {
        
       
        $paragrid = new ParamGrid($this->name, 'Stats'.$this->name, $this->name.'Table', 'id');
        $paragrid->requestModel = '{
            location: "remote",
            dataType: "json",
            method: "GET",
            recIndx: "id_image",
            url: "/app/grider.php",
            postData: function () {
                return {
                    action: "getStatisticRequest",
                    plugin: "'.$this->name.'",
                    render: "gridhtml",
                    id_user: '.$this->context->user->id.',
                    id_lang: '.$this->context->language->id.',
                    ajax: 1
                };
            },
            getData: function (dataJSON) {
					return { data: dataJSON };
            }
        }';
        $paragrid->gridFunction = [
			'get'.$this->name.'Fields()'                  => '
        	var result ;
            $.ajax({
                type: \'POST\',
                url: "/app/grider.php",
                data: {
                    action: \'getStatisticFields\',
                    plugin: "'.$this->name.'",
                    render: "gridhtml",
                    id_user: '.$this->context->user->id.',
                    id_lang: '.$this->context->language->id.',
                    ajax: true
                },
                async: false,
                dataType: \'json\',
                success: function success(data) {
                    result = data;
                }
            });
            return result;',
			'get'.$this->name.'Request()' => '
            var result;
            $.ajax({
            type: \'POST\',
            url: "/app/grider.php",
            data: {
                action: \'getStatisticRequest\',
				plugin: "'.$this->name.'",
                render: "gridhtml",
                id_user: '.$this->context->user->id.',
                id_lang: '.$this->context->language->id.',
                ajax: true
            },
            async: false,
            dataType: \'json\',
            success: function (data) {
                result = data;
            }
        });
        return result;',
		];
        
       
        
        $paragrid->heightModel = 700;
        
        $paragrid->complete = 'function(){
            window.dispatchEvent(new Event(\'resize\'));
        }';       
        
        $paragrid->pageModel = [
            'type'       => '\'local\'',
            'rPP'        => 40,
            'rPPOptions' => [40, 50, 100, 200, 500],
        ];

        $paragrid->title  = '\'' . $this->displayName . '\'';
        
        $option = $paragrid->generateParaGridOption();
		$script = $paragrid->generateParagridScript();

        $this->_script = '<script type="text/javascript">' . PHP_EOL . $script . PHP_EOL . '</script>';
        
        return $this->_script;
    }
    
   

    /**
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function render() {

        $this->_render->render();
    }

    /**
     * @param array $params
     *
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function engine($params) {

        if (!($render = Configuration::get('EPH_STATS_GRID_RENDER'))) {
            return Tools::displayError('No grid engine selected');
        }

        if (!Validate::isPluginName($render)) {
            die(Tools::displayError());
        }

        if (!file_exists(_EPH_ROOT_DIR_ . '/includes/plugins/' . $render . '/' . $render . '.php')) {
            return Tools::displayError('Grid engine selected is unavailable.');
        }

        
        $params['name'] = $this->name;
        $params['paragridScript'] = $this->generateParaGridScript();

        require_once _EPH_ROOT_DIR_ . '/includes/plugins/' . $render . '/' . $render . '.php';

        return call_user_func([$render, 'hookGridEngine'], $params, $grider);
    }

    /**
     * @param $datas
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    protected function csvExport($datas) {

        $this->_sort = $datas['defaultSortColumn'];
        $this->setLang(Context::getContext()->language->id);
        $this->getData();

        $layers = isset($datas['layers']) ? $datas['layers'] : 1;

        if (isset($datas['option'])) {
            $this->setOption($datas['option'], $layers);
        }

        if (count($datas['columns'])) {

            foreach ($datas['columns'] as $column) {
                $this->_csv .= $column['header'] . ';';
            }

            $this->_csv = rtrim($this->_csv, ';') . "\n";

            foreach ($this->_values as $value) {

                foreach ($datas['columns'] as $column) {
                    $this->_csv .= $value[$column['dataIndex']] . ';';
                }

                $this->_csv = rtrim($this->_csv, ';') . "\n";
            }

        }

        $this->_displayCsv();
    }

    /**
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    protected function _displayCsv() {

        if (ob_get_level() && ob_get_length() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $this->displayName . ' - ' . time() . '.csv"');
        echo $this->_csv;
        exit;
    }

    /**
     * @return string
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getDate() {

        return PluginGraph::getDateBetween($this->_employee);
    }

    /**
     * @return mixed
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function getLang() {

        return $this->_id_lang;
    }

}
