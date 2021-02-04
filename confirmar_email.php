<?php
  require_once "setup.php";

	//mensagem de erro exibida ao usuário
	$form_error = "";
	//Controla quando mostrar o paragrafo de erro
	$page_ok = false;

	if( (require_once "assets/scripts/connect.php") == true && (require_once "assets/scripts/check_session.php") == true ){
		$page_ok = true;

		//Vindo da página de cadastro com GET
		if($_SERVER["REQUEST_METHOD"] == "GET"){

			if( (require_once "assets/scripts/class.phpmailer.php") == true){

				function sendConfirmationMail($email, $cod, $email_hash){
					$assunto = "Confirme seu email para ter acesso completo à sua conta.";
					$mensagem = "
						<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
						<html xmlns='http://www.w3.org/1999/xhtml'>

							<head>
								<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
						    	<meta name='viewport' content='width=device-width'>

						    	<style>

							    	html, body{
							            background: #f1f1f1;
							        }

							    	*{
							    		margin: 0;
							    		padding: 0;
							    		border-spacing: 0px;
							    	}

							    	table{
							            position: absolute;
							    		width: 100%;
							    		height: 100%;
							    		font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;
							    		border: none;
							            color: #333;
							            background: #eee;
							    	}

							    	thead th{
							    		height: 50px;
							    		font-size: 18px;
							    		background: #054aa8;
							    		line-height: 20px;
							    		padding-left: 15px;
							    	}

							        thead th span{
							            position: absolute;
							            top: 13px;
							            padding-left: 5px;
							            text-decoration: underline;
							            color: #fff;
							        }

							    	.header-link{
							    		text-align: left;
							    	}

							    	.mail-title{
							    		height: 80px;
							    		color: #fff;
							    		background: #054aa8;
							    		padding-left: 15px;
							    		padding-bottom: 10px;
							    		vertical-align: bottom;
							            text-align: left;
							    	}

							        tbody{
							            text-align: center;
							        }

							        .mail-content{
							            padding:25px 15px;
							            font-size: 18px;
							        }

							        .mail-content a{
							            color: #337ab7;
							        }

							        .mail-content p{
							            margin: 10px 0px;
							        }

							        .cod{
							            font-size: 25px;
							            font-weight: bold;
							            padding: 15px;
							        }

							        .cod span{
							            background: #fff;
							            padding:10px;
							        }

							        tfoot td{
							            height: 50px;
							            font-size: 12px;
							            background: #054aa8;
							            line-height: 20px;
							            padding-left: 15px;
							            color: #fff;
							        }

						    	</style>
							</head>

							<body>
								<table>

									<thead>
										<tr>
											<th class='header-link'><a href='".$_ENV['APP_URL']."'><img src='assets/img/brand-icon.png'/><span>Recibeira</span></a></th>
										</tr>
									</thead>

									<tbody>
										<tr>
											<td class='mail-title'><h1>Código de verificação de email</h1></td>
										</tr>

										<tr>
											<td class='mail-content'>
						                        <p>Digite o código abaixo na página de confirmação de email. Caso a tenha fechado, <a href='".$_ENV['APP_URL']."/confirmar_email.php?key=$email_hash'>clique aqui</a> para ir até ela.</p>

						                        <p class='cod'><span>$cod</span></p>

						                        <p>Caso não tenha iniciado cadastro no site do <a href='".$_ENV['APP_URL']."'>Recibeira</a>, é possível que estejam usando seu email sem sua autorização.</p>
						                    </td>
										</tr>
									</tbody>

									<tfoot>
										<tr>
											<td>Essa mensagem é automática, não responda-a.</td>
										</tr>
									</tfoot>

								</table>
							</body>

						</html>";

					$mail = new PHPMailer();
					$mail -> CharSet = "UTF-8";
					$mail -> IsSMTP();
					$mail -> SMTPDebug = 1;
					$mail -> SMTPAuth = true;
					$mail -> SMTPSecure = $_ENV['SMTP_SECURE'] == 'true' ? 'ssl' : '';//Obrigatório para gmail
					$mail -> Host = $_ENV['SMTP_HOST'];
					$mail -> Port = $_ENV['SMTP_PORT'];
					$mail -> IsHTML(true);
					$mail -> Username=$_ENV['SMTP_USER'];//
					$mail -> Password=$_ENV['SMTP_PASSWORD'];//
					$mail -> SetFrom('noreply@recibeira.com', 'Recibeira');
					$mail -> Subject=$assunto;
					$mail -> Body=$mensagem;
					$mail -> AddAddress($email);

					if($mail -> Send()){
						return true;
					}
					else{
						return false;
					}
				}

				if(isset($_GET["e"]) && !empty($_GET["e"])){

					$email_hash = $_GET["e"];

					if(mysqli_connect_errno()){
						$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
					}
					else{
						//Obtém o email do usuário em questão
						$sql = $BD_Connection->prepare("SELECT $BD_Email_field, $BD_Acc_code_field, $BD_Email_hash_field, $BD_Email_verified_field  FROM $BD_Users_table WHERE $BD_Email_hash_field = ?");

						if($sql){
							$sql->bind_param('s', $email_hash);

							if($sql->execute()){

								$resultado = $sql->get_result();
								$sql = null;

								if($resultado && $resultado->num_rows > 0){
									$obj = $resultado->fetch_assoc();
									mysqli_free_result($resultado);

									//Checa se o email do usuário ainda não está verificado
									if($obj[$BD_Email_verified_field] == 0){
										$email_to_mail = $obj[$BD_Email_field];
										$cod = $obj[$BD_Acc_code_field];
										$hash = $obj[$BD_Email_hash_field];

										if(!sendConfirmationMail($email_to_mail, $cod, $hash)){
											$form_error="Não foi possível enviar o email. Atualize a página e se o problema persistir tente novamente mais tarde.";
										}
									}
									else{
										$BD_Connection->close();
										//Redireciona para o login
										header("Location: login.php");
									}

								}
								else{
									$form_error="Parece que você ainda não criou uma conta. <a href='registrar.php'>Crie uma agora.</a>";
								}

							}//if($sql->execute())
							else{
								$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
							}

						}
						else{
							$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
						}
					}//if(mysqli_connect_errno())

				}
				else{
					$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
				}//if(isset($_GET["e"]) && !empty($_GET["e"]))

			}
			else{
				$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
			}//if( (require_once "assets/scripts/class.phpmailer.php") == true)

		}//if($_SERVER["REQUEST_METHOD"] == "GET")

		//Submit do código com POST
		if($_SERVER["REQUEST_METHOD"] == "POST"){

			if( (require_once "assets/scripts/utils.class.php") == true){

				$codigo = $_POST["codigo"];
				$email_hash = $_POST["key"];

				$utils = new Utils();

				if(empty($codigo)){
					$form_error = "Digite seu código de ativação abaixo.";
				}
				else if(strlen($codigo) > 6){
					$form_error = "Seu código não deve conter mais que 6 caracteres.";
				}
				else if(strlen($codigo) < 6){
					$form_error = "Seu código deve conter 6 caracteres.";
				}
				else if(!$utils->isNumeric($codigo)){
					$form_error = "Seu código deve possuir apenas números.";
				}
				else{
					$codigo = $utils->checkInput($codigo);

					if(mysqli_connect_errno()){
						$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
					}
					else{
						//Fazer outra pesquisa pra confirmar o código
						$sql = $BD_Connection->prepare("SELECT $BD_Acc_code_field, $BD_Name_field, $BD_Email_field, $BD_Id_field FROM $BD_Users_table WHERE $BD_Email_hash_field = ?;");

						if($sql){
							$sql->bind_param('s', $email_hash);
							$sql->execute();

							$resultado = $sql->get_result();
							$sql = null;

							if($resultado->num_rows > 0){
								$obj = $resultado->fetch_assoc();

								//Obtém o código salvo no banco
								$cod_db = $obj[$BD_Acc_code_field];

								//Checa se os códigos são iguais
								if(strcmp($codigo, $cod_db) == 0){
									//Atualiza no banoc o estado do email da conta
									$sql = $BD_Connection->prepare("UPDATE $BD_Users_table SET $BD_Email_verified_field = ?, $BD_Acc_code_field = ? WHERE $BD_Acc_code_field = ?");

									if($sql){
										$verified = 1;
										$code = rand(100000, 999999);
										$sql->bind_param('iii', $verified, $code, $codigo);

										if($sql->execute()){
											$sql = null;

											//Inicia a sessão do usuário
											session_start();

											$_SESSION["user"] = $obj[$BD_Name_field];
											$_SESSION["email"] = $obj[$BD_Email_field];
											$_SESSION["id"] = $obj[$BD_Id_field];

											$BD_Connection->close();
											//Redireciona para a home
											header("Location: home.php");
											exit;
										}
										else{
											$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
										}
									}
									else{
										$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
									}
								}
								else{
									$form_error="Código incorreto, verifique-o e tente novamente.";
								}
							}
							else{
								$form_error="Esse email não está cadastrado no site.";
							}//if($resultado->num_rows > 0)
						}
						else{
							$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
						}//if($sql)
					}//if(mysqli_connect_errno())
				}//ifs da validação
			}//if( (require_once "assets/scripts/utils.php") == true)
			else{
				$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
			}
		}//if($_SERVER["REQUEST_METHOD"] == "POST")
	}//if( (require_once "assets/scripts/connect.php") == true )
	else{
		$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
	}

