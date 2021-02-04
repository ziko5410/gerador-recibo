<?php
  require_once "setup.php";

	//mensagem de erro exibida ao usuário
	$form_error = "";

	if( (require_once "assets/scripts/connect.php") == true && (require_once "assets/scripts/check_session.php") == true && (require_once "assets/scripts/utils.class.php") == true){

		if($_SERVER["REQUEST_METHOD"] == "POST"){

			$email = $_POST["email"];
			$utils = new Utils();

			//Validação do email
			if(empty($email)){
				$form_error = "Digite um email.";
			}
			else if(strlen($email) > 50){
				$form_error = "Seu email está muito longo, tente outro (máximo 50 caracteres).";
			}
			else if(!$utils->validateEmail($email)){
				$form_error = "Digite um email válido.";
			}
			else{
				$email = $utils->checkInput($email);

				function sendRecoverMail($email, $cod, $email_hash){
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

						        .btn{
						            display: block;
						            width: 200px;
						            margin: auto;
						            padding: 10px 16px;
						            font-size: 18px;
						            line-height: 1.3333333;
						            border-radius: 6px;
						            color: #fff !important;
						            text-decoration: none;
						            border: 1px solid #2e6da4;
						            background-color: #337ab7;
						            font-weight: 400;
						            margin-top: 10px;
						        }

						    	</style>

							</head>

							<body>
								<table>

									<thead>
										<tr>
											<th class='header-link'><a href='".$_ENV['APP_URL']."'><img src='".$_ENV['APP_URL']."/assets/img/brand-icon.png'/><span>Recibeira</span></a></th>
										</tr>
									</thead>

									<tbody>
										<tr>
											<td class='mail-title'><h1>Redefinir senha</h1></td>
										</tr>

										<tr>
											<td class='mail-content'>
						                        <p>Clique no botão abaixo para redefinir sua senha ou copie e cole o link no seu navegador.</p>
						                        <a href='".$_ENV['APP_URL']."/redefinir_senha.php?e=$email_hash'>".$_ENV['APP_URL']."/redefinir_senha.php?e=".$email_hash."</a>

						                        <a href='".$_ENV['APP_URL']."/redefinir_senha.php?e=$email_hash' class='btn'>Redefinir</a><br>

						                        <p>Digite o código abaxo quando solicitado.</p>

						                        <p class='cod'><span>$cod</span></p>

						                        <p>Caso não tenha solicitado a redefinição da sua senha, é possível que estejam usando seu email sem sua autorização.</p>
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
          $mail -> SetFrom($_ENV['SMTP_USER'], 'Recibeira');
					$mail -> Subject=$assunto;
					$mail -> Body=$mensagem;
					$mail -> AddAddress($email);

					if($mail -> Send()){
						return true;
					}
					else{
						return false;
					}
				}//function sendRecoverMail($email, $cod, $email_hash)

				if(mysqli_connect_errno()){
					$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
				}
				else{
					//Obtém o email do usuário em questão
					$sql = $BD_Connection->prepare("SELECT $BD_Acc_code_field, $BD_Email_hash_field FROM $BD_Users_table WHERE $BD_Email_field = ?");

					if($sql){
						$sql->bind_param('s', $email);
						$sql->execute();

						$resultado = $sql->get_result();
						$sql = null;

						if($resultado && $resultado->num_rows > 0){
							$obj = $resultado->fetch_assoc();
							mysqli_free_result($resultado);

							$cod = $obj[$BD_Acc_code_field];
							$hash = $obj[$BD_Email_hash_field];

							if(!sendRecoverMail($email, $cod, $hash)){
								$form_error="Não foi possível enviar o email. Atualize a página e se o problema persistir tente novamente mais tarde.";
							}

						}
						else{
							$form_error="Parece que você ainda não criou uma conta. <a href='registrar.php'>Crie uma agora.</a>";
						}
					}
					else{
						$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
					}
				}//if(mysqli_connect_errno())

			}//if(empty($email))

		}//if($_SERVER["REQUEST_METHOD"] == "POST")

		$BD_Connection->close();

	}//if( (require_once "assets/scripts/connect.php") == true ...
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
    	<meta name="description" content="Recupere sua senha caso tenha esquecido-a">
    	<meta name="author" content="Allex Rodrigues">
    	<link rel="icon" href="assets/img/favicon.ico">

		<title>Recuperar senha - Recibeira</title>

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

			<!--FORMULPÁRIO DE LOGIN-->
			<form class="form-signin" method="POST" action="recuperar.php" onsubmit="return validateForm('recover-form')" id="recover-form">

				<h2 class="form-signin-heading">Recuperar senha</h2>

				<p>Diga-nos seu email cadastrado para te enviarmos um link de recuperação da sua senha.</p>

				<?php if($_SERVER["REQUEST_METHOD"] == "POST"){ ?>
					<p class="info form-error" style="display: block"><?php echo $form_error; ?></p>
				<?php }else{ ?>
					<p class="info form-error"></p>
				<?php } ?>

				<label class="sr-only" for="inEmail">Email</label>
				<input type="email" id="inEmail" name="email" class="form-control bom-senso" placeholder="Email" maxlength="50" required autofocus>

				<!--INSERIR UM CAPTCHA AQUI-->

				<button type="submit" class="btn btn-lg btn-primary btn-block">Recuperar</button>

				<a href="login.php">Voltar para o login</a><br>
				<a href="registrar.php">Ainda não tem uma conta?</a>

			</form>
			<!--FIM FORMULÁRIO DE LOGIN-->

		</div>

		<?php include "assets/snippets/footer.php"; ?>

		<!--JavaScript-->
		<script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="js/form_validation.js" type="text/javascript"></script>

	</body>

</html>
