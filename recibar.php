<?php
/*
- A página home manda um GET com o id do perfil (profile_id) que será também o indice + 1 do cartão em um vetor.

- A página recibar recebe o GET e checa se o usuário (usando $_SESSION["id"] pra identificar o usuário) possui perfis ou não.

- Se o usuário não possuir perfis, nada será mostrado nos inputs, mas se já existir um perfil com aquele profile_id os inputs serão populados com as informações do banco de dados.

- Ao enviar o POST da página recibar para atualizar o perfil, deve-se checar se aquele perfil já existe para decidir entre insert ou update.
*/
	$form_error = "";
	//Usada no controle de quando ou não completar o formulário com os dados do banco
	$completa_form = false;

	if( ((require_once "assets/scripts/connect.php") == true)  && ((require_once "assets/scripts/utils.class.php") == true)  && ((require_once "assets/scripts/check_session.php") == true) ){

		if($_SERVER["REQUEST_METHOD"] == "GET"){
			
			if( isset($_GET["card"]) && !empty($_GET["card"])){

				$profile_id = $_GET["card"];

				if(!mysqli_connect_errno()){

					$sql = $BD_Connection->prepare("SELECT * FROM $BD_Profiles_table WHERE $BD_Profile_id_field = ? AND $BD_Profile_user_id_field = ?");

					if($sql){
						$user_id = $_SESSION["id"];
						$sql->bind_param('ii', $profile_id, $user_id);

						if($sql->execute()){

							$resultado = $sql->get_result();
							$sql = null;

							if($resultado->num_rows > 0){
								//Permite que os inputs sejam preenchidos
								$completa_form = true;
								//Os elementos do resultado são mostrados direto nos inputs
								$obj = $resultado->fetch_assoc();
								mysqli_free_result($resultado);
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
						$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
					}//if($sql)

				}
				else{
					$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
				}//if(!mysqli_connect_errno())

			}//if(!empty($_GET["card"]))

		}//if($_SERVER["REQUEST_METHOD"] == "GET")

		if($_SERVER["REQUEST_METHOD"] == "POST"){

			$utils = new Utils();

			$perfil_nome = $_POST["nomeperfil"];

			$locador = $_POST["locador"];//
			$locatario = $_POST["locatario"];//
			$contrato_inicio = $_POST["continicio"];//data
			$contrato_termino = $_POST["contterm"];//data
			$valor_aluguel = $_POST["valor"];//

			$rua = $_POST["rua"];//
			$numero = $_POST["numero"];//
			$bairro = $_POST["bairro"];//
			$cidade = $_POST["cidade"];//
			$casa_fundo; //fundos if set (checkbox)//

			$referente_from = $_POST["referentea"];//data
			$referente_to = $_POST["referenteb"];//data

			//Diferentes
			//Se a checkbox estiver marcada, o valor de $data_recibo é desconsiderado
			$data_recibo = $_POST["data"];//data
			$data_hoje; //hoje if set (checkbox). Essa vai ser salva

			if(!mysqli_connect_errno()){

				//Validação do nome do perfil
				if(empty($perfil_nome)){
					$form_error = "Digite um nome para o perfil.";
				}
				else if(strlen($perfil_nome) > 50){
					$form_error = "Seu nome está muito longo, tente reduzir um pouco (máximo 50 caracteres)";
				}
				else{
					$perfil_nome = $utils->checkInput($perfil_nome);
				}

				//Validação do nome do locador
				if(empty($locador)){
					$form_error = "Digite o nome do locador.";
				}
				else if(strlen($locador) > 50){
					$form_error = "O nome do locador está muito longo, tente reduzir um pouco (máximo 50 caracteres).";
				}
				else if($utils->isNumeric($locador)){
					$form_error = "O nome do locador não deveria possuir números...";
				}
				else{
					$locador = $utils->checkInput($locador);
				}

				//Validação do nome do locatario
				if(empty($locatario)){
					$form_error = "Digite o nome do locatário.";
				}
				else if(strlen($locatario) > 50){
					$form_error = "O nome do locatário está muito longo, tente reduzir um pouco (máximo 50 caracteres).";
				}
				else if($utils->isNumeric($locatario)){
					$form_error = "O nome do locatário não deveria possuir números...";
				}
				else{
					$locatario = $utils->checkInput($locatario);
				}

				//Validação da data do contrato
				if(empty($contrato_inicio)){

					$form_error = "Data de início do contrato inválida. Verifique os dias e o mês.";
						
				}

				if(empty($contrato_termino)){

					$form_error = "Data de término do contrato inválida. Verifique os dias e o mês.";
						
				}

				//Validação do valor do aluguel
				if(empty($valor_aluguel)){
					$form_error = "Digite o valor do aluguel.";
				}
				else if(strlen($valor_aluguel) > 10){
					$form_error = "Valor do aluguel muito longo (máximo 13 caracteres).";
				}
				else if($valor_aluguel == "0"){
					$form_error = "O valor do aluguel deve ter o valor mínimo de R$ 1,00.";
				}
				else if(!$utils->isCurrency($valor_aluguel)){
					$form_error = "O valor do aluguel só deve conter números e ',' ou '.'.";
				}
				else{
					$valor_aluguel = str_replace(",", ".", $utils->checkInput($valor_aluguel));
				}

				//Validação do nome da rua
				if(empty($rua)){
					$form_error = "Digite o nome da rua.";
				}
				else if(strlen($rua) > 40){
					$form_error = "Nome da rua muito longo, tente um mais curto (máximo 40 caracteres).";
				}
				else{
					$rua = $utils->checkInput($rua);
				}

				//Validação do número da casa
				if(empty($numero)){
					$form_error = "Digite o numero da casa.";
				}

				//Validação do bairro
				if(empty($bairro)){
					$form_error = "Digite o nome do bairro da residência.";
				}
				else if(strlen($bairro) > 40){
					$form_error = "Nome do bairro muito longo, tente um mais curto (máximo 40 caracteres).";
				}
				else{
					$bairro = $utils->checkInput($bairro);
				}

				//Validação da cidade
				if(empty($cidade)){
					$form_error = "Digite o nome da cidade da residência.";
				}
				else if(strlen($cidade) > 30){
					$form_error = "Nome da cidade muito longo, tente um mais curto (máximo 30 caracteres).";
				}
				else{
					$cidade = $utils->checkInput($cidade);
				}

				//Validação da checkbox casa de fundo
				if(!empty($_POST["casafundo"]) && isset($_POST["casafundo"])){
					$casa_fundo = $_POST["casafundo"];

					if(strcmp($casa_fundo, "fundos") == 0){
						$casa_fundo = 1;
					}
					else{
						$form_error = "Valor inválido.";
					}

				}
				else{
					$casa_fundo = 0;
				}

				//Validação da data de início do referente
				if(empty($referente_from)){
					$form_error = "Digite a data de início do referente.";
				}

				//Validação da data de término do referente
				if(empty($referente_to)){
					$form_error = "Digite a data de término do referente.";
				}

				//Validação da data do recibo
				if(isset($_POST["datahoje"]) && !empty($_POST["datahoje"])){
					$data_recibo = $utils->checkInput($_POST["datahoje"]);
				}
				else if(empty($data_recibo) && empty($data_hoje)){
					$form_error = "Digite a data do recibo ou marque a caixa \"Usar data de hoje\".";
				}

				//Checa se ocorreu algum erro
				if(empty($form_error)){
					//Salva os dados do recibo

					$sql = $BD_Connection->prepare("INSERT INTO $BD_Profiles_table 
						($BD_Profile_user_id_field, 
						$BD_Profile_name_field, 
						$BD_Locador_field, 
						$BD_Locatario_field, 
						$BD_Inicio_contrato_field, 
						$BD_Termino_contrato_field, 
						$BD_Aluguel_valor_field, 
						$BD_Rua_field, 
						$BD_Numero_casa_field, 
						$BD_Bairro_field, 
						$BD_Cidade_field, 
						$BD_Casa_fundo_field, 
						$BD_Referente_from_field, 
						$BD_Referente_to_field, 
						$BD_Data_recibo_field)
						VALUES 
						(? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,?)");

					if($sql){
						
						$user_id = $_SESSION["id"];
						$sql->bind_param('isssssdsississs', $user_id, $perfil_nome, $locador, $locatario, $contrato_inicio, $contrato_termino, $valor_aluguel, $rua, $numero, $bairro, $cidade, $casa_fundo, $referente_from, $referente_to, $data_recibo);

						if($sql->execute()){
							
							$sql = $BD_Connection->prepare("SELECT max($BD_Profile_id_field) FROM $BD_Profiles_table WHERE $BD_Profile_user_id_field = ?");

							if($sql){
								$sql->bind_param('i',  $user_id);

								if($sql->execute()){

									$resultado = $sql->get_result();
									$sql = null;

									if($resultado->num_rows > 0){
										//Permite que os inputs sejam preenchidos
										$completa_form = true;
										//Os elementos do resultado são mostrados direto nos inputs
										$obj = $resultado->fetch_assoc();
										mysqli_free_result($resultado);

										header("Location: recibar.php?card=".$obj["max($BD_Profile_id_field)"]);
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
								$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
							}//if($sql)

						}
						else{
							$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
						}//if($sql->execute())

					}
					else{
						$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
					}//if($sql)

				}
				else{
					$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
				}//if(empty($form_error))

			}
			else{
				$form_error="Desculpe, ocorreu um erro no servidor. Tente novamente mais tarde.";
			}//if(!mysqli_connect_errno())

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

		<title>Criar perfil - Recibeira</title>

		<link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/nav.css">
		<link rel="stylesheet" href="css/footer.css">
		<link rel="stylesheet" href="css/form.css">
		<link rel="stylesheet" href="css/geral.css">

		<style>

			a.navbar-btn{
				margin-right: 10px;
				margin-left: 10px;
				float: left;
			}

			.nome-perfil{
				background: transparent;
				width: 100%;
				border: none;
				font-size: 26px;
				border-bottom: 3px solid #337ab7;
				margin-bottom: 10px;
				transition: 0.5s;
			}

			.nome-perfil:focus{
				outline: none;
				padding: 6px 12px;
				background-color: #fff;
				border: 1px solid #66afe9;
    			border-radius: 4px;
    			box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102,175,233,.6);
			}

			@media only screen and (max-width:768px){
				.navbar-header{
					display: none;
				}

				.navbar-right .navbar-brand span{
					position: absolute;
					margin-left: 40px;
					line-height: 38px;
					text-align: center;
				}

				.navbar-nav{
					margin: 0;
				}

				.navbar-brand{
					padding-left: 0px;
				}

				a.navbar-btn{
					float: right;
				}
			}

		</style>
	</head>

	<body>

		<!--BARRA DE NAVEGAÇÃO-->
		<nav class="navbar navbar-default navbar-static-top">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand" href="index.php"><img src="assets/img/brand-icon.png" alt="Recibeira logo"/><span>Recibeira</span></a>
				</div>

				<div class="nav navbar-nav navbar-right">
					<a href="logout.php" class="btn btn-default navbar-btn">Sair</a>

					<?php
						//Obtém o nome de usuário e a foto do gravatar
						$gravatar_email = md5( strtolower( trim( $_SESSION["email"] ) ) );
						$user = isset($_SESSION["user"]) ? $_SESSION["user"] : "Usuário";

						echo "<a class='navbar-brand' href='home.php'><span> $user </span><img src='http://www.gravatar.com/avatar/$gravatar_email?s=36&d=mm' data-toggle='tooltip' data-placement='left' title='Sua imagem de usuário é obtida através do seu email, se estiver cadastrado no site Gravatar.com' class='profile-picture' alt='Foto do usuário'/></a>";
					?>
				
				</div>
			</div>
		</nav>
		<!--FIM BARRA DE NAVEGAÇÃO-->


		<div class="container">

			<form method="POST" action="recibar.php" onsubmit="return validateForm('perfil-form')" id="perfil-form">

			<?php

				//Checa se deve mostrar o formulário preenchido ou não
				if($_SERVER["REQUEST_METHOD"] == "GET" && $completa_form){

					$utils = new Utils();

					//Estado da checkbox casa de fundo
					$casa_fundo_state = $obj[$BD_Casa_fundo_field] == 1 ? "checked" : "unchecked";

					echo "
					<label for='inNomePerfil' class='sr-only'>Locador<span class='info'>*</span></label>
					<input type='text' id='inNomePerfil' name='nomeperfil' class='nome-perfil' data-toggle='tooltip' data-placement='bottom' title='Nome do perfil.' placeholder='Nome do perfil' maxlength='30' value='$obj[$BD_Profile_name_field]' required autofocus>

					<p class='info'>Campos marcados com * são obrigatórios.</p>"; 
					
					if($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET'){ 
						echo "<p class='info form-error' style='display: block'>$form_error</p>";
					}
					else{ 
						echo "<p class='info form-error'></p>";
					} 

					echo 
					"<fieldset class='col-md-4'>
						
						<legend>Informações gerais</legend>

						<label for='inLocador'>Locador<span class='info'>*</span></label>
						<input type='text' id='inLocador' name='locador' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Nome do locador (o dono da casa).' placeholder='João' maxlength='50' value='$obj[$BD_Locador_field]' required autofocus>

						<label for='inLocatario'>Locatário<span class='info'>*</span></label>
						<input type='text' id='inLocatario' name='locatario' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Nome do locatário (quem está alugando a casa).' placeholder='José' maxlength='50' value='$obj[$BD_Locatario_field]' required>

						<label for='inContInicio'>Início do contrato</label>
						<input type='date' id='inContInicio' name='continicio' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Data de início do contrato com o locatário. Se a data de término estiver em branco, esse campo não será considerado.' max='2038-01-01' min='1901-01-01' value='$obj[$BD_Inicio_contrato_field]'>

						<label for='inContTerm'>Término do contrato</label>
						<input type='date' id='inContTerm' name='contterm' class='form-control bom-senso' data-placement='bottom' data-toggle='tooltip' title='Data de término do contrato com o locatário. Se a data de início estiver em branco, esse campo não será considerado.' max='2038-01-01' min='1901-01-01' value='$obj[$BD_Termino_contrato_field]'>

						<label for='inValor'>Valor do aluguel (R$)<span class='info'>*</span></label>
						<input type='text' id='inValor' name='valor' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Digite apenas o valor, sem 'R$'' placeholder='1000,00' maxlength='13' value='$obj[$BD_Aluguel_valor_field]' required>

					</fieldset>

					<fieldset class='col-md-4'>
						
						<legend>Endereço do imóvel alugado</legend>

						<label for='inRua'>Rua<span class='info'>*</span></label>
						<input type='text' id='inRua' name='rua' class='form-control bom-senso' placeholder='R Recibeira' maxlength='40' value='$obj[$BD_Rua_field]' required>

						<label for='inNumero'>Número<span class='info'>*</span></label>
						<input type='number' id='inNumero' name='numero' class='form-control bom-senso' placeholder='123' max='99999' value='$obj[$BD_Numero_casa_field]' required>

						<label for='inBairro'>Bairro<span class='info'>*</span></label>
						<input type='text' id='inBairro' name='bairro' class='form-control bom-senso' placeholder='Jd. Recibeira' maxlength='40' value='$obj[$BD_Bairro_field]' required>

						<label for='inCidade'>Cidade<span class='info'>*</span></label>
						<input type='text' id='inCidade' name='cidade' class='form-control bom-senso' placeholder='Recibeira' maxlength='30' value='$obj[$BD_Cidade_field]' required>

						<div class='checkbox'>
							<label>
								<input type='checkbox' name='casafundo' value='fundos' $casa_fundo_state><strong>Casa de fundo</strong></input>
							</label>
						</div>

					</fieldset>

					<fieldset class='col-md-4'>

						<legend>Informações finais</legend>

						<label for='inReferenteA'>Referente de<span class='info'>*</span></label>

						<div data-toggle='tooltip' data-placement='bottom' title='Intervalo de meses aos quais o recibo de refere, normalmente de um mês.'>
							<input type='date' id='inReferenteA' name='referentea' class='form-control bom-senso data' max='2038-01-01' min='1901-01-01' value='$obj[$BD_Referente_from_field]' required>
							
							<label for='inReferente'>a</label>
							<input type='date' id='inReferente' name='referenteb' class='form-control bom-senso data' max='2038-01-01' min='1901-01-01' value='$obj[$BD_Referente_to_field]' required>
						</div>

						<label for='inData'>Data do recibo<span class='info'>*</span></label>
						<input type='date' id='inData' name='data' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Digite a data de quando o recibo foi (ou deveria ter) sido feito ou marque a caixa abaixo.' value='$obj[$BD_Data_recibo_field]' max='2038-01-01' min='1901-01-01' >

						<div class='checkbox'>
							<label>
								<input type='checkbox' onclick='putTodayDate(this)' id='cbDataHoje' name='datahoje' value='hoje' data-toggle='tooltip' data-placement='right' title='Marque essa caixa ou digite uma data acima'><strong>Usar data de hoje<strong></input>
							</label>
						</div>

					</fieldset>";
				}
				else{
					$usuario = "";
					$default_name = "";
					
					if(!mysqli_connect_errno()){

						//Busca o nome do usuário para colocar no título padrão do cartão
						$sql = $BD_Connection->prepare("SELECT $BD_Name_field FROM $BD_Users_table WHERE $BD_Id_field = ?");

						if($sql){
							$sql->bind_param('i', $_SESSION["id"]);

							if($sql->execute()){
								$resultado = $sql->get_result();
								$sql = null;

								if($resultado->num_rows > 0){
									$obbj = $resultado->fetch_assoc();

									mysqli_free_result($resultado);

									$usuario = $obbj[$BD_Name_field];
									$default_name = "Perfil de $usuario";
								}
								else{
									$default_name = "Meu novo perfil";
								}
							}
							else{
								$default_name = "Meu novo perfil";
							}
						}
						else{
							$default_name = "Meu novo perfil";
						}
					}else{
						$default_name = "Meu novo perfil";
					}//if(!mysqli_connect_errno())

					echo "
					<label for='inNomePerfil' class='sr-only'>Locador<span class='info'>*</span></label>
					<input type='text' id='inNomePerfil' name='nomeperfil' class='nome-perfil' data-toggle='tooltip' data-placement='bottom' title='Nome do perfil.' placeholder='Nome do perfil' maxlength='30' value='$default_name' required autofocus>

					<p class='info'>Campos marcados com * são obrigatórios.</p>"; 
					
					if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
						echo "<p class='info form-error' style='display: block'>$form_error</p>";
					}
					else{ 
						echo "<p class='info form-error'></p>";
					} 

					echo 
					"<fieldset class='col-md-4'>
						
						<legend>Informações gerais</legend>

						<label for='inLocador'>Locador<span class='info'>*</span></label>
						<input type='text' id='inLocador' name='locador' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Nome do locador (o dono da casa).' placeholder='João' maxlength='50' required autofocus>

						<label for='inLocatario'>Locatário<span class='info'>*</span></label>
						<input type='text' id='inLocatario' name='locatario' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Nome do locatário (quem está alugando a casa).' placeholder='José' maxlength='50' required>

						<label for='inContInicio'>Início do contrato</label>
						<input type='date' id='inContInicio' name='continicio' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Data de início do contrato com o locatário. Se a data de término estiver em branco, esse campo não será considerado.' max='2038-01-01' min='1901-01-01'>

						<label for='inContTerm'>Término do contrato</label>
						<input type='date' id='inContTerm' name='contterm' class='form-control bom-senso' data-placement='bottom' data-toggle='tooltip' title='Data de término do contrato com o locatário. Se a data de início estiver em branco, esse campo não será considerado.' max='2038-01-01' min='1901-01-01'>

						<label for='inValor'>Valor do aluguel (R$)<span class='info'>*</span></label>
						<input type='text' id='inValor' name='valor' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Digite apenas o valor, sem 'R$'' placeholder='1000,00' maxlength='13' required>

					</fieldset>

					<fieldset class='col-md-4'>
						
						<legend>Endereço do imóvel alugado</legend>

						<label for='inRua'>Rua<span class='info'>*</span></label>
						<input type='text' id='inRua' name='rua' class='form-control bom-senso' placeholder='R Recibeira' maxlength='40' required>

						<label for='inNumero'>Número<span class='info'>*</span></label>
						<input type='number' id='inNumero' name='numero' class='form-control bom-senso' placeholder='123' max='99999' required>

						<label for='inBairro'>Bairro<span class='info'>*</span></label>
						<input type='text' id='inBairro' name='bairro' class='form-control bom-senso' placeholder='Jd. Recibeira' maxlength='40' required>

						<label for='inCidade'>Cidade<span class='info'>*</span></label>
						<input type='text' id='inCidade' name='cidade' class='form-control bom-senso' placeholder='Recibeira' maxlength='30' required>

						<div class='checkbox'>
							<label>
								<input type='checkbox' name='casafundo' value='fundos' ><strong>Casa de fundo</strong></input>
							</label>
						</div>

					</fieldset>

					<fieldset class='col-md-4'>

						<legend>Informações finais</legend>

						<label for='inReferenteA'>Referente de<span class='info'>*</span></label>

						<div data-toggle='tooltip' data-placement='bottom' title='Intervalo de meses aos quais o recibo de refere, normalmente de um mês.'>
							<input type='date' id='inReferenteA' name='referentea' class='form-control bom-senso data' max='2038-01-01' min='1901-01-01' required>
							
							<label for='inReferente'>a</label>
							<input type='date' id='inReferente' name='referenteb' class='form-control bom-senso data' max='2038-01-01' min='1901-01-01' required>
						</div>

						<label for='inData'>Data do recibo<span class='info'>*</span></label>
						<input type='date' id='inData' name='data' class='form-control bom-senso' data-toggle='tooltip' data-placement='bottom' title='Digite a data de quando o recibo foi (ou deveria ter) sido feito ou marque a caixa abaixo.' max='2038-01-01' min='1901-01-01'>

						<div class='checkbox'>
							<label>
								<input type='checkbox' onclick='putTodayDate(this)' id='cbDataHoje' name='datahoje' value='hoje' data-toggle='tooltip' data-placement='right' title='Marque essa caixa ou digite uma data acima'><strong>Usar data de hoje<strong></input>
							</label>
						</div>

					</fieldset>";
				}

				$BD_Connection->close();

			?>

				<div class="col-md-12 form-footer">
					<button type="submit" class="btn btn-primary">Salvar</button>
					<button type="button" class="btn btn-default" onclick="clearInputs('perfil-form');">Limpar</button>
					<a href="home.php" class="btn btn-success">Voltar para o início</a>
				</div>

			</form>

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

			//Limpa o texto de todos os inputs
			function clearInputs(formID){
				//Formulário da página
				var form = document.forms[formID];
				//Controles dentro do formulário
				var controls = form.elements;

				for(var i = 0; i < controls.length; i++){
					if(controls[i].value != "" && controls[i].id != "inNomePerfil"){
						controls[i].value = "";
					}
				}
			}

			function putTodayDate(input){

				if(input.checked){
					var today = new Date();
					var day = today.getDate();
					var month = today.getMonth() + 1;
					var year = today.getFullYear();

					if(day < 10){
						day = "0" + day;
					}

					if(month < 10){
						month = "0" + month;
					}

					var today = year + "-" + month + "-" + day;

					$("#inData").val(today);
				}
				else{
					$("#inData").val("");
				}
				
			}

		</script>

	</body>

</html>