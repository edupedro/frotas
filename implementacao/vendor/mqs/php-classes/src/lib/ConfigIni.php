<?php
namespace MQS\lib;

ini_set('display_errors', TRUE);
date_default_timezone_set('America/Sao_Paulo');

/**
 * Classe de configurações iniciais
 *
 * @author Eduardo Andrade
 */
class ConfigIni{
    
    const AMBIENTE = 0; // (desenvolvimento 0, produção 1)
    const MESES_CONSULTA_GRAFICO = 6;
    const ITENS_TABELAS = 10;
    const IDIOMA_PADRAO = '';
    
    const TITULO_RELATORIO              = 'Frotas';
    const googleAPIkey		            = '';
    const API_GOOGLE_MARKERCLUSTERER    = "/res/admin/assets/js/markerclusterer/src/markerclusterer.js";
    const API_GOOGLE_INFOBUBBLE         = "/res/admin/assets/js/infobubble/src/infobubble.js";
    
    private $constanteSMTP = array(
    		"SERVIDOR_SMTP" => "mail.grupomqs.com.br",
    		"USUARIO_SMTP" => "contato@grupomqs.com.br",
    		"SENHA_SMTP" => "mqs@contato1",
    		"PORTA_SMTP" => "587",
    		"FROM_SMTP" => "contato@grupomqs.com.br"
    ); 
    private $constanteMSG = array(
    		"MSG_NOVA_SENHA1"	=> "Uma nova senha foi gerada e enviada para o e-mail: ",
    		"MSG_NOVA_SENHA2"	=> " (lembre-se de verificar o lixo eletr&ocirc;nico).",
    		"MSG_LOGIN_ERRO1"	=> "Dados incorretos!",
    		"MSG_LOGIN_ERRO2"	=> "Login ou senha incorretos.",
    		"MSG_LOGIN_VAZIO"	=> "Favor informe seu CPF e senha"
    );

    /**
     * Construtor
     */
    function __construct(){
    	
    }
    
    /**
     * Retorna valor da constante
     * @param string $posicao
     * @param string $listagem
     * @return string
     */
    function getConstante($posicao,$listagem){
    	$valor = "";
    	
    	$lista = $this->constanteSMTP;
    	if($listagem == "MSG"){
    	    $lista = $this->constanteMSG;    	    
    	}
    	
    	if (array_key_exists($posicao, $lista)) {
    		$valor = $lista[$posicao];
    	}    		
    	return $valor;
    }
    
	/**
     * Destruir instância
     */
    function __destruct(){            

    }
    
}//class