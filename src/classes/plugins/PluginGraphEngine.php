<?php

/**
 * Class PluginGraphEngineCore
 *
 * @since 1.9.1.0
 */
class PluginGraphEngine extends Plugin {

    // @codingStandardsIgnoreStart
    protected $_type;
    protected $_width;
    protected $_height;
    protected $_values;
    protected $_legend;
    protected $_titles;
    // @codingStandardsIgnoreEnd

    /**
     * PluginGraphEngineCore constructor.
     *
     * @param null|string $type
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function __construct($type = null) {

        $this->_type = $type;
    }

    /**
     * @return bool
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public function install() {

        if (!parent::install()) {
            return false;
        }

        return Configuration::updateValue('EPH_STATS_RENDER', $this->name);
    }

    /**
     * @return array
     *
     * @since 1.9.1.0
     * @version 1.8.1.0 Initial version
     */
    public static function getGraphEngines() {

        $result = Db::getInstance(_EPH_USE_SQL_SLAVE_)->executeS(
            (new DbQuery())
                ->select('m.`name`')
                ->from('plugin', 'm')
                ->leftJoin('hook_plugin', 'hm', 'hm.`id_plugin` = m.`id_plugin`')
                ->leftJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`')
                ->where('h.`name` = \'displayAdminStatsGraphEngine\'')
        );

        $arrayEngines = [];

        foreach ($result as $plugin) {
            $instance = Plugin::getInstanceByName($plugin['name']);

            if (!$instance) {
                continue;
            }

            $arrayEngines[$plugin['name']] = [$instance->displayName, $instance->description];
        }

        return $arrayEngines;
    }

    public static function hookGraphEngine($params, $drawer) {

        static $divid = 1;

        if (strpos($params['width'], '%') !== false) {
            $params['width'] = (int) preg_replace('/\s*%\s*/', '', $params['width']) . '%';
        } else {
            $params['width'] = (int) $params['width'] . 'px';
        }

        $nvd3Func = [
            'line' => '
                nv.models.lineChart()',
            'pie'  => '
                nv.models.pieChart()
                    .x(function(d) { return d.label; })
                    .y(function(d) { return d.value; })
                    .showLabels(true)
                    .showLegend(false)',
        ];

        return '
        <div id="nvd3_chart_' . $divid . '" class="chart with-transitions">
            <svg style="width:' . $params['width'] . ';height:' . (int) $params['height'] . 'px"></svg>
        </div>
        <script>
            $.ajax({
            url: "' . addslashes($drawer) . '",
            dataType: "json",
            type: "GET",
            cache: false,
            headers: {"cache-control": "no-cache"},
            success: function(jsonData){
                nv.addGraph(function(){
                    var chart = ' . $nvd3Func[$params['type']] . ';

                    if (jsonData.axisLabels.xAxis != null)
                        chart.xAxis.axisLabel(jsonData.axisLabels.xAxis);
                    if (jsonData.axisLabels.yAxis != null)
                        chart.yAxis.axisLabel(jsonData.axisLabels.yAxis);

                    d3.select("#nvd3_chart_' . ($divid++) . ' svg")
                        .datum(jsonData.data)
                        .transition().duration(500)
                        .call(chart);

                    nv.utils.windowResize(chart.update);

                    return chart;
                });
            }
        });
        </script>';
    }

    public function createValues($values) {

        $this->_values = $values;
    }

    public function setSize($width, $height) {

        $this->_width = $width;
        $this->_height = $height;
    }

    public function setLegend($legend) {

        $this->_legend = $legend;
    }

    public function setTitles($titles) {

        $this->_titles = $titles;
    }

    public function draw() {

        $array = [
            'axisLabels' => ['xAxis' => isset($this->_titles['x']) ? $this->_titles['x'] : null, 'yAxis' => isset($this->_titles['y']) ? $this->_titles['y'] : null],
            'data'       => [],
        ];

        if (!isset($this->_values[0]) || !is_array($this->_values[0])) {
            $nvd3Values = [];

            if (Tools::getValue('type') == 'pie') {

                foreach ($this->_values as $x => $y) {
                    $nvd3Values[] = ['label' => $this->_legend[$x], 'value' => $y];
                }

                $array['data'] = $nvd3Values;
            } else {

                foreach ($this->_values as $x => $y) {
                    $nvd3Values[] = ['x' => $x, 'y' => $y];
                }

                $array['data'][] = ['values' => $nvd3Values, 'key' => $this->_titles['main']];
            }

        } else {

            foreach ($this->_values as $layer => $grossValues) {
                $nvd3Values = [];

                foreach ($grossValues as $x => $y) {
                    $nvd3Values[] = ['x' => $x, 'y' => $y];
                }

                $array['data'][] = ['values' => $nvd3Values, 'key' => $this->_titles['main'][$layer]];
            }

        }

        die(preg_replace('/"([0-9]+)"/', '$1', json_encode($array)));
    }

}
