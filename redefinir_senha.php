<?php
  require_once "setup.php";

	//mensagem de erro exibida ao usuário
	$form_error = "";
	//Controla quando mostrar o paragrafo de erro
	$page_ok = false;

	if( (require_once "assets/scripts/connect.php") == true && (require_once "assets/scripts/check_session.php") == true ){
		$page_ok = true;

		if($_SERVER["REQUEST_METHOD"] == "GET"){

			$email_hash = $_GET["e"];

		}
		else if($_SERVER["REQUEST_METHOD"] == "POST"){

			if( (require_once "assets/scripts/utils.class.php") == true ){

				//Validação do captcha
				$captcha_data = $_POST["g-recaptcha-response"];
				$secret = $_ENV['CAPTCHA_SECRET'];

				$utils = new Utils();

				//Verifica a resposta do captcha
				if(!isset($captcha_data) || empty($captcha_data)){
					$form_error="Responda o captcha. É só clicar na caixinha ali embaixo.";
				}
				else{
					//Obtém o json com o resultado do captcha
					$json = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$captcha_data);

					$resposta = json_decode($json, true);

					if(!$resposta['success']){
						$form_error = "Captcha incorreto, tente novamente.";
					}
					//Se o captcha estiver correto, procede com a validação do formulário
					else{

						$senha = $_POST["senha"];
						$senhaconf = $_POST["senhaconf"];
						$codigo = $_POST["codigo"];
						$email_hash = $_POST["key"];

						if(isset($email_hash) && !empty($email_hash)){

							//Validação da senha
							if(empty($senha)){
								$form_error = "Digite uma senha.";
							}
							else if(strlen($senha) < 8){
								$form_error = "Senha muito curta, tente uma mais longa (mínimo 8 caracteres).";
							}
							else if(strlen($senha) > 12){
								$form_error = "Senha muito longa, tente uma mais curta (máximo 12 caracteres).";
							}
							else{
								$senha = $utils->checkInput($senha);
							}

							//Validação da confirmação da senha
							if(strcmp($senhaconf, $senha) != 0){
								$form_error = "As senhas não coincidem.";
							}
							else{
								$senhaconf = $utils->checkInput($senhaconf);
							}

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
							}

							if(mysqli_connect_errno()){
								$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
							}
							else{
								//Fazer outra pesquisa pra confirmar o código
								$sql = $BD_Connection->prepare("SELECT $BD_Acc_code_field FROM $BD_Users_table WHERE $BD_Email_hash_field = ?;");

								if($sql){
									$sql->bind_param('s', $email_hash);
									$sql->execute();

									$resultado = $sql->get_result();
									$sql = null;

									if($resultado->num_rows > 0){
										$obj = $resultado->fetch_assoc();
										mysqli_free_result($resultado);

										//Obtém o código salvo no banco
										$cod_db = $obj[$BD_Acc_code_field];

										//Checa se os códigos são iguais
										if(strcmp($codigo, $cod_db) == 0){
											//Atualiza no banoc o estado do email da conta
											$sql = $BD_Connection->prepare("UPDATE $BD_Users_table SET $BD_Password_field = ? , $BD_Acc_code_field = ? WHERE $BD_Acc_code_field = ?");

											if($sql){
												$new_code = rand(100000, 999999);
												$sql->bind_param('sii', password_hash($senha, PASSWORD_DEFAULT), $new_code, $codigo);

												if($sql->execute()){
													$sql = null;
													$BD_Connection->close();
													//Redireciona para o login
													header("Location: login.php?i=1");
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

						}
						else{
							$BD_Connection->close();
							header("Location: index.php");
							exit;
						}//if(isset($email_hash) && !empty($email_hash))

					}//if(!$resposta['success'])

				}//if(!isset($captcha_data) || empty($captcha_data))

			}//if( (require_once "assets/scripts/utils.class.php") == true )

		}//if($_SERVER["REQUEST_METHOD"] == "POST")
		else{
			$BD_Connection->close();
			header("Location: index.php");
			exit;
		}

		$BD_Connection->close();

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
    	<meta name="description" content="Acesse sua conta agora mesmo">
    	<meta name="author" content="Allex Rodrigues">
    	<link rel="icon" href="assets/img/favicon.ico">

		<title>Redefinir senha - Recibeira</title>

		<link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/nav.css">
		<link rel="stylesheet" href="css/footer.css">
		<link rel="stylesheet" href="css/form.css">
		<link rel="stylesheet" href="css/geral.css">

		<!--RECAPTCHA-->
		<script src="https://www.google.com/recaptcha/api.js" type="text/javascript"></script>

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
			<form class="form-signin" method="POST" action="redefinir_senha.php" onsubmit="return validateForm('login-form')" id="login-form">

				<h2 class="form-signin-heading">Redefinir senha</h2>

				<?php if(!$page_ok || $_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST"){ ?>
					<p class="info form-error" style="display: block"><?php echo $form_error; ?></p>
				<?php }else{ ?>
					<p class="info form-error"></p>
				<?php } ?>

				<label class="sr-only" for="inSenha">Nova senha</label>
				<input type="password" id="inSenha" name="senha" class="form-control bom-senso" placeholder="Nova senha" maxlength="12" >

				<label class="sr-only" for="inSenhaConf">Confirmar nova senha</label>
				<input type="password" id="inSenhaConf" name="senhaconf" class="form-control bom-senso" placeholder="Confirme sua nova senha" maxlength="12" required>

				<?php if(!$page_ok || $_SERVER["REQUEST_METHOD"] == "GET" || $_SERVER["REQUEST_METHOD"] == "POST"){ ?>
					<input type="hidden" name="key" value="<?php echo $email_hash; ?>"/>
				<?php }?>

				<label class="sr-only" for="inCodigo">Código</label>
				<input type="text" id="inCodigo" name="codigo" class="form-control bom-senso" placeholder="Código" maxlength="6" required autofocus>

				<!--INSERIR UM CAPTCHA AQUI-->
				<div class="g-recaptcha" data-sitekey="<?php echo $_ENV['CAPTCHA_SITEKEY'] ?>"></div>

				<button type="submit" class="btn btn-lg btn-primary btn-block">Redefinir</button>

			</form>
			<!--FIM FORMULÁRIO DE LOGIN-->

		</div>

		<?php include "assets/snippets/footer.php"; ?>

		<!--JavaScript-->
		<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="js/ie10-viewport-bug-workaround.js" type="text/javascript"></script>
		<script src="js/form_validation.js" type="text/javascript"></script>

	</body>

</html>
