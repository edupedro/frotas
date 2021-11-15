// JavaScript Document

	//Extras
	
	function dump(obj) {
		var out = '';
		for (var i in obj) {
			out += i + ": " + obj[i] + "\n";
		}
		alert(out);
	}

    function compararDatas(data){
        var retorno = false;
        var objDate = new Date();

        objDate.setYear(data.split("/")[2]);
        objDate.setMonth(data.split("/")[1]-1);// -1 pq em js é de 0 a 11 os meses
        objDate.setDate(data.split("/")[0]);

        if(objDate.getTime() >= new Date().getTime()){
            //alert("O dia passado é maior que a data atual..");
            retorno = true;
        }
        return retorno;
    }

	function validarCheckboxes(documento) {
		var inputs, x, selecionados = 0;
		inputs = documento.getElementsByTagName('input');
	
		for(x=0;x<inputs.length;x++){
			if(inputs[x].type=='checkbox'){
				if(inputs[x].checked == true){
					selecionados++;	
				}
			}
		}
		if(selecionados > 0){ return true;}else{return false;}
	}
	
	function converteMoedaFloat(valor){
		if(valor === ""){
			valor = 0;
		}else{
			valor = valor.replace(".","");
			valor = valor.replace(",",".");
			valor = parseFloat(valor);
		}
		return valor;		
	}
	
	function converteFloatMoeda(valor){
		var inteiro = null, decimal = null, c = null, j = null;
		var aux = new Array();
		valor = ""+valor;
		c = valor.indexOf(".",0);
		//encontrou o ponto na string
		if(c > 0){
			//separa as partes em inteiro e decimal
			inteiro = valor.substring(0,c);
			decimal = valor.substring(c+1,valor.length);
		}else{
			inteiro = valor;
		}
		
		//pega a parte inteiro de 3 em 3 partes
		for (j = inteiro.length, c = 0; j > 0; j-=3, c++){
			aux[c]=inteiro.substring(j-3,j);
		}
		
		//percorre a string acrescentando os pontos
		inteiro = "";
		for(c = aux.length-1; c >= 0; c--){
			inteiro += aux[c]+'.';
		}
		
		//retirando o ultimo ponto e finalizando a parte inteiro			
		inteiro = inteiro.substring(0,inteiro.length-1);			
		decimal = parseInt(decimal);
		if(isNaN(decimal)){
			decimal = "00";
		}else{
			decimal = ""+decimal;
			if(decimal.length === 1){
				decimal = decimal+"0";
			}
		}
		valor = "R$ "+inteiro+","+decimal;
		return valor;		
	}		

	function trim(campo){
		var valor 	= campo.value;		
		campo.value	= valor.trim(); 	
	}
	
	function mudar(pagina, param1, param2){
		document.getElementById('leitura').setAttribute('src',pagina+'?valor='+param1);
		document.getElementById('idRegistro').value = param1;
		document.getElementById('idSimples').value = param2;
	}	
	
	function formatarFloatEua(valor){
		if(valor == ''){ return 0; }
		var novo = ''+valor;
		
		for (i=0;i<novo.length;i++){  
			if (novo.charAt(i) == '.' ){  
				novo = novo.replace('.','');  
			}else if(novo.charAt(i) == ','){
				novo = novo.replace(',','.');
			}
		} 
		return novo;
	}	

	function formatarFloatBrasil(valor){
		if(valor == ''){ return 0; }
		var novo = ''+valor;
		
		for (i=0;i<novo.length;i++){  
			if (novo.charAt(i) == ',' ){  
				novo = novo.replace(',','');  
			}else if(novo.charAt(i) == '.'){
				novo = novo.replace('.',',');
			}
		} 
		return novo;
	}	
	
	function horizontal() {
	   var navItems = document.getElementById("menu_dropdown").getElementsByTagName("li");
	   for (var i=0; i< navItems.length; i++) {
		  if(navItems[i].className == "submenu"){
			 if(navItems[i].getElementsByTagName('ul')[0] != null){
				navItems[i].onmouseover=function() {this.getElementsByTagName('ul')[0].style.display="block";this.style.backgroundColor = "#f9f9f9";}
				navItems[i].onmouseout=function() {this.getElementsByTagName('ul')[0].style.display="none";this.style.backgroundColor = "#FFFFFF";}
			 }
		  }
	   }	 
	}
	
	function confirmarSenha(campo1,campo2) {
		var val1 = campo1.value;
		var val2 = campo2.value;
		 if (val1.length >= 6) {
			if (val1 != val2) {
				alert('A senha n\u00e3o confere');
				campo2.value='';
				campo1.value='';
				campo1.focus();
				return false;
			} else {
				return true;
			}
		} else {
			alert('Verifique o n\u00famero de caracteres da senha');
			campo2.value='';
			campo1.value='';
			campo1.focus();
			return false;
		}
	}

	function maiuscula(campo){
		var val = campo.value;
			campo.value = val.toUpperCase();
	}

	function minuscula(campo){
		var val = campo.value;
			campo.value = val.toLowerCase();
	}

	function soNumero(campo){  
		var digits="0123456789/.-,"  
		var campo_temp   
		
		for (var i=0;i<campo.value.length;i++){  
			campo_temp=campo.value.substring(i,i+1)   
			if (digits.indexOf(campo_temp)==-1){  
				campo.value = campo.value.substring(0,i);  
			}  
		}  
	}	

	function soNumeroRestrito(campo){  
		var digits="0123456789"  
		var campo_temp   
		
		for (var i=0;i<campo.value.length;i++){  
			campo_temp=campo.value.substring(i,i+1)   
			if (digits.indexOf(campo_temp)==-1){  
				campo.value = campo.value.substring(0,i);  
			}  
		}  
	}	

	function limpar(campo) {
		campo.value = "";
		campo.focus();
	}

 	function reloadDesativar(campo1,campo2){
		var	val = campo1.value;
			if (val == "outro" ) {
				campo2.disabled = false;
				campo2.focus();
			} else {
				campo2.value = '';
				campo2.disabled = true;
			}
	}
	
	function formatoMoeda(campo,tammax,teclapres) {	
		var tecla = teclapres.keyCode;
		var vr = campo.value;
		vr = vr.replace( "/", "" );
		vr = vr.replace( "/", "" );
		vr = vr.replace( ",", "" );
		vr = vr.replace( ".", "" );
		vr = vr.replace( ".", "" );
		vr = vr.replace( ".", "" );
		vr = vr.replace( ".", "" );
		tam = vr.length;
	
		if (tam < tammax && tecla != 8){ tam = vr.length + 1 ; }
	
		if (tecla == 8 ){	tam = tam - 1 ; }
			
		if ( tecla == 8 || (tecla >= 48 && tecla <= 57) || (tecla >= 96 && tecla <= 105) ){
			if ( tam <= 2 ){ 
				campo.value = vr ; }
			tam = tam - 1;
			if ( (tam > 2) && (tam <= 5) ){
				campo.value = vr.substr( 0, tam - 2 ) + ',' + vr.substr( tam - 2, tam ) ; }
			
			if ( (tam >= 6) && (tam <= 8) ){
				campo.value =  vr.substr( 0, tam - 5 ) + '.' +  vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ; }
			if ( (tam >= 9) && (tam <= 11) ){
				campo.value =  vr.substr( 0, tam - 8 ) + '.' +  vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ; }
			if ( (tam >= 12) && (tam <= 14) ){
				campo.value =  vr.substr( 0, tam - 11 ) + '.' +  vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ; }
			if ( (tam >= 15) && (tam <= 17) ){
				campo.value =  vr.substr( 0, tam - 14 ) + '.' +  vr.substr( tam - 14, 3 ) + '.' + vr.substr( tam - 11, 3 ) + '.' + vr.substr( tam - 8, 3 ) + '.' + vr.substr( tam - 5, 3 ) + ',' + vr.substr( tam - 2, tam ) ;
			}
			
		}
	}

	//Preenchimento automático
	
	function autoTab(campo1,campo2,qtd) {
		var val = campo1.value;
			if (val.length == qtd) {
				campo2.focus();
			}	
	}

	function autoFone(campo){
		var data = campo.value;
	    var ndata = '';
		ndata = ndata + data;
		if (ndata.length == 2) {
		   ndata = ndata + '-';
			campo.value = ndata;
		}
		if (ndata.length == 7) {
		   ndata = ndata + '.';
			campo.value = ndata;
		}
	}

	function autoHora( campo ) {
	    texto = campo.value;		
	    if( texto.length == 2 ) {
    	    texto += ':';
	    }
	    if( texto.length == 5 ) {
    	    texto += ':';
	    }
		campo.value = texto;
	}

	function autoCpf( campo ) {
	    texto = campo.value;		
	    if( texto.length == 3 ) {
    	    texto += '.';
	    } else if( texto.length == 7 ) {
	        texto = texto + '.';
	    } else if( texto.length == 11 ) {
    	    texto = texto + '-';
	    }
    	campo.value = texto;
	}

	function autoData( campo ) {
	    texto = campo.value;
    	if( texto.length == 2 ) {
	        texto += '/';
    	    campo.value = texto;
	    } else if( texto.length == 5 ) {
	        texto += '/';
	        campo.value = texto;
    	}
	}

	function autoCep( campo ) {
		texto = campo.value;
		if(texto.length == 5 ) {
			texto += "-";	
		}
		campo.value = texto;
	}

	//Fazendo validações
	
	function validarEmail( campo ){
		if (campo.value !=''){
			if ( campo.value.indexOf('@')==-1 ||
				campo.value.indexOf('.')==-1 ||
	            campo.value.indexOf(' ')!=-1 ||
    	        campo.value.indexOf('@.')!=-1 ||
        	    campo.value.indexOf('.@')!=-1 ||
            	campo.value.length<6) {
                alert('E-mail inválido');
                //form1.email.select();
                campo.focus();
                return false;
	        }
		}
        return true;
	}
	
	function validarSelect( campo, mensagem ) {
    	if( campo.value == '---' ) {
	        alert(mensagem );
    	    campo.focus();
        	return false;
	    } else {
    	    return true;
	    }
	}	

	function validarLogin(campo,mensagem) {
	    var val = campo.value;
		if (val.length < 6) {
			alert(mensagem);
			campo.value = "";
			campo.focus();
			return false;		
		} else {
			return true;
		}
	}

	function validarVazio( campo, mensagem ) {
    	if( campo.value == '' ) {
	        alert(mensagem );
    	    campo.focus();
        	return false;
	    } else {
    	    return true;
	    }
	}

	function validarCpf( campo ) {
		cpf = campo.value;
		
		cpf = cpf.replace(/[^\d]+/g,'');    
	    if(cpf == '') return false; 
	    // Elimina CPFs invalidos conhecidos    
	    if (cpf.length != 11 || 
	        cpf == "00000000000" || 
	        cpf == "11111111111" || 
	        cpf == "22222222222" || 
	        cpf == "33333333333" || 
	        cpf == "44444444444" || 
	        cpf == "55555555555" || 
	        cpf == "66666666666" || 
	        cpf == "77777777777" || 
	        cpf == "88888888888" || 
	        cpf == "99999999999")
	            return false;       
	    // Valida 1o digito 
	    add = 0;    
	    for (i=0; i < 9; i ++)       
	        add += parseInt(cpf.charAt(i)) * (10 - i);  
	        rev = 11 - (add % 11);  
	        if (rev == 10 || rev == 11)     
	            rev = 0;    
	        if (rev != parseInt(cpf.charAt(9)))     
	            return false;       
	    // Valida 2o digito 
	    add = 0;    
	    for (i = 0; i < 10; i ++)        
	        add += parseInt(cpf.charAt(i)) * (11 - i);  
	    rev = 11 - (add % 11);  
	    if (rev == 10 || rev == 11) 
	        rev = 0;    
	    if (rev != parseInt(cpf.charAt(10)))
	        return false;       
	    return true;   		
	}

    function validarData( campo ) {
        data = campo.value;
        resultado = true;
        if( data != "" ) {
            if( data.charAt(0) != '0' ) {
                    dia = data.charAt(0) + data.charAt(1);
            } else {
                    dia =data.charAt(1);
            }
            dia = parseInt(dia);

            if( data.charAt(3) != "0" ) {
                    mes = data.charAt(3) + data.charAt(4);
            } else {
                    mes = data.charAt(4);
            }
            mes = parseInt(mes);

            /*
            if( data.charAt(6) != '0' && data.charAt(7) != '0' && data.charAt(8) != '0' ) {
                    ano = data.charAt(7) + data.charAt(8) + data.charAt(9);
            } else if( data.charAt(7) != '0' && data.charAt(8) != '0' ) {
                    ano = data.charAt(8) + data.charAt(9);
            } else if( data.charAt(8) != '0' ) {
                    ano = data.charAt(9);
            } else {
                    ano = data.charAt(6) + data.charAt(7) + data.charAt(8) + data.charAt(9);
            }*/
            ano = data.charAt(6) + data.charAt(7) + data.charAt(8) + data.charAt(9);
            ano = parseInt(ano);

            if( campo.value.length != 10 ) {
                    alert( "Data Inv\u00e1lida!\nVerifique a quantidade de d\u00edgitos" );
                    campo.focus();
                    resultado = false;
            } else if( (mes == 4 || mes == 6 || mes == 9 || mes == 11) && dia > 30 ) {
                    alert( "Data Inv\u00e1lida!\nEsse m�s n\u00e3o permite dia 31" );
                    campo.focus();
                    resultado = false;
            } else if( mes == 2 && dia > 29 ) {
                    alert( "Data Inv\u00e1lida!\nFevereiro n\u00e3o permite dia com esse valor" );
                    campo.focus();
                    resultado = false;
            } else if( campo.value.charAt( 2 ) != '/' || campo.value.charAt( 5 ) != '/' ) {
                    alert( "Data Inv\u00e1lida!\nVerifique o formato da data" );
                    campo.focus();
                    resultado = false;
            } else if( dia < 1 || dia > 31 ) {
                    alert( "Data Inv\u00e1lida!\nVerifique o dia" );
                    campo.focus();
                    resultado = false;
            } else if( mes < 1 || mes > 12 ) {
                    alert( "Data Inv\u00e1lida!\nVerifique o m\u00eas" );
                    campo.focus();
                    resultado = false;
            } else if( ano < 1 ) {
                    alert( "Data Inv\u00e1lida!\nVerifique o ano" );
                    campo.focus();
                    resultado = false;
            }
            return resultado;
        }
    }

	function validarCep( campo ) {
		if( campo.value != '' ) {
			if( campo.value.length != 9 ) {
				alert( "Valor do CEP inválido" );
				return false;
				campo.focus();
			} else if( campo.value.charAt(0) == '5' ) {
				return true;
			} else {
				//alert( "Valor de CEP inv�lido n�o sei" );
				//campo.focus();
				//return false;
			}
		}
	}	

	
	function validarInscricao( campo ) {
		resultado = true;
		if( campo.value != '' ) {
			if( campo.value.length != 18 ) {
				resultado = false;
			} else if( campo.value.charAt( 2 ) != '.' ) {
				resultado = false;
			} else if( campo.value.charAt( 4 ) != '.' ) {
				resultado = false;
			} else if( campo.value.charAt( 8 ) != '.' ) {
				resultado = false;
			} else if( campo.value.charAt(16) != '-' ) {
				resultado = false;
			}
		}
	
		if( resultado == false ) {
			alert( 'Inscri\u00e7\u00e3o Estadual inválida' );
			campo.focus();
			return false;
		} else {
			return true;
		}
	}

	function auto_cnpj( campo ) {
		texto = campo.value;
		if(event.keyCode == 8) { texto = ""; }
		if( parseInt(texto.length) == 8 ) {
			texto += "/";
			campo.value = texto;
		} else if ( parseInt(texto.length) ==13 ) {
			texto += "-";
			campo.value = texto;
		}
	}
	var ncnpj = new Array;

	function valida_cnpj(Form,nForm){
		if (form1.cnpj.value != '') {
			var Campos = eval('document.' + nForm + '.vcnpj.value');
			var Contador = 0;
			var x = 0;
			var i = Campos.indexOf( "," );
			if (i==-1){
				ncnpj[Contador] = Campos.slice(0,Campos.length);
			} else {
				ncnpj[Contador] = Campos.slice(0,i);
				Campos = Campos.slice(i+1,Campos.length);
				//Rotina que recebe os demais campos
				for (;x<Campos.length;x++){
					if (Campos.slice(x,x+1) == ","){
						Contador = Contador + 1;
						ncnpj[Contador] = Campos.slice(0,x);
						Campos = Campos.slice(x+1,Campos.length);
						x = 0;
					}
				}
				ncnpj[ncnpj.length] = Campos;
			}
			x = 0;
			for (;x<ncnpj.length;x++){
				var Obj = eval ("document." + nForm + "." + ncnpj[x])
				if(!verifica_cnpj(Obj)){
				Obj.focus();
				ncnpj = new Array;
				return false;
			}
		}
	}
	return true;
	}

	function verifica_cnpj(S){
		Testa_Tamanho_do_Numero = true;
		Digitos_Verificadores_cnpj = 2;
		Digitos_cnpj = 14; //xx.xxx.xxx/xxxx-xx tem 14 numeros
		/*
		 * Alem de testar os digitos verificadores as funcoes seguintes
		 * tambem devem testar o tamanho dos numeros fornecidos (no caso
		 * desta constante ser True). Se for colocada como False sera'
		 * somente verificada a igualdade dos digitos verificadores.
		*/
		
		// S - � o OBJETO Text e n�o o valor!!!
		//Verifica se o string esta' ok (CPF ou cnpj)
		
		var Original = Limpa_cnpj(S);
		var Gerado = "";
		var Tamanho = Digitos_cnpj;  //tamanho esperado para o cnpj
		
		teste = (( !Testa_Tamanho_do_Numero) || (Testa_Tamanho_do_Numero && Original.length == Tamanho));
		//alert("Resposta da condi��o: "+teste);
		if( teste ) {
			//Gerado = Original;
			//retira digitos verificadores
			Gerado = Original.substring( 0, Original.length - Digitos_Verificadores_cnpj )
			Gerado = Completa_cnpj( Gerado ); //Gera numero completo
			
			cnpj_valido = (Gerado == Original) //compara com original
			//alert("Valor de cnpj_valido: "+cnpj_valido)
			if (!cnpj_valido) {
				alert("CNPJ (cnpj) inválido, favor corrigi-lo!");
				S.select();
				S.focus();
				return false
			}else{
				return true
			}
		} else {
			alert("A quantidade de n\u00fameros inválida, favor corrigir.");
			S.select();
			S.focus();
			return false    //Nao tem o tamanho certo
		}
	}

	function Limpa_cnpj( S_aux2 ) {
		//Retira tudo o que nao for numero,
		// mas n�o tira os n�meros do cnpj
		// S_aux2 - � o objeto Text e n�o o valor. Prestar aten��o!!!
		var SAux = '';
		S = S_aux2.value;
		//alert("cnpj: " + S)
		var pos = 0
		for( ; pos < S.length; pos++ ) {
			if( S.charAt(pos) >= '0' && S.charAt(pos) <= '9' ) {
				SAux = SAux + S.charAt(pos);
			}
			return SAux
		}
	}
	//Completa o numero colocando digitos verificadores
	function Completa_cnpj( S ) {
		//   var SAux = Limpa_String(S);
		var SAux = S;
		var Quantos = Digitos_Verificadores_cnpj;
		var c = 1
		for( ; c <= Quantos; c++ ){
			SAux = SAux + Digito_Verificador_cnpj( SAux );
			return SAux
		}
	}
	//Calcula um digito verificador em funcao do numero
	function Digito_Verificador_cnpj( S ) {
		//   S = Limpa_String(S);
		var soma = 0
		var comprimento = S.length
		var i = 1
		for( ; i <= comprimento; i++ ) {
			// fator = 2,3,4,5,6,7,8,9, 2, 3, 4, 5...
			var fator = 2+( (i-1) % 8 );
			soma = soma + parseInt( S.charAt(comprimento-i) ) * fator
		}
		return ((10*soma) % 11) % 10
	}

	function CNPJ(quadro) {	
		texto = quadro.value;
		if( parseInt(texto.length) == 8 ) {
			texto += "/";
			quadro.value = texto;
		} else if ( parseInt(texto.length) ==13 ) {
			texto += "-";
			quadro.value = texto;
		}
	}
	
	function validarRadio(campo){
		if(campo[0].checked == false && campo[1].checked == false &&
		   campo[2].checked == false && campo[3].checked == false){
			alert('Selecione uma op\u00e7\u00e3o!');
			return false;			
		} else {
			return true;
		}
	}
	
	function valorMOIP(valor){
		valor = valor+"";
		valor = valor.replace("R$", "");		
		valor = valor.replace(" ", "");		
		valor = valor.replace(".", "");		
		valor = valor.replace(",", "");		
		return valor;
	}
	
