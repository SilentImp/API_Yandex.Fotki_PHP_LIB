<?php
/**
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 * @package YandexFotki
 */
 
/**
 * Класс, который позволяет вам работать с коллекцией альбомов пользователя
 * 
 * @package YandexFotki
 * @throws YFException|YFRequestException|YFXMLException|YFNotFoundException
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 */
class YFAlbumCollection {	
	/**
	 * Токен, подтверждающий аутентификацию пользователя
	 * @var string
	 * @access protected
	 */
	protected $token = null;

	/**
	 * Адрес коллекции
	 * @var string
	 * @access protected
	 */
	protected $url = null;

	/**
	 * URL следующей страницы коллекции
	 * @var string
	 * @access protected
	 */
	protected $nextPageUrl = null;

	/**
	 * Массив, содержащий страницы(свой массив для каждой страницы), содержащие альбомы коллекции
	 * @access protected
	 */
	protected $albumList = array();

	/**
	 * Если не задан $token, то в коллекции будут показаны только ресурсы
	 * с уровнем доступа для всех.
	 *
	 * @param string $url адрес коллекции
	 * @param string $token	 
	 * @return void
	 * @access public
	 */
	public function __construct($url, $token=null){
		libxml_use_internal_errors(true);
		$this->url = $url;
		$this->token = $token;
	}

	/**
	 * Возвращает коллекцию альбомов
	 *
	 * @return array|YFAlbum
	 * @see YFAlbum
	 * @access public
	 */
	 public function getList(){
	 	return $this->albumList;
	 }
	 
	/**
	 * Возвращает страницу коллекции альбомов
	 *
	 * @throws YFNotFoundException
	 * @param int $pageNumber номер страницы
	 * @return array|YFAlbum
	 * @access public
	 * @see YFAlbum
	 */
	 public function getPage($pageNumber){
			if(count($this->albumList)<($pageNumber-1)){
				throw new YFException("Не найдена страница с указанным номером", E_ERROR, null, "pageNotFound");
			}
			return $this->albumList[$pageNumber];
	 }

	/**
	 * Возвращает альбом
	 *
	 * @throws YFNotFoundException
	 * @param int $pageNumber номер страницы
	 * @param int $albumNumber номер альбома на странице
	 * @return YFAlbum
	 * @access public
	 * @see YFAlbum
	 */
	 public function getAlbum($pageNumber,$albumNumber){
			if(count($this->albumList)<($pageNumber-1)){
				throw new YFException("Не найдена страница с указанным номером", E_ERROR, null, "pageNotFound");
			}
			if(count($this->albumList[$pageNumber])<($albumNumber-1)){
				throw new YFException("Не найден альбом с указанным номером", E_ERROR, null, "albumNotFound");
			}
			return $this->albumList[$pageNumber][$albumNumber];
	 }

	/**
	 * Осуществляет поиск по коллекции альбомов с заданным заголовком
	 * 
	 * Возвращает массив найденый соответствий
	 *
	 * @param string $albumTitle название альбома. Обязательный аргумент.
	 * @param int $limit максимально допустимое количество элементов выборки. Если установлено, то по достижении указанного числа найденных альбомов поиск будет завершен. В противном случае будут проверены все альбомы выборки на всех страницах. Если равно 0, то игнорируется.
	 * @return array|YFAlbum
	 * @see YFAlbum
	 * @access public
	 */
	public function getAlbumsByTitle($albumTitle, $limit=null){
		$albums = array();
		foreach($this->albumList as $album_page){
			foreach($album_page as $album){
				if($album->getTitle()==$albumTitle){
					$albums[] = $album;
					if($limit!=null&&(int)$limit>0&&count($albums)==(int)$limit){
						break 2;
					}
				}
			}
		}
		return $albums;
	}
	
	/**
	 * Осуществляет поиск по коллекции альбомов с заданным заголовком
	 * 
	 * Возвращает первый найденный альбом с указанным названием
	 *
	 * @throws YFNotFoundException
	 * @param string $albumTitle название альбома. Обязательный аргумент.
	 * @return YFAlbum
	 * @see YFAlbum
	 * @access public
	 */
	public function getAlbumByTitle($albumTitle){
		$albums = array();
		foreach($this->albumList as $album_page){
			foreach($album_page as $album){
				if($album->getTitle()==$albumTitle){
					return $album;
				}
			}
		}
		throw new YFException("Не найден альбом с указанным названием", E_ERROR, null, "albumNotFound");
	}

