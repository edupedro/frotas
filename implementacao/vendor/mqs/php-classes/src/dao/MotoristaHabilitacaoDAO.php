<?php
namespace MQS\dao;

use \MQS\ctrl\MotoristaCTRL;
use \Exception;

	
/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class MotoristaHabilitacaoDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    private $motoristaCTRL;
    
    /**
     * Construtor
     */
    public function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
        
        $this->motoristaCTRL = null;
    }
    
    /**
     * Retorna o objeto
     * @return object
     */
    private function getMotoristaCTRL(){
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
    public function retornaLista($where,$lazy = null){
        $retorno    = array();
        
        $sql 	= "	SELECT
						tab.id AS idMotoristaHabilitacao,
        				tab.created AS dataCadastro,
						tab.anotador_id AS idAnotador,
						tab.ativo ,
                        tab.descricao,
                        tab.numero,
                        tab.validade,
                        tab.pontos,
                        tab.imagem,
                        tab.motorista_id AS idMotorista,
                        m.nome AS motorista,
                        m.cpf,
                        m.telefone
        			FROM
        				motoristas_habilitacoes AS tab
                        INNER JOIN motoristas AS m ON tab.motorista_id = m.id
        			WHERE
        				tab.id IS NOT NULL ";
        if(!empty($where)){
            $sql .= $where;
        }

        try {
            $resultSet = $this->banco->executarSql($sql);
            $qtd = $this->banco->totalDeRegistros($resultSet);
            
            if($qtd > 0){
                $cont = 0;
                while($linha = $this->banco->registroComoObjeto($resultSet)){
                    $retorno[$cont]['idMotoristaHabilitacao']    = $linha->idMotoristaHabilitacao;
                    $retorno[$cont]['dataCadastro']      	= $this->datas->padraoBrasilIncompleto($linha->dataCadastro);
                    $retorno[$cont]['ativo']            	= $linha->ativo;
                    $retorno[$cont]['idAnotador']           = $linha->idAnotador;
                    $retorno[$cont]['motoristaHabilitacao'] = $this->textos->encodeToUtf8($linha->descricao);
                    $retorno[$cont]['numero']		        = $linha->numero;
                    $retorno[$cont]['validade']		        = $this->datas->padraoBrasilIncompleto($linha->validade);
                    $retorno[$cont]['pontos']		        = $linha->pontos;
                    $retorno[$cont]['imagem']		        = $linha->imagem;
                    $retorno[$cont]['idMotorista']		    = $linha->idMotorista;
                    $retorno[$cont]['motorista']			= $this->textos->encodeToUtf8($linha->motorista);                    
                    $retorno[$cont]['cpf']		            = $linha->cpf;
                    $retorno[$cont]['telefone']			    = $linha->telefone;

                    if($lazy){
                        $retorno[$cont]['Motorista']           = $this->getMotoristaCTRL()->retornaID($retorno[$cont]['idMotorista']);
                    }
                    $cont++;
                }
            }
            $this->banco->liberar($resultSet);
            unset($sql, $qtd, $linha);
            
        }catch (\Exception $e){
            throw $e;
        } finally {
            return $retorno;            
        }
    }
    
    /**
     * Retorna quantidade
     *
     * @param string $where
     * @return int
     */
    public function retornaQTD($where){
        $qtd = 0;
        
        $sql 	= "	SELECT
						COUNT(tab.id) AS qtd
        			FROM
        				motoristas_habilitacoes AS tab
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
            
            unset($sql, $linha);            
        }catch (\Exception $e){
            throw $e;
        } finally {
            return $qtd;
        }
    }
    
    /**
     * Inserir
     * 
     * @param array $campos
     * @return int
     */
    public function inserir($campos){
        $novoId = 0;
        $agora	= date("Y-m-d H:i:s");

        $sql = "INSERT INTO motoristas_habilitacoes (
                        created,
                        updated,
                        ativo,
                        anotador_id,
                        descricao,
                        numero,
                        validade,
                        pontos,
                        imagem,
                        motorista_id 
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        'true',
                        '".$campos['idAnotador']."',
						'".$this->textos->encodeToIso($campos['motoristaHabilitacao'])."',
						'".$campos['numero']."',
						'".$this->datas->padraoEuaIncompleto($campos['observacoes'])."',
						'".$campos['pontos']."',
						'".$campos['imagem']."',
                        '".$campos['idMotorista']."'
                    );";
        
        try {
            $novoId = $this->banco->executarSql($sql,true,true,true);
            $this->banco->confirmar();
            unset($campos, $sql);
        } catch (\Exception $e) {
            $this->banco->reverter();
            throw $e;
        } finally {
            return $novoId;
        }
    }
    
    /**
     * Alterar
     *
     * @param array $campos
     * @return void
     */
    public function alterar($campos){
    	$agora	= date("Y-m-d H:i:s");
    	
    	$sql = "UPDATE motoristas_habilitacoes SET ";
    	if(!empty($campos['ativo']))                   { $sql .= " ativo = '".$campos['ativo']."', "; }
    	if(!empty($campos['motoristaHabilitacao']))    { $sql .= " descricao = '".$this->textos->encodeToIso($campos['motoristaHabilitacao'])."', "; }
    	if(!empty($campos['numero']))                  { $sql .= " numero = '".$campos['numero']."', "; }
    	if(!empty($campos['validade']))                { $sql .= " validade = '".$this->datas->padraoEuaIncompleto($campos['validade'])."', "; }
    	if(!empty($campos['pontos']))                  { $sql .= " pontos = '".$campos['pontos']."', "; }
    	if(!empty($campos['imagem']))                  { $sql .= " imagem = '".$this->textos->encodeToIso($campos['observacoes'])."', "; }
    	if(!empty($campos['idMotorista']))             { $sql .= " motorista_id = '".$campos['idMotorista']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = ".$campos['idAnotador']." WHERE id = '".$campos['idMotoristaHabilitacao']."'; ";

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
    public function __destruct(){  

    }
    
}// class
    