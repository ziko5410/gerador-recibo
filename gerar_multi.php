<?php

	if( ((require_once "assets/scripts/connect.php") == true)  && ((require_once "assets/scripts/utils.class.php") == true)  && ((require_once "assets/scripts/check_session.php") == true) ){

    // Controla quando a lista de recibos gerados deve ser mostrada
    $registrouRecibosTemporarios = false;

		if($_SERVER["REQUEST_METHOD"] == "GET"){

      $utils = new Utils();

			if( !$utils->emptyString($_GET["c"]) ){

				$profile_id = $_GET["c"];
				$hash = $_GET["key"];
        $qhash = sha1( md5( "c=$profile_id&key=" ) );

        if( strcmp($hash, $qhash) == 0 ){
          $dbHelper = new DatabaseHelper($BD_Connection);

          if(!mysqli_connect_errno()){

            $sql = $BD_Connection->prepare("SELECT * FROM $BD_Profiles_table WHERE $BD_Profile_id_field = ? AND $BD_Profile_user_id_field = ?");

            if($sql){
              $user_id = $_SESSION["id"];
              $sql->bind_param('ii', $profile_id, $user_id);

              if($sql->execute()){

                $resultado = $sql->get_result();
                $sql = null;

                if($resultado->num_rows > 0){
                  //Os elementos do resultado são mostrados direto nos inputs
                  $profile = $resultado->fetch_assoc();
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

          if(!$utils->emptyString($_GET["meses"])) {
            // Usuário solicitou a geração dos recibos
            $qtdeMesesGerar = $_GET["meses"];

            // Remove todos os recibos gerados anteriormente, antes de criar os novos
            //$dbHelper->delete($BD_Recibos_table, "temporario = ?", "i", array(1));
            $dbHelper->delete($BD_Recibos_table, null, "i", array(1));

            $dataRecibo = new DateTime($profile[$BD_Data_recibo_field]);
            $dataReciboFrom = new DateTime($profile[$BD_Referente_from_field]);
            $dataReciboTo = new DateTime($profile[$BD_Referente_to_field]);

            for ($i = 0; $i < $qtdeMesesGerar; $i++) {
              $dbHelper->insert(
                $BD_Recibos_table,
                array(
                  $BD_Recibo_profile_id_field,
                  $BD_Recibo_Referente_from_field,
                  $BD_Recibo_Referente_to_field,
                  $BD_Recibo_Data_recibo_field,
                  $BD_Recibo_temporario_field
                ),
                "isssi",
                array(
                  $profile["profile_id"],
                  $dataReciboFrom->format('Y-m-d'),
                  $dataReciboTo->format('Y-m-d'),
                  $dataRecibo->format('Y-m-d'),
                  1
                )
              );

              // Avança 1 mês nas datas
              $dataRecibo->add(new DateInterval('P1M'));
              $dataReciboFrom->add(new DateInterval('P1M'));
              $dataReciboTo->add(new DateInterval('P1M'));
            }

            $registrouRecibosTemporarios = true;

            // Mostrar os recibos temporários
          }
        }
        else {
          $form_error="Não foi possível exibir o documento. Atualize a página ou tente novamente mais tarde.";
        }
			}//if(!empty($_GET["card"]))

		}//if($_SERVER["REQUEST_METHOD"] == "GET")

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

		<title>Gerar recibos - Recibeira</title>

		<link rel="stylesheet" href="//ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/css/bootstrap.min.css">
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

						echo "<a class='navbar-brand' href='home.php'><span> $user </span><img src='//www.gravatar.com/avatar/$gravatar_email?s=36&d=mm' data-toggle='tooltip' data-placement='left' title='Sua imagem de usuário é obtida através do seu email, se estiver cadastrado no site Gravatar.com' class='profile-picture' alt='Foto do usuário'/></a>";
					?>

				</div>
			</div>
		</nav>
		<!--FIM BARRA DE NAVEGAÇÃO-->


		<div class="container">

      <h1>Gerar recibos - <?php echo $profile[$BD_Profile_name_field] ?></h1>

      <form method="GET" action="gerar_multi.php" onsubmit="return validateForm('intervalo-form')" id="intervalo-form">

        <input type="hidden" name="c" value="<?php echo $_GET["c"] ?>"/>
        <input type="hidden" name="key" value="<?php echo $_GET["key"] ?>"/>

        <fieldset class='col-md-4 row'>

					<label for='inQtdeMeses'>Quantidade de meses para gerar<span class='info'>*</span></label>
          <input type='number' id='inQtdeMeses' name='meses' class='form-control bom-senso' placeholder='1' max='12' value='<?php echo !$utils->emptyString($_GET["meses"]) ? $_GET["meses"] : "2" ?>' required>

        </fieldset>

        <div class="col-md-12 row">
					<button type="submit" class="btn btn-primary">Gerar</button>
					<a href="home.php" class="btn btn-default">Voltar para o início</a>
				</div>

      </form>

      <?php if ($registrouRecibosTemporarios) { ?>

      <div class="col-md-12 row mt-4">
        <h2>Recibos gerados</h2>

        <ul class="list-unstyled">
          <?php

            $recibosTemp = $dbHelper->select(
              "select * from $BD_Recibos_table where $BD_Recibo_profile_id_field = ? and $BD_Recibo_temporario_field = ?",
              "ii",
              array($profile_id, 1)
            );

            for($i = 0; $i < sizeof($recibosTemp); $i++) {
              $reciboTemp = $recibosTemp[$i];

              $dataRecibo = (new DateTime($reciboTemp[$BD_Recibo_Data_recibo_field]))->format('d/m/Y');
              $dataReciboFrom = (new DateTime($reciboTemp[$BD_Recibo_Referente_from_field]))->format('d/m/Y');
              $dataReciboTo = (new DateTime($reciboTemp[$BD_Recibo_Referente_to_field]))->format('d/m/Y');

              $query_string_hash = sha1(md5("c=$profile_id&key="));
			        $query_string = "c=$profile_id&tmpId=$reciboTemp[$BD_Recibo_id_field]&key=$query_string_hash";
              ?>
              <li class="py-2">
                <p class="m-0">
                  <?php echo "Referente a <b>$dataRecibo</b> (de $dataReciboFrom a $dataReciboTo)"?>
                </p>
                <a target="_blank" href="gera_pdf.php?<?php echo $query_string ?>">Visualizar documento</a>
              </li>
              <?php
            }

          ?>
        </ul>
      </div>

      <?php } ?>

		</div>

		<?php include "assets/snippets/footer.php"; ?>

		<!--JavaScript-->
		<script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js" type="text/javascript"></script>
		<script src="//ajax.aspnetcdn.com/ajax/bootstrap/3.3.7/bootstrap.min.js" type="text/javascript"></script>
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
