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
 * @throws YFXMLException|YFRequestException|YFException
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
		$this->getServiceDocument();
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
		
		$connect = new YFConnect();
		$connect->setUrl("http://api-fotki.yandex.ru/api/users/".$this->login."/");
		$connect->exec();
		$code = $connect->getCode();
		$xml = $connect->getResponce();
		unset($connect);
		
		/*
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://api-fotki.yandex.ru/api/users/".$this->login."/");
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		*/
		
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
		
		YFSecurity::deleteXmlNamespace($xml);
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
		
		
		$connect = new YFConnect();
		$connect->setUrl("http://auth.mobile.yandex.ru/yamrsa/key/");
		$connect->exec();
		$code = $connect->getCode();
		$xml = $connect->getResponce();
		unset($connect);
						
		/*
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://auth.mobile.yandex.ru/yamrsa/key/");
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		*/
		
		switch($code){
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
		
		$connect = new YFConnect();
		$connect->setUrl("http://auth.mobile.yandex.ru/yamrsa/token/");
		$connect->setPost('request_id='.$this->requestId.'&credentials='.YFSecurity::encryptYFRSA($this->rsaKey, "<credentials login='".$this->login."' password='".$this->password."'/>"));
		$connect->exec();
		$code = $connect->getCode();
		$xml = $connect->getResponce();
		unset($connect);
		
		/*
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
		*/
		
		switch($code){
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

}