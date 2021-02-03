<?php
  require_once "setup.php";

	//mensagem de erro exibida ao usuário
	$form_error = "";

	if( (require_once "assets/scripts/connect.php") == true && (require_once "assets/scripts/check_session.php") == true){

		if($_SERVER["REQUEST_METHOD"] == "POST"){

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

						$nome = $_POST["nome"];
						$email = $_POST["email"];
						$senha = $_POST["senha"];
						$senhaconf = $_POST["senhaconf"];

						//Validação do nome
						if(empty($nome)){
							$form_error = "Digite um nome, queremos te conhecer.";
						}
						else if(strlen($nome) > 50){
							$form_error = "Seu nome está muito longo, tente reduzir um pouco (máximo 50 caracteres)";
						}
						else if($utils->isNumeric($nome)){
							$form_error = "Seu nome não deveria possuir números...";
						}
						else{
							$nome = $utils->checkInput($nome);
						}

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
						}

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

					}

					//Envia o formulário se tudo estiver OK//

					if(mysqli_connect_errno()){
						$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
					}
					else{

						//Realiza uma busca no banoc pelo email digitado para checar se já exite um cadastro
						$sql = $BD_Connection->prepare("SELECT * FROM $BD_Users_table WHERE $BD_Email_field = ?");

						if($sql){
							$sql->bind_param('s', $email);

							if($sql->execute()){

								$resultado = $sql->get_result();
								$sql = null;

								$dados = $resultado->fetch_assoc();

								//Checa se o email digitado já existe
								if( ($resultado->num_rows <= 0) || (!isset($dados[$BD_Email_field])) ){
									mysqli_free_result($resultado);

									//Salva no banco...
									$sql = $BD_Connection->prepare("INSERT INTO $BD_Users_table ($BD_Name_field, $BD_Email_field, $BD_Password_field, $BD_Email_hash_field, $BD_Email_verified_field, $BD_Acc_code_field, $BD_User_id_field, $BD_User_token_field) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

									if($sql){
										$email_verified = 0;
										$pwd_hash = password_hash($senha, PASSWORD_DEFAULT);
										$code = rand(100000, 999999);
										$email_hash = sha1(md5($email));
										$uid = mt_rand();
										$token = hash("sha256", mt_rand());

										$sql->bind_param('ssssiiis', $nome, $email, $pwd_hash, $email_hash, $email_verified, $code, $uid, $token);

										if($sql->execute()){
											$sql = null;
											$BD_Connection->close();

											//Redireciona para a página de confirmação do email
											header("Location: confirmar_email.php?e=$email_hash");
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
									$form_error="O email $email já está cadastrado. <a href='recuperar.php'>Esqueceu sua senha?</a>";
								}//if( ($resultado->num_rows <= 0) || (!isset($dados[$BD_Email_field])) )
							}
							else{
								$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
							}
						}
						else{
							$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
						}//if($sql)

					}

				}//(!isset($captcha_data) || empty($captcha_data))

			}
			else{
				$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
			}//if( (require_once "assets/scripts/utils.class.php") == true )

		}//if($_SERVER["REQUEST_METHOD"] == "POST")

		$BD_Connection->close();

	}//if( (require_once "assets/scripts/connect.php") == true )

?>

<!DOCTYPE html>
<html lang="pt-br">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
       	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<meta name="description" content="Crie uma conta grátis e comece a poupar tempo">
    	<meta name="author" content="Allex Rodrigues">
    	<link rel="icon" href="assets/img/favicon.ico">

		<title>Cadastrar-se - Recibeira</title>

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
			<form class="form-signin" method="POST" action="registrar.php" onsubmit="return validateForm('register-form')" id="register-form">

				<h2 class="form-signin-heading">Criar uma conta</h2>

				<p class="info">Todos os campos são obrigatórios!</p>

				<?php if($_SERVER["REQUEST_METHOD"] == "POST"){ ?>
					<p class="info form-error" style="display: block"><?php echo $form_error; ?></p>
				<?php }else{ ?>
					<p class="info form-error"></p>
				<?php } ?>

				<label class="sr-only" for="inNome">Nome</label>
				<input type="text" id="inNome" name="nome" class="form-control bom-senso" data-toggle="tooltip" data-placement="right" title="É por esse nome que vamos te identificar." placeholder="Como se chama?" maxlength="50" required autofocus>

				<label class="sr-only" for="inEmail">Email</label>
				<input type="email" id="inEmail" name="email" class="form-control bom-senso" data-toggle="tooltip" data-placement="right" title="É com esse email que você poderá acessar sua conta." placeholder="Diga-nos seu email" maxlength="50" required>

				<label class="sr-only" for="inSenha">Senha</label>
				<input type="password" id="inSenha" name="senha" class="form-control bom-senso" data-toggle="tooltip" data-placement="right" title="Essa será sua senha de acesso. ela deve ter de 8-12 caracteres. Use letras, números e símbolos para uma senha mais segura." placeholder="Crie uma senha" maxlength="12" required>

				<label class="sr-only" for="inSenhaConf">Confirmar senha</label>
				<input type="password" id="inSenhaConf" name="senhaconf" class="form-control bom-senso" placeholder="Confirme sua senha" data-toggle="tooltip" data-placement="right" title="Confirme a senha que você criou ali em cima." maxlength="12" required>

				<!--INSERIR UM CAPTCHA AQUI-->
				<div class="g-recaptcha" data-sitekey="<?php echo $_ENV['CAPTCHA_SITEKEY'] ?>"></div>

				<button type="submit" class="btn btn-lg btn-primary btn-block">Cadastrar-se</button>

				<a href="login.php">Já possui uma conta?</a>

			</form>
			<!--FIM FORMULÁRIO DE LOGIN-->

		</div>

		<?php include "assets/snippets/footer.php"; ?>

		<!--JavaScript-->
		<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="http://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/bootstrap.min.js" type="text/javascript"></script>
		<script src="js/ie10-viewport-bug-workaround.js" type="text/javascript"></script>
		<script src="js/form_validation.js" type="text/javascript"></script>

		<script type="text/javascript">

		//Habilita as tooltips
			$(function() {
				$('[data-toggle="tooltip"]').tooltip();
			});

		</script>

	</body>

</html>
