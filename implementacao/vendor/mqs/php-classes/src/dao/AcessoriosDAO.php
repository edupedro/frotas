<?php
namespace MQS\dao;

use \Exception;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class AcessoriosDAO{
	
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
	 * Retorna detalhes da funcionalidade
	 * 
	 * @param string $where
	 * @return array
	 */
	function retornaFuncionalidades($where = NULL){
		$retorno    = array();
		
		$sql 	= "SELECT 
						tab.id, 
						tab.descricao,
						tab.detalhe,
						tab.codigo,
						tab.menu,
						tab.pagina,
						tab.busca,
						tab.ativo
					FROM 
						acesso_funcionalidades AS tab
					WHERE 
						tab.id IS NOT NULL ";
		if(!empty($where)){
			$sql .= $where;
		}
		
		try {
		    $query  = $this->banco->executarSql($sql);
		    $qtd    = $this->banco->totalDeRegistros($query);
		    
		    $cont = 0;
		    if($qtd>0){
		        while($linha = $this->banco->registroComoObjeto($query)){
		            $retorno[$cont]['idFuncionalidade'] = $linha->id;
		            $retorno[$cont]['funcionalidade']	= $this->textos->encodeToUtf8($linha->descricao);
		            $retorno[$cont]['detalhe']			= $this->textos->encodeToUtf8($linha->detalhe);
		            $retorno[$cont]['codigo']           = $linha->codigo;
		            $retorno[$cont]['menu']           	= $this->textos->encodeToUtf8($linha->menu);
		            $retorno[$cont]['pagina']         	= $linha->pagina;
		            $retorno[$cont]['busca']           	= $linha->busca;
		            $retorno[$cont]['ativo']           	= $linha->ativo;
		            
		            $cont++;
		        }
		    }
		    $this->banco->liberar($query);
		    
		} catch (\Exception $e) {
		    throw $e;
		}finally {
		    unset($sql, $query, $qtd, $linha);
		    return $retorno;
		}		
	}
	
	
	/**
	 * Retorna funcionalidades e perfil
	 *
	 * @param string $where
	 * @param int $idPerfil
	 * @return array
	 */
	function retornaFuncionalidadePerfil($where){
		$retorno    = array();
	
		$sql 	= "SELECT
						tab.id AS idFuncionalidadePerfil,
						tab.ativo AS ativo,
						af.id AS idFuncionalidade,
						af.descricao AS funcionalidade,
						af.codigo,
						p.id AS idPerfil,
						p.descricao AS perfil
					FROM
						acesso_funcionalidades AS af
						INNER JOIN acesso_permissoes AS tab ON af.id = tab.acesso_funcionalidade_id
						INNER JOIN acesso_perfis AS p ON tab.acesso_perfil_id = p.id
					WHERE
						tab.id IS NOT NULL ";
		if(!empty($where)){
			$sql .= $where;
		}
		
		try {
		    $query  = $this->banco->executarSql($sql);
		    $qtd    = $this->banco->totalDeRegistros($query);
		    
		    $cont = 0;
		    if($qtd>0){
		        while($linha = $this->banco->registroComoObjeto($query)){
		            $retorno[$cont]['idFuncionalidadePerfil']	= $linha->idFuncionalidadePerfil;
		            $retorno[$cont]['ativo'] 					= $linha->ativo;
		            $retorno[$cont]['idFuncionalidade'] 		= $linha->idFuncionalidade;
		            $retorno[$cont]['funcionalidade']			= $this->textos->encodeToUtf8($linha->funcionalidade);
		            $retorno[$cont]['codigo']           		= $linha->codigo;
		            $retorno[$cont]['idPerfil']					= $linha->idPerfil;
		            $retorno[$cont]['perfil']        			= $this->textos->encodeToUtf8($linha->perfil);
		            
		            $cont++;
		        }
		    }
		    $this->banco->liberar($query);
		    
		} catch (\Exception $e) {
		    throw $e;
		}finally {
		    unset($sql, $query, $qtd, $linha);
		    return $retorno;
		}		
	}
	
	/**
	 * Valida se o usuário tem permissão de acesso
	 * 
	 * @param int $idPerfil
	 * @param string $funcionalidade
	 * @return boolean
	 */
	function validaPermissao($idPerfil,$funcionalidade){
		$permissao = FALSE;
		
		try {
		    $sql = "SELECT
					COUNT(apm.id) AS qtd
				FROM
					acesso_permissoes AS apm
					INNER JOIN acesso_perfis AS ap ON apm.acesso_perfil_id = ap.id
					INNER JOIN acesso_funcionalidades af ON apm.acesso_funcionalidade_id = af.id
				WHERE
					apm.id IS NOT NULL
					AND apm.ativo = 'true'
					AND af.ativo = 'true'
					AND ap.id = ".$idPerfil."
					AND af.codigo = '".$funcionalidade."' ";
		    
		    $resultSet = $this->banco->executarSql($sql);
		    $linha = $this->banco->registroComoObjeto($resultSet);
		    $qtd =  $linha->qtd;
		    if($qtd >0){ $permissao = TRUE; }
		    
		} catch (\Exception $e) {
		    throw $e;
		}finally {
		    unset($sql, $resultSet, $qtd, $linha);
		    return $permissao;
		}
	}        

	/**
	 * Retorna os endereços
	 *
	 * @param string $where
	 * @return array
	 */
	function retornaEnderecos($where){
		$retorno = array();
	
		$sql 	= "	SELECT
						end.id AS idEndereco,
						end.cep,
						end.logradouro,
						bai.id AS idBairro,
						bai.descricao AS bairro,
						cid.id AS idCidade,
						cid.descricao AS cidade,
						cid.cidade_cep AS cidadeCep,
						uf.id AS idEstado,
						uf.descricao AS estado,
						uf.sigla AS sigla
					FROM
						cep_enderecos AS end
						INNER JOIN cep_bairros AS bai ON end.bairro_id = bai.id
						INNER JOIN cep_cidades AS cid ON bai.cidade_id = cid.id
						INNER JOIN cep_estados AS uf ON cid.estado_id = uf.id
					WHERE
						end.id IS NOT NULL ";
		if(!empty($where)){
			$sql .= $where;
		}
		
		try {
		    $query 	= $this->banco->executarSql($sql);
		    $qtd 	= $this->banco->totalDeRegistros($query);
		    
		    if ($qtd > 0) {
		        $cont = 0;
		        while($row = $this->banco->registroComoObjeto($query)){
		            $retorno[$cont]['idEndereco']   = $row->idEndereco;
		            $retorno[$cont]['cep']       	= $row->cep;
		            $retorno[$cont]['endereco']     = $this->textos->encodeToUtf8($row->logradouro);
		            $retorno[$cont]['idBairro']     = $row->idBairro;
		            $retorno[$cont]['bairro']   	= $this->textos->encodeToUtf8($row->bairro);
		            $retorno[$cont]['idCidade']     = $row->idCidade;
		            $retorno[$cont]['cidade']       = $this->textos->encodeToUtf8($row->cidade);
		            $retorno[$cont]['cidadeCep']    = $row->cidadeCep;
		            $retorno[$cont]['idEstado']     = $row->idEstado;
		            $retorno[$cont]['estado']       = $this->textos->encodeToUtf8($row->estado);
		            $retorno[$cont]['sigla']  		= $row->sigla;
		            
		            $cont++;
		        }
		    }
		    
		} catch (\Exception $e) {
		  throw $e;     
		}finally {
		    unset($sql, $query, $qtd, $row);
		    return $retorno;
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

