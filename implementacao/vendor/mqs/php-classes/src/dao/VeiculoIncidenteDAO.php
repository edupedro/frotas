<?php
namespace MQS\dao;

use \Exception;
use \MQS\ctrl\VeiculoIncidenteTipoCTRL;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class VeiculoIncidenteDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    private $veiculoIncidenteTipoCTRL;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
        
        $this->veiculoIncidenteTipoCTRL = null;
    }
    
    /**
     * Retorna o objeto
     * @return object
     */
    function getVeiculoIncidenteTipoCTRL(){
        if(is_null($this->veiculoIncidenteTipoCTRL)){
            $this->veiculoIncidenteTipoCTRL = new VeiculoIncidenteTipoCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->veiculoIncidenteTipoCTRL;
    }    
    
    /**
     * Retorna lista de veiculoIncidente
     * 
     * @param string $where
     * @param boolean $lazy
     * @return array
     */
    function retornaLista($where,$lazy){
        $retorno    = array();
        
        $sql 	= "	SELECT 
						tab.id AS idVeiculoIncidente,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS veiculoIncidente,
                        tab.veiculo_incidente_tipo_id AS idVeiculoIncidenteTipo,
                        vit.descricao AS veiculoIncidenteTipo
        			FROM            		
        				veiculos_incidentes AS tab
                        INNER JOIN veiculos_incidentes_tipos AS vit ON tab.veiculo_incidente_tipo_id = vit.id
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
                    $retorno[$cont]['idVeiculoIncidente']       = $row->idVeiculoIncidente;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['veiculoIncidente']         = $this->textos->encodeToUtf8($row->veiculoIncidente);
                    $retorno[$cont]['idVeiculoIncidenteTipo']   = $row->idVeiculoIncidenteTipo;
                    $retorno[$cont]['veiculoIncidenteTipo']     = $this->textos->encodeToUtf8($row->veiculoIncidenteTipo);
                    
                    if($lazy){
                        $retorno[$cont]['VeiculoIncidenteTipo'] = $this->getVeiculoIncidenteTipoCTRL()->retornaID($retorno[$cont]['idVeiculoIncidente']);
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
     * Inserir
     * 
     * @param array $campos
     * @return int
     */
    function inserir($campos){
        $agora	= date("Y-m-d H:i:s");
        
        try {
            $sql = "INSERT INTO veiculos_incidentes (
                    created,
                    updated,
        			anotador_id,
                    ativo,
                    descricao,
                    veiculo_incidente_tipo_id
                ) VALUES (
                    '".$agora."',
                    '".$agora."',
                    '".$campos['idAnotador']."',
                    'true',
                    '".$this->textos->encodeToIso($campos['veiculoIncidente'])."',
                    '".$campos['idVeiculoIncidenteTipo']."'
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

    	$sql = "UPDATE veiculos_incidentes SET ";
    	if(!empty($campos['veiculoIncidente']))        { $sql .= " descricao = '".$this->textos->encodeToIso($campos['veiculoIncidente'])."', "; }
    	if(!empty($campos['idVeiculoIncidenteTipo']))  { $sql .= " veiculo_incidente_tipo_id = '".$campos['idVeiculoIncidenteTipo']."', "; }
    	if(!empty($campos['ativo']))			       { $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idVeiculoIncidente']."'; ";
    	
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