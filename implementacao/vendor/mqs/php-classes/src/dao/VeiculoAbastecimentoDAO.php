<?php
namespace MQS\dao;

use \Exception;
use \MQS\ctrl\FornecedorCTRL;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class VeiculoAbastecimentoDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    private $fornecedorCTRL;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
        
        $this->fornecedorCTRL = null;
    }
    
    /**
     * Retorna o objeto
     * @return object
     */
    function getFornecedorCTRL(){
        if(is_null($this->fornecedorCTRL)){
            $this->fornecedorCTRL = new FornecedorCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->fornecedorCTRL;
    }
    
    /**
     * Retorna lista de veiculoAbastecimento
     * 
     * @param string $where
     * @param boolean $lazy
     * @return array
     */
    function retornaLista($where,$lazy){
        $retorno    = array();
        
        $sql 	= "	SELECT 
						tab.id AS idVeiculoAbastecimento,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS veiculoAbastecimento,
                        tab.data_abastecimento ,
                        tab.combustivel ,
                        tab.quantidade ,
                        tab.valor_unitario ,
                        tab.valor_pago ,
                        tab.observacoes ,
                        tab.veiculo_id AS idVeiculo ,
                        tab.fornecedor_id AS idFornecedor,
                        f.descricao AS fornecedor
        			FROM            		
        				veiculos_abastecimentos AS tab
                        INNER JOIN fornecedores AS f ON tab.fornecedor_id = pc.id
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
                    $retorno[$cont]['idVeiculoAbastecimento']   = $row->idVeiculoAbastecimento;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['idVeiculo']                = $row->idVeiculo;
                    $retorno[$cont]['idFornecedor']             = $row->idFornecedor;
                    $retorno[$cont]['veiculoAbastecimento']     = $this->textos->encodeToUtf8($row->veiculoAbastecimento);
                    $retorno[$cont]['fornecedor']               = $this->textos->encodeToUtf8($row->fornecedor);
                    $retorno[$cont]['dataAbastecimento']        = $this->datas->padraoBrasilCompleto($row->data_abastecimento);
                    $retorno[$cont]['combustivel']              = $row->combustivel;
                    $retorno[$cont]['quantidade']               = $row->quantidade;
                    $retorno[$cont]['valorUnitario']            = $row->valor_unitario;
                    $retorno[$cont]['valorPago']                = $row->valor_pago;
                    $retorno[$cont]['observacoes']              = $this->textos->encodeToUtf8($row->observacoes);
                    
                    if($lazy){
                        $retorno[$cont]['Fornecedor']     = $this->getFornecedorCTRL()->retornaID($retorno[$cont]['idVeiculoAbastecimento']);
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
            $sql = "INSERT INTO veiculos_abastecimentos (
                    created,
                    updated,
        			anotador_id,
                    ativo,
                    descricao,
                    fornecedor_id,
                    veiculo_id,
                    data_abastecimento,
                    combustivel,
                    quantidade,
                    valor_unitario,
                    valor_pago,
                    observacoes
                ) VALUES (
                    '".$agora."',
                    '".$agora."',
                    '".$campos['idAnotador']."',
                    'true',
                    '".$this->textos->encodeToIso($campos['veiculoAbastecimento'])."',
                    '".$campos['idFornecedor']."',
                    '".$campos['idVeiculo']."',
                    '".$this->datas->padraoEuaCompleto($campos['dataAbastecimento'])."',
                    '".$campos['combustivel']."',
                    '".$this->textos->moeda($campos['quantidade'])."',
                    '".$this->textos->moeda($campos['valorUnitario'])."',
                    '".$this->textos->moeda($campos['valorPago'])."',
                    '".$this->textos->encodeToIso($campos['observacoes)'])."'
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

    	$sql = "UPDATE veiculos_abastecimentos SET ";
    	if(!empty($campos['veiculoAbastecimento']))    { $sql .= " descricao = '".$this->textos->encodeToIso($campos['veiculoAbastecimento'])."', "; }
    	if(!empty($campos['ativo']))			       { $sql .= " ativo = '".$campos['ativo']."', "; }
    	if(!empty($campos['idFornecedor']))            { $sql .= " fornecedor_id = '".$campos['idFornecedor']."', "; }
    	if(!empty($campos['idVeiculo']))               { $sql .= " veiculo_id = '".$campos['idVeiculo']."', "; }
    	if(!empty($campos['dataAbastecimento']))       { $sql .= " data_abastecimento = '".$this->datas->padraoEuaCompleto($campos['dataAbastecimento'])."', "; }
    	if(!empty($campos['combustivel']))             { $sql .= " combustivel = '".$campos['combustivel']."', "; }
    	if(!empty($campos['quantidade']))              { $sql .= " quantidade = '".$this->textos->moeda($campos['quantidade'])."', "; }
    	if(!empty($campos['valorUnitario']))           { $sql .= " valor_unitario = '".$this->textos->moeda($campos['valorUnitario'])."', "; }
    	if(!empty($campos['valorPago']))               { $sql .= " valor_pago = '".$this->textos->moeda($campos['valorPago'])."', "; }
    	if(!empty($campos['observacoes']))             { $sql .= " observacoes = '".$this->textos->encodeToIso($campos['observacoes)'])."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idVeiculoAbastecimento']."'; ";
    	
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