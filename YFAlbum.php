<?php
/**
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 * @package YandexFotki
 */
 
/**
 * Позволяет работать с альбомом.
 *
 * @throws YFException|YFRequestException|YFXMLException
 * @package YandexFotki
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 */
class YFAlbum {	
	/**
	 * Идентификатор Atom Entry альбома.
	 * Идентификатор является глобально уникальным и позволяет клиентскому
	 * приложению однозначно определить некоторый Atom Entry (например,
	 * с целью выявления дубликатов при постраничной выдаче коллекций).
	 * @var string
	 * @access protected
	 */
	protected $id = null;
		
	/**
	 * Cодержит информацию о владельце альбома.
	 * На данный момент информация ограничивается логином пользователя на Яндексе,
	 * который указывается во вложенном теге.
	 * @var string
	 * @access protected
	 */
	protected $author = null;
		
	/**
	 * Название альбома
	 * @var string
	 * @access protected
	 */
	protected $title = null;
		
	/**
	 * Описание альбома
	 * @var string
	 * @access protected
	 */
	protected $summary = null;

	/**
	 * Ссылка на ресурс альбома
	 * @var string
	 * @access protected
	 */
	protected $albumUrl = null;

	/**
	 * Ссылка для редактирования ресурса альбома
	 * @var string
	 * @access protected
	 */
	protected $albumEditUrl = null;
		
	/**
	 * Ссылка на коллекцию фотографий альбома
	 * @var string
	 * @access protected
	 */
	protected $albumPhotosUrl = null;
		
	/**
	 * @todo По идее это разметка для Яндекс карт
	 * @var string
	 * @access protected
	 */
	protected $ymapsmlUrl = null;
		
	/**
	 * Ссылка на веб-страницу альбома в интерфейсе Яндекс.Фоток
	 * @var string
	 * @access protected
	 */
	protected $albumPageUrl = null;
	
	/**
	 * Время создания альбома
	 * @access protected
	 */
	protected $createdOn = null;

	/**
	 * Время последнего редактирования альбома
	 * @access protected
	 */
	protected $updatedOn = null;

	/**
	 * Время последнего значимого с точки зрения системы изменения альбома
	 * (в текущей версии API Фоток любое изменение считается значимым,
	 * вследствие чего значение atom:updated совпадает с app:edited.
	 * @access protected
	 */
	protected $editedOn = null;
	
	/**
	 * Флаг защиты альбома паролем
	 * @var boolean
	 * @access protected
	 */
	protected $isProtected = false;

	/**
	 * Количество фотографий в альбоме
	 * @var int
	 * @access protected
	 */
	protected $imageCount = null;

	/**
	 * XMLка с описанием альбома	 
	 * @access protected
	 */
	protected $xml = null;
	
	/**
	 * Токен, подтверждающий аутентификацию пользователя
	 * @var string
	 * @access protected
	 */
	protected $token = null;

	/**
	 * Флаг, равный true, если альбом был удален
	 * @var boolean
	 * @access protected
	 */
	protected $isDeleted = false;

	/**
	 * Массив содержащий коллекцию фотографий альбома
	 * @var array
	 * @access protected
	 */
	protected $photoCollection = array();

	/**
	 * Возвращает идентификатор Atom Entry альбома.	 
	 * @return string
	 * @access public
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * Возвращает информацию о владельце альбома.
	 * @return string
	 * @access public
	 */
	public function getAuthor(){
		return $this->author;
	}

	/**
	 * Возвращает описание альбома
	 * @return string
	 * @access public
	 */
	public function getSummary(){
		return $this->summary;
	}

	/**
	 * Возвращает название альбома	 
	 * @return string
	 * @access public
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/**
	 * Возвращает ссылку на ресурс альбома
	 * @return string
	 * @access public
	 */
	public function getAlbumUrl(){
		return $this->albumUrl;
	}
	
	/**
	 * Возвращает ссылку для редактирования ресурса альбома
	 * @return string
	 * @access public
	 */
	public function getAlbumEditUrl(){
		return $this->albumEditUrl;
	}

	/**
	 * Возвращает ссылку на коллекцию фотографий альбома
	 * @return string
	 * @access public
	 */
	public function getAlbumPhotosUrl(){
		return $this->albumPhotosUrl;
	}

