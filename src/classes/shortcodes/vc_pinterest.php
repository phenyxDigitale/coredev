<?php

// return ;
// print_r(Context::getContext()->controller->php_self);
$page_type = Context::getContext()->controller->php_self;
$id_lang = (int)Context::getContext()->language->id;
$linkobj = Composer::GetLinkobj();
$vcparams = $_GET;
unset($vcparams['isolang'], $vcparams['controller']);
if($page_type == 'product'){
	$id_product = Tools::getvalue('id_product');
	$page_link = $linkobj->getProductLink((int)$id_product, null, null, null, (int)$id_lang);
}elseif($page_type == 'category'){
	$id_category = Tools::getvalue('id_category');
	$page_link = $linkobj->getCategoryLink($id_category, null, (int)$id_lang);
}elseif($page_type == 'supplier'){
	$id_supplier = Tools::getvalue('id_supplier');
	$page_link = $linkobj->getSupplierLink($id_supplier, null, (int)$id_lang);
}elseif($page_type == 'manufacturer'){
	$id_manufacturer = Tools::getvalue('id_manufacturer');
	$page_link = $linkobj->getManufacturerLink($id_manufacturer, null, (int)$id_lang);
}elseif($page_type == 'cms'){
	$id_cms = Tools::getvalue('id_cms');
	$id_cms_category = Tools::getvalue('id_cms_category');
	if(isset($id_cms)){
		$page_link = $linkobj->getCMSLink($id_cms, null, false, (int)$id_lang);
	}elseif(isset($id_cms_category)){
		$page_link = $linkobj->getCMSCategoryLink($id_cms_category,null,(int)$id_lang);
	}
}elseif(isset($vcparams['fc']) && $vcparams['fc'] == 'plugin'){
	$plugin = Validate::isPluginName(Tools::getValue('plugin')) ? Tools::getValue('plugin') : '';
	if(!empty($plugin))
	{
		unset($vcparams['fc'], $vcparams['plugin']);
		$page_link = $linkobj->getPluginLink($plugin, $page_type, $vcparams, null, (int)$id_lang);
	}else{
		$page_link = Context::getContext()->company->getBaseURL();
	}
}else{
	$vcpage_link = $linkobj->getPageLink($page_type, null, $id_lang, $vcparams);
	if(empty($vcpage_link)){
		$page_link = Context::getContext()->company->getBaseURL();
	}else{
		$page_link = $vcpage_link;
	}
}

$type = $params = $annotation = '';
extract(Composer::shortcode_atts(array(
	'type' => 'horizontal'
), $atts));

$params .= ( $type != '' ) ? ' size="'.$type.'" ' : '';
$params .= ( $annotation != '' ) ? ' annotation="'.$annotation.'"' : '';



$url = rawurlencode($page_link);
$ssl_enable = Configuration::get('EPH_SSL_ENABLED');
$base = ($ssl_enable == 1) ? 'https://' : 'http://';
$url = str_replace("http",$base,$url);

$media = '';

$description = '';

$css_class =  'wpb_pinterest wpb_content_element wpb_pinterest_type_' . $type;
$output .=  '<div class="'.$css_class.'">';
$output .= '<a href="http://pinterest.com/pin/create/button/?url='.$url.$media.$description.'" class="pin-it-button" count-layout="'.$type.'"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
$output .= '</div>'.$this->endBlockComment('wpb_pinterest')."\n";

echo $output;