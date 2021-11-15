<?php
namespace MQS\ctrl;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\AcessoriosDAO;
use \Exception;
    
/** 
 * Classe para CTRL
 * 
 * @author Eduardo Andrade
 */
class AcessoriosCTRL extends ModelCTRL{
            
    private $acessoriosDAO;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->acessoriosDAO	= new AcessoriosDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
    }
    
    /**
     * Retorna detalhes da funcionalidade
     *
     * @param string $where
     * @return array
     */
    function retornaFuncionalidades($where = NULL){
        try {
            return $this->acessoriosDAO->retornaFuncionalidades($where);            
        } catch (\Exception $e) {
            return array();
        }
    }
    
    /**
     * Retorna funcionalidades e perfil
     *
     * @param string $where
     * @param int $idPerfil
     * @return array
     */
    function retornaFuncionalidadePerfil($where){
        try {
            return $this->acessoriosDAO->retornaFuncionalidadePerfil($where);
        } catch (\Exception $e) {
            return array();
        }
    }
    
    /**
     * Retorna se a funcionalidade pertencente ao perfil
     *
     * @param array $codigos
     * @param int $idPerfil
     * @return boolean
     */
    function existeFuncionalidade($codigos,$idPerfil){
        $retorno	= FALSE;
        
        try {
            $idPerfilAdministrador = $this->getParametroCTRL()->retornaValor("perfil_administrador_id");
            if($idPerfilAdministrador == $idPerfil){ return true; }
            
            $tam		= count($codigos);
            $in 		= "";
            for($i=0;$i<$tam;$i++){
                $in	.= "'".$codigos[$i]."',";
            }
            $in = substr($in,0,-1);
            
            $where	= " AND ap.ativo = 'true' AND ac.codigo IN (".$in.") AND p.id = ".$idPerfil." ";
            $func	= $this->retornaFuncionalidadePerfil($where,$idPerfil);
            if(count($func)>0){
                $retorno = TRUE;
            }
            return $retorno;
        } catch (\Exception $e) {
            return FALSE;
        }        
    }
    
    
    /**
     * Valida se o usuário tem permissão de acesso
     *
     * @param int $idPerfil
     * @param int $funcionalidade
     * @return boolean
     */
    function validaPermissao($idPerfil,$funcionalidade){
        try {
            $perfilAdministrador = $this->getParametroCTRL()->retornaValor("perfil_administrador");
            if($idPerfil == $perfilAdministrador){
                return TRUE;
            }
            return $this->acessoriosDAO->validaPermissao($idPerfil, $funcionalidade);            
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Retorna endereços
     *
     * @param string $where
     * @return array
     */
    function retornaEnderecos($where){
        try {
            return $this->acessoriosDAO->retornaEnderecos($where);
        } catch (\Exception $e) {
            return array();
        }        
    }
    
    /**
     * Retorna o endereço
     *
     * @param string $cep
     * @return array
     */
    function retornaEnderecoCEP($cep){        
        try {
            $retorno	= array();
            $cep		= str_replace("-","",$cep);
            
            $where		= " AND end.cep = '".$cep."' ";
            $resultado	= $this->acessoriosDAO->retornaEnderecos($where);
            if(count($resultado)>0){
                $retorno = $resultado[0];
            }
            return $retorno;            
        } catch (\Exception $e) {
            return false;
        }        
    }
        
    /**
     * Destruir instância
     *
     * @return void
     */
    function __destruct(){

    }
    
}// class
