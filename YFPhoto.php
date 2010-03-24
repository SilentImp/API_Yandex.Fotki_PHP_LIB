<?php
/**
 * Класс, который позволяет вам работать с фотографией
 *
 * @package YandexFotki
 * @throws YFAuthenticationErrorException|YFException|YFRquestException|YFXMLErrorException
 */
class YFPhoto {

	/**
	 * Идентификатор Atom Entry фотографии.
	 * @var string
	 */
	private $id=null;

	/**
	 * Cодержит информацию о владельце фотографии. На данный момент информация ограничивается логином пользователя на Яндексе, который указывается во вложенном теге atom:name
	 * @var string
	 */
	private $author=null;

	/**
	 * Название фотографии
	 * @var string
	 */
	private $title=null;
	
	/**
	 * Дата создания фотографии согласно ее EXIF-данным. Формат времени соответствует RFC3339 без указания часового пояса.
	 * @var string
	 */
	private $exifDate=null;

	/**
	 * Время загрузки фотографии. Формат времени соответствует RFC3339.
	 * @var string
	 */
	private $publishedOn=null;
	
	/**
	 * Время последнего редактирования фотографии. Формат времени соответствует RFC3339.
	 * @var string
	 */
	private $editedOn=null;
		
	/**
	 * Время последнего значимого с точки зрения системы изменения альбома (в текущей версии API Фоток любое изменение считается значимым, вследствие чего значение atom:updated совпадает с app:edited. Формат времени соответствует RFC3339.
	 * @var string
	 */
	private $updatedOn=null;
	
	/**
	 * Уровень доступа к фотографии
	 * @var string
	 */
	private $accessLevel=null;
	
	/**
	 * Флаг доступности фотографии только взрослой аудитории. Данный параметр может быть установлен только один раз: после того, как фото было помечено "только для взрослых", сбросить данную установку будет невозможно.
	 * @var boolean
	 */
	private $isAdultPhoto=null;
	
	/**
	 * Флаг, запрещающий показ оригинала фотографии.
	 * @var boolean
	 */	
	private $hideOriginalPhoto=null;
	
	/**
	 * Флаг, запрещающий комментирование фотографии.
	 * @var boolean
	 */
	private $commentsDisabled=null;

	/**
	 * Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится XL
	 * @var string
	 */
	private $content=null;

	/**
	 * Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение в оригинальном размере
	 * @var string
	 */
	private $photoOriginalUrl=null;

	/**
	 * Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 800px
	 * @var string
	 */
	private $photoXLUrl=null;
	
	/**
	 * Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 500px
	 * @var string
	 */
	private $photoLUrl=null;
	
	/**
	 * Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 300px
	 * @var string
	 */
	private $photoMUrl=null;
	
	/**
	 * Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 150px
	 * @var string
	 */
	private $photoSUrl=null;
	
	/**
	 * Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 100px
	 * @var string
	 */
	private $photoXSUrl=null;
	
	/**
	 * Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 75px
	 * @var string
	 */
	private $photoXXSUrl=null;
	
	/**
	 * Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 50px
	 * @var string
	 */
	private $photoXXXSUrl=null;
	
	/**
	 * Ссылка на ресурс фотографии. Данная ссылка может, например, понадобиться для обращения к ресурсу, если его описание было получено в составе коллекции.
	 * @var string
	 */
	private $selfUrl = null;
	
	/**
	 * Ccылка для редактирования ресурса фотографии.
	 * @var string
	 */
	private $editUrl = null;
	
	/**
	 * Ссылка на web-страницу фотографии в интерфейсе Яндекс.Фоток
	 * @var string
	 */
	private $webUrl = null;

	/**
	 * Ссылка для редактирования содержания ресурса фотографии (графического файла). Для каждой фотографии создается несколько графических файлов разного размера. Для доступа к фотографии в другом размере нужно изменить суффикс в вышеприведенном URL (см. Хранение графического файла фотографии).
	 * @var string
	 */
	private $editMediaUrl = null;