?>

<!DOCTYPE html>
<html lang="pt-br">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
       	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<meta name="description" content="">
    	<meta name="author" content="Allex Rodrigues">
    	<link rel="icon" href="assets/img/favicon.ico">

		<title>Confirmar email - Recibeira</title>

		<link rel="stylesheet" href="//ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/nav.css">
		<link rel="stylesheet" href="css/footer.css">
		<link rel="stylesheet" href="css/form.css">
		<link rel="stylesheet" href="css/geral.css">

	</head>

	<body>

		<!--BARRA DE NAVEGAÇÃO-->
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php"><img src="assets/img/brand-icon.png" alt="Recibeira logo"/><span>Recibeira</span></a>
				</div>
			</div>
		</nav>
		<!--FIM BARRA DE NAVEGAÇÃO-->

		<div class="container">

			<form class="form-signin" method="POST" action="confirmar_email.php" onsubmit="return validateForm('recover-form')" id="recover-form">

				<h2 class="form-signin-heading">Confirmar Email</h2>

				<p>Enviamos um código de ativação de 6 digitos para o email cadastrado. Insira-o abaixo para concluir seu cadastro e ganhar acesso à sua conta. Caso não o encontre, aguarde alguns minutos e não esqueça de checar sua caixa de spam.</p>

				<?php if(!$page_ok || $_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST"){ ?>
					<p class="info form-error" style="display: block"><?php echo $form_error; ?></p>

					<input type="hidden" name="key" value="<?php echo $email_hash; ?>"/>
				<?php }else{ ?>
					<p class="info form-error"></p>
				<?php } ?>

				<label class="sr-only" for="inCodigo">Código</label>
				<input type="text" id="inCodigo" name="codigo" class="form-control bom-senso" placeholder="Código" maxlength="6" required autofocus>

				<button type="submit" class="btn btn-lg btn-primary btn-block">Confirmar</button>

			</form>

		</div>

		<?php include "assets/snippets/footer.php"; ?>

		<!--JavaScript-->
		<script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="js/form_validation.js" type="text/javascript"></script>

	</body>

</html>
