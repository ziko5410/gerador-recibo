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

					//Justifica o valor do aluguel. Ex: ALUGUEL........R$ x (a função adiciona os pontos ou outro caractere passado)
					function justificaValorAluguel($str, $fpdf_obj){

						$fpdf = $fpdf_obj;
						$tam_atual = $fpdf->GetStringWidth($str) - 0.2;//0.2 por causa do #
						$str_line = "";

						while( ( $tam_atual + $fpdf->GetStringWidth($str_line) ) < $fpdf->GetPageWidth() - 4.3 ){
							$str_line = $str_line.'.';
						}

						return str_replace('#', $str_line, $str);
					}

					function mkPDF($pdf, $locador, $locatario, $initcontrat, $termcontrat, $aluguel, $rua, $numero, $bairro, $cidade, $casafundo, $referentefrom, $referenteto, $data){

						$pdf->SetFont("Arial", "B", 15);

						//Título do recibo
						$pdf->Write(.8 , "Recibo de aluguel\n");
						//quebra de
						$pdf->Ln(.3);
						$pdf->Line(2, $pdf->GetY(), $pdf->GetPageWidth()-2,  $pdf->GetY());
						$pdf->Ln(.3);

						$pdf->SetFont("Arial", "", 12);

						$line_height = .6;

						//Cabeçalho
						$pdf->Write($line_height, utf8_decode("LOCADOR: $locador\n"));
						$pdf->Write($line_height, utf8_decode("LOCATÁRIO: $locatario\n"));

						$utils = new Utils();
						//Formata as data para o formato dd/mm/aaaa
						$initcontrat = $utils->formatDate("Y-m-d", $initcontrat, "d/m/Y");
						$termcontrat = $utils->formatDate("Y-m-d", $termcontrat, "d/m/Y");
						$referentefrom = $utils->formatDate("Y-m-d", $referentefrom, "d/m/Y");
						$referenteto = $utils->formatDate("Y-m-d", $referenteto, "d/m/Y");

						if(!empty($initcontrat) && !empty($termcontrat)){
							$pdf->Write($line_height, utf8_decode("INÍCIO: $initcontrat\n"));
							$pdf->Write($line_height, utf8_decode("TÉRMINO: $termcontrat\n"));
						}

						if($casafundo == 1){ $casafundo = "- FD"; }else{ $casafundo = ""; }

						$pdf->Write($line_height, utf8_decode("ENDEREÇO: $rua, $numero - $bairro $casafundo \n"));

						$pdf->Ln(.3);
						$pdf->Line(2, $pdf->GetY(), $pdf->GetPageWidth()-2,  $pdf->GetY());
						$pdf->Ln(.3);

						$pdf->Write($line_height, utf8_decode("Referente: $referentefrom a $referenteto"));
						$pdf->Ln(1.7);

						$pdf->Write($line_height, utf8_decode( justificaValorAluguel("ALUGUEL#R$ ".number_format($aluguel,2,",","."), $pdf) ) );

						$pdf->Ln(1.7);
						$pdf->Write($line_height, utf8_decode("$cidade, $data \n"));

						$pdf->Ln(.3);
						$pdf->Line(2, $pdf->GetY(), $pdf->GetPageWidth()-2,  $pdf->GetY());
						$pdf->Ln(6);
					}

					if(!mysqli_connect_errno()){

						$sql = $BD_Connection->prepare("SELECT * FROM $BD_Profiles_table WHERE $BD_Profile_user_id_field = ? AND $BD_Profile_id_field  = ?");

						if($sql){

							$sql->bind_param('ii', $_SESSION["id"], $card_id);

							if($sql->execute()){

								$resultado = $sql->get_result();
								$sql = null;

								if($resultado->num_rows > 0){

									$obj = $resultado->fetch_assoc();
									mysqli_free_result($resultado);

									$pdf = new FPDF('P', 'cm', 'A4');//Padrão A4 porta-retrato, medidas em mm
									$utils = new Utils();

									$pdf->SetCompression(true);

									$pdf->SetMargins(2, 2);

									$obj[$BD_Data_recibo_field] = $utils->formatDate("Y-m-d", $obj[$BD_Data_recibo_field], "d/m/Y");

									$username = $_SESSION['user'];
									$pdf->SetTitle(utf8_decode("Recibo de $username - $obj[$BD_Data_recibo_field]") , true);
									$pdf->SetAuthor($username, true);
									$pdf->SetCreator("Recibeira", true);

									$pdf->AddPage();

									for($i = 0; $i < 2; $i++){
										mkPDF(
										    $pdf,
											$obj[$BD_Locador_field],
											$obj[$BD_Locatario_field],
											$obj[$BD_Inicio_contrato_field],
											$obj[$BD_Termino_contrato_field],
											$obj[$BD_Aluguel_valor_field],
											$obj[$BD_Rua_field],
											$obj[$BD_Numero_casa_field],
											$obj[$BD_Bairro_field],
											$obj[$BD_Cidade_field],
											$obj[$BD_Casa_fundo_field],
											$obj[$BD_Referente_from_field],
											$obj[$BD_Referente_to_field],
											$obj[$BD_Data_recibo_field]);
									}

									//Envia o pdf para o client
									$pdf->Output('I', "Recibo_$obj[$BD_Data_recibo_field].pdf", true);

									$BD_Connection->close();

									exit;

								}//if($resultado->num_rows > 0)

							}//if($sql->execute())

						}//if($sql)

					}//if(!mysqli_connect_errno())

				}///if( strcmp($hash, $qhash) )
				else{
					echo "<p>Não foi possível exibir o documento. Atualize a página ou tente novamente mais tarde.</p>";
				}

			}//if( ( isset($_GET["c"]) && !empty($_GET["c"]) ) && ( isset($_GET["key"]) && !empty($_GET["key"]) ) ){

		}//if( (require_once "assets/libs/fpdf/fpdf.php") == true )
		else{
			echo "<p>Não foi possível exibir o documento. Atualize a página ou tente novamente mais tarde.</p>";
		}


	}//if($_SERVER["REQUEST_METHOD"] == "GET")

?>
