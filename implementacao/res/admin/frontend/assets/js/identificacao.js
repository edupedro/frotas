
/**
 * Conteúdo JS para identificação
 */
	$(document).ready(function() {

        $("#cpf").mask('99999999999');
        $("#celular").mask('(99) 99999.9999');			
        $("#dataNascimento").mask('99/99/9999');
		$('#valorInformado').mask("#,##0,00", {reverse: true});
		
        $('#formCadastrar').validate({
            rules:{
                nome:{
                    required: true,
                    minlength: 6,
                    maxlength: 100
                },
                email:{
                    required: true,
                    email: true
                },
                dataNascimento:{
                	required: true
                },
                celular:{
                    required: true
                },
                valorInformado:{
                	required: true
                },
                termos:{
                    required: true
                }                
            },
            messages:{
                nome:{
                    required: "Campo obrigat\u00f3rio.",
                    minlength: "Campo deve conter no m\u00ednimo 6 caracteres.",
                    maxlength: "Campo deve conter no m\u00e1ximo 100 caracteres."
                },
                email:{
                    required: "Campo obrigat\u00f3rio.",
                	email: "Campo deve conter um endere\u00e7o de e-mail."
                },
                dataNascimento:{
                    required: "Campo obrigat\u00f3rio."
                },
                celular:{
                    required: "Campo obrigat\u00f3rio."
                },
                valorInformado:{
                    required: "Campo obrigat\u00f3rio."
                },
                termos:{
                    required: "Campo obrigat\u00f3rio."
                }	                
            }
        });
    	
    });
	
	function mudarOpcaoPagamento(tipo){
    	var opcao =  tipo;

    	if(opcao == "ps"){
			$("#divPaypal").hide("slow");
			$("#divPagseguro").show("slow");
			
    	}else{
			$("#divPaypal").show("slow");    
			$("#divPagseguro").hide("slow");
    	}
    }	

	function verificarData(campo){
		var valor = campo.value;
		
		if(valor == "" || valor == "Data de Nascimento"){
			return false;
		}
		
	    $.getJSON('/funcoes_ajax.php?fn=validarData&vl1='+valor, function (dados){
		    if(dados.length == 0){
		    	alert('Erro na data'); 
		    	$('#dataNascimento').val("");
		    }
	    });                                
	}
	
	function verificarCPF(cpf){
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

	function validarCPF(campo){
		var valor = campo.value;
		
		if(valor == "" || valor == "CPF"){
			return false;
		}
		
		if(!verificarCPF(valor)){
	    	alert('Erro no CPF'); 
	    	$('#cpf').val("");
		}
	}
	
	function buscarPessoaEmail(campo){
		valor = campo.value;
		
		if(valor !== ""){
		    $.getJSON('/funcoes_ajax.php?fn=retornaPessoaEmail&vl1='+valor, function (dados){
		    	if(dados.length != 0){
			    	$("#idPessoa").val(dados['idPessoa']);
			    	$("#idPessoaReferencia").val(dados['idPessoaReferencia']);
			    	$("#nome").val(dados['nome']);
			    	$("#email").val(dados['email']);
			    	$("#cpf").val(dados['cpf']);
			    	$("#dataNascimento").val(dados['dataNascimento']);
			    	$("#celular").val(dados['celular']);
		    	}
		    });
		}		
	}
	
	function buscarPessoaCPF(campo){
		valor = campo.value;
		
		if(valor !== ""){
		    $.getJSON('/funcoes_ajax.php?fn=retornaPessoaCPF&vl1='+valor, function (dados){
		    	if(dados.length > 0){
			    	$("#idPessoa").val(dados['idPessoa']);
			    	$("#idPessoaReferencia").val(dados['idPessoaReferencia']);
			    	$("#nome").val(dados['nome']);
			    	$("#email").val(dados['email']);
			    	$("#cpf").val(dados['cpf']);
			    	$("#dataNascimento").val(dados['dataNascimento']);
			    	$("#celular").val(dados['celular']);
		    	}
		    });
		}
	}	

	function salvar(){
	    if($('#formCadastrar').valid()){        	
	    	$('#formCadastrar').submit();
	    }
	    return false;
	}
