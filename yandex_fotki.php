<?php
	/*! \mainpage Классы для работы с сервисом Яндекс.Фотки
	
	@author SilentImp
	@author http://twitter.com/SilentImp
	@author http://silentimp.habrahabr.ru/
	@author <a href="mailto:ravenb@mail.ru">ravenb@mail.ru</a>
	
	Тут собраны несколько классов, которые позволят вам организовать работу с Яндекс.Фотки.<br/>
	Полное описание API вы можете найти тут: http://api.yandex.ru/fotki/<br/>
	Вопросы по работе классов и API вы можете отправлять мне или задавать в <a href="http://clubs.ya.ru/api-fotki">Клубе API Яндекс.Фоток</a><br/><br/>
	Огромное спасибо <a href="http://ar2r.habrahabr.ru/">ar2r</a> и <a href="http://nickmitin.habrahabr.ru/">nickmitin</a> за помощь с портированием алгоритма шифрования, <a href="http://ijon-c.ya.ru/">proto</a> за разьяснения по поводу проекта <a href="http://api.yandex.ru/fotki/">API Яндекс.Фотки</a>, комментарии и всем всем всем за уделенное мне время.
	
	\defgroup yandex_fotki Классы для работы c сервисом Яндекс.Фотки
	С помощью классов этой группы происходит работа с сервисом Яндекс.Фотки
	
	@todo	
	- протестировать
	*/
	
	//!	Класс, который позволяет вам управлять пользователями, от литца которых осуществляется работа с сервисом Яндекс.Фотки
	/*!
		@ingroup yandex_fotki
	*/
	class yandex_fotki{
		//! Массив содержащий список пользователей
		private $user_list = array();
		//! Переменная с указателем на текущего пользователя
		private $current_user = null;
		
		//! Конструктор. Позволяет сразу создать первого пользователя. Если он создан, то автоматически становится текущим.
		/*!
			@param login строка, содержащая логин пользователя. Необязательный аргумент. Если не указан, пользователь создан не будет.
			@param password строка, содержащая пароль пользователя. Необязательный аргумент. Если указан логин, но не указан пароль, то пользователь будет создан, но не будет аутентифицирован.
		*/
		public function __construct($login=null, $password=null){
			if($login!==null){
				$this->add_user($login, $password);
			}
		}
		
		//! Возвращает текущего пользователя
		/*!
			@return экземпляр объекта yandex_fotki_user, содержащий текущего пользователя
			@see yandex_fotki_user
		*/
		public function user(){
			return $this->current_user;
		}
		
		//! Удаляет пользователя. Если пользователь был выбран как текущий, текщий пользователь сбрасывается в NULL
		/*!
			@param login строка, содержащая логин пользователя. Обязательный аргумент.
		*/
		public function remove_user($login=null){
			if($login==null){
				throw new Exception("Не задан логин пользователя", E_ERROR);
			}
			if(!array_key_exists($login, $this->user_list)){
				throw new Exception("Пользователь не найден", E_ERROR);
			}
			if($this->current_user->login==$login){
				$this->current_user=null;
			}
			unset($this->user_list[$login]);			
		}
		
		//! Создает нового пользователя
		/*!
			@param login строка, содержащая логин пользователя. Обязательный аргумент.
			@param password строка, содержащая пароль пользователя. Необязательный аргумент. Если не указан пароль, то пользователь будет создан, но не будет аутентифицирован.
		*/
		public function add_user($login=null, $password=null){
			if($login==null){
				throw new Exception("Не задан логин пользователя", E_ERROR);
			}
			if(array_key_exists($login, $this->user_list)){
				throw new Exception("Пользователь с таким логином уже существует", E_ERROR);
			}
			$this->user_list[$login] = new yandex_fotki_user($login, $password);
			$this->select_user($login);
			$this->current_user->get_service_document();
			if($password!=null){
				$this->current_user->authentication();
			}
		}
		
		//! Выбирает пользователя как текущего
		/*!
			@param login строка, содержащая логин пользователя. Обязательный аргумент.
		*/
		public function select_user($login=null){
			if($login==null){
				throw new Exception("Не задан логин пользователя",E_ERROR);
			}
			if(!array_key_exists($login,$this->user_list)){
				throw new Exception("Пользователь не найден",E_ERROR);
			}
			$this->current_user = &$this->user_list[$login];
		}
	}
?>