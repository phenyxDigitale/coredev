<?php

function loop_form_field( $settings, $value ) {
    $vc_manager = Context::getContext()->composer;
	$query_builder = new ComposerLoopSettings( $value );
	$params = $query_builder->getContent();
	$loop_info = '';
	foreach ( $params as $key => $param ) {
		$param_value = loop_get_value( $param );
		if ( ! empty( $param_value ) )
			$loop_info .= ' <b>' . $query_builder->getLabel( $key ) . '</b>: ' . $param_value . ';';
	}

	return '<div class="vc_loop">'
	  . '<input name="' . $settings['param_name'] . '" class="wpb_vc_param_value  ' . $settings['param_name'] . ' ' . $settings['type'] . '_field" type="hidden" value="' . $value . '"/>'
	  . '<a href="#" class="button vc_loop-build ' . $settings['param_name'] . '_button" data-settings="' . rawurlencode( json_encode( $settings['settings'] ) ) . '">' . $vc_manager->l('Build query') . '</a>'
	  . '<div class="vc_loop-info">' . $loop_info . '</div>'
	  . '</div>';
}

function loop_get_value( $param ) {
	$value = array();
	$selected_values = (array)$param['value'];
	if ( isset( $param['options'] ) && is_array( $param['options'] ) ) {
		foreach ( $param['options'] as $option ) {
			if ( is_array( $option ) && isset( $option['value'] ) ) {
				if ( in_array( ( ( $option['action'] === '-' ? '-' : '' ) . $option['value'] ), $selected_values ) ) $value[] = $option['action'] . $option['name'];
			} elseif ( is_array( $option ) && isset( $option[0] ) ) {
				if ( in_array( $option[0], $selected_values ) ) $value[] = $option[1];
			} elseif ( in_array( $option, $selected_values ) ) {
				$value[] = $option;
			}
		}
	} else {
		$value[] = $param['value'];
	}
	return implode( ', ', $value );
}



function build_loop_query( $query, $exclude_id = false ) {
	return ComposerLoopSettings::buildWpQuery( $query, $exclude_id );
}


function get_loop_suggestion() {
	$loop_suggestions = new ComposerLoopSuggestions(Tools::getValue('field'), Tools::getValue('q'), Tools::getValue('excludeIds'));
	$loop_suggestions->render();
	die();
}


function get_loop_settings_json() {
	$loop_settings = new ComposerLoopSettings(Tools::getValue('value'), Tools::getValue('settings'));
	$loop_settings->render();
	die();
}



Composer::$sds_action_hooks['wpb_get_loop_suggestion'] = 'get_loop_suggestion';
Composer::$sds_action_hooks['wpb_get_loop_settings'] = 'get_loop_settings_json';

function loop_include_templates() {
        
	require_once dirname(__FILE__).'/templates.html';
}

Composer::$sds_action_hooks['ps_admin_footer'] = 'loop_include_templates';



function set_loop_default_value($param) {
        
	if ( empty( $param['value']) && isset($param['settings'])) {
                
		$param['value'] = ComposerLoopSettings::buildDefault( $param );
	}
	return $param;
}
Composer::$sds_action_hooks['vc_mapper_attribute_loop'] = 'set_loop_default_value';
