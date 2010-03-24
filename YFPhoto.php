<?php
//!	Класс, который позволяет вам работать с фотографией
class YFPhoto {
	//! Идентификатор Atom Entry фотографии.
	private $id=null;
	//! Cодержит информацию о владельце фотографии. На данный момент информация ограничивается логином пользователя на Яндексе, который указывается во вложенном теге atom:name
	private $author=null;
	//! Название фотографии
	private $title=null;
	//! Дата создания фотографии согласно ее EXIF-данным. Формат времени соответствует RFC3339 без указания часового пояса.
	private $exifDate=null;
	//! Время загрузки фотографии. Формат времени соответствует RFC3339.
	private $publishedOn=null;
	//! Время последнего редактирования фотографии. Формат времени соответствует RFC3339.
	private $editedOn=null;
	//! Время последнего значимого с точки зрения системы изменения альбома (в текущей версии API Фоток любое изменение считается значимым, вследствие чего значение atom:updated совпадает с app:edited. Формат времени соответствует RFC3339.
	private $updatedOn=null;
	//! Уровень доступа к фотографии
	private $accessLevel=null;
	//! Флаг доступности фотографии только взрослой аудитории. Данный параметр может быть установлен только один раз: после того, как фото было помечено "только для взрослых", сбросить данную установку будет невозможно.
	private $isAdultPhoto=null;
	//! Флаг, запрещающий показ оригинала фотографии.
	private $hideOriginalPhoto=null;
	//! Флаг, запрещающий комментирование фотографии.
	private $commentsDisabled=null;
	//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится XL
	private $content=null;
	//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение в оригинальном размере
	private $photoOriginalUrl=null;
	//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 800px
	private $photoXLUrl=null;
	//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 500px
	private $photoLUrl=null;
	//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 300px
	private $photoMUrl=null;
	//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 150px
	private $photoSUrl=null;
	//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 100px
	private $photoXSUrl=null;
	//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 75px
	private $photoXXSUrl=null;
	//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 50px
	private $photoXXXSUrl=null;
	//! Ссылка на ресурс фотографии. Данная ссылка может, например, понадобиться для обращения к ресурсу, если его описание было получено в составе коллекции.
	private $selfUrl = null;
	//! Ccылка для редактирования ресурса  фотографии.
	private $editUrl = null;
	//! Ссылка на web-страницу фотографии в интерфейсе Яндекс.Фоток
	private $webUrl = null;
	//! Ссылка для редактирования содержания ресурса фотографии (графического файла). Для каждой фотографии создается несколько графических файлов разного размера. Для доступа к фотографии в другом размере нужно изменить суффикс в вышеприведенном URL (см. Хранение графического файла фотографии).
	private $editMediaUrl = null;
	//! Ссылка на альбом, в котором содержится фотография.
	private $albumUrl = null;
	//! Флаг того, что фотография была удалена
	private $isDeleted = false;

	//! Возвращает идентификатор Atom Entry фотографии
	/*!
		@return Идентификатор Atom Entry фотографии. Идентификатор является глобально уникальным и позволяет клиентскому приложению однозначно определить некоторый Atom Entry (например, с целью выявления дубликатов при постраничной выдаче коллекций).
	*/
	public function getId(){
		return $this->id;
	}

	//! Возвращает информацию о владельце фотографии.
	/*!
		@return информацию о владельце фотографии. На данный момент информация ограничивается логином пользователя на Яндексе, который указывается во вложенном теге atom:name
	*/
	public function getAuthor(){
		return $this->author;
	}

	//! Возвращает название фотографии
	/*!
		@return название фотографии
	*/
	public function getTitle(){
		return $this->title;
	}

	//! Возвращает время последнего редактирования фотографии.
	/*!
		@return время последнего редактирования фотографии. Формат времени соответствует RFC3339.
	*/
	public function getPublishedOn(){
		return $this->publishedOn;
	}

	//! Возвращает время загрузки фотографии.
	/*!
		@return время загрузки фотографии. Формат времени соответствует RFC3339.
	*/
	public function getEditedOn(){
		return $this->editedOn;
	}

	//! Возвращает время последнего значимого с точки зрения системы изменения альбома
	/*!
		@return время последнего значимого с точки зрения системы изменения альбома (в текущей версии API Фоток любое изменение считается значимым, вследствие чего значение atom:updated совпадает с app:edited. Формат времени соответствует RFC3339.
	*/
	public function getUpdatedOn(){
		return $this->updatedOn;
	}

