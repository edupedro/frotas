<?php
namespace MQS\dao;

use \Exception;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class LocalDAO{
    
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
     * Retorna lista de local
     * 
     * @param string $where
     * @param boolean $lazy
     * @return array
     */
    function retornaLista($where,$lazy){
        $retorno    = array();
        
        $sql 	= "	SELECT 
						tab.id AS idLocal,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
						tab.codigo,
 						tab.descricao AS local,
						tab.responsavel,
						tab.telefone,
						tab.email,
						tab.endereco,
						tab.numero,
						tab.complemento,
						tab.bairro,
						tab.cidade,
						tab.estado,
						tab.cep,
						tab.latitude,
						tab.longitude,
						tab.imagem01,
						tab.imagem02,
						tab.imagem03,
						tab.imagem04,
                        tab.observacoes
        			FROM            		
        				locais AS tab
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
                    $retorno[$cont]['idLocal']				    = $row->idLocal;
                    $retorno[$cont]['dataCadastro']     	    = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']            	    = $row->ativo;
                    $retorno[$cont]['codigo']                   = $row->codigo;
                    $retorno[$cont]['local']				    = $this->textos->encodeToUtf8($row->local);
                    $retorno[$cont]['responsavel']              = $this->textos->encodeToUtf8($row->responsavel);
                    $retorno[$cont]['telefone']                 = $row->telefone;
                    $retorno[$cont]['email']                    = $row->email;
                    $retorno[$cont]['endereco']                 = $this->textos->encodeToUtf8($row->endereco);
                    $retorno[$cont]['numero']                   = $row->numero;
                    $retorno[$cont]['complemento']              = $this->textos->encodeToUtf8($row->complemento);
                    $retorno[$cont]['bairro']                   = $this->textos->encodeToUtf8($row->bairro);
                    $retorno[$cont]['cidade']                   = $this->textos->encodeToUtf8($row->cidade);
                    $retorno[$cont]['estado']                   = $row->estado;
                    $retorno[$cont]['cep']                      = $row->cep;
                    $retorno[$cont]['latitude']                 = $row->latitude;
                    $retorno[$cont]['longitude']                = $row->longitude;
                    $retorno[$cont]['imagem01']                 = $row->imagem01;
                    $retorno[$cont]['imagem02']                 = $row->imagem02;
                    $retorno[$cont]['imagem03']                 = $row->imagem03;
                    $retorno[$cont]['imagem04']                 = $row->imagem04;
                    $retorno[$cont]['observacoes']			    = $this->textos->encodeToUtf8($row->observacoes);                    
                    
                    if($lazy){
                    }      
                    
                    if(empty($retorno[$cont]['imagem01'])){ $retorno[$cont]['imagem01'] =  "default-local.png"; }
                    if(empty($retorno[$cont]['imagem02'])){ $retorno[$cont]['imagem02'] =  "default-local.png"; }
                    if(empty($retorno[$cont]['imagem03'])){ $retorno[$cont]['imagem03'] =  "default-local.png"; }
                    if(empty($retorno[$cont]['imagem04'])){ $retorno[$cont]['imagem04'] =  "default-local.png"; }
                    
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
        				locais AS tab
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
            $sql = "INSERT INTO locais (
                        created,
                        updated,
            			anotador_id,
                        ativo,
                        codigo,
                        descricao,
                        responsavel,
                        telefone,
                        email,
                        endereco,
                        numero,
                        complemento,
                        bairro,
                        cidade,
                        estado,
                        cep,
                        latitude,
                        longitude,
                        imagem01,
                        imagem02,
                        imagem03,
                        imagem04,
                        observacoes
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        '".$campos['idAnotador']."',
                        'true',
                        '".$campos['codigo']."',
                        '".$this->textos->encodeToIso($campos['local'])."',
                        '".$this->textos->encodeToIso($campos['responsavel'])."',
                        '".$campos['telefone']."',
                        '".$campos['email']."',
                        '".$this->textos->encodeToIso($campos['endereco'])."',
                        '".$campos['numero']."',
                        '".$this->textos->encodeToIso($campos['complemento'])."',
                        '".$this->textos->encodeToIso($campos['bairro'])."',
                        '".$this->textos->encodeToIso($campos['cidade'])."',
                        '".$campos['estado']."',
                        '".$campos['cep']."',
                        '".$campos['latitude']."',
                        '".$campos['longitude']."', 
                        '".$campos['imagem01']."', 
                        '".$campos['imagem02']."', 
                        '".$campos['imagem03']."', 
                        '".$campos['imagem04']."', 
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

    	$sql = "UPDATE locais SET ";
    	if(!empty($campos['local']))			       { $sql .= " descricao = '".$this->textos->encodeToIso($campos['local'])."', "; }
    	if(!empty($campos['responsavel']))             { $sql .= " responsavel = '".$this->textos->encodeToIso($campos['responsavel'])."', "; }
    	if(!empty($campos['codigo']))        	       { $sql .= " codigo = '".$campos['codigo']."', "; }
    	if(!empty($campos['telefone']))        	       { $sql .= " telefone = '".$campos['telefone']."', "; }
    	if(!empty($campos['email']))        	       { $sql .= " email = '".$campos['email']."', "; }
    	if(!empty($campos['endereco']))        	       { $sql .= " endereco = '".$this->textos->encodeToIso($campos['endereco'])."', "; }
    	if(!empty($campos['numero']))        	       { $sql .= " numero = '".$campos['numero']."', "; }
    	if(!empty($campos['complemento']))             { $sql .= " complemento = '".$this->textos->encodeToIso($campos['complemento'])."', "; }
    	if(!empty($campos['bairro']))        	       { $sql .= " bairro = '".$this->textos->encodeToIso($campos['bairro'])."', "; }
    	if(!empty($campos['cidade']))        	       { $sql .= " cidade = '".$this->textos->encodeToIso($campos['cidade'])."', "; }
    	if(!empty($campos['estado']))        	       { $sql .= " estado = '".$campos['estado']."', "; }
    	if(!empty($campos['cep']))        		       { $sql .= " cep = '".$campos['cep']."', "; }
    	if(!empty($campos['latitude']))        	       { $sql .= " latitude = '".$campos['latitude']."', "; }
    	if(!empty($campos['longitude']))               { $sql .= " longitude = '".$campos['longitude']."', "; }
    	if(!empty($campos['imagem01']))                { $sql .= " imagem01 = '".$campos['imagem01']."', "; }
    	if(!empty($campos['imagem02']))                { $sql .= " imagem02 = '".$campos['imagem02']."', "; }
    	if(!empty($campos['imagem03']))                { $sql .= " imagem03 = '".$campos['imagem03']."', "; }
    	if(!empty($campos['imagem04']))                { $sql .= " imagem04 = '".$campos['imagem04']."', "; }
    	if(!empty($campos['observacoes']))		       { $sql .= " observacoes = '".$this->textos->encodeToIso($campos['observacoes'])."', "; }
    	if(!empty($campos['ativo']))			       { $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idLocal']."'; ";
    	
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
     * Destruir inst??ncia
     *
     * @return void
     */
    function __destruct(){  
    	
    }
    
}// class

?>