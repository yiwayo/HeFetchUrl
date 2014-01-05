<?php
/**
 * @desc 用于在SAE，BAE等环境中抓去数据，可以模拟登录，支持cookies
 * @author   heimonsy(heimonsy@gmail.com)
 * @version  1.0.1
 */

/**
 * @desc 抓去类
 */
class HeFetchUrl
{
	private $curl_handle;
	
	private $useragent = "HeFetchUrl/1.0.1";
	
	private $request_url = "";
	
	private $cookies = array();
	
	private $headers = array();
	
	private $postfields = "";
	
	function __construct() {

		$this->init();
		//设置默认的选项
		$this->setopt(CURLOPT_FILETIME, true);
		$this->setopt(CURLOPT_FRESH_CONNECT, false);
		$this->setopt(CURLOPT_SSL_VERIFYPEER, false);
		$this->setopt(CURLOPT_SSL_VERIFYHOST, false);
		$this->setopt(CURLOPT_CLOSEPOLICY, CURLCLOSEPOLICY_LEAST_RECENTLY_USED);
		$this->setopt(CURLOPT_MAXREDIRS, 5);
		$this->setopt(CURLOPT_HEADER, true);
		$this->setopt(CURLOPT_RETURNTRANSFER, true);
		$this->setopt(CURLOPT_TIMEOUT, 4000);
		$this->setopt(CURLOPT_CONNECTTIMEOUT, 120);
		$this->setopt(CURLOPT_NOSIGNAL, true);
		$this->setopt(CURLOPT_REFERER, $this->request_url);
		$this->setopt(CURLOPT_USERAGENT, $this->useragent);
	}
	
	/**
	 * @desc 设置选项
	 * @param CURL选项 $option
	 * @param 选项的值     $value
	 */
	function setopt($option, $value) {
		curl_setopt($this->curl_handle, $option, $value);
	}
	
	
	
	function init() {
		if(!empty($this->curl_handle))
			$this->close();
		$this->curl_handle = curl_init();
	}
	
	/**
	 * GET一个URL
	 * @param 要获取URL页面  $url
	 * @return 返回页面内容
	 */
	function get($url) {
		$response = $this->_exec($url, "GET");
		return $response;
	}
	
	/**
	 * POST一个URL
	 * @param 要获取URL页面  $url
	 * @return 返回页面内容
	 */
	function post($url) {
		$response = $this->_exec($url, "POST");
		return $response;
	}
	
	/**
	 * 执行curl_exec
	 * @return 返回获取的内容
	 */
	private function _exec($url, $method="GET"){
		$this->setopt(CURLOPT_URL, $url);
		
		$this->_write_headers();
		$this->_write_cookies();
		
		if($method=='POST') {
			$this->setopt(CURLOPT_POST, true);
			$this->_write_postfields();
		}
		
		$response = curl_exec($this->curl_handle);
		$this->_analyse_cookies($response);
		return $response;
	}
	
	
	
	/**
	 * 设置需要post的数据
	 * @param array $post_data
	 */
	function set_post_data($post_data) {
		array_push($this->postfields, $post_data);
	}
	
	function close() {
		curl_close($this->curl_handle);
	}
	
	private function _write_headers() {
		$this->headers["Accept"] = "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
		$this->headers["Accept-Encoding"] = "gzip, deflate";
		$this->headers["Connection"] = "keep-alive";
		$temp_headers = array();
		foreach($temp_headers as $key=>$value)
			$temp_headers[] = $key . ": " . $value;
	
		$this->setopt(CURLOPT_HTTPHEADER, $temp_headers);
	}
	
	private function _write_cookies() {
		$temp_cookies  = "";
		foreach($this->cookies as $key => $value)
			$temp_cookies .= " " . $key . "=" . $value;
		
		$this->setopt(CURLOPT_COOKIE, trim($temp_cookies));
	}
	
	private function _write_postfields(){
		$this->setopt(CURLOPT_POSTFIELDS, http_build_query($this->postfields));
	}
	
	/**
	 * 获取cookies数组
	 * @return multitype:
	 */
	function get_cookies() {
		return $this->cookies;
	}
	
	/**
	 * 设置cookies的值
	 * @param unknown $key
	 * @param unknown $value
	 */
	private function _set_cookie_key($key, $value) {
		$this->cookies[$key] = $value;
	} 
	
	private function _cookie_have_key($key) {
		return empty($this->cookies[$key]);
	}
	
	/**
	 * 分析返回header中的cookies
	 * @param 返回的内容  $response
	 */
	private function _analyse_cookies($response) {
		$str = explode("\r\n\r\n", $response);
		$str = $str[0];
		
		preg_match_all('/Set-Cookie: (.*?)=(.*?)[;\n]/', $str, $matchs);
		//print_r($matchs);
		
		$len = count($matchs[0]);
		for($i=0; $i<$len; $i++)
			$this->_set_cookie_key($matchs[1][$i], $matchs[2][$i]);
	}
}