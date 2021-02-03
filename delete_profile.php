<?php
	
	if($_SERVER["REQUEST_METHOD"] == "GET"){

		if( (require_once "assets/libs/fpdf/fpdf.php") == true && (require_once "assets/scripts/connect.php") == true && (require_once "assets/scripts/check_session.php") == true && (require_once "assets/scripts/utils.class.php") == true ){

			if( ( isset($_GET["c"]) && !empty($_GET["c"]) ) && ( isset($_GET["key"]) && !empty($_GET["key"]) ) ){

				$card_id = $_GET["c"];
				//Query string hash
				$hash = $_GET["key"];

				//Refaz a hash da query string e compara com a recebida
				$qhash = sha1( md5( "c=$card_id&key=" ) );

				if( strcmp($hash, $qhash) == 0 ){

					if(!mysqli_connect_errno()){

						$sql = $BD_Connection->prepare("DELETE FROM $BD_Profiles_table WHERE $BD_Profile_user_id_field = ? AND $BD_Profile_id_field  = ?");

						if($sql){

							$sql->bind_param('ii', $_SESSION["id"], $card_id);

							if($sql->execute()){

								$sql = null;
								$BD_Connection->close();
								header("Location: home.php");
								exit;

							}//if($sql->execute())

						}//if($sql)

					}//if(!mysqli_connect_errno())

				}//if( strcmp($hash, $qhash) == 0 )

			}//if( ( isset($_GET["c"]) && !empty($_GET["c"]) ) && ( isset($_GET["key"]) && !empty($_GET["key"]) ) )

		}//if( (require_once "assets/libs/fpdf/fpdf.php") == true ...

		header("Location: home.php?e=1");
		exit;

	}//if($_SERVER["REQUEST_METHOD"] == "GET")

?>