<?php
	//!	Класс, который позволяет вам работать с фотографией
	/*!
		@author SilentImp
		@author http://twitter.com/SilentImp
		@author http://silentimp.habrahabr.ru/
		@author <a href="mailto:ravenb@mail.ru">ravenb@mail.ru</a>
		@ingroup yandex_fotki
	*/
	class yandex_fotki_photo{
		//! Идентификатор Atom Entry фотографии.
		private $id=null;
		//! Cодержит информацию о владельце фотографии. На данный момент информация ограничивается логином пользователя на Яндексе, который указывается во вложенном теге atom:name
		private $author=null;
		//! Название фотографии
		private $title=null;
		//! Дата создания фотографии согласно ее EXIF-данным. Формат времени соответствует RFC3339 без указания часового пояса.
		private $created_date=null;
		//! Время загрузки фотографии. Формат времени соответствует RFC3339.
		private $published_date=null;
		//! Время последнего редактирования фотографии. Формат времени соответствует RFC3339.
		private $edited_date=null;
		//! Время последнего значимого с точки зрения системы изменения альбома (в текущей версии API Фоток любое изменение считается значимым, вследствие чего значение atom:updated совпадает с app:edited. Формат времени соответствует RFC3339.
		private $updated_date=null;
		//! Уровень доступа к фотографии
		private $access=null;
		//! Флаг доступности фотографии только взрослой аудитории. Данный параметр может быть установлен только один раз: после того, как фото было помечено "только для взрослых", сбросить данную установку будет невозможно.
		private $xxx=null;
		//! Флаг, запрещающий показ оригинала фотографии.
		private $hide_original=null;
		//! Флаг, запрещающий комментирование фотографии.
		private $disable_comments=null;
		//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится XL
		private $content=null;
		//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение в оригинальном размере
		private $photo_orig=null;
		//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 800px
		private $photo_XL=null;
		//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 500px
		private $photo_L=null;
		//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 300px
		private $photo_M=null;
		//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 150px
		private $photo_S=null;
		//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 100px
		private $photo_XS=null;
		//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 75px
		private $photo_XXS=null;
		//! Ссылка на графический файл фотографии. Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится изображение шириной в 50px
		private $photo_XXXS=null;
		//! Ссылка на ресурс фотографии. Данная ссылка может, например, понадобиться для обращения к ресурсу, если его описание было получено в составе коллекции. 
		private $self_url = null;
		//! Ccылка для редактирования ресурса  фотографии.
		private $edit_url = null;
		//! Ссылка на web-страницу фотографии в интерфейсе Яндекс.Фоток
		private $alternate_url = null;
		//! Ссылка для редактирования содержания ресурса фотографии (графического файла). Для каждой фотографии создается несколько графических файлов разного размера. Для доступа к фотографии в другом размере нужно изменить суффикс в вышеприведенном URL (см. Хранение графического файла фотографии).
		private $edit_media_url = null;
		//! Ссылка на альбом, в котором содержится фотография.
		private $album_url = null;
		//! Флаг того, что фотография была удалена
		private $dead = false;
		
		//! Возвращает идентификатор Atom Entry фотографии
		/*!
			@return Идентификатор Atom Entry фотографии. Идентификатор является глобально уникальным и позволяет клиентскому приложению однозначно определить некоторый Atom Entry (например, с целью выявления дубликатов при постраничной выдаче коллекций).
		*/
		public function get_id(){
			return $this->id;
		}
		
		//! Возвращает информацию о владельце фотографии.
		/*!
			@return информацию о владельце фотографии. На данный момент информация ограничивается логином пользователя на Яндексе, который указывается во вложенном теге atom:name
		*/
		public function get_author(){
			return $this->author;
		}

		//! Возвращает название фотографии
		/*!
			@return название фотографии
		*/
		public function get_title(){
			return $this->title;
		}

		//! Возвращает время последнего редактирования фотографии.
		/*!
			@return время последнего редактирования фотографии. Формат времени соответствует RFC3339.
		*/
		public function get_published_date(){
			return $this->published_date;
		}

		//! Возвращает время загрузки фотографии.
		/*!
			@return время загрузки фотографии. Формат времени соответствует RFC3339.
		*/		
		public function get_edited_date(){
			return $this->edited_date;
		}
		
		//! Возвращает время последнего значимого с точки зрения системы изменения альбома
		/*!
			@return время последнего значимого с точки зрения системы изменения альбома (в текущей версии API Фоток любое изменение считается значимым, вследствие чего значение atom:updated совпадает с app:edited. Формат времени соответствует RFC3339.
		*/
		public function get_updated_date(){
			return $this->updated_date;
		}
		
		//! Возвращает уровень доступа к фотографии
		/*!
			@return уровень доступа к фотографии "Для всех" (по умолчанию) Фотографию может увидеть любой желающий, даже не авторизованный на Яндекс.Фотках. "Для друзей" Фотография доступна загрузившему ее пользователю и всем его "друзьям". Используется совместная с Я.ру система "друзей". "Для себя" Фотографию может просматривать только загрузивший ее пользователь.
		*/
		public function get_access(){
			return $this->access;
		}

		//! Возвращает флаг доступности фотографии только взрослой аудитории.
		/*!
			@return флаг доступности фотографии только взрослой аудитории. Данный параметр может быть установлен только один раз: после того, как фото было помечено "только для взрослых", сбросить данную установку будет невозможно.
		*/
		public function get_xxx(){
			return $this->xxx;
		}

		//! Возвращает флаг, запрещающий показ оригинала фотографии.
		/*!
			@return флаг, запрещающий показ оригинала фотографии. 
		*/
		public function get_hide_original(){
			return $this->hide_original;
		}
		
		//! Возвращает флаг, запрещающий комментирование фотографии.
		/*!
			@return флаг, запрещающий комментирование фотографии. 
		*/
		public function get_disable_comments(){
			return $this->disable_comments;
		}
		
		//! Возвращает ссылку на графический файл фотографии.
		/*!
			@return ссыдку на графический файл фотографии.  Для каждой фотографии создается несколько графических файлов разного размера. Тут хранится XL.
		*/
		public function get_content(){
			return $this->content;
		}
		
		//! Возвращает ссылку на графический файл фотографии в оргинальном размере.
		/*!
			@return  ссылку на графический файл фотографии в оргинальном размере.
		*/
		public function get_orig(){
			return $this->photo_orig;
		}

		//! Возвращает ссылку на графический файл фотографии с шириной 800px.
		/*!
			@return ссылку на графический файл фотографии с шириной 800px.
		*/
		public function get_XL(){
			return $this->photo_XL;
		}
		
		//! Возвращает ссылку на графический файл фотографии с шириной 500px.
		/*!
			@return ссылку на графический файл фотографии с шириной 500px.
		*/
		public function get_L(){
			return $this->photo_L;
		}
		
		//! Возвращает ссылку на графический файл фотографии с шириной 300px.
		/*!
			@return ссылку на графический файл фотографии с шириной 300px.
		*/
		public function get_M(){
			return $this->photo_M;
		}
		
		//! Возвращает ссылку на графический файл фотографии с шириной 150px.
		/*!
			@return ссылку на графический файл фотографии с шириной 150px.
		*/
		public function get_S(){
			return $this->photo_S;
		}
		
		//! Возвращает ссылку на графический файл фотографии с шириной 100px.
		/*!
			@return ссылку на графический файл фотографии с шириной 100px.
		*/
		public function get_XS(){
			return $this->photo_XS;
		}
		
		//! Возвращает ссылку на графический файл фотографии с шириной 75px.
		/*!
			@return ссылку на графический файл фотографии с шириной 75px.
		*/
		public function get_XXS(){
			return $this->photo_XXS;
		}
		
		//! Возвращает ссылку на графический файл фотографии с шириной 50px.
		/*!
			@return ссылку на графический файл фотографии с шириной 50px.
		*/
		public function get_XXXS(){
			return $this->photo_XXXS;
		}
		
		//! Возвращает ссылку на ресурс фотографии.
		/*!
			@return ссылку на ресурс фотографии. Данная ссылка может, например, понадобиться для обращения к ресурсу, если его описание было получено в составе коллекции.
		*/
		public function get_self_url(){
			return $this->self_url;
		}
		
		//! Возвращает ссылку для редактирования ресурса фотографии.
		/*!
			@return ссылку для редактирования ресурса фотографии.
		*/
		public function get_edit_url(){
			return $this->edit_url;
		}
		
		//! Возвращает ссылку на web-страницу фотографии в интерфейсе Яндекс.Фоток
		/*!
			@return ссылку на web-страницу фотографии в интерфейсе Яндекс.Фоток
		*/
		public function get_alternate_url(){
			return $this->alternate_url;
		}
		
		//! Возвращает ссылку для редактирования содержания ресурса фотографии (графического файла).
		/*!
			@return  ссылку для редактирования содержания ресурса фотографии (графического файла). Для каждой фотографии создается несколько графических файлов разного размера. Для доступа к фотографии в другом размере нужно изменить суффикс в вышеприведенном URL (см. Хранение графического файла фотографии).
		*/
		public function get_edit_media_url(){
			return $this->edit_media_url;
		}
		
		//! Возвращает ссылку на альбом, в котором содержится фотография.
		/*!
			@return ссылку на альбом, в котором содержится фотография.
		*/
		public function get_album_url(){
			return $this->album_url;
		}
		
		//! Проверяет был ли альбом удален
		/*!
			@return FALSE если альбом не был удален и TRUE если альбом был удален вызовом метода delete
		*/
		public function is_dead(){
			return $this->dead;	
		}
		
		//! Конструктор фотографии
		/*!
			@param xml Atom Entry фотографии
			@param token токен, подтверждающий аутентификацию пользователя. Не обязательный аргумент. Если не задан, то нельзя будет удалить или отредактировать фотографию, если не введете токен в функцию редактирования или удаления
		*/
		public function __construct($xml, $token=null){
			$this->token=$token;
			$this->reload_xml($xml);
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
				throw new Exception("Эта операция доступна только для аутентифицированных пользователей", E_ERROR);
			}
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->edit_url);
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
				throw new Exception($error, E_ERROR);
			}
			curl_close($curl);
			$this->dead=true;
		}
		
		//! Метод является оберткой для edit и должен упростить работу с его аргументами.
		/*!
			@param args ассоциативный массив, в котором хранятся аргументы, значения которых отличаются от значений по умолчанию. Ключи ассоциативного массива: title, xxx, comments, hide, access, album, token. Точное описание аргументов смотрите в описании метода edit
		*/
		public function ed($args=array()){
			
			if(!array_key_exists($args, "token")){
				$token=$args["token"];
			}else{
				$token=null;
			}
			
			if(!array_key_exists($args, "album")){
				$album_url=$args["album"];
			}else{
				$album_url=null;
			}
			
			if(!array_key_exists($args, "access")){
				$access=$args["access"];
			}else{
				$access="public";
			}
			
			if(!array_key_exists($args, "hide")){
				$hide_original=$args["hide"];
			}else{
				$hide_original=false;
			}
			
			if(!array_key_exists($args, "comments")){
				$disable_comments=$args["comments"];
			}else{
				$disable_comments=false;
			}
			
			if(!array_key_exists($args, "xxx")){
				$xxx=$args["xxx"];
			}else{
				$xxx=false;
			}
			
			if(!array_key_exists($args, "title")){
				$title=$args["title"];
			}else{
				$title=null;
			}
			
			$this->edit($title, $xxx, $disable_comments, $hide_original, $access, $album_url, $token);
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
		public function edit($title=null, $xxx=false, $disable_comments=false, $hide_original=false, $access="public", $album_url=null, $token=null){
			
			$changes = false;
			
			if($token!==null){
				$this->token = $token;
			}
			if($this->token===null){
				throw new Exception("Эта операция доступна только для аутентифицированных пользователей", E_ERROR);
			}
			
			if($title!=null&&$title!=$this->title){
				$this->title = $title;
				$changes = true;
			}

			if((boolean)$xxx!=$this->xxx){
				$this->xxx = (boolean)$xxx;
				$changes = true;
			}
			
			if((boolean)$disable_comments!=$this->disable_comments){
				$this->disable_comments = (boolean)$disable_comments;
				$changes = true;
			}
			
			if((boolean)$hide_original!=$this->hide_original){
				$this->hide_original = (boolean)$hide_original;
				$changes = true;
			}
			
			if(!in_array($access, array("public","friends","private"))){
				$access="public";
			}
			
			if($access!=$this->access){
				$this->access = $access;
				$changes = true;
			}
			
			if($album_url!=null&&$album_url!=$this->album_url){
				$this->album_url = $album_url;
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
							<link href="'.$this->self_url.'" rel="self" />
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
			curl_setopt($curl, CURLOPT_URL, $this->edit_url);
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
				throw new Exception($response, E_ERROR);
			}
			
			$this->xml = $this->delete_ns($response);
			fclose($putData); 
			curl_close($curl);
			$this->refresh();
		}
		
		
		//! Обновлеяет свойства фотографии
		public function refresh(){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->edit_url);
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
				throw new Exception($response, E_ERROR);
			}
			curl_close($curl);
			$this->reload_xml($this->delete_ns($response));
		}
		
		//! Получив на вход XML на ее основе перезаписывает свойства текущего экземпляра классса
		/*!
			@param XML содержаий описание фотографии в формате атома
		*/
		private function reload_xml($xml){
			//Не проверяется формат XML. Вот неясно стоит ли его проверять или нет.
			$this->xml = '<?xml version="1.0" encoding="UTF-8"?>'.$xml;
			
			if(($sxml=simplexml_load_string($xml))===false){
				throw new Exception("Ответ не well-formed XML.".$response, E_ERROR);
			}
			$this->id = $sxml->id;
			$this->author = $sxml->author->name;
			$this->title = $sxml->title;
			$this->published_date = $sxml->published;
			$this->edited_date = $sxml->edited;
			$this->created_date = $sxml->created;
			$this->updated_date = $sxml->updated;
			
			if($sxml->xxx->attributes()->value=="false"){
				$this->xxx = false;
			}else{
				$this->xxx = true;
			}
			
			if($sxml->hide_original->attributes()->value=="false"){
				$this->hide_original = false;
			}else{
				$this->hide_original = true;
			}
			
			if($sxml->disable_comments->attributes()->value=="false"){
				$this->disable_comments = false;
			}else{
				$this->disable_comments = true;
			}
			
			$this->access  = $sxml->access->attributes()->value;
			$this->content = $sxml->content->attributes()->src;
			
			$photos_resourse = substr($sxml->content->attributes()->src,0,strlen($sxml->content->attributes()->src)-2);
			$this->photo_orig = $photos_resourse."orig";
			$this->photo_XL = $sxml->content->attributes()->src;
			$this->photo_L = $photos_resourse."L";
			$this->photo_M = $photos_resourse."M";
			$this->photo_S = $photos_resourse."S";
			$this->photo_XS = $photos_resourse."XS";
			$this->photo_XXS = $photos_resourse."XXS";
			$this->photo_XXXS = $photos_resourse."XXXS";
			
		
			foreach($sxml->link as $link){
				switch($link->attributes()->rel){
					case "self":
						$this->self_url = $link->attributes()->href;
						break;
					case "edit":
						$this->edit_url = $link->attributes()->href;
						break;
					case "edit-media":
						$this->edit_media_url = $link->attributes()->href;
						break;
					case "album":
						$this->album_url = $link->attributes()->href;
						break;
					case "alternate":
						$this->alternate_url = $link->attributes()->href;
						break;
				}
			}
		}
		
		//! Удаление информации о пространствах имен. Библиотеки php, работающие с XML просто не в состоянии нормально работать с ними. Плохие, плохие функции.
		/*! 
			@param xml строка, содержащая XML, который требуется оскопить
		*/
		private function delete_ns($xml){
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
?>