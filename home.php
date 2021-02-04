<?php
	session_start();

	if($_SERVER["REQUEST_METHOD"] == "GET"){

		if( isset($_GET["e"]) && !empty($_GET["e"]) ){

			if($_GET["e"] == 1){
				$error_msg = "Não foi possível excluir o perfil.";
			}

		}
		else if( isset($_GET["del"]) && !empty($_GET["del"]) && $_GET["del"] == 'a' ){

			if( (require_once "assets/scripts/connect.php") == true){

				$sql = $BD_Connection->prepare("DELETE FROM $BD_Profiles_table WHERE $BD_Profile_user_id_field = ?");

				if($sql){

					$sql->bind_param('i', $_SESSION["id"]);

					if(!$sql->execute()){
						$error_msg = "Não foi possível excluir todos os perfis.";
					}

				}//if($sql)
				else{
					$error_msg = "Não foi possível excluir todos os perfis.";
				}

			}//if( (require_once "assets/scripts/connect.php") == true)
			else{
				$error_msg = "Não foi possível excluir todos os perfis.";
			}

		}//else if( isset($_GET["del"]) && !empty($_GET["del"]) )

	}//if($_SERVER["REQUEST_METHOD"] == "GET")

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

		<title>Minha área - Recibeira</title>

		<link rel="stylesheet" href="//ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/css/bootstrap.min.css">
		<link rel="stylesheet" href="css/nav.css">
		<link rel="stylesheet" href="css/footer.css">
		<link rel="stylesheet" href="css/geral.css">
		<link rel="stylesheet" href="css/cards.css">
		<link rel="stylesheet" href="css/form.css">
		<link rel="stylesheet" href="css/busca.css">

		<style>

			a.navbar-btn{
				margin-right: 10px;
				margin-left: 10px;
				float: left;
			}

			.error{
				color: #ff1a1a;
			}

			.clean{
				border: none;
				background: transparent;
				margin: 0px;
				padding: 0px;
			}

			.clean:focus{
				outline: none;
			}

			#search-form{
				position: relative;
				z-index: 1;
			}

			.search-btn{
				border-left: 1px solid #e1e1e1;
				padding-left: 10px;
				height: 100%;
			}

			.search-txt{
				width: 79vw;
				width: calc(100% - 60px);
				float: left;
			}

			.bottom{
				display: flex;
				position: relative;
				width: 100%;
				margin: 15px auto;
				justify-content: center;
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

				.container > div:last-of-type{
					margin-bottom: 0px;
				}

			}

			@media only screen and (max-width: 530px){
			    .search-txt{
			        width: 70vw;
					width: calc(100% - 60px);
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
					<a href="home.php?del=a" class="btn btn-danger navbar-btn">Apagar tudo</a>
					<a href="recibar.php" class="btn btn-default navbar-btn">Novo perfil</a>

					<?php
						//Obtém o nome de usuário e a foto do gravatar
						$gravatar_email = md5( strtolower( trim($_SESSION["email"]) ) );
						$user = isset($_SESSION["user"]) ? $_SESSION["user"] : "Usuário";

						echo "<a class='navbar-brand' href='home.php'><span> $user </span><img src='//www.gravatar.com/avatar/$gravatar_email?s=36&d=mm' data-toggle='tooltip' data-placement='left' title='Sua imagem de usuário é obtida através do seu email, se estiver cadastrado no site Gravatar.com' class='profile-picture' alt='Foto do usuário'/></a>";
					?>

				</div>
			</div>
		</nav>
		<!--FIM BARRA DE NAVEGAÇÃO-->

		<div id="cards" class="container">

			<?php

				$error_msg = "Não foi possível carregar seus perfis. Recarregue a página ou tente novamente mais tarde.";

				if( (require_once "assets/scripts/connect.php") == true && (require_once "assets/scripts/check_session.php") == true ){

					function printCards($array){
						$BD_Profile_id_field = "profile_id";
						$BD_Profile_name_field = "profile_name";
						$BD_Locador_field = "locador";
						$BD_Locatario_field = "locatario";
						$BD_Aluguel_valor_field = "valor_aluguel";
						$BD_Rua_field = "rua";
						$BD_Numero_casa_field = "numero_casa";
						$BD_Bairro_field = "bairro";
						$BD_Cidade_field = "cidade";
						$BD_Casa_fundo_field = "casa_fundo";
						$BD_Referente_from_field = "referente_from";
						$BD_Referente_to_field = "referente_to";
						$BD_Data_recibo_field = "data_recibo";

						$card = new Cards();
						$size = count($array);

						$start = !empty($_GET["pag"]) ? ($_GET["pag"] - 1) * 8 : 0;

						//Exibe os perfis na tela
						for($i = $start; $i < $start + 8; $i++){

							if($i < $size){

								$obj = $array[$i];
								$utils = new Utils();

								$obj[$BD_Referente_from_field] = $utils->formatDate("Y-m-d", $obj[$BD_Referente_from_field], "d/m/Y");
								$obj[$BD_Referente_to_field] = $utils->formatDate("Y-m-d", $obj[$BD_Referente_to_field], "d/m/Y");

								//Lado direito = par
								if(($i + 1) % 2 == 0){

									echo $card->mkRightCard(
										$obj[$BD_Profile_id_field],
										$obj[$BD_Profile_name_field],
										$obj[$BD_Locador_field],
										$obj[$BD_Locatario_field],
										$obj[$BD_Aluguel_valor_field],
										"$obj[$BD_Rua_field], $obj[$BD_Numero_casa_field] - $obj[$BD_Bairro_field] - $obj[$BD_Cidade_field]", /*Endereço*/
										"$obj[$BD_Referente_from_field] a $obj[$BD_Referente_to_field]"); /*Referente*/

								}
								//Lado esquerdo = impar
								else{
									//Checa se é o último card
									if($i == count($array)-1){
										echo $card->mkLastOddCard(
											$obj[$BD_Profile_id_field],
											$obj[$BD_Profile_name_field],
											$obj[$BD_Locador_field],
											$obj[$BD_Locatario_field],
											$obj[$BD_Aluguel_valor_field],
											"$obj[$BD_Rua_field], $obj[$BD_Numero_casa_field] - $obj[$BD_Bairro_field] - $obj[$BD_Cidade_field]", /*Endereço*/
											"$obj[$BD_Referente_from_field] a $obj[$BD_Referente_to_field]"); /*Referente*/
									}
									else{
										echo $card->mkLeftCard(
											$obj[$BD_Profile_id_field],
											$obj[$BD_Profile_name_field],
											$obj[$BD_Locador_field],
											$obj[$BD_Locatario_field],
											$obj[$BD_Aluguel_valor_field],
											"$obj[$BD_Rua_field], $obj[$BD_Numero_casa_field] - $obj[$BD_Bairro_field] - $obj[$BD_Cidade_field]", /*Endereço*/
											"$obj[$BD_Referente_from_field] a $obj[$BD_Referente_to_field]"); /*Referente*/
									}

								}

							}//if($i < $size)
							else{
								break;
							}

						}//for($i = 0; $i < $size; $i++){

						if($size > 8){

							//Lista com as paginas
							$paginacao= "<ul class='pagination pagination-md bottom'>";
							//Quantidade total de páginas (qtd de prefis / numero de itens por pagina)
							$quantidade = ceil($size / 8);
							$pag = $_GET["pag"];
							//página inicial (que o usuario está vendo) do loop
							$start = !empty($pag) ? $pag : 1;

							//armazena a pagina que o usuario está
							$pagina_atual = $start;

							//limite de paginas mostradas na barra de paginacao
							$limite = 5;

							//Detecta se o usuario está num mobile ou desktop
							$useragent = $_SERVER['HTTP_USER_AGENT'];

							if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))){
								$limite = 5;
							}
							else{
								$limite = 15;
							}


							//Botões pag anterior e primeira pag
							$paginacao .= "<li><a href='home.php?pag=1'> << </a></li>";
							$paginacao .= "<li><a href='home.php?pag=".($pagina_atual == 1 ? $pagina_atual : $pagina_atual - 1)."'> < </a></li>";


							//Monta a barra de paginacao
							for($i = 1; $i <= $limite; $i++){
								if($start <= $quantidade){
									if(isset($pag) && $start == $pag){
										//marca a pagina que o usuario esta como ativa
										$paginacao .= "<li class='active'><a href='home.php?pag=$start'>$start</a></li>";
										$pagina_atual = $start;
									}
									else{
										$paginacao .= "<li><a href='home.php?pag=$start'>$start</a></li>";
									}

									$start++;
								}
							}

							//Botões ultima pag e prox pag
							$paginacao .= "<li><a href='home.php?pag=".($pagina_atual == $quantidade ? $pagina_atual : $pagina_atual + 1)."'> > </a></li>";
							$paginacao .= "<li><a href='home.php?pag=".$quantidade."'> >> </a></li>";
							$paginacao .= "</ul>";

							echo $paginacao;

						}
					}


					if(!mysqli_connect_errno()){

						if($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET["term"])){

							$error_msg = "Nenhum resultado para a busca.";

							$busca = '%'.$_GET["term"].'%';

							echo "
								<form method='GET' action='home.php' id='search-form'>
									<div class='form-control bom-senso'>
										<label for='inTermo' class='sr-only'>Termo da busca(nome do perfil, nome do locatário ou do locador)</label>
										<input type='text' id='inTermo' class='clean search-txt' name='term' value='".str_replace('%', "", $busca)."' placeholder='Perfil, locatário ou locador'/>
										<button type='submit' class='clean pull-right search-btn' >Buscar</button>
									</div>
									<button type='button' class='btn btn-default btn-block' onclick='voltar();'>Ver todos</button>
								</form>";

							if( (require_once "assets/scripts/cards.class.php") == true && (require_once "assets/scripts/utils.class.php") == true ){

								$sql = $BD_Connection->prepare("SELECT * FROM $BD_Profiles_table WHERE $BD_Profile_name_field LIKE ? OR $BD_Locador_field LIKE ? OR $BD_Locatario_field LIKE ?;");
								//SELECT * FROM user_profiles WHERE profile_name LIKE '%jonas%' OR locatario LIKE '%allex%' OR locador LIKE '%allex%';
								if($sql){

									$sql->bind_param('sss', $busca, $busca, $busca);

									if($sql->execute()){

										$resultado = $sql->get_result();
										$sql = null;

										if($resultado->num_rows > 0){

											//Array com todas as linhas do result set da query
											$set = array();
											while($obj = $resultado->fetch_assoc()){
												array_push($set, $obj);
											}

											mysqli_free_result($resultado);

											//Mostra os cards na tela
											printCards($set);

										}
										else{
											echo $error_msg;
										}//if($resultado)

									}
									else{
										echo $error_msg;
									}//if($sql->execute())

								}
								else{
									echo $error_msg;
								}//if($sql)

								$BD_Connection->close();

							}//if( (require_once "assets/scripts/cards.class.php") == true && (require_once "assets/scripts/utils.class.php") == true ){
							else{
								echo $error_msg;
							}

						}//if($_SERVER["REQUEST_METHOD"])
						else if( (require_once "assets/scripts/cards.class.php") == true && (require_once "assets/scripts/utils.class.php") == true ){

							$user_id = $_SESSION["id"];

							//Pesquisa os perfis do usuário no banco
							$sql = $BD_Connection->prepare("SELECT * FROM $BD_Profiles_table WHERE $BD_Profile_user_id_field = ?");

							if($sql){

								$sql->bind_param('i', $user_id);

								if($sql->execute()){

									$resultado = $sql->get_result();
									$sql = null;

									$card = new Cards();

									if($resultado->num_rows > 0){

										echo "
											<form method='GET' action='home.php' id='search-form'>
												<div class='form-control bom-senso'>
													<label for='inTermo' class='sr-only'>Termo da busca(nome do perfil, nome do locatário ou do locador)</label>
													<input type='text' id='inTermo' class='clean search-txt' name='term' placeholder='Perfil, locatário ou locador'/>
													<button type='submit' class='clean pull-right search-btn' >Buscar</button>
												</div>
											</form>";

										//Array com todas as linhas do result set da query
										$set = array();
										while($obj = $resultado->fetch_assoc()){
											array_push($set, $obj);
										}

										mysqli_free_result($resultado);

										//Mostra os cards na tela
										printCards($set);


									}
									else{
										echo $card->mkDefaultCard();
									}//if($resultado)

								}
								else{
									echo $error_msg;
								}//if($sql->execute())

							}
							else{
								echo $error_msg;
							}//if($sql)

							$BD_Connection->close();

						}//if($conectado)
						else{
							echo $error_msg;
						}

					}//if(!mysql_error_no())

				}//if( (require_once "assets/scripts/connect.php") == true && (require_once "assets/scripts/check_session.php") == true ){

				if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["e"])){
					echo "<p class='error'>$error_msg</p>";
				}

			?>

		</div>

		<?php include "assets/snippets/footer.php"; ?>

		<!--JavaScript-->
		<script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="//ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/bootstrap.min.js" type="text/javascript"></script>
		<script src="js/ie10-viewport-bug-workaround.js" type="text/javascript"></script>

		<script type="text/javascript">

			//Habilita as tooltips
			$(function() {
				$('[data-toggle="tooltip"]').tooltip();
			});

			function voltar(){
				history.go(-1);
				document.getElementById('inTermo').value() = '';
			}

		</script>

	</body>

</html>
