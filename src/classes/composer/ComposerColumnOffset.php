<?php

/**
 * @property mixed data
 */
class ComposerColumnOffset extends Composer {

	protected $settings = [];
	protected $value = '';
    protected $data;
	protected $size_types = [
		'lg' => 'Large',
		'md' => 'Medium',
		'sm' => 'Small',
		'xs' => 'Extra small',
	];
	protected $column_width_list = [];

	public function __construct($settings, $value) {

		$this->settings = $settings;
		$this->value = $value;

		$this->column_width_list = [
			$this->l('1 column - 1/12')    => '1',
			$this->l('2 columns - 1/6')    => '2',
			$this->l('3 columns - 1/4')    => '3',
			$this->l('4 columns - 1/3')    => '4',
			$this->l('5 columns - 5/12')   => '5',
			$this->l('6 columns - 1/2')    => '6',
			$this->l('7 columns - 7/12')   => '7',
			$this->l('8 columns - 2/3')    => '8',
			$this->l('9 columns - 3/4')    => '9',
			$this->l('10 columns - 5/6')   => '10',
			$this->l('11 columns - 11/12') => '11',
			$this->l('12 columns - 1/1')   => '12',
		];
	}

	public function render() {

        global $smarty;
        $context = Context::getContext();
        
        $jsDef = [
            'inherit' =>$this->l('Inherit: '),
		    'inherit_default' =>$this->l('Inherit from default')
        ];
       
		$data = $context->smarty->createTemplate(_EPH_COMPOSER_DIR_  .  'column_offset/template.tpl');
        $data->assign(
		  [
			'settings' => $this->settings,
			'value'    => $this->value,
			'data'     => $this->valueData(),
			'sizes'    => $this->size_types,
			'param'    => $this,
            'jsDef'    => Tools::jsonEncode($jsDef)
          ]
		);
        return $data->fetch();
		
	}

	public function valueData() {

		if (!isset($this->data)) {
			$this->data = preg_split('/\s+/', $this->value);
		}

		return $this->data;
	}

	public function sizeControl($size) {

		if ($size === 'sm') {
			return '<span class="vc_description">' . $this->l('Default value from width attribute') . '</span>';
		}

		$empty_label = $size === 'xs' ? '' : $this->l('Inherit from smaller');
		$output = '<select name="vc_col_' . $size . '_size" class="vc_column_offset_field" data-type="size-' . $size . '">'
			. '<option value="" style="color: #ccc;">' . $empty_label . '</option>';

		foreach ($this->column_width_list as $label => $index) {
			$value = 'vc_col-' . $size . '-' . $index;
			$output .= '<option value="' . $value . '"' . (in_array($value, $this->data) ? ' selected="true"' : '') . '>' . $label . '</option>';
		}

		$output .= '</select>';
		return $output;
	}

	public function offsetControl($size) {

		$prefix = 'vc_col-' . $size . '-offset-';
		$empty_label = $size === 'xs' ? $this->l('No offset') : $this->l('Inherit from smaller');
		$output = '<select name="vc_' . $size . '_offset_size" class="vc_column_offset_field" data-type="offset-' . $size . '">'
			. '<option value="" style="color: #ccc;">' . $empty_label . '</option>'
			. ($size === 'xs' ? '' :
			'<option value="' . $prefix . '0" style="color: #ccc;">' . $this->l('No offset') . '</option>'
		);

		foreach ($this->column_width_list as $label => $index) {
			$value = $prefix . $index;
			$output .= '<option value="' . $value . '"' . (in_array($value, $this->data) ? ' selected="true"' : '') . '>' . $label . '</option>';
		}

		$output .= '</select>';
		return $output;
	}

}