	/**
	 * По идее возвращает разметкау для Яндекс карт
	 * @return string
	 * @access public
	 */	
	public function getYmapsmlUrl(){
		return $this->ymapsmlUrl;
	}

	/**
	 * Возвращает ссылку на веб-страницу альбома в интерфейсе Яндекс.Фоток
	 * @return string
	 * @access public
	 */
	public function getAlbumPageUrl(){
		return $this->albumPageUrl;
	}

	/**
	 * Возвращает время создания альбома
	 * @return string
	 * @access public
	 */	
	public function getCreatedOn(){
		return $this->createdOn;
	}

	/**
	 * Возвращает время последнего редактирования альбома
	 * @return string
	 * @access public
	 */	
	public function getUpdatedOn(){
		return $this->updatedOn;
	}

	/**
	 * Возвращает время последнего значимого с точки зрения системы изменения альбома
	 * (в текущей версии API Фоток любое изменение считается значимым,
	 * вследствие чего значение atom:updated совпадает с app:edited.
	 * @return string
	 * @access public
	 */		
	public function getEditedOn(){
		return $this->editedOn;
	}

	/**
	 * Флаг защиты альбома паролем
	 * @return boolean
	 * @access public
	 */
	public function isProtected(){
		return $this->isProtected;
	}

	/**
	 * Количество фотографий в альбоме
	 * @return int
	 * @access public
	 */
	public function getImageCount(){
		return $this->imageCount;
	}

	/**
	 * XML с описанием альбома
	 * @return boolean
	 * @access public
	 */
	public function getXml(){
		return $this->xml;
	}
	
	/**
	 * Проверяет был ли альбом удален вызовом метода delete
	 * @return bool
	 * @access public
	 */
	public function isDeleted(){
		return $this->isDeleted;
	}	

	/**
	 * Если $token не задан, то в коллекции будут показаны только ресурсы
	 * с уровнем доступа "для всех".
	 *
	 * @param string $xml Atom Entry альбома
	 * @param string $token аутентификационный токен пользователя, если не был задан, то будет использован токен установленный ранее, в том числе через конструктор
	 * @return void
	 * @access public
	 */
	public function __construct($xml, $token=null){
		libxml_use_internal_errors(true);
		$this->token = $token;
		$this->reloadXml($xml);
	}
	
	/**
	 * Добавляет коллекцию фотографий с выбранным именем.
	 * Если коллекция с таким именем уже существует, она будет перезаписана.
	 * При создании происходит поиск в коллекции с условиями по умолчанию.
	 * 
	 * @param string $name имя коллекции фотографий
	 * @return YFPhotoCollection
	 * @access public
	 */
	public function addPhotoCollection($name){
		$id = explode(":", $this->id);
		$id = $id[count($id)-1];
		$this->photoCollection[$name] = new YFPhotoCollection($this->getAlbumPhotosUrl(), $this->token, $id);
		$this->photoCollection[$name]->search($this->token);
		return $this->photoCollection[$name];
	}

	/**
	 * Получает коллекцию.
	 * 
	 * Если $name не указан метод вернет массив, содержащий все коллекции.
	 *
	 * @param string $name имя коллекции фотографий
	 * @return YFPhotoCollection
	 * @access public
	 */
	public function getPhotoCollection($name=null){
		if($name===null)
			return $this->photoCollection;

		return $this->photoCollection[$name];		
	}

	/**
	 * Удаляет именованную коллекцию фотографий
	 *
	 * @param string $name имя коллекции, которая будет удалена
	 * @return void
	 * @access public
	 */
	public function removePhotoCollection($name){
		unset($this->photoCollection[$name]);
	}	

