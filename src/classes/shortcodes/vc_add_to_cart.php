<?php

extract(Composer::shortcode_atts(array(
            'id_product' => null,
            'style' => ''
                ), $atts));
if(empty($id_product)) return false;

$exid = str_replace('-',',',$id_product);
$exid = substr($exid,strlen($exid)-1) == ',' ? substr($exid,0,-1) : $exid;
$products = ContentAnyWhere::getSelectedProducts($exid);

if(empty($products)) return false;

$context = Context::getContext();

$context->smarty->assign(
        array(
            'products' => $products,
            'style' => $style,            
        )
);
$template_file_name = Composer::getTPLPath('vc_add_to_cart.tpl');
$out_put = $context->smarty->fetch($template_file_name);


echo $out_put;