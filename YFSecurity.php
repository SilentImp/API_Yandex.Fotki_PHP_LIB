<?php
/**
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 * @package YandexFotki
 */
 
/**
 * Класс, который используентся остальными классами для форматирования данных
 * 
 * @package YandexFotki
 * @throws YFException|YFRequestException|YFXMLException
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 */
class YFSecurity {
	
	/**
	 * Приводит текст к безопасному для отправки виду
	 * 
	 * @param string &$data переменная, содержащая потенциально небезопасный к отправке текст
	 * @return void
	 * @access public
	 */
	static public function clean(&$data){
		return $data = trim(htmlentities($data,ENT_COMPAT,"UTF-8"));
	}
	
	/**
	 * Удаление информации о пространствах имен.
	 * Библиотеки php, работающие с XML просто не в состоянии
	 * нормально работать с ним. Плохие, плохие функции.
	 * 
	 * @param string &$xml XML содержащий информацию о пространстве имен
	 * @return string
	 * @access public
	 */
	static public function deleteXmlNamespace(&$xml){
		$pattern = "|(<[/]*)[a-z][^:\s>]*:([^:\s>])[\s]*|sui";
		$replacement="\\1\\2";
		$xml = preg_replace($pattern, $replacement, $xml);
		$pattern = "|(<[/]*[^\s>]+)[-]|sui";
		$replacement="\\1_";
		$xml = preg_replace($pattern, $replacement, $xml);
		$pattern = "|xmlns[:a-z]*=\"[^\"]*\"|isu";
		$replacement="";
		$xml = preg_replace($pattern, $replacement, $xml);
	}
	
	/**
	 * RSA шифрование со вкусом Яндекса
	 * Это обертка, которая выберет функцию в зависимости от того, какая из библиотек есть в наличии
	 *
	 * @param string $key ключ шифрования
	 * @param string $data данные, которые будут зашифрованы
	 * @return string
	 * @access public
	 */
	static public function encryptYFRSA($key, $data){
		if(function_exists("gmp_strval")===true) return YFSecurity::encryptYFRSAGMP($key, $data);
		return YFSecurity::encryptYFRSABCMath($key, $data);
	}

	/**
	 * Этот метод переводит большое шестнадцатиричное число в десятичное, использует BCMath
	 *
	 * @param string $hex очень большое шестнадцатеричное число в виде строки
	 * @return string
	 * @access private
	 */		
	static private function bchexdec($hex){
			$dec = 0;
			$len = strlen($hex);
			for ($i = 1; $i <= $len; $i++) {
				$dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
			}
			return $dec;
		}
	
	/**
	 * Этот метод переводит большое десятичное число в шестнадцатиричное, использует BCMath
	 *
	 * @param string $number очень большое десятичное число в виде строки
	 * @return string
	 * @access private
	 */		
	static private function dec2hex($number){
			$hexvalues = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
			$hexval = '';
			while($number != '0'){
				$hexval = $hexvalues[bcmod($number,'16')].$hexval;
				$number = bcdiv($number,'16',0);
			}
		return $hexval;
		}