	//! Возвращает уровень доступа к фотографии
	/*!
		@return уровень доступа к фотографии "Для всех" (по умолчанию) Фотографию может увидеть любой желающий, даже не авторизованный на Яндекс.Фотках. "Для друзей" Фотография доступна загрузившему ее пользователю и всем его "друзьям". Используется совместная с Я.ру система "друзей". "Для себя" Фотографию может просматривать только загрузивший ее пользователь.
	*/
	public function getAccessLevel(){
		return $this->accessLevel;
	}

	//! Возвращает флаг доступности фотографии только взрослой аудитории.
	/*!
		@return флаг доступности фотографии только взрослой аудитории. Данный параметр может быть установлен только один раз: после того, как фото было помечено "только для взрослых", сбросить данную установку будет невозможно.
	*/
	public function isAdultPhoto(){
		return $this->isAdultPhoto;
	}

	//! Возвращает флаг, запрещающий показ оригинала фотографии.
	/*!
		@return флаг, запрещающий показ оригинала фотографии.
	*/
	public function getHideOriginalPhoto(){
		return $this->hideOriginalPhoto;
	}

	//! Возвращает флаг, запрещающий комментирование фотографии.
	/*!
		@return флаг, запрещающий комментирование фотографии.
	*/
	public function commentsDisabled(){
		return $this->commentsDisabled;
	}

	//! Возвращает ссылку на графический файл фотографии.
	/*!
		@return ссыдку на графический файл фотографии.  Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится XL.
	*/
	public function getContent(){
		return $this->content;
	}

	//! Возвращает ссылку на графический файл фотографии в оргинальном размере.
	/*!
		@return  ссылку на графический файл фотографии в оргинальном размере.
	*/
	public function getPhotoOriginalUrl(){
		return $this->photoOriginalUrl;
	}

	//! Возвращает ссылку на графический файл фотографии с шириной 800px.
	/*!
		@return ссылку на графический файл фотографии с шириной 800px.
	*/
	public function getPhotoXLUrl(){
		return $this->photoXLUrl;
	}

	//! Возвращает ссылку на графический файл фотографии с шириной 500px.
	/*!
		@return ссылку на графический файл фотографии с шириной 500px.
	*/
	public function getPhotoLUrl(){
		return $this->photoLUrl;
	}

	//! Возвращает ссылку на графический файл фотографии с шириной 300px.
	/*!
		@return ссылку на графический файл фотографии с шириной 300px.
	*/
	public function getPhotoMUrl(){
		return $this->photoMUrl;
	}

	//! Возвращает ссылку на графический файл фотографии с шириной 150px.
	/*!
		@return ссылку на графический файл фотографии с шириной 150px.
	*/
	public function getPhotoSUrl(){
		return $this->photoSUrl;
	}

	//! Возвращает ссылку на графический файл фотографии с шириной 100px.
	/*!
		@return ссылку на графический файл фотографии с шириной 100px.
	*/
	public function getPhotoXSUrl(){
		return $this->photoXSUrl;
	}

	//! Возвращает ссылку на графический файл фотографии с шириной 75px.
	/*!
		@return ссылку на графический файл фотографии с шириной 75px.
	*/
	public function getPhotoXXSUrl(){
		return $this->photoXXSUrl;
	}

	//! Возвращает ссылку на графический файл фотографии с шириной 50px.
	/*!
		@return ссылку на графический файл фотографии с шириной 50px.
	*/
	public function getPhotoXXXSUrl(){
		return $this->photoXXXSUrl;
	}

	//! Возвращает ссылку на ресурс фотографии.
	/*!
		@return ссылку на ресурс фотографии. Данная ссылка может, например, понадобиться для обращения к ресурсу, если его описание было получено в составе коллекции.
	*/
	public function getSelfUrl(){
		return $this->selfUrl;
	}

	//! Возвращает ссылку для редактирования ресурса фотографии.
	/*!
		@return ссылку для редактирования ресурса фотографии.
	*/
	public function getEditUrl(){
		return $this->editUrl;
	}

	//! Возвращает ссылку на web-страницу фотографии в интерфейсе Яндекс.Фоток
	/*!
		@return ссылку на web-страницу фотографии в интерфейсе Яндекс.Фоток
	*/
	public function getWebUrl(){
		return $this->webUrl;
	}

