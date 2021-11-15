<?php
namespace MQS\dao;

use \Exception;
use \MQS\ctrl\VeiculoViagemCTRL;
use \MQS\ctrl\LocalCTRL;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class VeiculoViagemLocalDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    private $veiculoViagemCTRL;
    private $localCTRL;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
        
        $this->veiculoViagemCTRL = null;
        $this->localCTRL = null;
    }
    
    /**
     * Retorna o objeto
     * @return object
     */
    function getVeiculoViagemCTRL(){
        if(is_null($this->veiculoViagemCTRL)){
            $this->veiculoViagemCTRL = new VeiculoViagemCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->veiculoViagemCTRL;
    }    

    /**
     * Retorna o objeto
     * @return object
     */
    function getLocalCTRL(){
        if(is_null($this->localCTRL)){
            $this->localCTRL = new LocalCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->localCTRL;
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
						tab.id AS idVeiculoViagemLocal,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS veiculoViagemLocal,
 						tab.ordem,
                        tab.veiculo_viagem_id AS idVeiculoViagem,
                        tab.local_id AS idLocal,
                        vv.descricao AS veiculoViagem,
                        loc.descricao AS local,
                        loc.latitude,
                        loc.longitude
        			FROM            		
        				veiculos_viagens_locais AS tab
                        INNER JOIN veiculos_viagens AS vv ON tab.veiculo_viagem_id = vv.id
                        INNER JOIN locais AS loc ON tab.local_id = loc.id
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
                    $retorno[$cont]['idVeiculoViagemLocal']     = $row->idVeiculoViagemLocal;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['veiculoViagemLocal']       = $this->textos->encodeToUtf8($row->veiculoViagemLocal);
                    $retorno[$cont]['ordem']                    = $row->ordem;
                    $retorno[$cont]['idVeiculoViagem']          = $row->idVeiculoViagem;
                    $retorno[$cont]['veiculoViagem']            = $this->textos->encodeToUtf8($row->veiculoViagem);
                    $retorno[$cont]['idLocal']                  = $row->idLocal;
                    $retorno[$cont]['local']                    = $this->textos->encodeToUtf8($row->local);
                    $retorno[$cont]['latitude']                 = $row->latitude;
                    $retorno[$cont]['longitude']                = $row->longitude;
                    
                    if($lazy){
                        $retorno[$cont]['VeiculoViagem'] = $this->getVeiculoViagemCTRL()->retornaID($retorno[$cont]['idVeiculoViagem']);
                        $retorno[$cont]['Local'] = $this->getLocalCTRL()->retornaID($retorno[$cont]['idLocal']);
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
        				veiculos_viagens_locais AS tab
                        INNER JOIN veiculos_viagens AS vv ON tab.veiculo_viagem_id = vv.id
                        INNER JOIN locais AS loc ON tab.local_id = loc.id
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
            $sql = "INSERT INTO veiculos_viagens_locais (
                        created,
                        updated,
            			anotador_id,
                        ativo,
                        descricao,
                        ordem,
                        veiculo_viagem_id,
                        local_id
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        '".$campos['idAnotador']."',
                        'true',
                        '".$this->textos->encodeToIso($campos['veiculoViagemLocal'])."',
                        '".$campos['ordem']."',
                        '".$campos['idVeiculoViagem']."',
                        '".$campos['idLocal']."'
                    );";
            
            $novoId = $this->banco->executarSql($sql,true,true,true);
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

    	$sql = "UPDATE veiculos_viagens_locais SET ";
    	if(!empty($campos['veiculoViagemLocal']))      { $sql .= " descricao = '".$this->textos->encodeToIso($campos['veiculoViagemLocal'])."', "; }
    	if(!empty($campos['idVeiculoViagem']))         { $sql .= " veiculo_viagem_id = '".$campos['idVeiculoViagem']."', "; }
    	if(!empty($campos['idLocal']))                 { $sql .= " local_id = '".$campos['idLocal']."', "; }
    	if(!empty($campos['ordem']))                   { $sql .= " ordem = '".$campos['ordem']."', "; }
    	if(!empty($campos['ativo']))			       { $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idVeiculoViagemLocal']."'; ";
    	
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