	/**
	 * Удаляет альбом с указанным идентификатором
	 * 
	 * @param string $albumId идентификатор альбома, который вы хотите удалить
	 * @return void
	 * @access public
	 */

	public function deleteAlbumById($albumId){
		foreach($this->albumList as $album_page){
			foreach($album_page as $album){
				$parts = explode(":", $album->get_id());
				if($parts[count($parts)-1]==(int)$albumId){
					$album->delete();
					return;
				}
			}
		}
	}

	/**
	 * Удаляет альбомы с указанным заголовком. Внимание! Будут удалены все альбомы с этим заголовком. Альбомы сами не исчезают из коллекции. Не забудьте ее обновить. Удаленный альбом при вызова метода is_dead аозвращает true.
	 * 
	 * @param string $albumTitle заголовок альбома, который вы хотите удалить
	 * @see YFAlbum
	 * @return void
	 * @access public
	 */
	public function deleteAlbumByTitle($albumTitle){
		foreach($this->albumList as $album_page){
			foreach($album_page as $album){
				if($album->getTitle()==$albumTitle){
					$album->delete();
				}
			}
		}
	}

	/**
	 * Создает новый альбом. Внимание! Не забудьте обновить коллекцию.
	 * 
	 * @throws YFRequestException|YFRequestError
	 * @param string $title Заголовок альбома. Обязательный аргумент.
	 * @param string $summary Описание альбома. Необязательный аргумент.
	 * @param string $password Пароль альбома. Необязательный аргумент.
	 * @param string $token Токен, подтверждающий аутентификацию пользователя. Если не задан, используется токен, который был передан конструктору. Если не задан и он, то метод вызовет исключение.
	 * @return void
	 * @access public
	 */
	public function addAlbum($title, $summary="", $password="", $token=null){
		$title=trim($title);
		$summary=trim($summary);
		$password=trim($password);
		
		if(empty($title)){
			throw new YFException("Не задан заголовок альбома", E_ERROR, null, "titleNotSet");
		}
		
		if($token!==null){
			$this->token = $token;
		}
		
		if($this->token==null){
			throw new YFException("Эта операция доступна только для аутентифицированных пользователей", E_ERROR,null,"authenticationNeeded");
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
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
	
		switch((int)$code){
			case 200:
				//если код не 200 и не оговоренные в документации Яндекс ошибки, то будет вызвано прерывание общего типа.
				break;
			case 400:
				throw new YFRequestException($xml, $code, "Переданный клиентским приложением XML не является валидным Atom Entry альбома или не задан title.", "badRequest");
				break;
			case 401:
				throw new YFRequestException($xml, $code, null, "unauthorized");
				break;
			case 403:
				throw new YFRequestException($xml, $code, "Для доступа к альбому требуется пароль.", "forbidden");
				break;
			case 404:
				throw new YFRequestException($xml, $code, "Пользователя с указанным логином не существует.", "userNotFound");
				break;
			case 405:
				throw new YFRequestException($xml, $code, "Клиентское приложение попыталось выполнить по данному адресу неправильный метод.", "methodNotAllowed");
				break;
			case 415:
				throw new YFRequestException($xml, $code, "Заголовок Content-Type содержит тип, отличный от типа Atom Entry.", "notAtomEntry");
				break;
			case 500:
				throw new YFRequestException($xml, $code, "Сервер не смог обработать запрос.","internalServerError");
				break;
			default:
				throw new YFRequestException($xml, $code);
				break;
		}
	}

	/**
	 * Получает следующую страницу коллекции. Если ее нет или вы предварительно не вызвали метод search, выполняющий поиск по коллекции, то метод вызовет исключение
	 * 
	 * @throws YFException|YFRequestException|YFXMLException
	 * @param array $args ассоциативный массив, в котором хранятся аргументы, значения которых отличаются от значений по умолчанию. Ключи ассоциативного массива: order, time, id, limit, token. Точное описание аргументов смотрите в описании метода search
	 * @return void
	 * @access public
	 */
	public function next(){
		if($this->nextPageUrl===null){
			throw new YFException("Не задан URL следующей страницы. Вы уже получили последнюю страницу коллекции или поиск по коллекции не был выполнен.", E_ERROR,null,"lastPageFound");
		}
		$this->query($this->nextPageUrl);
	}

	/**
	 * Метод является оберткой для search и должен упростить работу с его аргументами.
	 * 
	 * @throws YFRequestException|YFXMLException
	 * @param array $args ассоциативный массив, в котором хранятся аргументы, значения которых отличаются от значений по умолчанию. Ключи ассоциативного массива: order, time, id, limit, token. Точное описание аргументов смотрите в описании метода search
	 * @return void
	 * @access public
	 */
	public function search($args = array()){

		if(array_key_exists("order", $args)){
			$order=$args["order"];
		}else{
			$order="updated";
		}

		if(array_key_exists("time", $args)){
			$offset_time=$args["time"];
		}else{
			$offset_time=null;
		}

		if(array_key_exists("id", $args)){
			$offset_id=$args["id"];
		}else{
			$offset_id="";
		}

		if(array_key_exists("limit", $args)){
			$limit=$args["limit"];
		}else{
			$limit=100;
		}

		if(array_key_exists("token", $args)){
			$token=$args["token"];
		}else{
			$token=null;
		}

		$this->searchEx($order, $offset_time, $offset_id, $limit, $token);
	}

	/**
	 * Выполняет поиск по коллекции с заданными условиями
	 * 
	 * @throws YFRequestException|YFXMLException
	 * @param string $order Порядок отображения элементов выдачи. Допустимые значения: updated (по умолчанию) - по времени последнего изменения, от новых к старым; rupdated - по времени последнего изменения, от старых к новым; published - по времени загрузки (для фотографии) или создания (для альбома), от новых к старым; rpublished - по времени загрузки (для фотографии) или создания (для альбома), от старых к новым;
	 * @param string $offset_time Время создания ресурса в формате UTC с точностью до секунды.
	 * @param string $offset_id Численный идентификатор ресурса на Яндекс.Фотках.
	 * @param int $limit Количество элементов на странице выдачи.
	 * @param string $token Токен, подтверждающий аутентификацию пользователя. Если не задан, используется токен, который был передан конструктору. Если не задан и он, то метод вызовет исключение.
	 * @return void
	 * @access public
	 */
	public function searchEx($order="updated", $offset_time=null, $offset_id="", $limit=100, $token=null){
		$this->albumList = array();
		$this->nextPageUrl = null;
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

	/**
	 * Метод непосредственно осуществляет запрос к серверу на получение коллекции
	 * 
	 * @throws YFRequestException|YFXMLException
	 * @param string $url URL содержащий адрес коллекции, параметры сортировки, смещение и количество элементов на странице
	 * @return void
	 * @access private
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
		$xml = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
	
		switch((int)$code){
			case 200:
				//если код не 200 и не оговоренные в документации Яндекс ошибки, то будет вызвано прерывание общего типа.
				break;
			case 400:
				throw new YFRequestException($xml, $code, "Неправильно указан параметр limit.", "badRequest");
				break;
			case 401:
				throw new YFRequestException($xml, $code, null, "unauthorized");
				break;
			case 403:
				throw new YFRequestException($xml, $code, "Для доступа к альбому требуется пароль.", "forbidden");
				break;
			case 404:
				throw new YFRequestException($xml, $code, "Запрашиваемый элемент коллекции отсутствует: неправильно указано значение параметра сортировки или неверно задано время.", "notFound");
				break;
			case 500:
				throw new YFRequestException($xml, $code, "Сервер не смог обработать запрос.","internalServerError");
				break;
			default:
				throw new YFRequestException($xml, $code);
				break;
		}	
		
		$xml = $this->deleteXmlNamespace($xml);
		if(($sxml=simplexml_load_string($xml))===false){
			throw new YFXMLException($xml, E_ERROR,"Не удалось распознать ответ Яндекс как валидный XML документ","canNotCreateXML");
		}

		$result = $sxml->xpath("//link[@rel='next']");
		if(count($result)>0){
			$this->nextPageUrl = $result[0]->attributes()->href;
		}

		$result = $sxml->xpath("//entry");
		$album = array();
		foreach($result as $xml){
			$album[] = new YFAlbum($xml->asXML(), $this->token);
		}
		$this->albumList[] = $album;
	}

	/**
	 * Удаление информации о пространствах имен.
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