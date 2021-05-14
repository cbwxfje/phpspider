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

$html = requests::get('https://grad.cnu.edu.cn/jz/tzgg.htm');
// $data = selector::select($html,'//div[contains(@index,"1")]');
$data = selector::select($html,'//div[contains(@class,"research_trends")]//ul//li//a');
// $data = selector::select($html,'//ul//li//a');
// var_dump($data);

foreach($data as $v){
	echo $v."<br/>";
}

// for($i=0;$i<count($data);$i++){
// 	echo $data[$i];
// 	sleep(3);
// }