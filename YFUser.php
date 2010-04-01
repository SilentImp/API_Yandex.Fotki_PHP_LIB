<?php

/**
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @package YandexFotki
 */

/**
 * Позволяет провести аутентификацию пользователя на сервисе Яндекс.Фотки и
 * получить коллекции фотографий и альбомов пользователя.
 *
 * @throws YFUserException|YFXMLException|YFRequestException|YFException
 * @package YandexFotki
 */
class YFUser {	
	/**
	 * @var string RSA ключ, необходимый для получения токена (аутентификации пользователя)
	 * @access protected
	 */
	protected $rsaKey = null;
	
	/**
	 * @var string Идентификатор запроса, необходимый для получения токена (аутентификации пользователя)
	 * @access protected
	 */
	protected $requestId = null;

	/**
	 * @var string Токен, подтверждающий, что пользователь аутентифицирован
	 * @access protected
	 */
	protected $token = null;

	/**
	 * @var string Логин пользователя
	 * @access protected
	 */
	protected $login = null;
	
	/**
	 * @var string Пароль пользователя
	 * @access protected
	 */
	protected $password = null;

	/**
	 * @var string Адрес, по которому можно получить кллекцию альбомов пользователя
	 * @access protected
	 */
	protected $albumCollectionUrl = null;

	/**
	 * @var string Адрес, по которому можно получить кллекцию фотографий пользователя
	 * @access protected
	 */
	protected $photoCollectionUrl = null;
	
	/**
	 * @var YFAlbumCollection Коллекция альбомов пользователя
	 * @access protected
	 */
	protected $albumCollection = array();

	/**
	 * @var YFPhotoCollection Коллекция фотографий пользователя
	 * @access protected
	 */
	protected $photoCollection = array();
	
	/**
	 * @param string $login Логин пользователя.
	 * @param string $password Пароль пользователя.	 
	 * @return void
	 * @access public
	 */
	public function __construct($login, $password=null){
		$this->login = $login;
		$this->password = $password;
		libxml_use_internal_errors(true);
	}
	
	/**
	 * Возвращает токен
	 *
	 * @return string
	 * @access public
	 */
	public function getToken(){
		return $this->token;
	}

	/**
	 * Устанавливает токен
	 * 
	 * @param string $token
	 * @return void
	 * @access public
	 */
	public function setToken($token){
		$this->token = $token;
	}

	/**
	 * @param string $name Имя коллекции альбомов. Если не указано, метод вернет массив, содержащий все коллекции.
	 * @return array|YFAlbumCollection
	 * @access public
	 */
	public function getAlbumCollection($name=null){
		if($name===null) return $this->albumCollection;
		return $this->albumCollection[$name];		
	}

	/**
	 * Возвращает именованную коллекцию фотографий
	 * 
	 * @param string $collection_name Имя коллекции фотографий. Если не указано, метод вернет массив, содержащий все коллекции.
	 * @return array|YFPhotoCollection
	 * @access public
	 */
	public function getPhotoCollection($collection_name=null){
		if($collection_name===null) return $this->photoCollection;
		return $this->photoCollection[$collection_name];		
	}
	
	/**
	 * Удаляет именованную коллекцию фотографий
	 *
	 * @param $name Имя коллекции фотографий.
	 * @return void
	 * @access public
	 */
	public function removePhotoCollection($name){
		unset($this->photoCollection[$name]);
	}
	
	/**
	 * Удаляет именованную коллекцию альбомов
	 *
	 * @param string $name Имя коллекции альбомов.
	 * @return void
	 * @access public
	 */
	public function removeAlbumCollection($name){
		unset($this->albumCollection[$name]);
	}

	/**
	 * Добавляет коллекцию фотографий.
	 * Если коллекция с таким именем уже существует, она будет перезаписана.
	 * При создании происходит поиск в коллекции с условиями по умолчанию.
	 *
	 * @param string $name Имя новой коллекции
	 * @return array|YFPhotoCollection
	 * @access public
	 */
	public function addPhotoCollection($name){
		$this->photoCollection[$name] = new YFPhotoCollection($this->photoCollectionUrl, $this->getToken());
		$this->photoCollection[$name]->searchEx($this->getToken());
		return $this->photoCollection[$name];
	}

	/**
	 * Добавляет коллекцию альбомов.
	 * Если коллекция с таким именем уже существует, она будет перезаписана.
	 * При создании происходит поиск в коллекции с условиями по умолчанию.
	 * 
	 * @param string $name Имя новой коллекции.
	 * @return YFAlbumCollection
	 * @access public
	 */
	public function addAlbumCollection($name){
		$this->albumCollection[$name] = new YFAlbumCollection($this->albumCollectionUrl, $this->getToken());
		$this->albumCollection[$name]->search($this->getToken());
		return $this->albumCollection[$name];
	}


