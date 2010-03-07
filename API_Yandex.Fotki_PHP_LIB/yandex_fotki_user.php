<?php
	//!	Класс, который позволяет вам провести аутентификацию пользователя на сервисе Яндекс.Фотки и получить коллекции фотографий и альбомов данного пользователя
	/*!
		@author SilentImp
		@ingroup yandex_fotki
	*/
	class yandex_fotki_user{
		
		//! RSA ключ, необходимый для получения токена (аутентификации пользователя)
		private $rsa_key = null;
		//! Идентификатор запроса, необходимый для получения токена (аутентификации пользователя)
		private $request_id = null;
		//! Токен, подтверждающий, что пользователь аутентифицирован
		private $token = null;
		//! Логин пользователя
		private $login = null;
		//! Пароль пользователя
		private $password = null;
		//! Адрес, по которому можно получить кллекцию альбомов пользователя
		private $album_collection_href = null;
		//! Адрес, по которому можно получить кллекцию фотографий пользователя
		private $photo_collection_href = null;
		//! Массив соержащий экземпляры объектов коллекции альбомов
		/*!
			@see yandex_fotki_album_collection
		*/
		private $album_collection = array();
		//! Массив соержащий экземпляры объектов коллекции фотографий
		/*!
			@see yandex_fotki_photo_collection
		*/
		private $photo_collection = array();
		
		//! Конструктор, создающий пользователя
		/*!
			@param login строка, содержащая логин пользователя. Обязательный аргумент.
			@param password строка, содержащая пароль пользователя. Необязательный аргумент.
		*/
		public function __construct($login, $password=null){
			$this->login = $login;
			$this->password = $password;
		}

		//! Возвращает токен пользователя, подтверждающий его аутентификацию.
		/*!
			@return строку содержащую токен пользователя, подтверждающий его аутентификацию.
		*/		
		public function get_token(){
			return $this->token;
		}
		
		//! Возвращает именованую коллекцию альбомов
		/*!
			@param collection_name содержит строку содержащую имя коллекции альбомов, которую вы хотите получить. Необязательный аргумент. Если не указан метод вернет массив, содержащий все коллекции.
			@return экземпляр объекта содержащего коллекцию альбомов
			@see yandex_fotki_album_collection
		*/
		public function album_collection($collection_name=null){
			if($collection_name===null){
				return $this->album_collection;
			}else{
				return $this->album_collection[$collection_name];
			}
		}
		
		//! Возвращает именованную коллекцию фотографий
		/*!
			@param collection_name содержит строку содержащую имя коллекции фотографий, которую вы хотите получить. Необязательный аргумент. Если не указан метод вернет массив, содержащий все коллекции.
			@return экземпляр объекта содержащего коллекцию фотографий
			@see yandex_fotki_photo_collection
		*/
		public function photo_collection($collection_name=null){
			if($collection_name===null){
				return $this->photo_collection;
			}else{
				return $this->photo_collection[$collection_name];
			}
		}
		
		//! Удаляет именованную коллекцию фотографий
		/*!
			@param collection_name содержит строку содержащую имя коллекции фотографий, которую вы хотите удалить. Обязательный аргумент.
			@see yandex_fotki_photo_collection
		*/
		public function remove_photo_collection($collection_name){
			unset($this->photo_collection[$collection_name]);
		}

		//! Удаляет именованную коллекцию альбомов
		/*!
			@param collection_name содержит строку содержащую имя коллекции альбомов, которую вы хотите удалить. Обязательный аргумент.
			@see yandex_fotki_album_collection
		*/		
		public function remove_album_collection($collection_name){
			unset($this->album_collection[$collection_name]);
		}
		
		//! Добавляет коллекцию фотографий
		/*!
			@param collection_name содержит строку содержащую имя новой коллекции. Если коллекция с таким именем уже существуе, она будет перезаписана. Обязательный аргумент. При создании происходит поиск в коллекции с условиями "по умолчанию".
			@return созданную коллекцию
			@see yandex_fotki_photo_collection			
		*/	
		public function add_photo_collection($collection_name){
			$this->photo_collection[$collection_name] = new yandex_fotki_photo_collection($this->photo_collection_href, $this->token);
			$this->photo_collection[$collection_name]->search($this->token);
			return $this->photo_collection[$collection_name];
		}
		
		//! Добавляет коллекцию альбомов
		/*!
			@param collection_name содержит строку содержащую имя новой коллекции. Если коллекция с таким именем уже существуе, она будет перезаписана. Обязательный аргумент. При создании происходит поиск в коллекции с условиями "по умолчанию".
			@return созданную коллекцию
			@see yandex_fotki_album_collection
		*/	
		public function add_album_collection($collection_name){
			$this->album_collection[$collection_name] = new yandex_fotki_album_collection($this->album_collection_href, $this->token);
			$this->album_collection[$collection_name]->search($this->token);
			return $this->album_collection[$collection_name];
		}

		//! Получает сервисный документ, содержащий URL, которые используются для получения коллекций пользователя. Вызывается конструктором класса. Пользователю, по идее, вызывать его не нужно.
		public function get_service_document(){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "http://api-fotki.yandex.ru/api/users/".$this->login."/");
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($curl, CURLOPT_HTTPGET, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$xml = curl_exec($curl);
			$xml = $this->delete_ns($xml);
			if(curl_getinfo($curl, CURLINFO_HTTP_CODE)!=200){
				throw new Exception($xml, E_ERROR);
			}
			curl_close($curl);
			
			if(($sxml=simplexml_load_string($xml))===false){
				throw new Exception("Нестандартный ответ.".$xml, E_ERROR);
			}
			
			$result = $sxml->xpath("//collection[@id='album-list']");
			if(count($result)<1){
				throw new Exception("Адресс коллекции альбомов не был получен", E_ERROR);
			}
			$this->album_collection_href = $result[0]->attributes()->href;
			$result = $sxml->xpath("//collection[@id='photo-list']");
			if(count($result)<1){
				throw new Exception("Адресс коллекции фотографий не был получен", E_ERROR);
			}
			$this->photo_collection_href = $result[0]->attributes()->href;
		}
		
		//! Удаляет токен. После вызова этого метода пользователю будут доступны только ресурсы с уровнем доступа "для всех"
		public function annulment(){
			$this->token = null;
		}
		
		//Метод проводит аутентификацию пользователя
		/*!
			@param password содержит строку с паролем. Если не указан, то будет использоватся пароль, установленный при создании экземпляра класса. Если не задан и он, то метод вызовет исключение.
		*/
		public function authentication($password=null){		
			
			if($password!=null){
				$this->password=$password;
			}			
			
			if($this->password===null){
				throw new Exception("Не задан пароль", E_ERROR);
			}
									
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "http://auth.mobile.yandex.ru/yamrsa/key/");
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTPGET, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$xml = curl_exec($curl);
			if(($sxml=simplexml_load_string($xml))===false||curl_getinfo($curl, CURLINFO_HTTP_CODE)!=200){
				throw new Exception("RSA-ключ не был получен.".$xml, E_ERROR);
			}
			curl_close($curl);
			
			$this->rsa_key = $sxml->key;
			$this->request_id = $sxml->request_id;
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "http://auth.mobile.yandex.ru/yamrsa/token/");
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, 'request_id='.$this->request_id.'&credentials='.$this->encrypt_yarsa($this->rsa_key, "<credentials login='".$this->login."' password='".$this->password."'/>"));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			$xml = curl_exec($curl);
			
			if(curl_getinfo($curl, CURLINFO_HTTP_CODE)!=200){
				$sxml = new SimpleXMLElement();
				if($sxml->simplexml_load_string($xml)!==false||isset($sxml->error)){
					throw new Exception($sxml->error, E_ERROR);
				}
				throw new Exception("Ответ не well-formed XML.".$xml, E_ERROR);
			}
			curl_close($curl);
			
			if(($sxml=simplexml_load_string($xml))===false){
				throw new Exception("Ответ не well-formed XML.".$xml, E_ERROR);
			}
			
			$this->token = $sxml->token;
		}
		
		//! Функция RSA шифрования
		/*! 
			@param key ключ шифрования
			@param data данные, которые будут зашифрованы
		*/
		private function encrypt_yarsa($key, $data){
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
		
		//! Удаление информации о пространствах имен. Библиотеки php, работающие с XML просто не в состоянии нормально работать с ними. Плохие, плохие функции.
		/*! 
			@param xml строка, содержащая XML, который требуется оскопить
		*/
		private function delete_ns($xml){
			$pattern = "|(<[/]*)[a-z][^:\s>]*:([^:\s>])[\s]*|sui";
			$replacement="\\1\\2";
			$xml = preg_replace($pattern,$replacement,$xml);
			$pattern = "|(<[/]*[^\s>]+)[-]|sui";
			$replacement="\\1_";
			$xml = preg_replace($pattern,$replacement,$xml);
			$pattern = "|xmlns[:a-z]*=\"[^\"]*\"|isu";
			$replacement="";
			return preg_replace($pattern,$replacement,$xml);
		}
	}
?>