	//! Возвращает ссылку для редактирования содержания ресурса фотографии (графического файла).
	/*!
		@return  ссылку для редактирования содержания ресурса фотографии (графического файла). Для каждой фотографии создается несколько графических файлов разного размера. Для доступа к фотографии в другом размере нужно изменить суффикс в вышеприведенном URL (см. Хранение графического файла фотографии).
	*/
	public function getEditMediaUrl(){
		return $this->editMediaUrl;
	}

	//! Возвращает ссылку на альбом, в котором содержится фотография.
	/*!
		@return ссылку на альбом, в котором содержится фотография.
	*/
	public function getAlbumUrl(){
		return $this->albumUrl;
	}

	//! Проверяет был ли альбом удален
	/*!
		@return FALSE если альбом не был удален и TRUE если альбом был удален вызовом метода delete
	*/
	public function isDeleted(){
		return $this->isDeleted;
	}

	//! Конструктор фотографии
	/*!
		@param xml Atom Entry фотографии
		@param token токен, подтверждающий аутентификацию пользователя. Не обязательный аргумент. Если не задан, то нельзя будет удалить или отредактировать фотографию, если не введете токен в функцию редактирования или удаления
	*/
	public function __construct($xml, $token=null){
		libxml_use_internal_errors(true);
		$this->token=$token;
		$this->reloadXml($xml);
	}

	//! Удаляет фотографию. В случае успешного удаления фотография будет помечена, как удаленная. Провреить удлаен ли объект можно с помощью метода is_dead
	/*!
		@param token токен, подтверждающий аутентификацию пользователя. Необязательный аргумент. Если не задан, то будет использован токен, который был передан конструктору. Если он тоже не был задан, то метод вызовет исключение.
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

	//! Метод является оберткой для edit и должен упростить работу с его аргументами.
	/*!
		@param args ассоциативный массив, в котором хранятся аргументы, значения которых отличаются от значений по умолчанию. Ключи ассоциативного массива: title, xxx, comments, hide, access, album, token. Точное описание аргументов смотрите в описании метода edit
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

	//! Редактирует свойства фотографии
	/*!
		@param title Название фотографии.
		@param xxx Флаг «для взрослых», write-only (можно только установить, снять нельзя). Значение по умолчанию: "false".
		@param disable_comments Флаг запрета комментариев. Значение по умолчанию: "false".
		@param hide_original Флаг запрета публичного доступа к оригиналу фотографии. Значение по умолчанию: "false". Если данный флаг установлен в "true", автор не сможет получить оригинал фотографии при помощи API Фоток. Для этого нужно воспользоваться возможностями сервиса Яндекс.Фотки.
		@param access Уровень доступа к фотографии. Значение по умолчанию: "public" ("Для всех").
		@param album_url Ссылка на альбом, в котором содержится фотография. Нужно для перемещение фотографии между альбомами.
		@param token Токен, подтверждающий аутентификацию пользователя. Если не задан, используется токен, который был передан конструктору. Если не задан и он, то метод вызовет исключение.
	*/
	public function editEx($title=null, $xxx=false, $disable_comments=false, $hide_original=false, $access="public", $album_url=null, $token=null){

		$changes = false;

		if($token!==null){
			$this->token = $token;
		}
		if($this->token===null){
			throw new AuthenticationError("Эта операция доступна только для аутентифицированных пользователей", E_ERROR);
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
			throw new Exception("Никаких изменений сделано не было", E_ERROR);
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


	//! Обновлеяет свойства фотографии
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

	//! Получив на вход XML на ее основе перезаписывает свойства текущего экземпляра классса
	/*!
		@param XML содержаий описание фотографии в формате атома
	*/
	private function reloadXml($xml){
		//Не проверяется формат XML. Вот неясно стоит ли его проверять или нет.
		$this->xml = '<?xml version="1.0" encoding="UTF-8"?>'.$xml;

		if(($sxml=simplexml_load_string($xml))===false){
			throw new XMLError("Ответ не well-formed XML.".$response, E_ERROR);
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

	//! Удаление информации о пространствах имен. Библиотеки php, работающие с XML просто не в состоянии нормально работать с ними. Плохие, плохие функции.
	/*!
		@param xml строка, содержащая XML, который требуется оскопить
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