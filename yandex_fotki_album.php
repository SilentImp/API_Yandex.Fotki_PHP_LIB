<?php
	//!	Класс, который позволяет вам работать с альбомом
	/*!
		@author SilentImp
		@author http://twitter.com/SilentImp
		@author http://silentimp.habrahabr.ru/
		@author <a href="mailto:ravenb@mail.ru">ravenb@mail.ru</a>
		@ingroup yandex_fotki
	*/
	class yandex_fotki_album{
		//! Идентификатор Atom Entry альбома. Идентификатор является глобально уникальным и позволяет клиентскому приложению однозначно определить некоторый Atom Entry (например, с целью выявления дубликатов при постраничной выдаче коллекций).
		private $id = null;
		//! Cодержит информацию о владельце альбома. На данный момент информация ограничивается логином пользователя на Яндексе, который указывается во вложенном теге
		private $author = null;
		//! Название альбома
		private $title = null;
		//! Описание альбома
		private $summary = null;
		//! Ссылка на ресурс альбома
		private $self_url = null;
		//! Ссылка для редактирования ресурса альбома
		private $edit_url = null;
		//! Ссылка на коллекцию фотографий альбома
		private $photos_url = null;
		//! Хрен его знает, что это за чудо
		private $ymapsml_url = null;
		//! Ссылка на веб-страницу альбома в интерфейсе Яндекс.Фоток
		private $alternate_url = null;
		//! Время создания альбома
		private $published_date = null;
		//! Время последнего редактирования альбома
		private $edited_date = null;
		//! Время последнего значимого с точки зрения системы изменения альбома (в текущей версии API Фоток любое изменение считается значимым, вследствие чего значение atom:updated совпадает с app:edited
		private $updated_date = null;
		//! Флаг защиты альбома паролем
		private $protected = false;
		//! Количество фотографий в альбоме
		private $image_count = null;
		//! XMLка с описанием альбома
		private $xml = null;
		//! Токен, подтверждающий аутентификацию пользователя
		private $token=null;
		//! Флаг, равный true, если альбом был удален
		private $dead = false;
		//! Массив содержащий коллекцию фотографий альбома
		private $photo_collection = array();
		
		//! Возвращает идентификатор Atom Entry альбома.
		/*!
			@return Идентификатор Atom Entry альбома. Идентификатор является глобально уникальным и позволяет клиентскому приложению однозначно определить некоторый Atom Entry (например, с целью выявления дубликатов при постраничной выдаче коллекций).
		*/
		public function get_id(){
			return $this->id;
		}
		
		//! Возвращает информацию о владельце альбома.
		/*!
			@return информацию о владельце альбома. На данный момент информация ограничивается логином пользователя на Яндексе, который указывается во вложенном теге
		*/
		public function get_author(){
			return $this->author;
		}

		//! Возвращает описание альбома
		/*!
			@return описание альбома
		*/		
		public function get_summary(){
			return $this->summary;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_title(){
			return $this->title;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_self_url(){
			return $this->self_url;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_edit_url(){
			return $this->edit_url;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_photos_url(){
			return $this->photos_url;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_ymapsml_url(){
			return $this->ymapsml_url;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_alternate_url(){
			return $this->alternate_url;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_published_date(){
			return $this->published_date;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_edited_date(){
			return $this->edited_date;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_updated_date(){
			return $this->updated_date;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_protected(){
			return $this->protected;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_image_count(){
			return $this->image_count;
		}
		
		//! Возвращает название альбома
		/*!
			@return название альбома
		*/	
		public function get_xml(){
			return $this->xml;
		}
		
		//! Проверяет был ли альбом удален
		/*!
			@return FALSE если альбом не был удален и TRUE если альбом был удален вызовом метода delete
		*/
		public function is_dead(){
			return $this->dead;	
		}
		
		//! Конструктор альбома
		/*!
			@param xml Atom Entry альбома
			@param token токен, подтверждающий аутентификацию пользователя. Не обязательный аргумент. Если не задан, то в коллекции будут показаны только ресурсы с уровнем доступа "для всех"
		*/
		public function __construct($xml=null,$token=null){
			$this->token = $token;
			$this->reload_xml($xml);
		}
		
		//! Добавляет коллекцию фотографий с выбранным именем
		/*!
			@param collection_name содержит строку содержащую имя новой коллекции. Если коллекция с таким именем уже существуе, она будет перезаписана. Обязательный аргумент. При создании происходит поиск в коллекции с условиями "по умолчанию".
			@return созданную коллекцию
			@see yandex_fotki_photo_collection
		*/
		public function add_photo_collection($collection_name=null){
			if($collection_name===null){
				throw new Exception("Не выбрано имя коллекции",E_ERROR);
			}
			$id = explode(":",$this->id);
			$id = $id[count($id)-1];
			$this->photo_collection[$collection_name] = new yandex_fotki_photo_collection($this->photos_url,$this->token,$id);
			$this->photo_collection[$collection_name]->search($this->token);
			return $this->photo_collection[$collection_name];
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
		public function remove_photo_collection($collection_name=null){
			if($collection_name===null){
				throw new Exception("Не выбрано имя коллекции",E_ERROR);
			}
			unset($this->photo_collection[$collection_name]);
		}
		
		//! Обновляет данные текущего альбома
		public function refresh(){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->edit_url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($curl, CURLOPT_HTTPGET, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			if($this->token!=null){
				curl_setopt($curl, CURLOPT_HTTPHEADER,array(
					'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$this->token.'"'
				));
			}			
			$response = curl_exec($curl);
			if(curl_getinfo($curl,CURLINFO_HTTP_CODE)!=200){
				throw new Exception($response,E_ERROR);
			}
			curl_close($curl);
			$this->reload_xml($this->delete_ns($response));
		}
		
		//! Редактирует данные альбома
		/*!
			@param title Название альбома.
			@param summary Описание альбома.
			@param password Пароль альбома. Если выставлена пустая строка, то пароль будет снят.
			@param token токен, подтверждающий аутентификацию пользователя. Не обязательный аргумент. Если не задан, то будет использован токен, переданный конструктору. Если не задан и он, то метод вызовет исключение.
		*/
		public function edit($title=null,$summary=null,$password=null,$token=null){
			if($title===null&&$summary===null&&$password===null){
				throw new Exception("Метод должен изменить заголовок, описание или пароль альбома",E_ERROR);
			}
			
			if($token!==null){
				$this->token = $token;
			}
			
			if($this->token===null){
				throw new Exception("Эта операция доступна только для аутентифицированных пользователей",E_ERROR);
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
			
			if($this->protected==true){
				$protected="true";
			}else{
				$protected="false";
			}
			
			$pass = "";
			if($password!==null){
				$pass = "<f:password>".$password."</f:password>";
			}
			
			$message = '
			<entry xmlns="http://www.w3.org/2005/Atom" xmlns:app="http://www.w3.org/2007/app" xmlns:f="yandex:fotki">
				<id>'.$this->id.'</id>
				<author>
					<name>'.$this->author.'</name>
				</author>
				<title>'.$this->title.'</title>
				<summary>'.$this->summary.'</summary>
				<link href="'.$this->self_url.'" rel="self" />
				<link href="'.$this->edit_url.'" rel="edit" />
				<link href="'.$this->photos_url.'" rel="photos" />
				<link href="'.$this->alternate_url.'" rel="alternate" />
				<published>'.$this->published_date.'</published>
				<app:edited>'.$this->edited_date.'</app:edited>
				<updated>'.$this->updated_date.'</updated>
				<f:protected value="'.$this->image_count.'" />
				'.$pass.'
				<f:image-count value="'.$protected.'" />
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
			curl_setopt($curl, CURLOPT_HTTPHEADER,array(
				'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$this->token.'"',
				'Content-Type: application/atom+xml; charset=utf-8; type=entry',
				'Expect:'
			));
			$response = curl_exec($curl);
			if(curl_getinfo($curl,CURLINFO_HTTP_CODE)!=200){
				throw new Exception($response,E_ERROR);
			}
			
			$this->xml = $this->delete_ns($response);
			fclose($putData); 
			curl_close($curl);
			$this->refresh();
		}
		
		//! Удаляет альбом. В случае успешного удаления альбом будет помечен, как удаленный. Провреить удлаен ли объект можно с помощью метода is_dead
		/*!
			@param token токен, подтверждающий аутентификацию пользователя. Необязательный аргумент. Если не задан, то будет использован токен, который был передан конструктору. Если он тоже не был задан, то метод вызовет исключение.
		*/
		public function delete($token=null){
			if($token!==null){
				$this->token = $token;
			}
			if($this->token===null){
				throw new Exception("Эта операция доступна только для аутентифицированных пользователей",E_ERROR);
			}
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->edit_url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLINFO_HEADER_OUT, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,array(
				'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$this->token.'"'
			));
			$error = curl_exec($curl);
			if(curl_getinfo($curl,CURLINFO_HTTP_CODE)!=204){
				throw new Exception($error,E_ERROR);
			}
			
			curl_close($curl);
			$this->dead=true;
		}
		
		//! Получив на вход XML на ее основе перезаписывает свойства текущего экземпляра классса
		/*!
			@param XML содержаий описание альбома в формате атома
		*/
		private function reload_xml($xml){
			$this->xml = '<?xml version="1.0" encoding="UTF-8"?>'.$xml;
			
			if(($sxml=simplexml_load_string($xml))===false){
				throw new Exception("Ответ не well-formed XML.".$response,E_ERROR);
			}
			$this->id = $sxml->id;
			$this->author = $sxml->author->name;
			$this->title = $sxml->title;
			$this->summary = $sxml->summary;
			
			$this->image_count = $sxml->image_count->attributes()->value;
			$this->published_date = $sxml->published;
			$this->edited_date = $sxml->edited;
			$this->updated_date = $sxml->updated;
			if($sxml->protected->attributes()->value=="false"){
				$this->protected = false;
			}else{
				$this->protected = true;
			}
			foreach($sxml->link as $link){
				switch($link->attributes()->rel){
					case "self":
						$this->self_url = $link->attributes()->href;
						break;
					case "edit":
						$this->edit_url = $link->attributes()->href;
						break;
					case "photos":
						$this->photos_url = $link->attributes()->href;
						break;
					case "ymapsml":
						$this->ymapsml_url = $link->attributes()->href;
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