	/**
	 * Ссылка на альбом, в котором содержится фотография.
	 * @var string
	 */
	private $albumUrl = null;
	

	/**
	 * Флаг того, что фотография была удалена
	 * @var string
	 */
	private $isDeleted = false;

	/**
	 * Возвращает идентификатор Atom Entry фотографии. Идентификатор является глобально уникальным и позволяет клиентскому приложению однозначно определить некоторый Atom Entry (например, с целью выявления дубликатов при постраничной выдаче коллекций).
	 * @return string
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Возвращает  информацию о владельце фотографии. На данный момент информация ограничивается логином пользователя на Яндексе, который указывается во вложенном теге atom:name
	 * @return string
	 */
	public function getAuthor(){
		return $this->author;
	}

	/**
	 * Возвращает название фотографии
	 * @return string
	 */
	public function getTitle(){
		return $this->title;
	}

	/**
	 * Возвращает время последнего редактирования фотографии.
	 * @return string
	 */
	public function getPublishedOn(){
		return $this->publishedOn;
	}

	/**
	 * Возвращает время загрузки фотографии. Формат времени соответствует RFC3339.
	 * @return string
	 */
	public function getEditedOn(){
		return $this->editedOn;
	}

	/**
	 * Возвращает время последнего значимого с точки зрения системы изменения альбома (в текущей версии API Фоток любое изменение считается значимым, вследствие чего значение atom:updated совпадает с app:edited. Формат времени соответствует RFC3339.
	 * @return string
	 */
	public function getUpdatedOn(){
		return $this->updatedOn;
	}

	/**
	 * Возвращает уровень доступа к фотографии "Для всех" (по умолчанию) Фотографию может увидеть любой желающий, даже не авторизованный на Яндекс.Фотках. "Для друзей" Фотография доступна загрузившему ее пользователю и всем его "друзьям". Используется совместная с Я.ру система "друзей". "Для себя" Фотографию может просматривать только загрузивший ее пользователь.
	 * @return string
	 */
	public function getAccessLevel(){
		return $this->accessLevel;
	}

	/**
	 * Возвращает флаг доступности фотографии только взрослой аудитории. Данный параметр может быть установлен только один раз: после того, как фото было помечено "только для взрослых", сбросить данную установку будет невозможно.
	 * @return boolean
	 */
	public function isAdultPhoto(){
		return $this->isAdultPhoto;
	}

	/**
	 * Возвращает флаг, запрещающий показ оригинала фотографии.
	 * @return boolean
	 */
	public function getHideOriginalPhoto(){
		return $this->hideOriginalPhoto;
	}

	/**
	 * Возвращает флаг, запрещающий комментирование фотографии.
	 * @return boolean
	 */
	public function commentsDisabled(){
		return $this->commentsDisabled;
	}

	/**
	 * Возвращает  ссылку на графический файл фотографии.  Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится XL.
	 * @return string
	 */
	public function getContent(){
		return $this->content;
	}

	/**
	 * Возвращает ссылку на графический файл фотографии в оргинальном размере.
	 * @return string
	 */
	public function getPhotoOriginalUrl(){
		return $this->photoOriginalUrl;
	}

	/**
	 * Возвращает ссылку на графический файл фотографии с шириной 800px.
	 * @return string
	 */
	public function getPhotoXLUrl(){
		return $this->photoXLUrl;
	}

	/**
	 * Возвращает ссылку на графический файл фотографии с шириной 500px.
	 * @return string
	 */
	public function getPhotoLUrl(){
		return $this->photoLUrl;
	}

	/**
	 * Возвращает ссылку на графический файл фотографии с шириной 300px.
	 * @return string
	 */
	public function getPhotoMUrl(){
		return $this->photoMUrl;
	}

