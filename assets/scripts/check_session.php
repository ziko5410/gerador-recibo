<?php
	//Inicia uma sessão para o usuário
	if(session_status() != PHP_SESSION_ACTIVE){
		session_start();
	}

	$loggedin_only_pages = ["home.php", "recibar.php", "gera_pdf.php", "logout.php", "delete_profile.php", "gerar_multi.php"];

	function arrayContainsString($array, $str){

		for($i = 0; $i < count($array); $i++){
			if(strcmp($array[$i], $str) == 0 || stripos($str, $array[$i]) ){
				return true;
			}
		}

		return false;
	}

	//Checa se está logado
	if(isset($_SESSION["id"])){
		//Checa se o usuário está em alguma página que ele não deveria enquanto logado
		if( !arrayContainsString($loggedin_only_pages, $_SERVER["PHP_SELF"]) ){
			header("Location: home.php");
			exit();
		}

	}
	else{
		//Checa se os cookies existem para iniciar a sessão automaticamente
		if(isset($_COOKIE["uid"]) && isset($_COOKIE["tk"])){

			if( (require_once "assets/scripts/connect.php") == true ){

				if(!mysqli_connect_errno()){

					$uid = $_COOKIE["uid"];
					$token = $_COOKIE["tk"];

					//Fazer uma busca no banco, iniciar a sessão, atualiza o token
					$sql = $BD_Connection->prepare("SELECT * FROM $BD_Users_table WHERE $BD_User_id_field = ?");

					if($sql){
						$sql->bind_param('i', $uid);

						if($sql->execute()){

							$resultado = $sql->get_result();
							$sql = null;

							if($resultado->num_rows > 0){
								$obj = $resultado->fetch_assoc();
								mysqli_free_result($resultado);

								//Se o token guardado e o token do cookie forem iguais
								if(strcmp($obj[$BD_User_token_field], $token) == 0){
									//Define os dados da sessão
									$_SESSION["user"] = $obj[$BD_Name_field];
									$_SESSION["email"] = $obj[$BD_Email_field];
									$_SESSION["id"] = $obj[$BD_Id_field];

									//Atualiza o token
									$token = hash("sha256", mt_rand());

									//Atualiza o token no banco
									$sql = $BD_Connection->prepare("UPDATE $BD_Users_table SET $BD_User_token_field = ? WHERE $BD_Id_field = ?");

									if($sql){
									 	$sql->bind_param('si', $token, $obj[$BD_Id_field]);

									 	if($sql->execute()){
									 		//Salva os cookies com o token e o id, que serão usados para validar a sessão numa visita futura
									 		setcookie("uid", $obj[$BD_User_id_field], time() + 24*60*60*7);
											setcookie("tk", $token, time() + 24*60*60*7);

											if( (stripos($_SERVER['PHP_SELF'], "home.php") === false) && (stripos($_SERVER['PHP_SELF'], "recibar.php") === false) ){

												$BD_Connection->close();
												header("Location: home.php");
												exit();
											}
									 	}
									}

								}//if(strcmp($obj[$BD_User_token_field], $token) == 0)

							}//if($resultado->num_rows > 0)

						}//if($sql->execute())

					}//if($sql)

				}//if(mysqli_connect_errno())

			}//if( (require_once "assets/scripts/connect.php") == true )

			$BD_Connection->close();
			header("Location: login.php?erro=1");
			exit();
		}
		else{
			//Redireciona o usuário se ele estiver em uma página que exige login
			if( arrayContainsString($loggedin_only_pages, $_SERVER["PHP_SELF"]) ){
				header("Location: login.php");
				exit();
			}
		}//if(isset($_COOKIE["uid"]) && isset($_COOKIE["tk"]))

	}//if(isset($_SESSION["id"]))

?>
