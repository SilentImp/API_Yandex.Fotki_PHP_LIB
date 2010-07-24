<?php
/**
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 * @package YandexFotki
 */
 
/**
 * Класс, который позволяет вам работать с коллекцией фотографий
 *
 * @throws YFException|YFRquestException|YFXMLException
 * @package YandexFotki
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 */
class YFPhotoCollection {

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
	 * Массив, содержащий страницы(свой массив для каждой страницы), содержащие фотографии коллекции
	 * @var string
	 * @access protected
	 */
	protected $photoList = array();
	
	/**
	 * Идентификатор альбома, если применимо
	 * @var string
	 * @access protected
	 */
	protected $albumId = null;

	/**
	 * Конструктор коллекции
	 * 
	 * @param string $url адрес коллекции
	 * @param string $token числовой идентификатор альбома
	 * @param string $album_id токен, подтверждающий аутентификацию пользователя. Не обязательный аргумент. Если не задан, то в коллекции будут показаны только ресурсы с уровнем доступа "для всех"
	 * @return void
	 * @access public
	 */
	public function __construct($url, $token=null, $album_id=null){
		libxml_use_internal_errors(true);
		$this->url = $url;
		$this->token = $token;
		$this->albumId = $album_id;
	}

	/**
	 * Возвращает коллекцию альбомов
	 *
	 * @return array|YFPhoto
	 * @see YFPhoto
	 * @access public
	 */
	 public function getList(){
	 	return $this->photoList;
	 }
	 
	/**
	 * Возвращает страницу коллекции альбомов
	 *
	 * @throws YFException
	 * @param int $page номер страницы
	 * @return array|YFPhoto
	 * @see YFPhoto
	 * @access public
	 */
	 public function getPage($page){
			if(count($this->photoList)<($page-1)){
				throw new YFException("Не найдена страница с указанным номером", E_ERROR, null, "pageNotFound");
			}
			return $this->photoList[$page];
	 }

	/**
	 * Возвращает фотографию
	 *
	 * @throws YFException
	 * @param int $page номер страницы
	 * @param int $index номер альбома на странице
	 * @return YFPhoto
	 * @see YFPhoto
	 * @access public
	 */
	 public function getAlbum($page,$index){
			if(count($this->photoList)<($page-1)){
				throw new YFException("Не найдена страница с указанным номером", E_ERROR, null, "pageNotFound");
			}
			if(count($this->photoList[$page])<($index-1)){
				throw new YFException("Не найдена фотография с указанным номером", E_ERROR, null, "photoNotFound");
			}
			return $this->photoList[$page][$index];
	 }

	/**
	 * Осуществляет поиск по коллекции фотографий с заданным заголовком. Возвращает массив найденых соответствий.
	 * 
	 * @param string $title название фотографии. Обязательный аргумент.
	 * @param int $limit максимально допустимое количество элементов выборки. Если установлено, то по достижении указанного числа найденных фотографий поиск будет завершен. В противном случае будут проверены все альбомы выборки на всех страницах. Если равно 0, то игнорируется.
	 * @return array|YHPhoto
	 * @see YFPhoto
	 * @access public
	 */
	public function getPhotosByTitle($title, $limit=null){
		$photos = array();
		foreach($this->photoList as $photo_page){
			foreach($photo_page as $photo){
				if($photo->getTitle()==$title){
					$photos[] = $photo;
					if($limit!=null&&(int)$limit>0&&count($photos)==(int)$limit){
						break 2;
					}
				}
			}
		}
		return $photos;
	}
	
	/**
	 * Осуществляет поиск по коллекции фотографии с заданным заголовком. Возвращает первую найденую. Если поиск не дал результата, то вызовет исключение.
	 * 
	 * @throws YFException
	 * @param string $photoTitle название фотографии. Обязательный аргумент.
	 * @return YHPhoto
	 * @see YFPhoto
	 * @access public
	 */
	public function getPhotoByTitle($photoTitle){
		foreach($this->photoList as $photo_page){
			foreach($photo_page as $photo){
				if($photo->getTitle()==$photoTitle){
					return $photo;
				}
			}
		}
		throw new YFException("Не найдена фотография с указанным названием", E_ERROR, null, "photoNotFound");
	}

