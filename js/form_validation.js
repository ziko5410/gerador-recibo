function validateForm(formID){
	//Mensagem de erro
	var error = $(".form-error:first");
	//Formulário da página
	var form = document.forms[formID];
	//Controles dentro do formulário
	var controls = form.elements;

	//
	//VALIDAÇÃO DO NOME
	if(controls.namedItem("inNome") != null){

		//Checa se o nome foi digitado
		if($("#inNome").val().length <= 0){
			error.html("Digite um nome, queremos te conhecer.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se o nome está dentro do tamanho determinado
		if($("#inNome").val().length > 50){
			error.html("Seu nome está muito longo, tente reduzir um pouco (máximo 50 caracteres)");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se o nome contém apenas letras
		if(isNumeric($("#inNome").val())){
			error.html("Seu nome não deveria possuir números...");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DO EMAIL
	if(controls.namedItem("inEmail") != null){
		
		//Checa se o email foi digitado
		if($("#inEmail").val().length <= 0){
			error.html("Digite um email.");
			error.show();
			return false;
		}	
		else{
			error.hide();
		}

		//Checa se o email está dentro do tamanho determinado
		if($("#inEmail").val().length > 50){
			error.html("Seu email está muito longo, tente outro (máximo 50 caracteres).");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se o email está em um formato correto
		if(!validateEmail($("#inEmail").val())){
			error.html("Digite um email válido.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DA SENHA
	if(controls.namedItem("inSenha") != null){

		//Checa se uma senha foid digitada
		if($("#inSenha").val().length <= 0){
			error.html("Digite uma senha."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}

		//Checa se a senha está dentro do tamanho determinado
		if($("#inSenha").val().length < 8){
			error.html("Senha muito curta, tente uma mais longa (mínimo 8 caracteres).");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se a senha está dentro do tamanho determinado
		if($("#inSenha").val().length > 12){
			error.html("Senha muito longa, tente uma mais curta (máximo 12 caracteres).");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	if(controls.namedItem("inSenhaConf") != null){
		//Checa se a confirmação da senha é igual a senha digitada
		if($("#inSenhaConf").val() != $("#inSenha").val()){
			error.html("As senhas não coincidem.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DO EMAIL
	if(controls.namedItem("inCodigo") != null){
		
		//Checa se o email foi digitado
		if($("#inCodigo").val().length <= 0){
			error.html("Digite seu código de ativação abaixo.");
			error.show();
			return false;
		}	
		else{
			error.hide();
		}

		//Checa se o email está dentro do tamanho determinado
		if($("#inCodigo").val().length > 6){
			error.html("Seu código não deve conter mais que 6 caracteres.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se o email está dentro do tamanho determinado
		if($("#inCodigo").val().length < 6){
			error.html("Seu código deve conter 6 caracteres.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}	

		if(!isNumeric($("#inCodigo").val())){
			error.html("Seu código deve possuir apenas números.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DO LOCADOR
	if(controls.namedItem("inNomePerfil") != null){

		//Checa se o nome foi digitado
		if($("#inNomePerfil").val().length <= 0){
			error.html("Digite um nome para o perfil.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se o nome está dentro do tamanho determinado
		if($("#inNomePerfil").val().length > 30){
			error.html("O nome do perfil está muito longo, tente reduzir um pouco (máximo 30 caracteres).");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DO LOCADOR
	if(controls.namedItem("inLocador") != null){

		//Checa se o nome foi digitado
		if($("#inLocador").val().length <= 0){
			error.html("Digite o nome do locador.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se o nome está dentro do tamanho determinado
		if($("#inLocador").val().length > 50){
			error.html("O nome do locador está muito longo, tente reduzir um pouco (máximo 50 caracteres).");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se o nome contém apenas letras
		if(isNumeric($("#inLocador").val())){
			error.html("O nome do locador não deveria possuir números...");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DO LOCATÁRIO
	if(controls.namedItem("inLocatario") != null){

		//Checa se o nome foi digitado
		if($("#inLocatario").val().length <= 0){
			error.html("Digite o nome do locatário.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se o nome está dentro do tamanho determinado
		if($("#inLocatario").val().length > 50){
			error.html("O nome do locatário está muito longo, tente reduzir um pouco (máximo 50 caracteres).");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

		//Checa se o nome contém apenas letras
		if(isNumeric($("#inLocatario").val())){
			error.html("O nome do locatário não deveria possuir números...");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DA DATA DE INÍCIO DO CONTRATO
	if(controls.namedItem("inContInicio") != null){

		//checa se a data de início foi digitada
		//checa se a data de início foi digitada
		if($("#inContInicio").val().length <= 0){
			error.html("Digite a data de início do contrato."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DA DATA DE TÉRMINO DO CONTRATO
	if(controls.namedItem("inContTerm") != null){

		//checa se a data de início foi digitada
		if($("#inContTerm").val().length <= 0){
			error.html("Digite a data de término do contrato."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DO VALOR DO ALUGUEL
	if(controls.namedItem("inValor") != null){

		//Checa se o valor do aluguel foi digitado
		if($("#inValor").val().length <= 0){
			error.html("Digite o valor do aluguel."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}

		if($("#inValor").val().length > 10){
			error.html("Valor do aluguel muito longo."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}

		if($("#inValor").val() == "0"){
			error.html("O valor do aluguel deve ter o valor mínimo de R$ 1,00."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}

		if(!isCurrency($("#inValor").val())){
			error.html("O valor do aluguel só deve conter números e ',' ou '.'."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DA RUA
	if(controls.namedItem("inRua") != null){

		//Checa se uma rua foid digitada
		if($("#inRua").val().length <= 0){
			error.html("Digite o nome da rua."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}

		//Checa se a RUA está dentro do tamanho determinado
		if($("#inRua").val().length > 40){
			error.html("Nome da rua muito longo, tente um mais curto (máximo 40 caracteres).");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DO NÚMERO DA CASA
	if(controls.namedItem("inNumero") != null){

		//Checa se p número da casa foi digitado
		if($("#inNumero").val().length <= 0){
			error.html("Digite o numero da casa."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DO BAIRRO
	if(controls.namedItem("inBairro") != null){

		//Checa se uma rua foid digitada
		if($("#inBairro").val().length <= 0){
			error.html("Digite o nome do bairro da residência."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}

		//Checa se a RUA está dentro do tamanho determinado
		if($("#inBairro").val().length > 40){
			error.html("Nome do bairro muito longo, tente um mais curto (máximo 40 caracteres).");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DA CIDADE
	if(controls.namedItem("inCidade") != null){

		//Checa se uma rua foid digitada
		if($("#inCidade").val().length <= 0){
			error.html("Digite o nome da cidade da residência."); 
			error.show();
			return false;
		}	
		else{
			error.hide();
		}

		//Checa se a RUA está dentro do tamanho determinado
		if($("#inCidade").val().length > 30){
			error.html("Nome da cidade muito longo, tente um mais curto (máximo 30 caracteres).");
			error.show();
			return false;
		}
		else{
			error.hide();
		}
	}

	//
	//VALIDAÇÃO DA PRIMEIRA DATA DO REFERENTE
	if(controls.namedItem("inReferenteA") != null){

		//checa se a data de início foi digitada
		if($("#inReferenteA").val().length <= 0){
			error.html("Digite a data de início do referente.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

	}

	//
	//VALIDAÇÃO DA SEGUNDA DATA DO REFERENTE
	if(controls.namedItem("inReferente") != null){

		//checa se a data de início foi digitada
		if($("#inReferente").val().length <= 0){
			error.html("Digite a data de término do referente.");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

	}

	//
	//VALIDAÇÃO DA DATA DO RECIBO
	if(controls.namedItem("inData") != null){

		//Se a checkbox não estiver marcada e não houver uma data digitada
		if( ($("#inData").val().length <= 0) && ($("#cbDataHoje").prop('checked') == false) ){
			error.html("Digite a data do recibo ou marque a caixa \"Usar data de hoje\"");
			error.show();
			return false;
		}
		else{
			error.hide();
		}

	}

	//Retorna True se a string contiver números e false caso contrário.
	function isNumeric(n) {
		var re = /^[0-9]+$/g;
		return re.test(n);
	}

	//Checa se o valor de entrada corresponde a moeda
	function isCurrency(n){
		var re = /^[0-9,.]+$/g;
		return re.test(n);
	} 

	//Valida um email
	function validateEmail(email) {
		var re = /^[A-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])+$/g;
		return re.test(email);
	}

	function containsInt(array, value){

		for(var i = 0; i < array.length; i++){
			if(array[i] == value){
				return true;
			}
		}

		return false;
	}

}