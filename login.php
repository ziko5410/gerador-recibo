<?php
	$form_error = "";

	if( (require_once "assets/scripts/connect.php") == true && (require_once "assets/scripts/check_session.php") == true ){
			
		$BD_Connection->close();

		if($_SERVER["REQUEST_METHOD"] == "GET"){

			if( isset($_GET["erro"]) && !empty($_GET["erro"]) ){

				$cod = $_GET["erro"];

				if($cod == 1){
					$form_error="Ocorreu um erro no servidor. Tente novamente.";
				}
				if($cod == 2){
					$form_error="Email ou senha incorreto.";
				}
				if($cod == 3){
					$form_error="Digite um email válido";
				}
				if($cod == 4){
					$form_error="Digite uma senha válida";
				}
			}

			if( isset($_GET["i"]) && !empty($_GET["i"]) ){

				$info = $_GET["i"];

				if($info == 1){
					$form_error="Sua senha foi alterada com sucesso!";
				}
			}
		}

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

		<title>Entrar - Recibeira</title>

		<link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/css/bootstrap.min.css">
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
			<form class="form-signin" method="POST" action="auth_user.php" onsubmit="return validateForm('login-form')" id="login-form">

				<h2 class="form-signin-heading">Acessar minha conta</h2>

				<?php if($_SERVER["REQUEST_METHOD"] == "GET"){ ?>
					<p class="info form-error" style="display: block"><?php echo $form_error; ?></p>
				<?php }else{ ?>
					<p class="info form-error"></p>
				<?php } ?>

				<label class="sr-only" for="inEmail">Email</label>
				<input type="email" id="inEmail" name="email" class="form-control bom-senso" placeholder="Email" maxlength="50"  autofocus>

				<label class="sr-only" for="inSenha">Senha</label>
				<input type="password" id="inSenha" name="senha" class="form-control bom-senso" placeholder="Senha" maxlength="12" >

				<div>

					<a href="recuperar.php">Esqueci minha senha</a>

					<div class="checkbox">
						<label>
							<input type="checkbox" name="lembrar" value="lembrar">Lembrar-se</input>
						</label>
					</div>

									
				</div>

				<!--INSERIR UM CAPTCHA AQUI-->

				<button type="submit" class="btn btn-lg btn-primary btn-block">Entrar</button>

				<a href="registrar.php">Ainda não possui uma conta?</a>

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