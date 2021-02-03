<?php
	
	class Cards{

		public function mkDefaultCard(){
			$card = "<div class='col-sm-12 profile-card no-card'>
						<h1>Você ainda não tem nenhum perfil...</h1>
						<a class='btn btn-lg btn-success' href='recibar.php'>Criar um agora!</a>
					</div>";

			return $card;
		}

		public function mkLeftCard($cardId, $nomePerfil, $locador, $locatario, $valAluguel, $endereco, $referente){
			$query_string_hash = sha1(md5("c=$cardId&key="));
			$query_string = "c=$cardId&key=$query_string_hash";

			$card = "<div class='col-sm-6 profile-card card-left'>

						<div class='profile-info'>
							<h1>$nomePerfil</h1>
							<p><strong>Locador:</strong> $locador</p>
							<p><strong>Locatário:</strong> $locatario</p>
							<p><strong>Valor do aluguel:</strong> R$ $valAluguel</p>
							<p><strong>Endereço:</strong> $endereco</p>
							<p><strong>Referente:</strong> $referente</p>
						</div>

						<div>
							<a href='recibar.php?card=$cardId' class='btn btn-primary '>Editar</a>
							<a target='_blank' href='gera_pdf.php?$query_string' class='btn btn-default '>Abrir documento</a>
							<a class='btn btn-danger' href='delete_profile.php?$query_string'>Apagar</a>
						</div>

					</div>";

			return $card;
		}

		public function mkRightCard($cardId, $nomePerfil, $locador, $locatario, $valAluguel, $endereco, $referente){

			$query_string_hash = sha1(md5("c=$cardId&key="));
			$query_string = "c=$cardId&key=$query_string_hash";

			$card = "<div class='col-sm-6 profile-card card-right'>

						<div class='profile-info'>
							<h1>$nomePerfil</h1>
							<p><strong>Locador:</strong> $locador</p>
							<p><strong>Locatário:</strong> $locatario</p>
							<p><strong>Valor do aluguel:</strong> R$ $valAluguel</p>
							<p><strong>Endereço:</strong> $endereco</p>
							<p><strong>Referente:</strong> $referente</p>
						</div>

						<div>
							<a href='recibar.php?card=$cardId' class='btn btn-primary '>Editar</a>
							<a target='_blank' href='gera_pdf.php?$query_string' class='btn btn-default '>Abrir documento</a>
							<a class='btn btn-danger' href='delete_profile.php?$query_string'>Apagar</a>
						</div>

					</div>";

			return $card;			
		}

		public function mkLastOddCard($cardId, $nomePerfil, $locador, $locatario, $valAluguel, $endereco, $referente){

			$query_string_hash = sha1(md5("c=$cardId&key="));
			$query_string = "c=$cardId&key=$query_string_hash";

			$card = "<div class='col-sm-6 profile-card last-odd-card'>

						<div class='profile-info'>
							<h1>$nomePerfil</h1>
							<p><strong>Locador:</strong> $locador</p>
							<p><strong>Locatário:</strong> $locatario</p>
							<p><strong>Valor do aluguel:</strong> R$ $valAluguel</p>
							<p><strong>Endereço:</strong> $endereco</p>
							<p><strong>Referente:</strong> $referente</p>
						</div>

						<div>
							<a href='recibar.php?card=$cardId' class='btn btn-primary '>Editar</a>
							<a target='_blank' href='gera_pdf.php?$query_string' class='btn btn-default '>Abrir documento</a>
							<a class='btn btn-danger' href='delete_profile.php?$query_string'>Apagar</a>
						</div>

					</div>";

			return $card;
		}

	}

?>