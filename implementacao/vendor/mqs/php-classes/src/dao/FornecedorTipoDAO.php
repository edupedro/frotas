<?php
namespace MQS\dao;

use \Exception;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class FornecedorTipoDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
    }
    
    /**
     * Retorna lista de fornecedorTipo
     * 
     * @param string $where
     * @param boolean $lazy
     * @return array
     */
    function retornaLista($where,$lazy){
        $retorno    = array();
        
        $sql 	= "	SELECT 
						tab.id AS idFornecedorTipo,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS fornecedorTipo,
                        tab.codigo,
                        tab.observacoes
        			FROM            		
        				fornecedores_tipos AS tab
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
                    $retorno[$cont]['idFornecedorTipo']   = $row->idFornecedorTipo;
                    $retorno[$cont]['dataCadastro']       = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']         = $row->anotador_id;
                    $retorno[$cont]['ativo']              = $row->ativo;
                    $retorno[$cont]['fornecedorTipo']     = $this->textos->encodeToUtf8($row->fornecedorTipo);
                    $retorno[$cont]['codigo']             = $row->codigo;
                    $retorno[$cont]['observacoes']        = $this->textos->encodeToUtf8($row->observacoes);
                    
                    if($lazy){
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
        				fornecedores_tipos AS tab
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
            $sql = "INSERT INTO fornecedores_tipos (
                        created,
                        updated,
            			anotador_id,
                        ativo,
                        descricao,
                        codigo,
                        observacoes
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        '".$campos['idAnotador']."',
                        'true',
                        '".$this->textos->encodeToIso($campos['fornecedorTipo'])."',
                        '".$campos['codigo']."',
                        '".$this->textos->encodeToIso($campos['observacoes'])."'
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

    	$sql = "UPDATE fornecedores_tipos SET ";
    	if(!empty($campos['fornecedorTipo']))       { $sql .= " descricao = '".$this->textos->encodeToIso($campos['fornecedorTipo'])."', "; }
    	if(!empty($campos['observacoes']))          { $sql .= " observacoes = '".$this->textos->encodeToIso($campos['observacoes'])."', "; }
    	if(!empty($campos['codigo']))			    { $sql .= " codigo = '".$campos['codigo']."', "; }
    	if(!empty($campos['ativo']))			    { $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idFornecedorTipo']."'; ";
    	
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