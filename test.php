<?php
include 'HeFetchUrl.class.php';

$userName = "2011960509";
$password = "123456";

$ch = new HeFetchUrl();


$fields = array(
		'username'=>$userName, 'password'=>$password,
		'identity'=>'student', 'role'=>'1');
$ch->set_post_data($fields);

iconv("GBK", "UTF-8//IGNORE",
			$ch->post("http://202.197.224.134:8083/jwgl/logincheck.jsp"));

$ch->post("http://202.197.224.134:8083/jwgl/index1.jsp");

$str = iconv("GBK", "UTF-8//IGNORE",
		$ch->get("http://202.197.224.134:8083/jwgl/xk/xk1_kb_gr.jsp?xq1=01"));


//print_r($ch->get_http_code());
print_r(htmlspecialchars($str));

exit();

echo "\r\n\r\n";
echo $ch->get("http://localhost/test/cookie_output.php");