	/**
	 * Ищет в коллекции фотографию по заданному id
	 * 
	 * @throws YFException
	 * @param string $photoId идентификатор фотографии, которую вы хотите найти
	 * @return YHPhoto
	 * @see YFPhoto
	 * @access public
	 */
	public function getPhotoById($photoId){
		foreach($this->photoList as $photo_page){
			foreach($photo_page as $photo){
				$parts = explode(":", $photo->getId());
				if($parts[count($parts)-1]==(int)$photoId){
					return $photo;
				}
			}
		}
		throw new YFException("Не найдена фотография с указанным идентификатором", E_ERROR, null, "photoNotFound");
	}

	/**
	 * Удаляет фотографию с указанным идентификатором
	 * 
	 * @param string $photoId идентификатор фотографии, которую вы хотите удалить
	 * @return void
	 * @see YFPhoto
	 * @access public
	 */
	public function deletePhotoById($photoId){
		foreach($this->photoList as $photo_page){
			foreach($photo_page as $photo){
				$parts = explode(":", $photo->getId());
				if($parts[count($parts)-1]==(int)$photoId){
					$photo->delete();
					return;
				}
			}
		}
	}

	/**
	 * Удаляет фотографии с указанным названием. Внимание! Будут удалены все фотографии с этим заголовком. Фотографии сами не исчезают из коллекции. Не забудьте ее обновить. Удаленная фотография при вызова метода is_dead аозвращает true.
	 * 
	 * @param string $photoTitle Название фотогрфии, который вы хотите удалить
	 * @param int $limit Максимальное количество фотографий, которые будут удалены. Необязательный параметр.
	 * @return void
	 * @see YFPhoto
	 * @access public
	 */
	public function deletePhotosByTitle($photoTitle,$limit=null){
		if($limit!==null){
			$limit=(int)$limit;
		}
		foreach($this->photoList as $photo_page){
			foreach($photo_page as $photo){
				if($photo->getTitle()==$photoTitle){
					$photo->delete();
					if($limit!==null){
						$limit--;
						if($limit===0) return;
					}
				}
			}
		}
	}
	
	/**
	 * Удаляет первую фотографию с указанным названием. Внимание! Фотографии сами не исчезают из коллекции. Не забудьте ее обновить. Удаленная фотография при вызова метода is_dead аозвращает true.
	 * 
	 * @param string $photoTitle Название фотогрфии, который вы хотите удалить
	 * @return void
	 * @see YFPhoto
	 * @access public
	 */
	public function deletePhotoByTitle($photoTitle){
		foreach($this->photoList as $photo_page){
			foreach($photo_page as $photo){
				if($photo->getTitle()==$photoTitle){
					$photo->delete();
					return;
				}
			}
		}
	}

