<?php
/**
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 * @package YandexFotki
 */
 
/**
 * Класс, который осуществляет непосредственно запросы к серверу.
 *
 * @throws YFRequestException
 * @package YandexFotki
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 */
class YFConnect {

	/**
	 * @var array Массив заголовков, которые нужно послать
	 * @access protected
	 */
	protected $headers = array();
	/**
	 * @var string Строка, содержащая POST данные
	 * @access protected
	 */
	protected $post = null;
	/**
	 * @var string URI которому будет направлен запрос
	 * @access protected
	 */
	protected $url = null;
	/**
	 * @var cURL ресурс cURL
	 * @access protected
	 */
	protected $curl = null;
	/**
	 * @var string Ответ сервера, которому был выслан запрос
	 * @access protected
	 */
	protected $responce = null;
	/**
	 * @var string Код ответа сервера, которому был выслан запрос
	 * @access protected
	 */
	protected $code = null;
	
	/**
	 * Конструктор. Создает ресурс cURL. Можно сразу задать URL, на который будет отправлен запрос
	 * @param string $url URI которому будет направлен запрос. Необязательный аргумент
	 * @param string $post Строка, которая будет передана в запросе методом POST. Необязательный аргумент
	 * @return void
	 * @throws YFRequestException
	 * @access public
	 */
	public function __construct($url=null,$post=null){
		$this->curl = curl_init();
		if($this->curl===false){
			throw new YFRequestException("Невозможно создать ресурс cURL", E_ERROR);
		}
		if($url!=null){
			$this->setUrl($url);
		}
		if($post!=null){
			$this->setPost($post);
		}
	}

	/**
	 * Удаляет ресурс cURL
	 * @return void
	 * @access public
	 */
	public function __destruct(){
		curl_close($this->curl);
	}
	
	/**
	 * Метод задает POST данные, которые будут отправлены.  Устанавливает тип запроса как POST
	 * @param string $post Строка, которая будет передана в запросе методом POST
	 * @return void
	 * @access public
	 */
	public function setPost($post){
		$this->post = $post;
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->post);
	}

	/**
	 * Метод задает URI, которому адресован запрос
	 * @param string $url URI которому будет направлен запрос
	 * @return void
	 * @access public
	 */	
	public function setUrl($url){
		$this->url = $url;
		curl_setopt($this->curl, CURLOPT_URL, $this->url);
	}
	
	/**
	 * Заголовок аутентифицированного пользователя
	 * @param string $token строка, содержащая token пользователя
	 * @return void
	 * @access public
	 */	
	public function setToken($token){
		$this->headers[] = 'Authorization: FimpToken realm="fotki.yandex.ru", token="'.$token.'"';
	}
	
	/**
	 * Добавляет файл, который будет отправлен PUT запросом. Устанавливает тип запроса как PUT
	 * @param string $file Файл, откуда данные, которые будут отправлены, должны быть считаны.
	 * @param string $size Ожидаемый размер файла в байтах.
	 * @return void
	 * @access public
	 */
	public function setPutFile($file, $size){
		curl_setopt($this->curl, CURLOPT_PUT, true);
		curl_setopt($this->curl, CURLOPT_INFILE, $file);
		curl_setopt($this->curl, CURLOPT_INFILESIZE, $size);
	}
	
	
	/**
	 * Устанавливает тип запроса как DELETE
	 * @return void
	 * @access public
	 */
	public function setDelete(){
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
	}
	
	/**
	 * Добавить дополнительный заголовок, который будет передан в запросе
	 * @param string $header строка, содержащая дополнительный заголовок, который будет передан в запросе
	 * @return void
	 * @access public
	 */	
	public function addHeader($header){
		$this->headers[] = $header;
	}

	/**
	 * Возвращает код ответа сервера
	 * @return int Код http ответа сервера
	 * @access public
	 */		
	public function getCode(){
		return (int)$this->code;
	}
	
	/**
	 * Возвращает код ответа сервера
	 * @return string тело ответа сервера
	 * @access public
	 */	
	public function getResponce(){
		return $this->responce;
	}

	/**
	 * Выполняет сформированный запрос
	 * @return void
	 * @access public
	 */		
	public function exec(){
		if($this->url === null){
			throw new YFRequestException("Не задан URI, которому должен быть направлен запрос.", E_ERROR);
		}
		if(count($this->headers)>0){
			curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
		}
		curl_setopt($this->curl, CURLOPT_HEADER, false);
		curl_setopt($this->curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		$this->responce = curl_exec($this->curl);
		$this->code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
	}
	
}
?>