<?php
/**
 * Позволяет работать с альбомом.
 *
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * 
 * @package YandexFotki
 * @throws YFAuthenticationErrorException|YFException|YFRequestErrorException|YFXMLErrorException
 */
class YFAlbum {	
	/**
	 * Идентификатор Atom Entry альбома.
	 * Идентификатор является глобально уникальным и позволяет клиентскому
	 * приложению однозначно определить некоторый Atom Entry (например,
	 * с целью выявления дубликатов при постраничной выдаче коллекций).
	 * @var string
	 */
	private $id = null;
		
	/**
	 * Cодержит информацию о владельце альбома.
	 * На данный момент информация ограничивается логином пользователя на Яндексе,
	 * который указывается во вложенном теге.
	 * @var string
	 */
	private $author = null;
		
	/**
	 * Название альбома
	 * @var string
	 */
	private $title = null;
		
	/**
	 * Описание альбома
	 * @var string
	 */
	private $summary = null;

	/**
	 * Ссылка на ресурс альбома
	 * @var string
	 */
	private $albumUrl = null;

	/**
	 * Ссылка для редактирования ресурса альбома
	 * @var string
	 */
	private $albumEditUrl = null;
		
	/**
	 * Ссылка на коллекцию фотографий альбома
	 * @var string
	 */
	private $albumPhotosUrl = null;
		
	/**
	 * @todo По идее это разметка для Яндекс карт
	 * @var string
	 */
	private $ymapsmlUrl = null;
		
	/**
	 * Ссылка на веб-страницу альбома в интерфейсе Яндекс.Фоток
	 * @var string
	 */
	private $albumPageUrl = null;
	
	/**
	 * Время создания альбома
	 */
	private $createdOn = null;

	/**
	 * Время последнего редактирования альбома
	 */
	private $updatedOn = null;

	/**
	 * Время последнего значимого с точки зрения системы изменения альбома
	 * (в текущей версии API Фоток любое изменение считается значимым,
	 * вследствие чего значение atom:updated совпадает с app:edited.
	 */
	private $editedOn = null;
	
	/**
	 * Флаг защиты альбома паролем
	 * @var boolean
	 */
	private $isProtected = false;

	/**
	 * Количество фотографий в альбоме
	 * @var int
	 */
	private $imageCount = null;

	/**
	 * XMLка с описанием альбома	 
	 */
	private $xml = null;
	
	/**
	 * Токен, подтверждающий аутентификацию пользователя
	 * @var string
	 */
	private $token = null;

	/**
	 * Флаг, равный true, если альбом был удален
	 * @var boolean
	 */
	private $isDeleted = false;

	/**
	 * Массив содержащий коллекцию фотографий альбома
	 * @var array
	 */
	private $photoCollection = array();

	/**
	 * Возвращает идентификатор Atom Entry альбома.	 
	 * @return string
	 */
	public function getId(){
		return $this->id;
	}
	
	/**
	 * Возвращает информацию о владельце альбома.
	 * @return string
	 */
	public function getAuthor(){
		return $this->author;
	}

	/**
	 * Возвращает описание альбома
	 * @return string
	 */
	public function getSummary(){
		return $this->summary;
	}

	/**
	 * Возвращает название альбома	 
	 * @return string
	 */
	public function getTitle(){
		return $this->title;
	}
	
	/**
	 * Возвращает ссылку на ресурс альбома
	 * @return string
	 */
	public function getAlbumUrl(){
		return $this->albumUrl;
	}
	
	/**
	 * Возвращает ссылку для редактирования ресурса альбома
	 * @return string
	 */
	public function getAlbumEditUrl(){
		return $this->albumEditUrl;
	}

	/**
	 * Возвращает ссылку на коллекцию фотографий альбома
	 * @return string
	 */
	public function getAlbumPhotosUrl(){
		return $this->albumPhotosUrl;
	}

	/**
	 * По идее возвращает разметкау для Яндекс карт
	 * @return string
	 */	
	public function getYmapsmlUrl(){
		return $this->ymapsmlUrl;
	}

	/**
	 * Возвращает ссылку на веб-страницу альбома в интерфейсе Яндекс.Фоток
	 * @return string
	 */
	public function getAlbumPageUrl(){
		return $this->albumPageUrl;
	}