	/**
	 * Возвращает ссылку на графический файл фотографии с шириной 150px.
	 * @return string
	 */
	public function getPhotoSUrl(){
		return $this->photoSUrl;
	}

	/**
	 * Возвращает ссылку на графический файл фотографии с шириной 100px.
	 * @return string
	 */
	public function getPhotoXSUrl(){
		return $this->photoXSUrl;
	}

	/**
	 * Возвращает ссылку на графический файл фотографии с шириной 75px.
	 * @return string
	 */
	public function getPhotoXXSUrl(){
		return $this->photoXXSUrl;
	}

	/**
	 * Возвращает ссылку на графический файл фотографии с шириной 50px.
	 * @return string
	 */
	public function getPhotoXXXSUrl(){
		return $this->photoXXXSUrl;
	}

	/**
	 * Возвращает ссылку на ресурс фотографии. Данная ссылка может, например, понадобиться для обращения к ресурсу, если его описание было получено в составе коллекции.
	 * @return string
	 */
	public function getSelfUrl(){
		return $this->selfUrl;
	}

	/**
	 * Возвращает ссылку для редактирования ресурса фотографии.
	 * @return string
	 */
	public function getEditUrl(){
		return $this->editUrl;
	}

	/**
	 * Возвращает ссылку на web-страницу фотографии в интерфейсе Яндекс.Фоток
	 * @return string
	 */
	public function getWebUrl(){
		return $this->webUrl;
	}

	/**
	 * Возвращает ссылку для редактирования содержания ресурса фотографии (графического файла). Для каждой фотографии создается несколько графических файлов разного размера. Для доступа к фотографии в другом размере нужно изменить суффикс в вышеприведенном URL (см. Хранение графического файла фотографии).
	 * @return string
	 */
	public function getEditMediaUrl(){
		return $this->editMediaUrl;
	}

	/**
	 * Возвращает ссылку на альбом, в котором содержится фотография.
	 * @return string
	 */
	public function getAlbumUrl(){
		return $this->albumUrl;
	}

	/**
	 * Проверяет был ли альбом удален.
	 * 
	 * Вернет FALSE если альбом не был удален и TRUE если альбом был удален вызовом метода delete
	 *
	 * @return boolean
	 */
	public function isDeleted(){
		return $this->isDeleted;
	}

	/**
	 * Конструктор фотографии
	 * @param string $xml Atom Entry фотографии
	 * @param string $token токен, подтверждающий аутентификацию пользователя. Не обязательный аргумент. Если не задан, то нельзя будет удалить или отредактировать фотографию, если не введете токен в функцию редактирования или удаления
	 * @return void
	 */
	public function __construct($xml, $token=null){
		libxml_use_internal_errors(true);
		$this->token=$token;
		$this->reloadXml($xml);
	}

