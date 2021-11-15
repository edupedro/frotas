<?php
namespace MQS\dao;

use \Exception;
use \MQS\ctrl\VeiculoMotoristaCTRL;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class VeiculoViagemDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    private $veiculoMotoristaCTRL;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
        
        $this->veiculoMotoristaCTRL = null;
    }
    
    /**
     * Retorna o objeto
     * @return object
     */
    function getVeiculoMotoristaCTRL(){
        if(is_null($this->veiculoMotoristaCTRL)){
            $this->veiculoMotoristaCTRL = new VeiculoMotoristaCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->veiculoMotoristaCTRL;
    }    
    
    /**
     * Retorna lista
     * 
     * @param string $where
     * @param boolean $lazy
     * @return array
     */
    function retornaLista($where,$lazy){
        $retorno    = array();
        
        $sql 	= "	SELECT 
						tab.id AS idVeiculoViagem,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS veiculoViagem,
 						tab.data_inicio,
 						tab.data_termino,
 						tab.situacao,
 						tab.marcador,
 						tab.observacoes,
                        tab.veiculo_motorista_id AS idVeiculoMotorista,
                        vm.descricao AS veiculoMotorista
        			FROM            		
        				veiculos_viagens AS tab
                        INNER JOIN veiculos_motoristas AS vm ON tab.veiculo_motorista_id = vm.id
        			WHERE
        				tab.id IS NOT NULL ";
        if(!empty($where)){
            $sql .= $where;
        }
        
        try {
            $resultSet = $this->banco->executarSql($sql);
            $qtd = $this->banco->totalDeRegistros($resultSet);
            
            if ($qtd > 0) {
                $cont = 0;
                while($row = $this->banco->registroComoObjeto($resultSet)){
                    $retorno[$cont]['idVeiculoViagem']          = $row->idVeiculoViagem;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['veiculoViagem']            = $this->textos->encodeToUtf8($row->veiculoViagem);
                    $retorno[$cont]['dataInicio']               = $this->datas->padraoBrasilCompleto($row->data_inicio);
                    $retorno[$cont]['dataTermino']              = $this->datas->padraoBrasilCompleto($row->data_termino);
                    $retorno[$cont]['situacao']                 = $row->situacao;                    
                    $retorno[$cont]['marcador']                 = $row->marcador;
                    $retorno[$cont]['observacoes']              = $this->textos->encodeToUtf8($row->observacoes);
                    $retorno[$cont]['idVeiculoMotorista']       = $row->idVeiculoMotorista;
                    $retorno[$cont]['veiculoMotorista']         = $this->textos->encodeToUtf8($row->veiculoMotorista);
                    
                    if($lazy){
                        $retorno[$cont]['VeiculoMotorista'] = $this->getVeiculoMotoristaCTRL()->retornaID($retorno[$cont]['idVeiculoMotorista']);
                    }                
                    $cont++;
                }
            }
            return $retorno;
        }catch (\Exception $e){
            throw $e;
        } finally {
            $this->banco->liberar($resultSet);
            unset($sql, $resultSet, $qtd);
        }
    }
        
    /**
     * Retorna QTD
     *
     * @param string $where
     * @return array
     */
    function retornaQTD($where){
        
        $sql 	= "	SELECT
						COUNT(tab.id) AS qtd
        			FROM            		
        				veiculos_viagens AS tab
                        INNER JOIN veiculos_motoristas AS vm ON tab.veiculo_motorista_id = vm.id
        			WHERE
        				tab.id IS NOT NULL ";
        if(!empty($where)){
            $sql .= $where;
        }
        
        try {
            
            $resultSet = $this->banco->executarSql($sql);
            $linha = $this->banco->registroComoObjeto($resultSet);
            $qtd = $linha->qtd;
            
            return $qtd;
        }catch (\Exception $e){
            throw $e;
        } finally {
            $this->banco->liberar($resultSet);
            unset($sql, $resultSet, $qtd);
        }
    }
    
    /**
     * Inserir
     * 
     * @param array $campos
     * @return int
     */
    function inserir($campos){
        $agora	= date("Y-m-d H:i:s");
        
        try {
            $sql = "INSERT INTO veiculos_viagens (
                        created,
                        updated,
            			anotador_id,
                        ativo,
 						descricao,
 						data_inicio,
 						situacao,
 						marcador,
 						observacoes,
                        veiculo_motorista_id
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        '".$campos['idAnotador']."',
                        'true',
 						'".$this->textos->encodeToIso($campos['veiculoViagem'])."',
 						'".$this->datas->padraoEuaCompleto($campos['dataInicio'])."',
                        '".$campos['situacao']."',
                        '".$campos['marcador']."',
 						'".$this->textos->encodeToIso($campos['observacoes'])."',
                        '".$campos['idVeiculoMotorista']."'
                    );";
            
            $novoId = $this->banco->executarSql($sql,true,true,true);
            
            if($novoId>0 && !empty($campos['dataTermino'])){
                $camposAlt = array();
                $camposAlt['idVeiculoViagem']   = $novoId;
                $camposAlt['dataTermino']       = $campos['dataTermino'];
                
                $this->alterar($camposAlt);    
            }            
            
            $this->banco->confirmar();
            return $novoId;
        } catch (\Exception $e) {
            $this->banco->reverter();
            throw $e;
        } finally {
            unset($campos, $sql);
        }
    }
    
    /**
     * Alterar
     *
     * @param array $campos
     * @return int
     */
    function alterar($campos){
    	$agora	= date("Y-m-d H:i:s");

    	$sql = "UPDATE veiculos_viagens SET ";
    	if(!empty($campos['veiculoViagem']))           { $sql .= " descricao = '".$this->textos->encodeToIso($campos['veiculoViagem'])."', "; }
    	if(!empty($campos['idVeiculoMotorista']))      { $sql .= " veiculo_motorista_id = '".$campos['idVeiculoMotorista']."', "; }
    	if(!empty($campos['dataInicio']))              { $sql .= " data_inicio = '".$this->datas->padraoEuaCompleto($campos['dataInicio'])."', "; }
    	if(!empty($campos['dataTermino']))             { $sql .= " data_termino = '".$this->datas->padraoEuaCompleto($campos['dataTermino'])."', "; }    	
    	if(!empty($campos['situacao']))                { $sql .= " situacao = '".$campos['situacao']."', "; }
    	if(!empty($campos['marcador']))                { $sql .= " marcador = '".$campos['marcador']."', "; }
    	if(!empty($campos['ativo']))			       { $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idVeiculoViagem']."'; ";
    	
    	try {
    	    $this->banco->executarSql($sql,true,true,false);
    	    $this->banco->confirmar();
    	} catch (\Exception $e) {
    	    $this->banco->reverter();
    	    throw $e;
    	} finally {
    	    unset($campos, $sql);
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

?>