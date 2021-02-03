<?php
	//CONECTA NO BANCO DE DADOS//

	//Informação do servidor
	$BD_Server = "localhost";
	$BD_User = "root";
	$BD_Password = "96314651Allex";
	$BD_Database = "recibeira";

	/*$BD_Server = "localhost";
	$BD_User = "id567638_rec_admin";
	$BD_Password = "EYOu5NLwSLMp";
	$BD_Database = "id567638_recibeira";*/

	//Tabela
	$BD_Users_table = "usuarios";
	$BD_Profiles_table = "user_profiles";

	//Campos usuário
	$BD_Id_field = "id";//PK
	$BD_Name_field = "username";
	$BD_Email_field = "user_email";
	$BD_Password_field = "user_password";
	$BD_Email_hash_field = "email_hash";
	$BD_Email_verified_field = "email_verified";
	$BD_Acc_code_field = "acc_code";
	$BD_User_id_field = "user_id";
	$BD_User_token_field = "user_token";

	//Campos Perfis
	$BD_Profile_id_field = "profile_id";//PK
	$BD_Profile_user_id_field = "user_id";//FK
	$BD_Profile_name_field = "profile_name";
	$BD_Locador_field = "locador";
	$BD_Locatario_field = "locatario";
	$BD_Inicio_contrato_field = "inicio_contrato";
	$BD_Termino_contrato_field = "termino_contrato";
	$BD_Aluguel_valor_field = "valor_aluguel";
	$BD_Rua_field = "rua";
	$BD_Numero_casa_field = "numero_casa";
	$BD_Bairro_field = "bairro";
	$BD_Cidade_field = "cidade";
	$BD_Casa_fundo_field = "casa_fundo";
	$BD_Referente_from_field = "referente_from";
	$BD_Referente_to_field = "referente_to";
	$BD_Data_recibo_field = "data_recibo";

	$BD_Connection = mysqli_connect($BD_Server, $BD_User, $BD_Password);

	mysqli_select_db($BD_Connection, $BD_Database);

	mysqli_set_charset($BD_Connection,"utf8");

?>