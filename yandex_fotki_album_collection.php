<?php
	//!	Класс, который позволяет вам работать с коллекцией альбомов пользователя
	/*!
		@author SilentImp
		@author <a href="http://twitter.com/SilentImp/">http://twitter.com/SilentImp/</a>
		@author <a href="http://silentimp.habrahabr.ru/">http://silentimp.habrahabr.ru/</a>
		@author <a href="mailto:ravenb@mail.ru">ravenb@mail.ru</a>
		@ingroup yandex_fotki
	*/
	class yandex_fotki_album_collection{
		//! Токен, подтверждающий аутентификацию пользователя
		private $token = null;
		//! Адрес коллекции
		private $url = null;
		//! URL следующей страницы коллекции
		private $next_url = null;
		//! Массив, содержащий страницы(свой массив для каждой страницы), содержащие альбомы коллекции
		private $album_list = array();
		
		//! Конструктор коллекции
		/*!
			@param url адрес коллекции
			@param token токен, подтверждающий аутентификацию пользователя. Не обязательный аргумент. Если не задан, то в коллекции будут показаны только ресурсы с уровнем доступа "для всех"
		*/
		public function __construct($url, $token=null){
			$this->url = $url;
			$this->token = $token;
		}
		
		//! В зависимости от переданных аргументов возвращает массив страниц, содержащих альбомы, страницу с альбомами или конкретный альбом
		/*!
			@param page числовой индекс страницы начиная с 0. Необязательный аргумент.
			@param index числовой индекс альбома начиная с 0. Необязательный аргумент.
			@return В зависимости от переданных аргументов возвращает массив страниц, содержащих альбомы, страницу с альбомами или конкретный альбом
			@see yandex_fotki_album
		*/
		public function album_list($page=null, $index=null){
			if($page===null){
				return $this->album_list;
			}else if($index===null){
				if(!array_key_exists($page, $this->album_list)){
					throw new Exception("Не найдена страница с указанным номером", E_ERROR);
				}
				return $this->album_list[$page];
			}else{
				if(!array_key_exists($page, $this->album_list)){
					throw new Exception("Не найдена страница с указанным номером", E_ERROR);
				}
				if(!array_key_exists($index, $this->album_list[$page])){
					throw new Exception("Не найден альбом с указанным номером", E_ERROR);
				}
				return $this->album_list[$page][$index];
			}			
		}

		//! Осуществляет поиск по коллекции альбомов с заданным заголовком
		/*!
			@param title название альбома. Обязательный аргумент.
			@param limit максимально допустимое количество элементов выборки. Если установлено, то по достижении указанного числа найденных альбомов поиск будет завершен. В противном случае будут проверены все альбомы выборки на всех страницах. Если равно 0, то игнорируется.
			@return FALSE если альбомов с таким названием не найдено, альбом, если найдено единственное соответствие и массив альбомов, если найдено более одного вхождения.
			@see yandex_fotki_album
		*/
		public function get_by_title($title, $limit=null){
			$albums = array();
			foreach($this->album_list as $album_page){
				foreach($album_page as $album){
					if($album->get_title()==$title){
						$albums[] = $album;
						if($limit!=null&&(int)$limit>0&&count($albums)==(int)$limit){
							break 2;
						}
					}
				}
			}
			switch(count($albums)){
				case 0:
					return false;
					break;
				case 1:
					return $albums[0];
					break;
				default:
					return $albums;
					break;
			}
		}
		
		//! Удаляет альбом с указанным идентификатором
		/*!
			@param id идентификатор альбома, который вы хотите удалить
		*/
		public function delete_album_by_id($id){
			foreach($this->album_list as $album_page){
				foreach($album_page as $album){
					$parts = explode(":", $album->get_id());
					if($parts[count($parts)-1]==(int)$id){
						$album->delete();
						return;
					}
				}
			}
		}
		
		//! Удаляет альбомы с указанным заголовком. Внимание! Будут удалены все альбомы с этим заголовком. Альбомы сами не исчезают из коллекции. Не забудьте ее обновить. Удаленный альбом при вызова метода is_dead аозвращает true.
		/*!
			@param title заголовок альбома, который вы хотите удалить
			@see yandex_fotki_album
		*/
		public function delete_album_by_title($title){
			foreach($this->album_list as $album_page){
				foreach($album_page as $album){
					if($album->get_title()==$title){
						$album->delete();
					}
				}
			}
		}
		
		//! Создает новый альбом. Внимание! Не забудьте обновить коллекцию. 
		/*!
			@param title Заголовок альбома. Обязательный аргумент.
			@param summary Описание альбома. Необязательный аргумент.
			@param password Пароль альбома. Необязательный аргумент.
			@param token Токен, подтверждающий аутентификацию пользователя. Если не задан, используется токен, который был передан конструктору. Если не задан и он, то метод вызовет исключение.
		*/
		public function add_album($title, $summary="", $password="", $token=null){
			$title=trim($title);
			$summary=trim($summary);
			$password=trim($password);
			if(empty($title)){
				throw new Exception("Не задан заголовок альбома", E_ERROR);
			}
			if($token!==null){
				$this->token=$token;
			}
			if($this->token===null){
				throw new AuthenticationError("Эта операция доступна только для аутентифицированных пользователей", E_ERROR);
			}
			
			$body='<entry xmlns="http://www.w3.org/2005/Atom" xmlns:f="yandex:fotki"><title>'.$title.'</title><summary>'.$summary.'</summary><f:password>'.$password.'</f:password></entry>';
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $this->url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$this->token.'"',
				'Content-Type: application/atom+xml; charset=utf-8; type=entry'
			));
			$response = curl_exec($curl);
			if(curl_getinfo($curl, CURLINFO_HTTP_CODE)!=201){
				throw new RequestError($response, curl_getinfo($curl, curl_getinfo($curl, CURLINFO_HTTP_CODE)));
			}
			curl_close($curl);
		}
		
		//! Получает следующую страницу коллекции. Если ее нет или вы предварительно не вызвали метод search, выполняющий поиск по коллекции, то метод вызовет исключение
		public function next(){
			if($this->next_url===null){
				throw new Exception("Не задан URL следующей страницы. Вы уже получили последнюю страницу коллекции или поиск по коллекции не был выполнен.", E_ERROR);
			}
			$this->query($this->next_url);
		}
		
		//! Метод является оберткой для search и должен упростить работу с его аргументами.
		/*!
			@param args ассоциативный массив, в котором хранятся аргументы, значения которых отличаются от значений по умолчанию. Ключи ассоциативного массива: order, time, id, limit, token. Точное описание аргументов смотрите в описании метода search
		*/
		public function se($args = array()){
			
			if(!array_key_exists($args, "order")){
				$order=$args["order"];
			}else{
				$order="updated";
			}
				
			if(!array_key_exists($args, "time")){
				$offset_time=$args["time"];
			}else{
				$offset_time=null;
			}
			
			if(!array_key_exists($args, "id")){
				$offset_id=$args["id"];
			}else{
				$offset_id="";
			}
			
			if(!array_key_exists($args, "limit")){
				$limit=$args["limit"];
			}else{
				$limit=100;
			}
			
			if(!array_key_exists($args, "token")){
				$token=$args["token"];
			}else{
				$token=null;
			}
						
			$this->search($order, $offset_time, $offset_id, $limit, $token);
		}
		
		//! Выполняет поиск по коллекции с заданными условиями
		/*!
			@param order Порядок отображения элементов выдачи. Допустимые значения: updated (по умолчанию) - по времени последнего изменения, от новых к старым; rupdated - по времени последнего изменения, от старых к новым; published - по времени загрузки (для фотографии) или создания (для альбома), от новых к старым; rpublished - по времени загрузки (для фотографии) или создания (для альбома), от старых к новым;
			@param offset_time Время создания ресурса  в формате UTC с точностью до секунды.
			@param offset_id Численный идентификатор ресурса на Яндекс.Фотках.
			@param limit Количество элементов на странице выдачи.
			@param token Токен, подтверждающий аутентификацию пользователя. Если не задан, используется токен, который был передан конструктору. Если не задан и он, то метод вызовет исключение.
		*/
		public function search($order="updated", $offset_time=null, $offset_id="", $limit=100, $token=null){
			$this->album_list = array();
			$this->next_url = null;
			if($token!=null){
				$this->token = $token;
			}
			if(!in_array($order, array("updated", "rupdated", "published", "rpublished"))){
				$order="updated";
			}
			if($offset_time===null){
				switch($order){
					default:
					case "updated":
					case "published":
						$offset_time=gmdate(DATE_ATOM);
						break;
					case "rupdated":
					case "rpublished":
						$offset_time=gmdate(DATE_ATOM, strtotime("2000-01-01"));
						break;
				}
			}
			if($offset_id!=""){
				$offset_id=",".$offset_id;
			}
			if((int)$limit>100){
				$limit=100;
			}elseif($limit<1){
				$limit=1;
			}			
			$url = $this->url.$order.";".$offset_time.$offset_id."/?limit=".$limit;
			$this->query($url);
		}

		//! Метод непосредственно осуществляет запрос к серверу на получение коллекции
		/*!
			@param url URL содержащий адрес коллекции, параметры сортировки, смещение и количество элементов на странице
		*/
		private function query($url){
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
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
				throw new RequestError($response, E_ERROR);
			}
			curl_close($curl);
			
			$response = $this->delete_ns($response);
			if(($sxml=simplexml_load_string($response))===false){
				throw new XMLError("Ответ не well-formed XML.".$response, E_ERROR);
			}
			
			$result = $sxml->xpath("//link[@rel='next']");
			if(count($result)>0){
				$this->next_url = $result[0]->attributes()->href;
			}
			
			$result = $sxml->xpath("//entry");
			$album = array();
			foreach($result as $xml){
				$album[] = new yandex_fotki_album($xml->asXML(), $this->token);
			}
			$this->album_list[] = $album;
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