	/**
	 * Получает сервисный документ, содержащий URL, которые используются
	 * для получения коллекций пользователя.
	 *
	 * @throws YFXMLException|YFRequestException
	 * @return void
	 * @access public
	 */
	public function getServiceDocument(){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://api-fotki.yandex.ru/api/users/".$this->login."/");
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		switch((int)$code){
			case 200:
				//если код не 200 и не оговоренные в документации Яндекс ошибки, то будет вызвано прерывание общего типа.
				break;
			case 404:
				throw new YFRequestException($xml, $code, "Пользователя с указанным логином не существует.","userNotFound");
				break;
			case 500:
				throw new YFRequestException($xml, $code, "Сервер не смог обработать запрос.","internalServerError");
				break;
			default:
				throw new YFRequestException($xml, $code);
				break;
		}
		
		$xml = $this->deleteXMLNameSpace($xml);
		if(($sxml=simplexml_load_string($xml))===false){
			throw new YFXMLException($xml, E_ERROR,"Не удалось распознать ответ Яндекс как валидный XML документ","canNotCreateXML");
		}

		$result = $sxml->xpath("//collection[@id='album-list']");
		if(count($result)<1){
			throw new YFXMLException($xml, E_ERROR,"При обработке XML не был найден URL коллекции альбомов пользователя","albumCollectionURLNotFound");
		}
		$this->albumCollectionUrl = $result[0]->attributes()->href;
		
		$result = $sxml->xpath("//collection[@id='photo-list']");
		if(count($result)<1){
			throw new YFXMLException($xml, E_ERROR,"При обработке XML не был найден URL коллекции фотографий пользователя","photoCollectionURLNotFound");
		}
		$this->photoCollectionUrl = $result[0]->attributes()->href;
	}	

	/**
	 * Аутентифицирует пользователя.
	 * Если пароль не указан, будет использоватся пароль, установленный
	 * при создании экземпляра класса.
	 *
	 * @throws YFXMLException|YFRequestException|YFException
	 * @param string $password пароль пользователя
	 * @return void
	 * @access public
	 */
	public function authenticate($password=null){
			
		if($password!=null){
			$this->password=$password;
		}			
		
		if($this->password===null){
			throw new YFException("Не задан пароль", E_ERROR, null,"passwordNotSet");
		}
								
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://auth.mobile.yandex.ru/yamrsa/key/");
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		switch((int)$code){
			case 200:
				//если код не 200 и не оговоренные в документации Яндекс ошибки, то будет вызвано прерывание общего типа.
				break;
			case 500:
				throw new YFRequestException($xml, $code, "Сервер не смог обработать запрос.","internalServerError");
				break;
			default:
				throw new YFRequestException($xml, $code);
				break;
		}

		if(($sxml=simplexml_load_string($xml))===false){
			throw new YFXMLException($xml, E_ERROR);
		}			
			
		$this->rsaKey = $sxml->key;
		$this->requestId = $sxml->request_id;
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://auth.mobile.yandex.ru/yamrsa/token/");
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 'request_id='.$this->requestId.'&credentials='.$this->encryptYFRSA($this->rsaKey, "<credentials login='".$this->login."' password='".$this->password."'/>"));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		switch((int)$code){
			case 200:
				//если код не 200 и не оговоренные в документации Яндекс ошибки, то будет вызвано прерывание общего типа.
				break;
			case 400:
				throw new YFRequestException($xml, $code, null, "badRequest");
				break;
			case 403:
				throw new YFRequestException($xml, $code, "Указана неверная пара логин-пароль.","authorizationFailed");
				break;
			case 500:
				throw new YFRequestException($xml, $code, "Сервер не смог обработать запрос.","internalServerError");
				break;
			default:
				throw new YFRequestException($xml, $code);
				break;
		}
		
		if(($sxml=simplexml_load_string($xml))===false){
			throw new YFXMLException($xml, E_ERROR,"Не удалось распознать ответ Яндекс как валидный XML документ","canNotCreateXML");
		}
		
		$this->token = $sxml->token;
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
	private function encryptYFRSA($key, $data){
		if(function_exists("gmp_strval")===true) return $this->encryptYFRSAGMP($key, $data);
		return $this->encryptYFRSABCMath($key, $data);
	}

	/**
	 * Этот метод переводит большое шестнадцатиричное число в десятичное, использует BCMath
	 *
	 * @param string $hex очень большое шестнадцатеричное число в виде строки
	 * @return string
	 * @access private
	 */		
	private function bchexdec($hex){
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
	private function dec2hex($number){
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
	private function encryptYFRSABCMath($key, $data){
		$buffer = array();
		list($nstr, $estr) = explode('#', $key);
		
		$nv = $this->bchexdec($nstr);
		$ev = $this->bchexdec($estr);
		
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
			$plain_pow_str = strtoupper($this->dec2hex($plain_pow));
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
	private function encryptYFRSAGMP($key, $data){
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
	
	/**
	 * Удаляет объявления пространств имён 
	 * 
	 * @param string $xml
	 * @return string
	 * @access private
	 */
	private function deleteXMLNameSpace($xml){
		$pattern = "|(<[/]*)[a-z][^:\s>]*:([^:\s>])[\s]*|sui";
		$replacement="\\1\\2";
		$xml = preg_replace($pattern, $replacement, $xml);
		$pattern = "|(<[/]*[^\s>]+)[-]|sui";
		$replacement="\\1_";
		$xml = preg_replace($pattern,  $replacement, $xml);
		$pattern = "|xmlns[:a-z]*=\"[^\"]*\"|isu";
		$replacement="";
		return preg_replace($pattern, $replacement, $xml);
	}
}