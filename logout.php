<?php
	session_start();
	
	//checa se a sessão está aberta
	if(isset($_SESSION["id"]) && !empty($_SESSION["id"]) ){
		//Remove todos os cookies
		setcookie("uid", "" , time() - 3600);
		setcookie("tk", "" , time() - 3600);
		setcookie("PHPSESSID", "", time() - 3600);

		//Limpa os dados da sessão
		session_unset();

		session_destroy();
	}
	
	header("Location: login.php");
	exit;
?>