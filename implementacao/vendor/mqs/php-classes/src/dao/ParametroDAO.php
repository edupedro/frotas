<?php
namespace MQS\dao;

use \Exception;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class ParametroDAO{
    
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
     * Retorna o parâmetro
     * 
     * @param string $where
     * @return array
     */
    function retornaLista($where){
        $retorno    = array();
        
        $sql 	= "	SELECT
                        tab.id AS idParametro ,
                        tab.created,
                        tab.descricao AS parametro,
                        tab.valor ,
                        tab.codigo ,
                        tab.ativo ,
                        tab.anotador_id AS idAnotador
                    FROM
                        parametros AS tab
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
                while($linha = $this->banco->registroComoObjeto($resultSet)){
                    $retorno[$cont]['idParametro']		= $linha->idParametro;
                    $retorno[$cont]['dataCadastro']		= $this->datas->padraoBrasilCompleto($linha->created);
                    $retorno[$cont]['parametro']		= $this->textos->encodeToUtf8($linha->parametro);
                    $retorno[$cont]['valor']			= $this->textos->encodeToUtf8($linha->valor);
                    $retorno[$cont]['codigo']			= $linha->codigo;
                    $retorno[$cont]['ativo']			= $linha->ativo;
                    $retorno[$cont]['idAnotador']		= $linha->idAnotador;
            
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
        				parametros AS tab
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
     * Retorna o valor do parâmetro
     * 
     * @param string codigo
     * @return string
     */
    function retornaValor($codigo){
        $sql = "SELECT valor FROM parametros WHERE codigo = '".$codigo."' ";
        
        try {
            $resultSet = $this->banco->executarSql($sql);
            $retorno = $this->banco->registroComoObjeto($resultSet)->valor;
        }catch (\Exception $e){
            return "";            
        } finally {
            unset($sql);            
        }        
        return $retorno;
    }
    
    /**
     * Alterar
     *
     * @param array $campos
     * @return void
     */
    function alterar($campos){
        $agora = date("Y-m-d H:i:s");
        
        $sql = "UPDATE parametros SET ";
        if(!empty($campos['parametro']))	{ $sql .= " descricao = '".$this->textos->encodeToIso($campos['parametro'])."', "; }
        if(!empty($campos['valor']))		{ $sql .= " valor = '".$this->textos->encodeToIso($campos['valor'])."', "; }
        if(!empty($campos['codigo']))		{ $sql .= " codigo = '".$campos['codigo']."', "; }
        if(!empty($campos['ativo']))		{ $sql .= " ativo = '".$campos['ativo']."', "; }
        $sql .= " updated = '".$agora."', anotador_id = ".$campos['idAnotador']." WHERE id = '".$campos['idParametro']."'; ";
        
        try {
            $this->banco->executarSql($sql,true,true,false);
            $this->banco->confirmar();
        } catch (Exception $e) {
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

