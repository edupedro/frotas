<?php

	session_start();
	require_once("vendor/autoload.php");
	
	if(!defined('DS')) { define('DS',DIRECTORY_SEPARATOR); }
	if(!defined('ROOT')) { define('ROOT',dirname(__FILE__)); }
	
	use \MQS\lib\ConfigIni;
	use \MQS\lib\Banco;
	use \MQS\lib\Datas;
	use \MQS\lib\Textos;
	
	use \MQS\ctrl\AcessoriosCTRL;
	use \MQS\ctrl\ParametroCTRL;
	use \MQS\ctrl\UsuarioCTRL;
	use \MQS\ctrl\PerfilCTRL;
	
	use \MQS\ctrl\MotoristaCTRL;
	
	
	use \MQS\ctrl\FornecedorTipoCTRL;
	use \MQS\ctrl\VeiculoIncidenteTipoCTRL;
	use \MQS\ctrl\FornecedorCTRL;
	use \MQS\ctrl\ProprietarioCTRL;
	use \MQS\ctrl\LocalCTRL;
	use \MQS\ctrl\VeiculoCTRL;
	use \MQS\ctrl\UnidadeCTRL;
	use \MQS\ctrl\RotaCTRL;
	
	$banco     = new Banco(ConfigIni::AMBIENTE);
    $datas     = new Datas();
    $textos    = new Textos();
        
    $acessoriosCTRL     	      = new AcessoriosCTRL($banco, $datas, $textos);
    $parametroCTRL      	      = new ParametroCTRL($banco, $datas, $textos);
    $usuarioCTRL      		      = new UsuarioCTRL($banco, $datas, $textos);
    $perfilCTRL			          = new PerfilCTRL($banco, $datas, $textos);
    
    $motoristaCTRL                = new MotoristaCTRL($banco, $datas, $textos);    
    
    $fornecedorTipoCTRL           = new FornecedorTipoCTRL($banco, $datas, $textos);
    $veiculoIncidenteTipoCTRL     = new VeiculoIncidenteTipoCTRL($banco, $datas, $textos);
    $fornecedorCTRL               = new FornecedorCTRL($banco, $datas, $textos);
    $proprietarioCTRL             = new ProprietarioCTRL($banco, $datas, $textos);
    $localCTRL                    = new LocalCTRL($banco, $datas, $textos);
    $veiculoCTRL                  = new VeiculoCTRL($banco, $datas, $textos);
    $unidadeCTRL                  = new UnidadeCTRL($banco, $datas, $textos);
    $rotaCTRL                     = new RotaCTRL($banco, $datas, $textos);
    
	try{
	
		if($_SERVER['REQUEST_METHOD'] == "POST"){
		    
		    $_POST['fn'] = $_POST['fn1'];
		    if(isset($_POST['fn2'])){
		        $_POST['fn'] = $_POST['fn2'];
		    }
		    
		    // recebendo POST
		    $funcao		= $_POST['fn'];
			$valores	= $_POST;
		}else{
			// recebendo GET
			$funcao     = $_GET['fn'];
			$valores	= $_GET;
		}
        
        // seleção da função	
        switch($funcao){
        	case "retornaEnderecoCEP":
        		$resultados	= retornaEnderecoCEP($valores,$acessoriosCTRL);
        		break;
        	case "validarData":
        	    $resultados	= validarData($valores,$datas);
        	    break;
        	case "retornaFuncionalidade":
        	    $resultados	= retornaEntidade('funcionalidade', $valores, $acessoriosCTRL, $textos, $usuarioCTRL);
        	    break;        	    
        	case "retornaPerfil":
        	    $resultados	= retornaEntidade('perfil', $valores, $perfilCTRL, $textos, $usuarioCTRL);
        	    break; 
        	case "retornaUsuario":
        	    $resultados	= retornaEntidade('usuario', $valores, $usuarioCTRL, $textos, $usuarioCTRL);
        	    break;
        	case "retornaParametro":
        	    $resultados	= retornaEntidade('parametro', $valores, $parametroCTRL, $textos, $usuarioCTRL);
        	    break; 
        	case "retornaFornecedorTipo":
        	    $resultados	= retornaEntidade('fornecedorTipo', $valores, $fornecedorTipoCTRL, $textos, $usuarioCTRL);
        	    break;
        	case "retornaVeiculoIncidenteTipo":
        	    $resultados	= retornaEntidade('veiculoIncidenteTipo', $valores, $veiculoIncidenteTipoCTRL, $textos, $usuarioCTRL);
        	    break;
        	case "retornaFornecedor":
        	    $resultados	= retornaEntidade('fornecedor', $valores, $fornecedorCTRL, $textos, $usuarioCTRL);
        	    break;
        	case "retornaProprietario":
        	    $resultados	= retornaEntidade('proprietario', $valores, $proprietarioCTRL, $textos, $usuarioCTRL);
        	    break;
        	case "retornaLocal":
        	    $resultados	= retornaEntidade('local', $valores, $localCTRL, $textos, $usuarioCTRL);
        	    break;
        	case "retornaVeiculo":
        	    $resultados	= retornaEntidade('veiculo', $valores, $veiculoCTRL, $textos, $usuarioCTRL);
        	    break;
        	case "retornaUnidade":
        	    $resultados	= retornaEntidade('unidade', $valores, $unidadeCTRL, $textos, $usuarioCTRL);
        	    break;
        	case "retornaRota":
        	    $resultados	= retornaEntidade('rota', $valores, $rotaCTRL, $textos, $usuarioCTRL);
        	    break;
        	case "retornaMotorista":
        	    $resultados	= retornaEntidade('motorista', $valores, $motoristaCTRL, $textos, $usuarioCTRL);
        	    break;        	    
        	default:
        	    $resultados = array();
                break;        	   			    
        }
        
        echo json_encode($resultados);
        unset($acessoriosCTRL,$parametroCTRL,$usuarioCTRL);
        
	}catch(\Exception $e){
		echo json_encode(array($e->getMessage()));
	}
        
	/**
	 * Retorna informação do endereço
	 *
	 * @param array $valor
	 * @param object $acessoriosCTRL
	 * @return array
	 */
	function retornaEnderecoCEP($valores,$acessoriosCTRL){
		$valor = $valores['vl1'];
		return $acessoriosCTRL->retornaEnderecoCEP($valor);
	}	
	
	/**
	 * Retorna informações da entidade
	 *
	 * @param string $tipo
	 * @param array $valores
	 * @param object $controllerCTRL
	 * @param object $textos
	 * @param object $usuarioCTRL
	 * @return array
	 */
	function retornaEntidade($tipo, $valores, $controllerCTRL, $textos, $usuarioCTRL){
		$retorno = array();
		$lazy = FALSE;
		$id	= $valores['vl1'];
		
		if(!empty($id)){
		    if($tipo == 'perfil'){ $lazy = TRUE; }
		    
		    if($tipo == 'funcionalidade'){
		        $lista = $controllerCTRL->retornaFuncionalidades(" AND tab.id = ".$id." ");
		    }else{
		        $lista = $controllerCTRL->retornaID($id,$lazy);
		    }
		    
			if(count($lista) > 0){
				$ativo = "Sim";
				if($lista['ativo'] == 'false'){
					$ativo = "Não";
				}
				$lista['ativo'] = $ativo;
				
				if($tipo != 'funcionalidade'){
				    $usuario	= $usuarioCTRL->retornaID($lista['idAnotador']);
				    $lista['anotador'] = $usuario['nome'];
				}				
				
				if($tipo == 'usuario'){
				    $liberado = "Liberado";
				    if($lista['liberado'] == 'false'){
				        $liberado = "Não Liberado";
				    }
				    $lista['liberadoExib'] = $liberado;
				}
				
				$retorno = $lista;
			}
		}else{
			$where = " AND tab.ativo = 'true' ";
			
			if($tipo == 'perfil'){ $lazy = TRUE; }
			
			if($tipo == 'funcionalidade'){
			    $lista = $controllerCTRL->retornaFuncionalidades(" ORDER BY tab.descricao ASC ");
			}else{
			    $lista = $controllerCTRL->retornaLista($where,$lazy);
			}
			$qtd      = count($lista);
			for($i=0;$i<$qtd;$i++){
				$ativo = "Sim";
				if($lista[$i]['ativo'] == 'false'){
					$ativo = "Não";
				}
				$lista[$i]['ativo'] = $ativo;
				
				if($tipo != 'funcionalidade'){
				    $usuario	= $usuarioCTRL->retornaID($lista[$i]['idAnotador']);
				    $lista[$i]['anotador'] = $usuario['usuario'];
				}				
				
				if($tipo == 'usuario'){
				    $liberado = "Liberado";
				    if($lista[$i]['liberado'] == 'false'){
				        $liberado = "Não Liberado";
				    }
				    $lista[$i]['liberadoExib'] = $liberado;
				}				
				
				$retorno[$i] = $lista[$i];
			}
		}
		
		return $retorno;
	}
	
	/**
	 * Validação para campos data
	 *
	 * @param array $valores
	 * @param object $datas
	 * @return int
	 */
	function validarData($valores,$datas){
	    $retorno  = array();
	    $data     = $valores['vl1'];
	    
	    $dataBR		= $datas->separaDataIncompleta($data);
	    if(checkdate($dataBR[1], $dataBR[2], $dataBR[0])){
	        $retorno[0] = "OK";
	    }
	    
	    return $retorno;
	}
	

	/**
	 * Salvar o atendimento
	 *
	 * @param array $valores
	 * @param object $agendaPacienteCTRL
	 * @param object $textos
	 * @param object $usuarioCTRL
	 * @return array
	 */
	function salvarAtendimento($valores, $agendaPacienteCTRL, $textos, $usuarioCTRL){
	    $retorno['erro'] = "false";
	    
	    $campos = array();
	    $campos['validacao'] = "false";
	    
	    if(!empty($valores['idAgenda1'])){
	        $campos['idAgendaPaciente'] = $valores['idAgenda1'];
	        if($valores['atendido1'] == "on"){ 
	            $campos['dataAtendimento'] = date("d/m/Y H:i:s");
	            $campos['atendido'] = "true"; 
	        }else{
	            $campos['dataAtendimento'] = "";
	            $campos['atendido'] = "false";	            
	        }
	    }else{
	        $campos['idAgendaPaciente'] = $valores['idAgenda2'];
	        if($valores['atendido2'] == "on"){
	            $campos['dataAtendimento'] = date("d/m/Y H:i:s");
	            $campos['atendido'] = "true";
	        }else{
	            $campos['dataAtendimento'] = "";
	            $campos['atendido'] = "false";
	        }	        
	    }
	    
	    try{
	        $agendaPacienteCTRL->alterar($campos);
	        $retorno['erro'] = "false";
	    }catch (\Exception $e){
	        $retorno['erro'] = "true";	        
	    }
	    
	    return $retorno;
	}
	
?>
