<?php
/**
 * Позволяет управлять пользователями, от лица которых осуществляется работа
 * с сервисом Яндекс.Фотки.
 * 
 * @author SilentImp <ravenb@mail.ru>
 * @link http://twitter.com/SilentImp/
 * @link http://silentimp.habrahabr.ru/
 *
 * @throws YFUserException
 * @package YandexFotki
 * 
 * @todo протестировать
 * @todo разделить исключения по типам
 * @todo подумать над тем, что нужно выделить в отдельные классы
 * @todo организовтаь работу без удаления пространства имен из отетов Яндекса, если возможно
 * @todo написать плагин для WP. "ну можно сделать отображение всей галереи, только указанной или только указанной фотки из какой-нить галереи", "ну и красивый выбор фотки для вставки как у WP-Gallery", "и лайтбокс, как у фб"
 */
class YFUserManager {
	private $userList = array();

	/**
	 * Текущий активный пользователь
	 * @var YFUser
	 */
	private $activeUser = null;

	/**
	 * Позволяет сразу создать первого пользователя.
	 * Если он создан, то автоматически становится текущим.
	 *
	 * @param string $login Логин пользователя. Если не указан, пользователь создан не будет.
	 * @param string $password Пароль пользователя. Если указан логин, но не указан пароль, то пользователь будет создан, но не будет аутентифицирован.
	 * @return YFUser
	 */
	public function __construct($login=null, $password=null){
		if($login!==null){
			return $this->addUser($login, $password);
		}
	}	

	/**
	 * Возвращает текущего активного пользователя
	 *
	 * @return YFUser
	 */
	public function getCurrentUser(){
		return $this->activeUser;
	}
	
	/**
	 * Удаляет пользователя. Если пользователь был выбран как текущий, он
	 * сбрасывается в null.
	 *
	 * @throws YFUserException
	 * @param string $login Логин пользователя.
	 * @return boolean
	 */
	public function removeUser($login){
		if(!array_key_exists($login, $this->userList)){
			throw new YFUserException("Пользователь не найден", E_WARNING);
		}
		if($this->activeUser->login==$login){
			$this->activeUser=null;
		}
		unset($this->userList[$login]);

		return true;
	}	

	/**
	 * Создает нового пользователя.
	 *
	 * @throws YFUserException
	 * @param string $login Логин пользователя.
	 * @param string $password Пароль пользователя. Если не указан, то пользователь будет создан, но не будет аутентифицирован.
	 * @return YFUser
	 */
	public function addUser($login, $password=null){
		if(array_key_exists($login, $this->userList)){
			throw new YFUserException("Пользователь с таким логином уже существует", E_ERROR);
		}
		$this->userList[$login] = new YFUser($login, $password);
		$this->selectActiveUser($login);
		$this->activeUser->getServiceDocument();
		if($password!==null){
			$this->activeUser->authenticate();
		}
		return $this->activeUser;
	}
	
	/**
	 * Выбирает пользователя как текущего активного
	 * 
	 * @throws YFUserException
	 * @param string $login Логин пользователя. Обязательный аргумент.
	 * @return YFUser
	 */
	public function selectActiveUser($login){
		if(!array_key_exists($login,$this->userList)){
			throw new YFUserException("Пользователь не найден", E_ERROR);
		}
		return $this->activeUser = &$this->userList[$login];
	}
}