	/**
	 * Возвращает время создания альбома
	 * @return string
	 */	
	public function getCreatedOn(){
		return $this->createdOn;
	}

	/**
	 * Возвращает время последнего редактирования альбома
	 * @return string
	 */	
	public function getUpdatedOn(){
		return $this->updatedOn;
	}

	/**
	 * Возвращает время последнего значимого с точки зрения системы изменения альбома
	 * (в текущей версии API Фоток любое изменение считается значимым,
	 * вследствие чего значение atom:updated совпадает с app:edited.
	 * @return string
	 */		
	public function getEditedOn(){
		return $this->editedOn;
	}

	/**
	 * Флаг защиты альбома паролем
	 * @return boolean
	 */
	public function isProtected(){
		return $this->isProtected;
	}

	/**
	 * Количество фотографий в альбоме
	 * @return int
	 */
	public function getImageCount(){
		return $this->imageCount;
	}

	/**
	 * XML с описанием альбома
	 * @return boolean
	 */
	public function getXml(){
		return $this->xml;
	}
	
	/**
	 * Проверяет был ли альбом удален вызовом метода delete
	 * @return bool
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
	 */
	public function removePhotoCollection($name){
		unset($this->photoCollection[$name]);
	}	

	/**
	 * Обновляет данные альбома
	 *
	 * @throws YFRequestException
	 * @return void
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
		$response = curl_exec($curl);
		if(curl_getinfo($curl, CURLINFO_HTTP_CODE)!=200){
			throw new YFRequestException($response, curl_getinfo($curl, CURLINFO_HTTP_CODE));
		}

		curl_close($curl);
		$this->reloadXml($this->deleteXmlNamespace($response));
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
	 */
	public function edit($title=null, $summary=null, $password=null, $token=null){
		if($title===null&&$summary===null&&$password===null){
			throw new YFException("Метод должен изменить заголовок, описание или пароль альбома", E_ERROR);
		}

		if($token!==null){
			$this->token = $token;
		}
		else{
			throw new YFAuthenticationException("Эта операция доступна только для аутентифицированных пользователей", E_ERROR);
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
		$response = curl_exec($curl);
		if(curl_getinfo($curl, CURLINFO_HTTP_CODE)!=200){
			throw new YFRequestError($response, curl_getinfo($curl, CURLINFO_HTTP_CODE));
		}

		$this->xml = $this->deleteXmlNamespace($response);
		fclose($putData);
		curl_close($curl);
		$this->refresh();
	}

	/**
	 * Удаляет альбом. В случае успешного удаления альбом будет помечен, как удаленный.
	 * Провреить удлаен ли объект можно с помощью метода isDeleted.
	 *
	 * @throws YFAuthenticationException|YFRequestException
	 * @param string $token аутентификационный токен пользователя, если не был задан, то будет использован токен установленный ранее, в том числе через конструктор
	 * @return void
	 */
	public function delete($token=null){
		if($token!==null){
			$this->token = $token;
		}
		else{
			throw new YFAuthenticationException("Эта операция доступна только для аутентифицированных пользователей", E_ERROR);
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
		$error = curl_exec($curl);
		if(curl_getinfo($curl, CURLINFO_HTTP_CODE)!=204){
			throw new YFRequestException($error, curl_getinfo($curl, CURLINFO_HTTP_CODE));
		}

		curl_close($curl);
		$this->isDeleted=true;
	}	

	/**
	 * Получив на вход XML на ее основе перезаписывает свойства текущего экземпляра классса
	 *
	 * @throws YFXMLException
	 * @param string $xml XML содержащий информацию об Альбоме
	 * @return void
	 */
	private function reloadXml($xml){
		$this->xml = '<?xml version="1.0" encoding="UTF-8"?>'.$xml;

		if(($sxml=simplexml_load_string($xml))===false){
			throw new YFXMLException("Ответ не well-formed XML.".$response, E_ERROR);
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

		foreach ($sxml->link as $link) {
			$rel = $link->attributes()->rel;
			foreach (array('self','edit', 'photos', 'ymapsml', 'alternate') as $a) {
				if ($a == $rel){
					$this->{$a.'_url'} = $link->attributes()->href;
					break;
				}
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