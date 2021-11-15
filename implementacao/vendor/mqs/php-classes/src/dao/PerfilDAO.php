<?php
namespace MQS\dao;

use \Exception;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class PerfilDAO{
    
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
     * Retorna lista
     * 
     * @param string $where
     * @param boolean $lazy
     * @return array
     */
    function retornaLista($where,$lazy){
        $retorno    = array();
        
        $sql 	= "	SELECT 
						tab.id AS idPerfil,
        				tab.created AS dataCadastro,
						tab.descricao AS perfil,
                        tab.anotador_id,
						tab.ativo 
        			FROM            		
        				acesso_perfis AS tab
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
                    $retorno[$cont]['idPerfil']				= $row->idPerfil;
                    $retorno[$cont]['dataCadastro']     	= $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['perfil']				= $this->textos->encodeToUtf8($row->perfil);
                    $retorno[$cont]['idAnotador']           = $row->anotador_id;
                    $retorno[$cont]['ativo']            	= $row->ativo;
                    
                    if($lazy){
                    	$retorno[$cont]['Permissoes']		= $this->getPermissoes(" AND ap.acesso_perfil_id = '".$retorno[$cont]['idPerfil']."' "); 
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
        				acesso_perfis AS tab
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
     * Retorna as permissões
     *
     * @param string $where
     * @return array
     */
    function getPermissoes($where){
    	$retorno = array();
    		
		$sql 	= "SELECT 
    					ac.id AS idFuncionalidade, 
    					ac.descricao AS funcionalidade,
						ac.detalhe,
    					ac.codigo,
                        ac.ativo,
    					p.id AS idPerfil,
    					p.descricao AS perfil
    				FROM 
    					acesso_permissoes AS ap
    					INNER JOIN acesso_funcionalidades AS ac ON ap.acesso_funcionalidade_id = ac.id
    					INNER JOIN acesso_perfis AS p ON ap.acesso_perfil_id = p.id
    				WHERE 
    					ap.ativo = 'true' ";
    	if(!empty($where)){
    		$sql .= $where;
    	}

    	try {
    	    $resultSet = $this->banco->executarSql($sql);
    	    $qtd = $this->banco->totalDeRegistros($resultSet);
    	    
    	    if ($qtd > 0) {
    	        $cont = 0;
    	        while($row = $this->banco->registroComoObjeto($resultSet)){
    	            $retorno[$cont]['idFuncionalidade'] = $row->idFuncionalidade;
    	            $retorno[$cont]['funcionalidade']	= $this->textos->encodeToUtf8($row->funcionalidade);
    	            $retorno[$cont]['detalhe']			= $this->textos->encodeToUtf8($row->detalhe);
    	            $retorno[$cont]['codigo']           = $row->codigo;
    	            $retorno[$cont]['ativo']            = $row->ativo;
    	            $retorno[$cont]['idPerfil']			= $row->idPerfil;
    	            $retorno[$cont]['perfil']        	= $this->textos->encodeToUtf8($row->perfil);
        			
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
     * Modifica as permissões do perfil
     * 
     * @param array $campos
     * @return int
     */
    function setPermissoes($campos){
    	$agora  = date("Y-m-d H:i:s");
    	
    	try {
    	    // atualizando os itens atuais
    	    $sql	= "UPDATE acesso_permissoes SET updated = '".$agora."', anotador_id = ".$campos[0]['idAnotador'].", ativo = 'false' WHERE acesso_perfil_id = ".$campos[0]['idPerfil']."; ";
    	    $this->banco->executarSql($sql,true,true,false);
    	    $this->banco->confirmar();
    	    
    	    // listando itens para atualização das permissões
    	    foreach ($campos as $item){
    	        
    	        // identificando a permissão
    	        $sql = "SELECT id FROM acesso_permissoes WHERE acesso_funcionalidade_id = '".$item['idFuncionalidade']."' AND acesso_perfil_id = '".$item['idPerfil']."'; ";
    	        $resultSet = $this->banco->executarSql($sql);
    	        $linha = $this->banco->registroComoObjeto($resultSet);
    	        $id = $linha->id;
    	        
    	        if(!empty($id)){
    	            // atualizando a permissão
    	            $sql = "UPDATE acesso_permissoes SET updated = '".$agora."', anotador_id = ".$campos[0]['idAnotador'].", ativo = 'true' WHERE id = ".$id."; ";
    	            $this->banco->executarSql($sql,true,true,false);
    	            $this->banco->confirmar();
    	        }else{
    	            // inserindo a permissão
    	            $sql 	= "INSERT INTO acesso_permissoes (
	        					created,
	        					updated,
	        					ativo,
	        					anotador_id,
	        					acesso_funcionalidade_id,
	        					acesso_perfil_id
	        				) VALUES (
	        					'".$agora."',
	        					'".$agora."',
	        					'true',
	        					'".$item['idAnotador']."',
	        					'".$item['idFuncionalidade']."',
	        					'".$item['idPerfil']."'
	        				);";
    	            $this->banco->executarSql($sql,true,true,false);
    	            $this->banco->confirmar();
    	        }
    	    }    	    
    	}catch (\Exception $e){
    	    throw $e;
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
            $sql = "INSERT INTO acesso_perfis (
                    created,
                    updated,
                    descricao,
        			anotador_id,
                    ativo
                ) VALUES (
                    '".$agora."',
                    '".$agora."',
                    '".$this->textos->encodeToIso($campos['perfil'])."',
                    '".$campos['idAnotador']."',
                    'true'
                );";
            
            $novoId = $this->banco->executarSql($sql,true,true,true);
            $this->banco->confirmar();
            
            if($novoId>0){
                if(!empty($campos['permissoes'])){
                    $qtd = count($campos['permissoes']);
                    if($qtd>0){
                        $permissoes = array();
                        for($i=0;$i<$qtd;$i++){
                            $permissoes[$i]['idPerfil'] 		= $novoId;
                            $permissoes[$i]['idFuncionalidade'] = $campos['permissoes'][$i];
                            $permissoes[$i]['idAnotador'] 		= $campos['idAnotador'];
                        }
                        $this->setPermissoes($permissoes);
                    }
                }
            }
            
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

    	$sql = "UPDATE acesso_perfis SET ";
    	if(!empty($campos['perfil']))			{ $sql .= " descricao = '".$this->textos->encodeToIso($campos['perfil'])."', "; }
    	if(!empty($campos['ativo']))			{ $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idPerfil']."'; ";
    	
    	try {
    	    $this->banco->executarSql($sql,true,true,false);
    	    $this->banco->confirmar();
    	    
    	    if(!empty($campos['permissoes'])){
        	    $qtd = count($campos['permissoes']);
        	    if($qtd>0){
        	        $permissoes = array();
        	        for($i=0;$i<$qtd;$i++){
        	            $permissoes[$i]['idPerfil'] 		= $campos['idPerfil'];
        	            $permissoes[$i]['idFuncionalidade'] = $campos['permissoes'][$i];
        	            $permissoes[$i]['idAnotador'] 		= $campos['idAnotador'];
        	        }
        	        $this->setPermissoes($permissoes);
        	    }
    	    }
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