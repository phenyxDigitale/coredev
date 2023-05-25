<?php
$vc_manager = ephenyx_manager();
/** Landing page template */
$data               = array();
$data['name']       = $vc_manager->l('Landing Page');
$data['image_path'] = '/content/themes/blacktie/vc/templates/landing_page.png' ;
$data['custom_class'] = ''; // default is ''
$data['content']    = <<<CONTENT
[vc_row][vc_column width="1/1"][vc_single_image border_color="grey" img_link_target="_self"][/vc_column][/vc_row][vc_row][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row]
CONTENT;

add_default_templates( $data );

/** Call to Action Page template */
$data               = array();
$data['name']       = $vc_manager->l('Call to Action Page');
$data['image_path'] = '/content/themes/blacktie/vc/templates/call_to_action_page.png' ;
$data['content']    = <<<CONTENT
[vc_row][vc_column width="1/1"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row]
CONTENT;

add_default_templates( $data );

/** Feature List template */
$data               = array();
$data['name']       = $vc_manager->l('Feature List');
$data['image_path'] = '/content/themes/blacktie/vc/templates/feature_list.png';
$data['content']    = <<<CONTENT
[vc_row][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][/vc_column][vc_column width="1/2"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][/vc_column][vc_column width="1/2"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][/vc_column][/vc_row]
CONTENT;

add_default_templates( $data );

/** Description Page template */
$data               = array();
$data['name']       = $vc_manager->l('Description Page');
$data['image_path'] = '/content/themes/blacktie/vc/templates/description_page.png';
$data['content']    = <<<CONTENT
[vc_row][vc_column width="1/1"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][vc_separator color="grey"][vc_single_image border_color="grey" img_link_target="_self"][vc_separator color="grey"][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][vc_button2 title="Text on the button" style="rounded" color="blue" size="md"][/vc_column][vc_column width="1/2"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][vc_button2 title="Text on the button" style="rounded" color="blue" size="md"][/vc_column][/vc_row]
CONTENT;

add_default_templates( $data );

/** Service List template */
$data               = array();
$data['name']       = $vc_manager->l('Service List');
$data['image_path'] = '/content/themes/blacktie/vc/templates/service_list.png';
$data['content']    = <<<CONTENT
[vc_row][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/1"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row][vc_row][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][vc_column width="1/3"][vc_single_image border_color="grey" img_link_target="_self"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][/vc_column][/vc_row]
CONTENT;

add_default_templates( $data );

/** Product Page template */
$data               = array();
$data['name']       = $vc_manager->l('Product Page');
$data['image_path'] = '/content/themes/blacktie/vc/templates/product_page.png';
$data['content']    = <<<CONTENT
[vc_row][vc_column width="1/1"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][vc_separator color="grey"][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][/vc_column][vc_column width="1/2"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][vc_button2 title="Text on the button" style="rounded" color="blue" size="md"][/vc_column][/vc_row][vc_row][vc_column width="1/2"][vc_single_image border_color="grey" img_link_target="_self"][/vc_column][vc_column width="1/2"][vc_column_text]I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.[/vc_column_text][vc_button2 title="Text on the button" style="rounded" color="blue" size="md"][/vc_column][/vc_row]
CONTENT;

add_default_templates( $data );


