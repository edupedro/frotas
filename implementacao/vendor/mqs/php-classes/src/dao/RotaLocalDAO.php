<?php
namespace MQS\dao;

use \Exception;
use \MQS\ctrl\RotaCTRL;
use \MQS\ctrl\LocalCTRL;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class RotaLocalDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    private $rotaCTRL;
    private $localCTRL;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
        
        $this->rotaCTRL = null;
        $this->localCTRL = null;
    }
    
    /**
     * Retorna o objeto
     * @return object
     */
    function getRotaCTRL(){
        if(is_null($this->rotaCTRL)){
            $this->rotaCTRL = new RotaCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->rotaCTRL;
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
						tab.id AS idRotaLocal,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS rota,
 						tab.ordem,
 						tab.posicao,
                        tab.rota_id AS idRota,
                        tab.local_id AS idLocal,
                        r.descricao AS rota,
                        l.descricao AS local,
                        l.latitude,
                        l.longitude
        			FROM            		
        				rotas_locais AS tab
                        INNER JOIN rotas AS r ON tab.rota_id = r.id
                        INNER JOIN locais AS l ON tab.local_id = l.id
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
                    $retorno[$cont]['idRotaLocal']              = $row->idRotaLocal;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['rotas']                    = $this->textos->encodeToUtf8($row->rotas);
                    $retorno[$cont]['ordem']                    = $row->ordem;
                    $retorno[$cont]['posicao']                  = $row->posicao;
                    $retorno[$cont]['idRota']                   = $row->idRota;
                    $retorno[$cont]['rota']                     = $this->textos->encodeToUtf8($row->rota);
                    $retorno[$cont]['idLocal']                  = $row->idLocal;
                    $retorno[$cont]['local']                    = $this->textos->encodeToUtf8($row->local);
                    $retorno[$cont]['latitude']                 = $row->latitude;
                    $retorno[$cont]['longitude']                = $row->longitude;
                    
                    if($lazy){
                        $retorno[$cont]['Rota'] = $this->getRotaCTRL()->retornaID($retorno[$cont]['idRota']);
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
        				rotas_locais AS tab
                        INNER JOIN rotas AS r ON tab.rota_id = r.id
                        INNER JOIN locais AS l ON tab.local_id = l.id
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
            $sql = "INSERT INTO rotas_locais (
                        created,
                        updated,
            			anotador_id,
                        ativo,
                        descricao,
                        ordem,
                        posicao,
                        rota_id,
                        local_id
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        '".$campos['idAnotador']."',
                        'true',
                        '".$this->textos->encodeToIso($campos['rota'])."',
                        '".$campos['ordem']."',
                        '".$campos['posicao']."',
                        '".$campos['idRota']."',
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

    	$sql = "UPDATE rotas_locais SET ";
    	if(!empty($campos['rota']))                    { $sql .= " descricao = '".$this->textos->encodeToIso($campos['rota'])."', "; }
    	if(!empty($campos['idRota']))                  { $sql .= " rota_id = '".$campos['idRota']."', "; }
    	if(!empty($campos['idLocal']))                 { $sql .= " local_id = '".$campos['idLocal']."', "; }
    	if(!empty($campos['ordem']))                   { $sql .= " ordem = '".$campos['ordem']."', "; }
    	if(!empty($campos['posicao']))                 { $sql .= " posicao = '".$campos['posicao']."', "; }
    	if(!empty($campos['ativo']))			       { $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idRotaLocal']."'; ";
    	
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