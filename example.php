<?php

	session_start();
	header('content-type:text/html; charset=utf-8');
	function __autoload($class_name){
		require_once("../../core/".$class_name.".php");
	}


	// Вывод списка обломов, создание и удаление альбома
	try{
		$user = new YFUser('SilentImp');
		$user->authenticate('fkytnyjfy_2002');
		
		$user->addAlbumCollection("Мимикрия")->addAlbum("Тестальбом","Этот альбом тестовый");
		$user->getAlbumCollection("Мимикрия")->search();
		
		$i=0;
		foreach($user->addAlbumCollection("Мимикрия")->getPage(0) as $album){
			echo "<br/>".$i.". ";
			echo $album->getTitle();
			$i++;
		}		
		$user->getAlbumCollection("Мимикрия")->deleteAlbumByTitle("Тестальбом");

	}catch(Exception $err){
		die($err->getMessage());
	}

?>