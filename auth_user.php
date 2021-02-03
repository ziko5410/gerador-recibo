<?php
	session_start();
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){

		if( ((require_once "assets/scripts/connect.php") == true) && ((require_once "assets/scripts/utils.class.php") == true) ){
			//Recebe as mensagens de erro
			$error_cod=0;

			$email = $_POST["email"];
			$senha = $_POST["senha"];

			$utils = new Utils();

			//Validação do email
			if(empty($email)){
				$error_cod = 3;
			}
			else if(strlen($email) > 50){
				$error_cod = 3;
			}
			else if(!$utils->validateEmail($email)){
				$error_cod = 3;
			}
			else{
				$email = $utils->checkInput($email);
			}

			//Validação da senha
			if(empty($senha)){
				$error_cod = 4;
			}
			else if(strlen($senha) < 8){
				$error_cod = 4;
			}
			else if(strlen($senha) > 12){
				$error_cod = 4;
			}
			else{
				$senha = $utils->checkInput($senha);
			}

			if($error_cod == 0){
				//Busca no banco pelo usuário
				$sql = $BD_Connection->prepare("SELECT * FROM $BD_Users_table WHERE $BD_Email_field = ?");

				if($sql){
					$sql->bind_param('s', $email);
					
					if($sql->execute()){

						$resultado = $sql->get_result();

						if($resultado->num_rows > 0){
							$obj = $resultado->fetch_assoc();
							mysqli_free_result($resultado);
							//Obtém a senha salva no banco (hash)
							$senha_bd = $obj[$BD_Password_field];

							//Compara as duas senhas (digitada e salva no banco)
							if(password_verify($senha, $senha_bd)){
								$_SESSION["user"] = $obj[$BD_Name_field];
								$_SESSION["email"] = $obj[$BD_Email_field];
								$_SESSION["id"] = $obj[$BD_Id_field];

								if(!empty($_POST["lembrar"]) && strcmp($_POST["lembrar"], "lembrar") == 0 ){
									//Criar um token para validar a sessão quando o usuário revisitar o site
									$token = hash("sha256", mt_rand());

									//Atualiza o token no banco
									$sql = $BD_Connection->prepare("UPDATE $BD_Users_table SET $BD_User_token_field = ? WHERE $BD_Id_field = ?");

									 if($sql){
									 	$sql->bind_param('si', $token, $obj[$BD_Id_field]);

									 	if($sql->execute()){
									 		$duracao = time()+24*60*60*7;

									 		//Salva os cookies com o token e o id, que serão usados para validar a sessão numa visita futura
									 		setcookie("uid", $obj[$BD_User_id_field], $duracao);
											setcookie("tk", $token, $duracao);
									 	}
									 }

								}

								$BD_Connection->close();
								header("Location: home.php");
								exit;
							}
							else{
								$error_cod = 2;
							}//if(password_verify($senha, $senha_bd))
						}
						else{
							$error_cod = 2;
						}//if($resultado->num_rows > 0)
					}
					else{
						$error_cod = 1;
					}//if($sql->execute())
				}
				else{
					$error_cod = 1;
				}//if($sql)

			}//if($error_cod == 0)
		}
		else{
			$error_cod = 1;
		}//if( ((require_once "assets/scripts/connect.php") == true) && ((require_once "assets/scripts/utils.class.php") == true) )

		$BD_Connection->close();
		header("Location: login.php?erro=$error_cod");
		exit;
	}

?>