	/**
	 * RSA шифрование со вкусом Яндекса
	 * Использует BCMath библиотеку
	 *
	 * @param string $key ключ шифрования
	 * @param string $data данные, которые будут зашифрованы
	 * @return string
	 * @access private
	 */	
	static private function encryptYFRSABCMath($key, $data){
		$buffer = array();
		list($nstr, $estr) = explode('#', $key);
		
		$nv = YFSecurity::bchexdec($nstr);
		$ev = YFSecurity::bchexdec($estr);
		
		$stepSize = strlen($nstr)/2 - 1;
		$prev_crypted = array();
		$prev_crypted = array_fill(0, $stepSize, 0);
		$hex_out = '';
		for($i=0; $i<strlen($data); $i++){
			$buffer[] = ord($data{$i});
		}
		for($i=0; $i<(int)(((count($buffer)-1)/$stepSize)+1); $i++){
			$tmp = array_slice($buffer, $i * $stepSize, ($i + 1) * $stepSize);
			for ($j=0;$j<count($tmp); $j++){
				$tmp[$j] = ($tmp[$j] ^ $prev_crypted[$j]);
			}				
			$tmp = array_reverse($tmp);
			$plain = "0";
			$pn="0";
			for($x = 0; $x < count($tmp); ++$x){
				$pow = bcpowmod(256,$x,$nv);
				$pow_mult = bcmul($pow,$tmp[$x]);
				$plain = bcadd($plain,$pow_mult);
			}
			$plain_pow = bcpowmod($plain, $ev, $nv);
			$plain_pow_str = strtoupper(YFSecurity::dec2hex($plain_pow));
			$hex_result = array();
			
			for($k=0;$k<(strlen($nstr)-strlen($plain_pow))+ 1;$k++){
				$hex_result[]="";
			}
			
			$hex_result = implode("0",$hex_result).$plain_pow_str;
			$min_x = min(strlen($hex_result), count($prev_crypted) * 2);
			
			for($x=0;$x<$min_x;$x=$x+2){
				$prev_crypted[$x/2] = hexdec('0x'.substr($hex_result,$x,2));
			}
			if(count($tmp) < 16){
				$hex_out.= '00';
			}
			$hex_out.= strtoupper(dechex(count($tmp)).'00');
			$ks = strlen($nstr) / 2;
			if($ks<16){
				$hex_out.='0';
			}
			$hex_out.= dechex($ks).'00';
			$hex_out.= $hex_result;
		}
		return UrlEncode(base64_encode(pack("H*" , $hex_out)));
	}
	
	/**
	 * RSA шифрование со вкусом Яндекса
	 * Использует GMP библиотеку
	 *
	 * @param string $key ключ шифрования
	 * @param string $data данные, которые будут зашифрованы
	 * @return string
	 * @access private
	 */
	static private function encryptYFRSAGMP($key, $data){
		$buffer = array();
		
		list($nstr, $estr) = explode('#', $key);
		$n = gmp_init($nstr,16);
		$e = gmp_init($estr,16);
		$stepSize = strlen($nstr)/2 - 1;
		$prev_crypted = array();
		$prev_crypted = array_fill(0, $stepSize, 0);
		$hex_out = '';
	
		for($i=0; $i<strlen($data); $i++){
			$buffer[] = ord($data{$i});
		}
		
		for($i=0; $i<(int)(((count($buffer)-1)/$stepSize)+1); $i++){
			$tmp = array_slice($buffer, $i * $stepSize, ($i + 1) * $stepSize);
			for ($j=0;$j<count($tmp); $j++){
				$tmp[$j] = ($tmp[$j] ^ $prev_crypted[$j]);
			}
			$tmp = array_reverse($tmp);
			$plain = gmp_init(0);
			for($x = 0; $x < count($tmp); ++$x){
				$pow = gmp_powm(gmp_init(256), gmp_init($x), $n);
				$pow_mult = gmp_mul($pow, gmp_init($tmp[$x]));
				$plain = gmp_add($plain, $pow_mult);
			}
			$plain_pow = gmp_powm($plain, $e, $n);
			$plain_pow_str = strtoupper(gmp_strval($plain_pow, 16));
			$hex_result = array();
			for($k=0;$k<(strlen($nstr)-strlen($plain_pow_str))+ 1;$k++){
				$hex_result[]="";
			}
			$hex_result = implode("0",$hex_result).$plain_pow_str;
			$min_x = min(strlen($hex_result), count($prev_crypted) * 2);
			
			for($x=0;$x<$min_x;$x=$x+2){
				$prev_crypted[$x/2] = hexdec('0x'.substr($hex_result,$x,2));
			}
			
			if(count($tmp) < 16){
				$hex_out.= '00';
			}
			$hex_out.= strtoupper(dechex(count($tmp)).'00');
			$ks = strlen($nstr) / 2;
			if($ks<16){
				$hex_out.='0';
			}
			$hex_out.= dechex($ks).'00';
			$hex_out.= $hex_result;
		}
		return UrlEncode(base64_encode(pack("H*" , $hex_out)));
	}
	
}

?>