	/**
	 * Метод является оберткой для add_photo и должен упростить работу с его аргументами.
	 * 
	 * В случае успеха возвращает ассоциативный массив. Если yaru==0, то возвращается array('image_id'=>photo_id), где {photo_id} - численный идентификатор фотографии. Если yaru==1 то возвращается array('image_id'=>photo_id,'post_id'=>post_id), где {photo_id} - идентификатор фотографии, а {post_id} - идентификатор поста на Я.ру.
	 *
	 * @throws YFException|YFRequestException
	 * @param array $args ассоциативный массив, в котором хранятся аргументы, значения которых отличаются от значений по умолчанию. Ключи ассоциативного массива: path, channel, platform, version, title, tags, yaru, access, album, comments, xxx, hide, private, token. Точное описание аргументов смотрите в описании метода add_photo.
	 * @return array
	 * @access public
	 */
	public function addPhoto($args = array()){

		if(array_key_exists("path", $args)){
			$path=$args["path"];
		}else{
			throw new YFException("Не задан путь к файлу, содержащему изображение", E_ERROR, null, "imageNotFound");
		}

		if(array_key_exists("channel", $args)){
			$pub_channel=$args["channel"];
		}else{
			$pub_channel=null;
		}

		if(array_key_exists("platform", $args)){
			$app_platform=$args["platform"];
		}else{
			$app_platform=null;
		}

		if(array_key_exists("version", $args)){
			$app_version=$args["version"];
		}else{
			$app_version=null;
		}

		if(array_key_exists("title", $args)){
			$title=$args["title"];
		}else{
			$title=null;
		}

		if(array_key_exists("tags", $args)){
			$tags=$args["tags"];
		}else{
			$tags=array();
		}

		if(array_key_exists("yaru", $args)){
			$yaru=$args["yaru"];
		}else{
			$yaru=1;
		}

		if(array_key_exists("access", $args)){
			$access_type=$args["access"];
		}else{
			$access_type="public";
		}

		if(array_key_exists("album", $args)){
			$album=$args["album"];
		}else{
			$album=null;
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

		if(array_key_exists("hide", $args)){
			$hide=$args["hide"];
		}else{
			$hide=false;
		}

		if(array_key_exists("private", $args)){
			$storage_private=$args["private"];
		}else{
			$storage_private=false;
		}

		if(array_key_exists("token", $args)){
			$token=$args["token"];
		}else{
			$token=null;
		}

		return $this->addPhotoEx($path, $pub_channel, $app_platform, $app_version, $title, $tags, $yaru, $access_type, $album, $disable_comments, $xxx, $hide, $storage_private, $token);
	}

	/**
	 * Добавляет ноую фотографию в коллекцию
	 * 
	 * В случае успеха возвращает ассоциативный массив. Если yaru==0, то возвращается array('image_id'=>photo_id), где {photo_id} - численный идентификатор фотографии. Если yaru==1 то возвращается array('image_id'=>photo_id,'post_id'=>post_id), где {photo_id} - идентификатор фотографии, а {post_id} - идентификатор поста на Я.ру.
	 *
	 * @throws YFException|YFRequestException
	 * @param string $path путь к файлу, содержащему изображения
	 * @param string $pub_channel Уникальное имя-маркер клиентского приложения, осуществляющего загрузку фотографии.
	 * @param string $app_platform Дополнительная информация о платформе клиентского приложения, осуществляющего загрузку.
	 * @param string $app_version Версия клиентского приложения, осуществляющего загрузку.
	 * @param string $title Название фотографии. Не может быть пустой строкой.
	 * @param array $tags Массив содержащий теги (метки), уточняющие смысл фотографии.
	 * @param int $yaru Флаг публикации фотографии на странице пользователя на Я.ру. Допустимые значения: "1" - опубликовать (по умолчанию); "0" - не опубликовывать.
	 * @param $access_type Уровень доступа к фотографии. Допустимые значения: public (по умолчанию) - для всех; friends - для друзей; private - для себя.
	 * @param string $album Идентификатор альбома для загрузки фотографии. Альбом должен существовать.
	 * @param boolean $disable_comments Флаг запрета комментариев. Значение по умолчанию: false.
	 * @param boolean $xxx Флаг «только для взрослых» (можно только установить, снять нельзя). Значение по умолчанию: false.
	 * @param boolean $hide_orig Флаг запрета публичного доступа к оригиналу фотографии. Значение по умолчанию: false. Если данный флаг установлен в true, автор не сможет получить оригинал фотографии при помощи API Фоток. Для этого нужно воспользоваться возможностями сервиса Яндекс.Фотки.
	 * @param boolean $storage_private Флаг, закрывающий доступ к фотографии по URL со страниц вне домена Яндекс.Фоток. Значение по умолчанию: false.
	 * @param string $token токен, подтверждающий аутентификацию пользователя. Обязательный аргумент.
	 * @return array
	 * @access public
	 */
	public function addPhotoEx($path, $pub_channel=null, $app_platform=null, $app_version=null, $title=null, $tags=array(), $yaru=1, $access_type="public", $album=null, $disable_comments=false, $xxx=false, $hide_orig=false, $storage_private=false, $token=null){
		
		$path = realpath($path);
		
		if(!file_exists($path)){
			throw new YFException("Файл, содержащий изображение, не найден", E_ERROR, null, "imageNotFound");
		}

		if($token!==null){
			$this->token=$token;
		}
		if($this->token===null){
			throw new YFException("Эта операция доступна только для аутентифицированных пользователей", E_ERROR, null, "authenticationNeeded");
		}

		$url = array("image"=>"@".$path);


		if($pub_channel!==null){
			$url["pub_channel"]=htmlentities($pub_channel,ENT_COMPAT,"UTF-8");
		}

		if($app_platform!==null){
			$url["app_platform"]=htmlentities($app_platform,ENT_COMPAT,"UTF-8");
		}

		if($app_version!==null){
			$url["app_version"]=htmlentities($app_version,ENT_COMPAT,"UTF-8");
		}

		if($title!==null){
			$url["title"]=htmlentities($title,ENT_COMPAT,"UTF-8");
		}

		if(count($tags)>0){
			$url["tags"]=htmlentities(implode(",", $tags),ENT_COMPAT,"UTF-8");
		}

		if(!in_array($yaru, array(0,1))){
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
			$url["album"]=htmlentities($album,ENT_COMPAT,"UTF-8");
		}else if($this->albumId!==null){
			$url["album"]=$this->albumId;
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
		
		$connect = new YFConnect();
		$connect->setUrl("http://api-fotki.yandex.ru/post/");
		$connect->addHeader('Content-Type: multipart/form-data; charset=utf-8; type=entry');
		$connect->setToken($this->token);
		$connect->setPost($url);
		$connect->exec();
		$code = $connect->getCode();
		$xml = $connect->getResponce();
		unset($connect);
		
		switch((int)$code){
			case 200:
				//если код не 200 и не оговоренные в документации Яндекс ошибки, то будет вызвано прерывание общего типа.
				break;
			case 400:
				throw new YFRequestException($xml, $code, "Загружаемый файл не является изображением или имеет недопустимый формат.", "unsupportedImageType");
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
		
		parse_str($xml,$xml);
		return $xml;
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
			throw new YFException("Не задан URL следующей страницы. Вы уже получили последнюю страницу коллекции или поиск по коллекции не был выполнен.", E_ERROR, null, "pageNotFound");
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
	 * @param strging $order Порядок отображения элементов выдачи. Допустимые значения: updated (по умолчанию) - по времени последнего изменения, от новых к старым; rupdated - по времени последнего изменения, от старых к новым; published - по времени загрузки (для фотографии) или создания (для альбома), от новых к старым; rpublished - по времени загрузки (для фотографии) или создания (для альбома), от старых к новым; created - по времени создания согласно EXIF-данным, от новых к старым; rcreated - по времени создания согласно EXIF-данным, от старых к новым.
	 * @param string $offset_time Время создания ресурса  в формате UTC с точностью до секунды. Исключение: ссылки с order равным created или rcreated, в которых время указывается без часового пояса.
	 * @param string $offset_id Численный идентификатор ресурса на Яндекс.Фотках.
	 * @param int $limit Количество элементов на странице выдачи.
	 * @param string $token Токен, подтверждающий аутентификацию пользователя. Если не задан, используется токен, который был передан конструктору. Если не задан и он, то метод вызовет исключение.
	 * @return void
	 * @access public
	 */
	public function searchEx($order="updated", $offset_time=null, $offset_id="", $limit=100, $token=null){
		$this->photoList = array();
		$this->nextPageUrl = null;
		if($token!=null){
			$this->token = $token;
		}
		if(!in_array($order, array("updated","rupdated","published","rpublished","created","rcreated"))){
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

	/**
	 * Метод непосредственно осуществляет запрос к серверу на получение коллекции
	 * 
	 * @throws YFRequestException|YFXMLException
	 * @param string $url URL содержащий адрес коллекции, параметры сортировки, смещение и количество элементов на странице
	 * @return void
	 * @access private
	 */
	private function query($url){
		
		$connect = new YFConnect();
		$connect->setUrl($url);
		if($this->token!=null){
			$connect->setToken($this->token);
		}
		$connect->exec();
		$code = $connect->getCode();
		$xml = $connect->getResponce();
		unset($connect);		
	
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

		YFSecurity::deleteXmlNamespace($xml);
		if(($sxml=simplexml_load_string($xml))===false){
			throw new YFXMLException($xml, E_ERROR,"Не удалось распознать ответ Яндекс как валидный XML документ","canNotCreateXML");
		}

		$result = $sxml->xpath("//link[@rel='next']");
		if(count($result)>0){
			$this->nextPageUrl = $result[0]->attributes()->href;
		}

		$result = $sxml->xpath("//entry");
		$photo = array();
		foreach($result as $xml){
			$photo[] = new YFPhoto($xml->asXML(), $this->token);
		}
		$this->photoList[] = $photo;
		$connect = new YFConnect();
		$connect->setUrl("http://api-fotki.yandex.ru/post/");
		$connect->setPost($url);
		$connect->setToken($this->token);
		$connect->addHeader('Content-Type: application/atom+xml; charset=utf-8; type=entry');
		$connect->addHeader('Expect:');
		$connect->exec();
		$code = $connect->getCode();
		$xml = $connect->getResponce();
		unset($connect);	}
}