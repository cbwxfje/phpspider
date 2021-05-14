<?php
header("Content-Type:text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
ini_set("memory_limit", "10240M");
require_once __DIR__ . '/../autoloader.php';
use phpspider\core\phpspider;
use phpspider\core\requests;
use phpspider\core\selector;

/* Do NOT delete this comment */
/* 不要删除这段注释 */

$url = file_get_contents(__DIR__ . '/../demo/url.txt');
$html = requests::get($url);
// $html = requests::get('https://alim2.com/collections/hot-sale/products/sexy-slim-sleeveless-white-dress');
// $html = requests::get('https://alipepe.com/collections/hot-sale/products/2021-new-european-and-american-plaid-long-sleeved-stitching-round-neck-t-shirt-top-233');
// $html = requests::get('https://fansinally.com/collections/hot-sale/products/fashion-trends-lace-loose-short-sleeved-dress');
// $html = requests::get('https://alim2.com/collections/hot-sale/products/sexy-slim-sleeveless-white-dress?variant=39664776413334');

$imgs = selector::select($html,'//div[contains(@class,"product__main-photos")]//div[contains(@class,"image-wrap")]//noscript//img');
$goods_name = selector::select($html,'//div[contains(@class,"product-single__meta")]//h1');
$market_price = selector::select($html,'//div[contains(@class,"product-single__meta")]//span[contains(@class,"product__price--compare")]');
$sale_price = selector::select($html,'//div[contains(@class,"product-single__meta")]//span[contains(@class,"on-sale")]');
$attrs = selector::select($html,'//div[contains(@class,"product-single__meta")]//div[contains(@class,"product-single__description")]');
$title = selector::select($html,'//html[contains(@class,"no-js")]//head//title');
$goods_desc = selector::select($html,'//div[contains(@class,"tw-col-span-12")]//div[contains(@class,"product_detail_description_content")]');

// $frm = selector::select($html,'//form[contains(@class,"product-single__form")]');
/*$prg='/<fieldset .*?name="(.*?)".*?>/is';
preg_match_all($prg,$frm,$mth);
for($i=0;$i<count($mth[1]);$i++){
	echo $mth[1][$i];
}
*/

$goods_attrs_html = selector::select($html,'//form[contains(@class,"product-single__form")]//fieldset');
$goods_attrs = array();
foreach($goods_attrs_html as $gk=>$ga){
	$p='/<input .*?checked="checked" .*value="(.*?)" .*?name="(.*?)".*?>/is';
	preg_match_all($p,$ga,$mt);
	for($i=0;$i<count($mt[1]);$i++){
		$goods_attrs[$mt[2][$i]] = $mt[1][$i];
	}
}


if(!is_array($imgs)){
	$imgs_arr[] = "https:".$imgs;
}else{
    $imgarr = array();
    foreach($imgs as $k=>$imgv){
        $imgarr[$k] = "https:".$imgv;
    }
	$imgs_arr = $imgarr;
}

$shopifydata['imgs'] = $imgs_arr; //主图和多张详图
$shopifydata['goods_name'] = trim($goods_name); //商品名称
$shopifydata['market_price'] = trim(str_replace(array('＄','￥','￡','$'),'',$market_price)); //市场价
$shopifydata['sale_price'] = trim(str_replace(array('＄','￥','￡','$'),'',$sale_price)); //销售价
$weight = substr($attrs,strrpos($attrs,'Weight'),25);

$weight = trim(strip_tags($weight));
preg_match('/\d+/', $weight,$match);
$shopifydata['weight'] = $match[0]; //weight

$sku = substr($attrs,strrpos($attrs,'SKU'),30);
$sku = trim(strip_tags($sku));
preg_match('/^SKU(.*?)$/is', $sku,$skumatch);
$sku_str = mb_ereg_replace('：','',$skumatch[1]);
$sku_str = str_replace(':', '', $sku_str);
$shopifydata['sku'] = $sku_str; //sku

$shopifydata['meta_title'] = trim($title); //meta_title
$metas = array();
$preg='/<meta .*?name="(.*?)"(.*?)>/is';
preg_match_all($preg,$html,$arr);
for($i=0;$i<count($arr[1]);$i++){
	$metas[$i]['name'] = $arr[1][$i];
	$metas[$i]['content'] = $arr[2][$i];
}
foreach($metas as $v){
	if($v['name'] == 'description'){
		$shopifydata['meta_description'] = $v['content']; //meta_description
	}
}
$shopifydata['meta_keywords'] = $title; // meta_keywords
$shopifydata['goods_desc'] = $goods_desc; //goods_desc
$shopifydata['goods_attrs'] = $goods_attrs; //goods_attrs


//echo "<pre>";
//print_r($shopifydata);
//echo count($shopifydata);