	/**
	 * Обновляет данные альбома
	 *
	 * @throws YFRequestException
	 * @return void
	 * @access public
	 */
	public function refresh(){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->getAlbumEditUrl());
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_HTTPGET, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if($this->token!=null){
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$this->token.'"'
			));
		}
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		switch((int)$code){
			case 200:
				//если код не 200 и не оговоренные в документации Яндекс ошибки, то будет вызвано прерывание общего типа.
				break;
			case 401:
				throw new YFRequestException($xml, $code, null, "unauthorized");
				break;
			case 403:
				throw new YFRequestException($xml, $code, "Аутентифицированный пользователь попытался что-либо изменить в чужом альбоме", "forbidden");
				break;
			case 404:
				throw new YFRequestException($xml, $code, "Такого пользователя или альбома не существует.","userOrAlbumNotFound");
				break;
			case 500:
				throw new YFRequestException($xml, $code, "Сервер не смог обработать запрос.","internalServerError");
				break;
			default:
				throw new YFRequestException($xml, $code);
				break;
		}

		$this->reloadXml($this->deleteXmlNamespace($xml));
	}

	/**
	 * Редактирует данные альбома
	 *
	 * @throws RequestError|YFAuthenticationException|YFException
	 * @param string $title Название альбома
	 * @param string $summary Описание альбома
	 * @param string $password Пароль альбома. Если выставлена пустая строка, то пароль будет снят.
	 * @param string $token аутентификационный токен пользователя, если не был задан, то будет использован токен установленный ранее, в том числе через конструктор
	 * @return void
	 * @access public
	 */
	public function edit($title=null, $summary=null, $password=null, $token=null){
		
		if($title===null&&$summary===null&&$password===null){
			throw new YFException("Метод должен изменить заголовок, описание или пароль альбома", E_ERROR, null, "noDifference");
		}

		if($token!==null){
			$this->token = $token;
		}
		
		if($this->token==null){
			throw new YFException("Эта операция доступна только для аутентифицированных пользователей", E_ERROR, null, "authenticationNeeded");
		}

		if($title!=null){
			$this->title = (string)$title;
		}
		if($summary!=null){
			$this->summary = (string)$summary;
		}
		if($password!=null){
			$this->password = (string)$password;
		}

		$putData = tmpfile();

		$protected = $this->isProtected() ? "true" : "false";			

		$pass = "";
		if($password!==null){
			$pass = "<f:password>$password</f:password>";
		}

		$message = '
		<entry xmlns="http://www.w3.org/2005/Atom" xmlns:app="http://www.w3.org/2007/app" xmlns:f="yandex:fotki">
			<id>'.$this->getId().'</id>
			<author>
				<name>'.$this->getAuthor().'</name>
			</author>
			<title>'.$this->getTitle().'</title>
			<summary>'.$this->getSummary().'</summary>
			<link href="'.$this->getAlbumUrl().'" rel="self" />
			<link href="'.$this->getAlbumEditUrl().'" rel="edit" />
			<link href="'.$this->getAlbumPhotosUrl().'" rel="photos" />
			<link href="'.$this->getAlbumPageUrl().'" rel="alternate" />
			<published>'.$this->getCreatedOn().'</published>
			<app:edited>'.$this->getUpdatedOn().'</app:edited>
			<updated>'.$this->getEditedOn().'</updated>
			<f:protected value="'.$this->getImageCount().'" />
			'.$pass.'
			<f:image-count value="'.$protected.'" />
		</entry>';

		fwrite($putData, $message);
		fseek($putData, 0);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->getAlbumEditUrl());
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_PUT, true);
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_INFILE, $putData);
		curl_setopt($curl, CURLOPT_INFILESIZE, strlen($message));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$this->token.'"',
			'Content-Type: application/atom+xml; charset=utf-8; type=entry',
			'Expect:'
		));
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		fclose($putData);
	
		switch((int)$code){
			case 200:
				//если код не 200 и не оговоренные в документации Яндекс ошибки, то будет вызвано прерывание общего типа.
				break;
			case 400:
				throw new YFRequestException($xml, $code, null, "badRequest");
				break;
			case 401:
				throw new YFRequestException($xml, $code, null, "unauthorized");
				break;
			case 403:
				throw new YFRequestException($xml, $code, "Для доступа к альбому требуется пароль.", "forbidden");
				break;
			case 404:
				throw new YFRequestException($xml, $code, "Такого пользователя или альбома не существует.","userOrAlbumNotFound");
				break;
			case 415:
				throw new YFRequestException($xml, $code, "Заголовок Content-Type содержит тип, отличный от типа Atom Entry.","atomEntryNotFound");
				break;
			case 500:
				throw new YFRequestException($xml, $code, "Сервер не смог обработать запрос.","internalServerError");
				break;
			default:
				throw new YFRequestException($xml, $code);
				break;
		}

		$this->xml = $this->deleteXmlNamespace($xml);
		$this->refresh();
	}

	/**
	 * Удаляет альбом. В случае успешного удаления альбом будет помечен, как удаленный.
	 * Провреить удлаен ли объект можно с помощью метода isDeleted.
	 *
	 * @throws YFAuthenticationException|YFRequestException
	 * @param string $token аутентификационный токен пользователя, если не был задан, то будет использован токен установленный ранее, в том числе через конструктор
	 * @return void
	 * @access public
	 */
	public function delete($token=null){
		
		if($token!==null){
			$this->token = $token;
		}
		
		if($this->token==null){
			throw new YFException("Эта операция доступна только для аутентифицированных пользователей", E_ERROR,null,"authenticationNeeded");
		}
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->getAlbumEditUrl());
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$this->token.'"'
		));
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
	
		switch((int)$code){
			case 204:
				//если код не 204 и не оговоренные в документации Яндекс ошибки, то будет вызвано прерывание общего типа.
				break;
			case 401:
				throw new YFRequestException($xml, $code, null, "unauthorized");
				break;
			case 403:
				throw new YFRequestException($xml, $code, "Аутентифицированный пользователь попытался что-либо изменить в чужом альбоме.", "forbidden");
				break;
			case 500:
				throw new YFRequestException($xml, $code, "Сервер не смог обработать запрос.","internalServerError");
				break;
			default:
				throw new YFRequestException($xml, $code);
				break;
		}
		
		$this->isDeleted=true;
	}	

	/**
	 * Получив на вход XML на ее основе перезаписывает свойства текущего экземпляра классса
	 *
	 * @throws YFXMLException
	 * @param string $xml XML содержащий информацию об Альбоме
	 * @return void
	 * @access private
	 */
	private function reloadXml($xml){
		$this->xml = '<?xml version="1.0" encoding="UTF-8"?>'.$xml;

		if(($sxml=simplexml_load_string($xml))===false){
			throw new YFXMLException($xml, E_ERROR,"Не удалось распознать ответ Яндекс как валидный XML документ","canNotCreateXML");
		}
		
		$this->id = $sxml->id;
		$this->author = $sxml->author->name;
		$this->title = $sxml->title;
		$this->summary = $sxml->summary;

		$this->imageCount = $sxml->image_count->attributes()->value;
		$this->createdOn = $sxml->published;
		$this->updatedOn = $sxml->edited;
		$this->editedOn = $sxml->updated;
		if($sxml->protected->attributes()->value=="false"){
			$this->isProtected = false;
		}else{
			$this->isProtected = true;
		}
		
		foreach($sxml->link as $link){
			switch($link->attributes()->rel){
				case "self":
					$this->albumUrl = $link->attributes()->href;
					break;
				case "edit":
					$this->albumEditUrl = $link->attributes()->href;
					break;
				case "photos":
					$this->albumPhotosUrl = $link->attributes()->href;
					break;
				case "ymapsml":
					$this->getYmapsmlUrl = $link->attributes()->href;
					break;
				case "alternate":
					$this->albumPageUrl = $link->attributes()->href;
					break;
			}
		}
	}

	/**
	 * Удаление информации о пространствах имен.
	 * Библиотеки php, работающие с XML просто не в состоянии
	 * нормально работать с ним. Плохие, плохие функции.
	 * 
	 * @param string $xml XML содержащий информацию о пространстве имен
	 * @return string
	 * @access private
	 */
	private function deleteXmlNamespace($xml){
		$pattern = "|(<[/]*)[a-z][^:\s>]*:([^:\s>])[\s]*|sui";
		$replacement="\\1\\2";
		$xml = preg_replace($pattern, $replacement, $xml);
		$pattern = "|(<[/]*[^\s>]+)[-]|sui";
		$replacement="\\1_";
		$xml = preg_replace($pattern, $replacement, $xml);
		$pattern = "|xmlns[:a-z]*=\"[^\"]*\"|isu";
		$replacement="";
		return preg_replace($pattern, $replacement, $xml);
	}
}