	/**
	 * Удаляет фотографию. В случае успешного удаления фотография будет помечена, как удаленная. Провреить удлаен ли объект можно с помощью метода is_dead
	 * 
	 * @throws YFAuthenticationException|YFRequestException
	 * @param string $token токен, подтверждающий аутентификацию пользователя. Не обязательный аргумент. Если не задан, то нельзя будет удалить или отредактировать фотографию, если не введете токен в функцию редактирования или удаления
	 * @return void
	 */
	public function delete($token){
		if($token!==null){
			$this->token = $token;
		}
		if($this->token===null){
			throw new YFAuthenticationException("Эта операция доступна только для аутентифицированных пользователей", E_ERROR);
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->editUrl);
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
			throw new YFRequestException($error, E_ERROR);
		}
		curl_close($curl);
		$this->isDeleted=true;
	}

	/**
	 * Метод является оберткой для edit и должен упростить работу с его аргументами.
	 * 
	 * @param array $args ассоциативный массив, в котором хранятся аргументы, значения которых отличаются от значений по умолчанию. Ключи ассоциативного массива: title, xxx, comments, hide, access, album, token. Точное описание аргументов смотрите в описании метода edit
	 * @return void
	 */
	public function edit($args=array()){

		if(array_key_exists("token", $args)){
			$token=$args["token"];
		}else{
			$token=null;
		}

		if(array_key_exists("album", $args)){
			$album_url=$args["album"];
		}else{
			$album_url=null;
		}

		if(array_key_exists("access", $args)){
			$access=$args["access"];
		}else{
			$access="public";
		}

		if(array_key_exists("hide", $args)){
			$hide_original=$args["hide"];
		}else{
			$hide_original=false;
		}

		if(array_key_exists("comments", $args)){
			$disable_comments=$args["comments"];
		}else{
			$disable_comments=false;
		}

		if(array_key_exists("xxx", $args)){
			$xxx=$args["xxx"];
		}else{
			$xxx=false;
		}

		if(array_key_exists("title", $args)){
			$title=$args["title"];
		}else{
			$title=null;
		}

		$this->editEx($title, $xxx, $disable_comments, $hide_original, $access, $album_url, $token);
	}

	/**
	 * Редактирует свойства фотографии
	 * 
	 * @throws YFRequestException|YFAuthenticationErrorException|YFXMLErrorException
	 * @param string $title Название фотографии.
	 * @param boolean $xxx Флаг «для взрослых», write-only (можно только установить, снять нельзя). Значение по умолчанию: "false".
	 * @param boolean $disable_comments Флаг запрета комментариев. Значение по умолчанию: "false".
	 * @param boolean $hide_original Флаг запрета публичного доступа к оригиналу фотографии. Значение по умолчанию: "false". Если данный флаг установлен в "true", автор не сможет получить оригинал фотографии при помощи API Фоток. Для этого нужно воспользоваться возможностями сервиса Яндекс.Фотки.
	 * @param string $access Уровень доступа к фотографии. Значение по умолчанию: "public" ("Для всех").
	 * @param string $album_url Ссылка на альбом, в котором содержится фотография. Нужно для перемещение фотографии между альбомами.
	 * @param string $token Токен, подтверждающий аутентификацию пользователя. Если не задан, используется токен, который был передан конструктору. Если не задан и он, то метод вызовет исключение.
	 * @return void
	 */
	public function editEx($title=null, $xxx=false, $disable_comments=false, $hide_original=false, $access="public", $album_url=null, $token=null){

		$changes = false;

		if($token!==null){
			$this->token = $token;
		}
		if($this->token===null){
			throw new YFAuthenticationErrorException("Эта операция доступна только для аутентифицированных пользователей", E_ERROR);
		}

		if($title!=null&&$title!=$this->title){
			$this->title = $title;
			$changes = true;
		}

		if((boolean)$xxx!=$this->isAdultPhoto){
			$this->isAdultPhoto = (boolean)$xxx;
			$changes = true;
		}

		if((boolean)$disable_comments!=$this->commentsDisabled){
			$this->commentsDisabled = (boolean)$disable_comments;
			$changes = true;
		}

		if((boolean)$hide_original!=$this->hideOriginalPhoto){
			$this->hideOriginalPhoto = (boolean)$hide_original;
			$changes = true;
		}

		if(!in_array($access, array("public","friends","private"))){
			$access="public";
		}

		if($access!=$this->accessLevel){
			$this->accessLevel = $access;
			$changes = true;
		}

		if($album_url!=null&&$album_url!=$this->albumUrl){
			$this->albumUrl = $album_url;
			$changes = true;
		}

		if($changes === false){
			throw new YFException("Никаких изменений сделано не было", E_ERROR);
		}

		$message = '
					<entry>
						<id>'.$this->id.'</id>
						<title>'.$this->title.'</title>
						<author>
							<name>'.$this->author.'</name>
						</author>
						<link href="'.$this->selfUrl.'" rel="self" />
						<link href="'.$this->author.'" rel="edit" />
						<link href="'.$this->author.'" rel="alternate" />
						<link href="'.$this->author.'" rel="edit-media" />
						<link href="'.$this->author.'" rel="album" />
						<published>'.$this->author.'</published>
						<app:edited>'.$this->author.'</app:edited>
						<updated>'.$this->author.'</updated>
						<f:created>'.$this->author.'</f:created>
						<f:access value="'.$this->author.'" />
						<f:xxx value="'.$this->author.'" />
						<f:hide_original value="'.$this->author.'" />
						<f:disable_comments value="'.$this->author.'" />
						<content src="'.$this->author.'" type="image/*" />
					</entry>';

		fwrite($putData, $message);
		fseek($putData, 0);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->editUrl);
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
			throw new YFRequestException($response, E_ERROR);
		}

		$this->xml = $this->deleteXmlNamespace($response);
		fclose($putData);
		curl_close($curl);
		$this->refresh();
	}

	/**
	 * Обновляет свойства фотографии
	 * 
	 * @throws YFRequestException|YFXMLErrorException
	 * @return void
	 */
	public function refresh(){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->editUrl);
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
			throw new YFRequestException($response, E_ERROR);
		}
		curl_close($curl);
		$this->reloadXml($this->deleteXmlNamespace($response));
	}

	/**
	 * Получив на вход XML на ее основе перезаписывает свойства текущего экземпляра классса
	 * 
	 * @throws YFXMLErrorException
	 * @param string $xml XML содержаий описание фотографии в формате атома
	 * @return void
	 */
	private function reloadXml($xml){
		//Не проверяется формат XML. Вот неясно стоит ли его проверять или нет.
		$this->xml = '<?xml version="1.0" encoding="UTF-8"?>'.$xml;

		if(($sxml=simplexml_load_string($xml))===false){
			throw new YFXMLErrorException("Ответ не well-formed XML.".$response, E_ERROR);
		}
		$this->id = $sxml->id;
		$this->author = $sxml->author->name;
		$this->title = $sxml->title;
		$this->publishedOn = $sxml->published;
		$this->editedOn = $sxml->edited;
		$this->exifDate = $sxml->created;
		$this->updatedOn = $sxml->updated;

		if($sxml->xxx->attributes()->value=="false"){
			$this->isAdultPhoto = false;
		}else{
			$this->isAdultPhoto = true;
		}

		if($sxml->hide_original->attributes()->value=="false"){
			$this->hideOriginalPhoto = false;
		}else{
			$this->hideOriginalPhoto = true;
		}

		if($sxml->disable_comments->attributes()->value=="false"){
			$this->commentsDisabled = false;
		}else{
			$this->commentsDisabled = true;
		}

		$this->accessLevel  = $sxml->access->attributes()->value;
		$this->content = $sxml->content->attributes()->src;

		$photos_resourse = substr($sxml->content->attributes()->src,0,strlen($sxml->content->attributes()->src)-2);
		$this->photoOriginalUrl = $photos_resourse."orig";
		$this->photoXLUrl = $sxml->content->attributes()->src;
		$this->photoLUrl = $photos_resourse."L";
		$this->photoMUrl = $photos_resourse."M";
		$this->photoSUrl = $photos_resourse."S";
		$this->photoXSUrl = $photos_resourse."XS";
		$this->photoXXSUrl = $photos_resourse."XXS";
		$this->photoXXXSUrl = $photos_resourse."XXXS";

		foreach($sxml->link as $link){
			switch($link->attributes()->rel){
				case "self":
					$this->selfUrl = $link->attributes()->href;
					break;
				case "edit":
					$this->editUrl = $link->attributes()->href;
					break;
				case "edit-media":
					$this->editMediaUrl = $link->attributes()->href;
					break;
				case "album":
					$this->albumUrl = $link->attributes()->href;
					break;
				case "alternate":
					$this->webUrl = $link->attributes()->href;
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