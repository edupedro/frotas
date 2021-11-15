<?php
namespace MQS\dao;

use \Exception;
use \MQS\ctrl\VeiculoCTRL;
use \MQS\ctrl\UnidadeCTRL;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class UnidadeVeiculoDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    private $veiculoCTRL;
    private $unidadeCTRL;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
        
        $this->veiculoCTRL = null;
        $this->unidadeCTRL = null;
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
    function getUnidadeCTRL(){
        if(is_null($this->unidadeCTRL)){
            $this->unidadeCTRL = new UnidadeCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->unidadeCTRL;
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
						tab.id AS idUnidadeVeiculo,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS unidadeVeiculo,
 						tab.observacoes,
                        tab.veiculo_id AS idVeiculo,
                        tab.unidade_id AS idUnidade,
                        u.descricao AS unidade,
                        u.codigo,
                        v.descricao AS veiculo,
                        v.placa
        			FROM            		
        				unidades_veiculos AS tab
                        INNER JOIN veiculos AS v ON tab.veiculo_id = v.id
                        INNER JOIN unidades AS u ON tab.unidade_id = u.id
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
                    $retorno[$cont]['idUnidadeVeiculo']         = $row->idUnidadeVeiculo;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['unidadeVeiculo']           = $this->textos->encodeToUtf8($row->unidadeVeiculo);
                    $retorno[$cont]['observacoes']              = $this->textos->encodeToUtf8($row->observacoes);                    
                    $retorno[$cont]['idVeiculo']                = $row->idVeiculo;                    
                    $retorno[$cont]['idUnidade']                = $row->idUnidade;
                    $retorno[$cont]['unidade']                  = $this->textos->encodeToUtf8($row->unidade);
                    $retorno[$cont]['codigo']                   = $row->codigo;
                    $retorno[$cont]['veiculo']                  = $this->textos->encodeToUtf8($row->veiculo);
                    $retorno[$cont]['placa']                    = $row->placa;
                    
                    if($lazy){
                        $retorno[$cont]['Veiculo']      = $this->getVeiculoCTRL()->retornaID($retorno[$cont]['idVeiculo']);
                        $retorno[$cont]['Unidade']      = $this->getUnidadeCTRL()->retornaID($retorno[$cont]['idUnidade']);
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
        				unidades_veiculos AS tab
                        INNER JOIN veiculos AS v ON tab.veiculo_id = v.id
                        INNER JOIN unidades AS m ON tab.unidade_id = m.id
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
            $sql = "INSERT INTO unidades_veiculos (
                        created,
                        updated,
            			anotador_id,
                        ativo,
 						descricao,
 						observacoes,
 						veiculo_id,
 						unidade_id
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        '".$campos['idAnotador']."',
                        'true',
 						'".$this->textos->encodeToIso($campos['unidadeVeiculo'])."',
 						'".$this->textos->encodeToIso($campos['observacoes'])."',
                        '".$campos['idVeiculo']."',
                        '".$campos['idUnidade']."'
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

    	$sql = "UPDATE unidades_veiculos SET ";
    	if(!empty($campos['unidadeVeiculo']))        { $sql .= " descricao = '".$this->textos->encodeToIso($campos['unidadeVeiculo'])."', "; }
    	if(!empty($campos['observacoes']))           { $sql .= " observacoes = '".$this->textos->encodeToIso($campos['observacoes'])."', "; }
    	if(!empty($campos['codigo']))                { $sql .= " codigo = '".$campos['codigo']."', "; }
    	if(!empty($campos['idVeiculo']))             { $sql .= " veiculo_id = '".$campos['idVeiculo']."', "; }
    	if(!empty($campos['idUnidade']))             { $sql .= " unidade_id = '".$campos['idUnidade']."', "; }
    	if(!empty($campos['ativo']))			     { $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idUnidadeVeiculo']."'; ";
    	
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