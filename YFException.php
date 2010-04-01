<?php
/**
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 * @package YandexFotki
 */
 
/**
 * Абстрактное исключение, которое сигнализирует о не типизированой ошибке при работе с Яндекс.Фотками
 * 
 * @package YandexFotki
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 * @link http://code.websaints.net/
 */
class YFException extends Exception {
	/**
	 * Строка, содержащая человекопонятное описание ошибки
	 * 
	 * @var string
	 * @access protected
	 */
	protected $details;
	
	/**
	 * Строка, содержащая символьный идентификатор ошибки
	 * 
	 * @var string
	 * @access protected
	 */
	protected $shortCode;
		
	/**
	 * Конструктор
	 * 
	 * @param string $message
	 * @param int $code
	 * @param Exception $previous
	 * @param string $details
	 * @return void
	 * @access public
	 */
	public function __construct($message, $code = 0, $details=null, $shortCode=null){
		if(empty($details)){
			$details = $message;
		}
		$this->details = $details;
		$this->shortCode = $shortCode;
		parent::__construct($message, $code);
	}
	
	/**
	 * Возвращает строку, содержащую человекопонятное описание ошибки
	 * 
	 * @return string
	 * @access public
	 */
	public function getDetails(){
		return $this->details;
	}
	
	/**
	 * Возвращает строку, содержащую символьный идентификатор ошибки
	 * 
	 * @return string
	 * @access public
	 */
	public function getShortCode(){
		return $this->shortCode;
	}
}