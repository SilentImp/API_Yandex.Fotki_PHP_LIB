<?php
	//!	Класс, который позволяет вам работать с коллекцией фотографий
	/*!
		@author SilentImp
		@author http://twitter.com/SilentImp
		@author http://silentimp.habrahabr.ru/
		@author <a href="mailto:ravenb@mail.ru">ravenb@mail.ru</a>
		@ingroup yandex_fotki
	*/
	class yandex_fotki_photo_collection{
		//! Токен, подтверждающий аутентификацию пользователя
		private $token = null;
		//! Адрес коллекции
		private $url = null;
		//! URL следующей страницы коллекции
		private $next_url = null;
		//! Массив, содержащий страницы(свой массив для каждой страницы), содержащие фотографии коллекции
		private $photo_list = array();
		//! Идентификатор альбома, если применимо
		private $album_id = null;
		
		//! Конструктор коллекции
		/*!
			@param url адрес коллекции
			@param token токен, подтверждающий аутентификацию пользователя. Не обязательный аргумент. Если не задан, то в коллекции будут показаны только ресурсы с уровнем доступа "для всех"
		*/
		public function __construct($url=null,$token=null,$album_id=null){
			if($url===null){
				throw new Exception("Не задан URL коллекции",E_ERROR);
			}
			$this->url = $url;
			$this->token = $token;
			$this->album_id = $album_id;
		}
		
		//! В зависимости от переданных аргументов возвращает массив страниц, содержащих альбомы, страницу с альбомами или конкретный альбом
		/*!
			@param page числовой индекс страницы начиная с 0. Необязательный аргумент.
			@param index числовой индекс альбома начиная с 0. Необязательный аргумент.
			@return В зависимости от переданных аргументов возвращает массив страниц, содержащих альбомы, страницу с альбомами или конкретный альбом
			@see yandex_fotki_album
		*/
		public function photo_list($page=null,$index=null){
			if($page===null){
				return $this->photo_list;
			}else if($index===null&&$page!==null){
				if(!array_key_exists($page,$this->photo_list)){
					throw new Exception("Не найдена страница с указанным номером",E_ERROR);
				}
				return $this->photo_list[$page];
			}else{
				if(!array_key_exists($page,$this->photo_list)){
					throw new Exception("Не найдена страница с указанным номером",E_ERROR);
				}
				if(!array_key_exists($index,$this->photo_list[$page])){
					throw new Exception("Не найден альбом с указанным номером",E_ERROR);
				}
				return $this->photo_list[$page][$index];
			}			
		}
		
		//! Осуществляет поиск по коллекции фотографий с заданным заголовком
		/*!
			@param title название фотографии. Обязательный аргумент.
			@param limit максимально допустимое количество элементов выборки. Если установлено, то по достижении указанного числа найденных фотографий поиск будет завершен. В противном случае будут проверены все альбомы выборки на всех страницах. Если равно 0, то игнорируется.
			@return FALSE если альбомов с таким названием не найдено, альбом, если найдено единственное соответствие и массив альбомов, если найдено более одного вхождения.
			@see yandex_fotki_photo
		*/
		public function get_by_title($title=null,$limit=null){
			if($title===null){
				throw new Exception("Не задано название фотографии",E_ERROR);
			}
			$photos = array();
			foreach($this->photo_list as $photo_page){
				foreach($photo_page as $photo){
					if($photo->get_title()==$title){
						$photos[] = $photo;
						if($limit!=null&&(int)$limit>0&&count($photos)==(int)$limit){
							break 2;
						}
					}
				}
			}
			switch(count($photos)){
				case 0:
					return false;
					break;
				case 1:
					return $photos[0];
					break;
				default:
					return $photos;
					break;
			}
		}
		
		//! Удаляет фотографию с указанным идентификатором
		/*!
			@param id идентификатор фотографии, которую вы хотите удалить
		*/
		public function delete_photo_by_id($id=null){
			if($id===null){
				throw new Exception("Не задан идентификатор фотографии",E_ERROR);
			}
			foreach($this->photo_list as $photo_page){
				foreach($photo_page as $photo){
					$parts = explode(":",$photo->get_id());
					if($parts[count($parts)-1]==(int)$id){
						$photo->delete();
						return;
					}
				}
			}
		}
		
		//! Удаляет фотографию с указанным названием. Внимание! Будут удалены все фотографии с этим заголовком. Фотографии сами не исчезают из коллекции. Не забудьте ее обновить. Удаленная фотография при вызова метода is_dead аозвращает true.
		/*!
			@param title Название фотогрфии, который вы хотите удалить
			@see yandex_fotki_album
		*/
		public function delete_photo_by_title($title=null){
			if($title===null){
				throw new Exception("Не задано название фотографии",E_ERROR);
			}
			foreach($this->photo_list as $photo_page){
				foreach($photo_page as $photo){
					if($photo->get_title()==$title){
						$photo->delete();
					}
				}
			}
		}
		
		//! Добавляет ноую фотографию в коллекцию
		/*!
			@param path путь к файлу, содержащему изображения
			@param pub_channel Уникальное имя-маркер клиентского приложения, осуществляющего загрузку фотографии. 
			@param app_platform Дополнительная информация о платформе клиентского приложения, осуществляющего загрузку.
			@param app_version Версия клиентского приложения, осуществляющего загрузку.
			@param title Название фотографии. Не может быть пустой строкой.
			@param tags Массив содержащий теги (метки), уточняющие смысл фотографии.
			@param yaru Флаг публикации фотографии на странице пользователя на Я.ру. Допустимые значения: "1" - опубликовать (по умолчанию); "0" - не опубликовывать.
			@param access_type Уровень доступа к фотографии. Допустимые значения: public (по умолчанию) - для всех; friends - для друзей; private - для себя.
			@param album Идентификатор альбома для загрузки фотографии. Альбом должен существовать.
			@param disable_comments Флаг запрета комментариев. Значение по умолчанию: false.
			@param xxx Флаг «только для взрослых» (можно только установить, снять нельзя). Значение по умолчанию: false.
			@param hide_orig Флаг запрета публичного доступа к оригиналу фотографии. Значение по умолчанию: false. Если данный флаг установлен в true, автор не сможет получить оригинал фотографии при помощи API Фоток. Для этого нужно воспользоваться возможностями сервиса Яндекс.Фотки.
			@param storage_private Флаг, закрывающий доступ к фотографии по URL со страниц вне домена Яндекс.Фоток. Значение по умолчанию: false.
			@param token токен, подтверждающий аутентификацию пользователя. Обязательный аргумент.
			@return ассоциативный массив. Если yaru==0, то возвращается array('image_id'=>photo_id), где {photo_id} - численный идентификатор фотографии. Если yaru==1 то возвращается array('image_id'=>photo_id,'post_id'=>post_id), где {photo_id} - идентификатор фотографии, а {post_id} - идентификатор поста на Я.ру.
		*/
		public function add_photo($path=null,$pub_channel=null,$app_platform=null,$app_version=null,$title=null,$tags=array(),$yaru=1,$access_type="public",$album=null,$disable_comments=false,$xxx=false,$hide_orig=false,$storage_private=false,$token=null){
			if($path===null){
				throw new Exception("Не задан путь к файлу, содержащему изображение",E_ERROR);
			}
			$path = realpath($path);
			if(!file_exists($path)){
				throw new Exception("Файл, содержащий изображение, не найден",E_ERROR);
			}			
			
			if($token!==null){
				$this->token=$token;
			}
			if($this->token===null){
				throw new Exception("Эта операция доступна только для аутентифицированных пользователей",E_ERROR);
			}
			
			$url = array("image"=>"@".$path);
			
			if($pub_channel!==null){
				$url["pub_channel"]=$pub_channel;
			}
			
			if($app_platform!==null){
				$url["app_platform"]=$app_platform;
			}
			
			if($app_version!==null){
				$url["app_version"]=$app_version;
			}
			
			if($title!==null){
				$url["title"]=$title;
			}
			
			if(count($tags)>0){
				$url["tags"]=implode(",",$tags);
			}
			
			if(!in_array($yaru,array(0,1))){
				$url["yaru"]=1;
			}else{
				$url["yaru"]=$yaru;
			}
			
			if(!in_array($access_type,array("public","friends","private"))){
				$url["access_type"]="public";
			}else{
				$url["access_type"]=$access_type;
			}
			
			if($album!==null){
				$url["album"]=$album;
			}else if($this->album_id!==null){
				$url["album"]=$this->album_id;
			}
			
			if($disable_comments!==false){
				$url["disable_comments"]="true";
			}
			
			if($xxx!==false){
				$url["xxx"]="true";
			}
			
			if($hide_orig!==false){
				$url["hide_orig"]="true";
			}
			
			if($storage_private!==false){
				$url["storage_private"]="true";
			}
			
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "http://api-fotki.yandex.ru/post/");
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLINFO_HEADER_OUT, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,array(
				'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$this->token.'"',
				'Accept: ',
				'Expect: '
			));
			$response = curl_exec($curl);
			if(curl_getinfo($curl,CURLINFO_HTTP_CODE)!=200){
				throw new Exception(curl_getinfo($curl,CURLINFO_HTTP_CODE)." : ".$response,E_ERROR);
			}
			curl_close($curl);
			return $response = parse_str($response);
			//yaru==0
			//image_id={photo_id}, где {photo_id} - численный идентификатор фотографии. 
			//yaru==1
			//image_id={photo_id}&post_id={post_id}, где {photo_id} - идентификатор фотографии, а {post_id} - идентификатор поста на Я.ру.
			//Не то что бы это было очень полезно, но ... пойду поставлю свечку великому АНАХУА.
		}
	
		//! Получает следующую страницу коллекции. Если ее нет или вы предварительно не вызвали метод search, выполняющий поиск по коллекции, то метод вызовет исключение
		public function next(){
			if($this->next_url===null){
				throw new Exception("Не задан URL следующей страницы. Вы уже получили последнюю страницу коллекции или поиск по коллекции не был выполнен.",E_ERROR);
			}
			$this->query($this->next_url);
		}
		
		//! Выполняет поиск по коллекции с заданными условиями
		/*!
			@param order Порядок отображения элементов выдачи. Допустимые значения: updated (по умолчанию) - по времени последнего изменения, от новых к старым; rupdated - по времени последнего изменения, от старых к новым; published - по времени загрузки (для фотографии) или создания (для альбома), от новых к старым; rpublished - по времени загрузки (для фотографии) или создания (для альбома), от старых к новым; created - по времени создания согласно EXIF-данным, от новых к старым; rcreated - по времени создания согласно EXIF-данным, от старых к новым.
			@param offset_time Время создания ресурса  в формате UTC с точностью до секунды. Исключение: ссылки с order равным created или rcreated, в которых время указывается без часового пояса.
			@param offset_id Численный идентификатор ресурса на Яндекс.Фотках.
			@param limit Количество элементов на странице выдачи.
			@param token Токен, подтверждающий аутентификацию пользователя. Если не задан, используется токен, который был передан конструктору. Если не задан и он, то метод вызовет исключение.
		*/
		public function search($order="updated",$offset_time=null,$offset_id="",$limit=100,$token=null){
			$this->photo_list = array();
			$this->next_url = null;
			if($token!=null){
				$this->token = $token;
			}
			if(!in_array($order,array("updated","rupdated","published","rpublished","created","rcreated"))){
				$order="updated";
			}
			if($offset_time===null){
				$offset_time=gmdate(DATE_ATOM);
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
				curl_setopt($curl, CURLOPT_HTTPHEADER,array(
					'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$this->token.'"'
				));
			}
			$response = curl_exec($curl);
			if(curl_getinfo($curl,CURLINFO_HTTP_CODE)!=200){
				throw new Exception($response,E_ERROR);
			}
			curl_close($curl);
			
			$response = $this->delete_ns($response);
			if(($sxml=simplexml_load_string($response))===false){
				throw new Exception("Ответ не well-formed XML.".$response,E_ERROR);
			}
			
			$result = $sxml->xpath("//link[@rel='next']");
			if(count($result)>0){
				$this->next_url = $result[0]->attributes()->href;
			}
			
			$result = $sxml->xpath("//entry");
			$photo = array();
			foreach($result as $xml){
				$photo[] = new yandex_fotki_photo($xml->asXML(),$this->token);
			}
			$this->photo_list[] = $photo;
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