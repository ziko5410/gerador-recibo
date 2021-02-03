<?php
	if( (require_once "assets/scripts/connect.php") == true && (require_once "assets/scripts/check_session.php") == true  ){
		$BD_Connection->close();
	}
?>

<!DOCTYPE html>
<html lang="pt-br">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
       	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	<meta name="description" content="Aproveite seu tempo e deixe a parte chata conosco. Aqui você não perde tempo digitando seus recibos de aluguel, nós fazemos isso por você! Tenha seus recibos em mãos em poucos segundos, garanta os recibos de vários meses facilmente, mantenha vários inquilinos supridos de uma vez.">
    	<meta name="author" content="Allex Rodrigues">
    	<link rel="icon" href="assets/img/favicon.ico">

		<title>Recibeira - Seus recibos de aluguel sem complicação</title>

		<link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/nav.css">
		<link rel="stylesheet" href="css/jumbotron.css">
		<link rel="stylesheet" href="css/content.css">
		<link rel="stylesheet" href="css/login-modal.css">
		<link rel="stylesheet" href="css/footer.css">
		<link rel="stylesheet" href="css/geral.css">

	</head>

	<body>

		<!--BARRA DE NAVEGAÇÃO-->
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container">
				<div class="navbar-header">

					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Alternar navegação</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>

					<a class="navbar-brand" href="/"><img src="assets/img/brand-icon.png" alt="Recibeira logo"/><span>Recibeira</span></a>
				</div>

				<!--Navegação-->
				<div id="navbar" class="navbar-collapse collapse" aria-expanded="false" style="height: 1px;">

					<ul class="nav navbar-nav navbar-right">

						<li><a href="registrar.php" class="nav-link">Sou novo aqui</a></li>
						<!--Botão entrar abre um modal para o login-->
						<li><button type="button" id="btn-login" class="btn btn-default navbar-btn" data-toggle="modal" data-target="#login-modal-sm">Entrar</button></li>

					</ul>

				</div>
			</div><!--div=container-->
		</nav>
		<!--FIM BARRA DE NAVEGAÇÃO-->

		<!--MODAL DE LOGIN-->
		<div class="modal fade" id="login-modal-sm" tab-index="-1" role="dialog" aria-labelledby="login-modal-label">

			<div class="modal-dialog modal-sm" role="document">
				<div class="modal-content">

					<!--Cabeçalho do modal-->
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="login-modal-label">Acessar minha conta</h4>
					</div>

					<!--Corpo do modal-->
					<div class="modal-body">
						<form class="form-signin" method="POST" action="auth_user.php" onsubmit="return validateForm('login-form')" id="login-form">

							<p class="info form-error"></p>

							<label class="sr-only" for="inEmail">Email</label>
							<input type="email" id="inEmail" name="email" class="form-control bom-senso" placeholder="Email" maxlength="50" required autofocus>

							<label class="sr-only" for="inSenha">Senha</label>
							<input type="password" id="inSenha" name="senha" class="form-control bom-senso" placeholder="Senha" maxlength="12" required>

							<div>

								<a href="recuperar.php">Esqueci minha senha</a>

								<div class="checkbox">
									<label>
										<input type="checkbox" name="lembrar" value="lembrar">Lembrar-se</input>
									</label>
								</div>


							</div>

							<button type="submit" class="btn btn-lg btn-primary btn-block">Entrar</button>

						</form>
					</div>

					<div class="modal-footer">
						<!--Atributo 'form' não funciona no IE-->
						<button type="button" class="btn btn-default" data-dismiss="modal">Agora não</button>
					</div>

				</div>
			</div>

		</div>
		<!--FIM MODAL LOGIN-->

		<!--MEIUCA-->
		<section class="container marketing">

			<div id="jumbotron-wrapper">
				<div class="jumbotron" id="jumbotron-cover">
					<h1>Bem-vindo!</h1>
					<p class="lead">Aproveite seu tempo e deixe a parte chata conosco. Aqui você não perde tempo digitando seus recibos de aluguel, nós fazemos isso por você!</p>
					<p><a class="btn btn-lg btn-success" href="registrar.php" role="button">Começar agora</a></p>
				</div>
			</div>

			<div class="row">

			    <div class="col-lg-4">
			    	<div>
					    <img src="assets/img/rapidez.png"/>
					    <h2>Rapidez</h2>
					    <p>Tenha seus recibos em mãos em poucos segundos!</p>
			        </div>
			    </div>

		        <div class="col-lg-4">
		        	<div>
			        	<img src="assets/img/praticidade.png"/>
				        <h2>Praticidade</h2>
			        	<p>Garanta os recibos de vários meses facilmente!</p>
		        	</div>
			    </div>

		    	<div class="col-lg-4">
		    		<div>
			        	<img src="assets/img/agilidade.png"/>
			        	<h2>Agilidade</h2>
			        	<p>Mantenha vários inquilinos supridos de uma vez!</p>
		        	</div>
		    	</div>

		    </div>

		</section>
		<!--FIM MEIUCA-->

		<?php include "assets/snippets/footer.php"; ?>

		<!--JavaScript-->
		<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="http://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/bootstrap.min.js" type="text/javascript"></script>
		<script src="js/ie10-viewport-bug-workaround.js" type="text/javascript"></script>
		<script src="js/form_validation.js" type="text/javascript"></script>

	</body>

</html>
