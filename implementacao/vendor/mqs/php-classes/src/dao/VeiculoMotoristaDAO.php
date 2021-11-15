<?php
namespace MQS\dao;

use \Exception;
use \MQS\ctrl\VeiculoCTRL;
use \MQS\ctrl\MotoristaCTRL;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class VeiculoMotoristaDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    private $veiculoCTRL;
    private $motoristaCTRL;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
        
        $this->veiculoCTRL = null;
        $this->motoristaCTRL = null;
    }
    
    /**
     * Retorna o objeto
     * @return object
     */
    function getVeiculoCTRL(){
        if(is_null($this->veiculoCTRL)){
            $this->veiculoCTRL = new VeiculoCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->veiculoCTRL;
    }    

    /**
     * Retorna o objeto
     * @return object
     */
    function getMotoristaCTRL(){
        if(is_null($this->motoristaCTRL)){
            $this->motoristaCTRL = new MotoristaCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->motoristaCTRL;
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
						tab.id AS idVeiculoMotorista,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS veiculoMotorista,
 						tab.observacoes,
                        tab.veiculo_id AS idVeiculo,
                        tab.motorista_id AS idMotorista,
                        m.descricao AS motorista,
                        m.cpf,
                        v.descricao AS veiculo,
                        v.placa
        			FROM            		
        				veiculos_motoristas AS tab
                        INNER JOIN veiculos AS v ON tab.veiculo_id = v.id
                        INNER JOIN motoristas AS m ON tab.motorista_id = m.id
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
                    $retorno[$cont]['idVeiculoMotorista']       = $row->idVeiculoMotorista;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['veiculoMotorista']         = $this->textos->encodeToUtf8($row->veiculoMotorista);
                    $retorno[$cont]['observacoes']              = $this->textos->encodeToUtf8($row->observacoes);                    
                    $retorno[$cont]['idVeiculo']                = $row->idVeiculo;                    
                    $retorno[$cont]['idMotorista']              = $row->idMotorista;
                    $retorno[$cont]['motorista']                = $this->textos->encodeToUtf8($row->motorista);
                    $retorno[$cont]['cpf']                      = $row->cpf;
                    $retorno[$cont]['veiculo']                  = $this->textos->encodeToUtf8($row->veiculo);
                    $retorno[$cont]['placa']                    = $row->placa;
                    
                    if($lazy){
                        $retorno[$cont]['Veiculo']      = $this->getVeiculoCTRL()->retornaID($retorno[$cont]['idVeiculo']);
                        $retorno[$cont]['Motorista']    = $this->getMotoristaCTRL()->retornaID($retorno[$cont]['idMotorista']);
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
        				veiculos_motoristas AS tab
                        INNER JOIN veiculos AS v ON tab.veiculo_id = v.id
                        INNER JOIN motoristas AS m ON tab.motorista_id = m.id
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
            $sql = "INSERT INTO veiculos_motoristas (
                        created,
                        updated,
            			anotador_id,
                        ativo,
 						descricao,
 						observacoes,
 						veiculo_id,
 						motorista_id
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        '".$campos['idAnotador']."',
                        'true',
 						'".$this->textos->encodeToIso($campos['veiculoMotorista'])."',
 						'".$this->textos->encodeToIso($campos['observacoes'])."',
                        '".$campos['idVeiculo']."',
                        '".$campos['idMotorista']."'
                    );";
            
            $novoId = $this->banco->executarSql($sql,true,true,true);
            
            if($novoId>0 && !empty($campos['dataTermino'])){
                $camposAlt = array();
                $camposAlt['idVeiculoMotorista']   = $novoId;
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

    	$sql = "INSERT INTO veiculos_motoristas (
                        created,
                        updated,
            			anotador_id,
                        ativo,
 						descricao,
 						observacoes,
 						veiculo_id,
 						motorista_id
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        '".$campos['idAnotador']."',
                        'true',
 						'".$this->textos->encodeToIso($campos['veiculoMotorista'])."',
 						'".$this->textos->encodeToIso($campos['observacoes'])."',
                        '".$campos['idVeiculo']."',
                        '".$campos['idMotorista']."'
                    );";
    	
    	$sql = "UPDATE veiculos_motoristas SET ";
    	if(!empty($campos['veiculoMotorista']))        { $sql .= " descricao = '".$this->textos->encodeToIso($campos['veiculoMotorista'])."', "; }
    	if(!empty($campos['idVeiculo']))               { $sql .= " veiculo_id = '".$campos['idVeiculo']."', "; }
    	if(!empty($campos['idMotorista']))             { $sql .= " motorista_id = '".$campos['idMotorista']."', "; }
    	if(!empty($campos['observacoes']))             { $sql .= " observacoes = '".$this->textos->encodeToIso($campos['observacoes'])."', "; }
    	if(!empty($campos['idVeiculo']))               { $sql .= " veiculo_id = '".$campos['idVeiculo']."', "; }
    	if(!empty($campos['idMotorista']))             { $sql .= " motorista_id = '".$campos['idMotorista']."', "; }
    	if(!empty($campos['ativo']))			       { $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idVeiculoMotorista']."'; ";
    	
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