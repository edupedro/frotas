<?php
namespace MQS\dao;

use \Exception;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class UsuarioDAO{
    
    private $banco;
    private $datas;
    private $textos;


    /**
     * Construtor
     */
    public function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
    }
    
    /**
     * Retorna lista
     * 
     * @param string $where
     * @return array
     */
    public function retornaLista($where){
        $retorno = array();

        $sql 	= "	SELECT 
                        tab.id,
                        tab.created AS dataCadastro,
                        tab.nome,
                        tab.cpf,
                        tab.email,
                        tab.imagem,
                        tab.senha,
                        tab.acessos,
                        tab.data_acesso,
                        tab.ip_acesso,
                        tab.ativo,
                        tab.liberado,
                        tab.anotador_id,
                        tab.acesso_perfil_id,
                        ap.descricao AS perfil
                    FROM 
                        acesso_usuarios AS tab 
                        INNER JOIN acesso_perfis AS ap ON tab.acesso_perfil_id = ap.id
                    WHERE 
                        tab.id IS NOT NULL ";
        if(!empty($where)){
            $sql .= $where;
        }
        
        try {
            
            $resultSet 	= $this->banco->executarSql($sql);
            $qtd 	= $this->banco->totalDeRegistros($resultSet);
            if ($qtd > 0) {
                $cont = 0;
                while($row = $this->banco->registroComoObjeto($resultSet)){
                    $retorno[$cont]['idUsuario']        = $row->id;
                    $retorno[$cont]['dataCadastro']	    = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['nome']             = $this->textos->encodeToUtf8($row->nome);
                    $retorno[$cont]['email']          	= $row->email;
                    $retorno[$cont]['cpf']          	= $row->cpf;
                    $retorno[$cont]['imagem']		    = $row->imagem;
                    $retorno[$cont]['senha']            = $row->senha;
                    $retorno[$cont]['acessos']          = $row->acessos;
                    $retorno[$cont]['dataAcesso']       = $this->datas->padraoBrasilCompleto($row->data_acesso);
                    $retorno[$cont]['ipAcesso']		    = $row->ip_acesso;
                    $retorno[$cont]['ativo']		    = $row->ativo;
                    $retorno[$cont]['liberado']		    = $row->liberado;
                    $retorno[$cont]['idAnotador']	    = $row->anotador_id;
                    $retorno[$cont]['idPerfil']         = $row->acesso_perfil_id;
                    $retorno[$cont]['perfil']		    = $this->textos->encodeToUtf8($row->perfil);
                    
                    if(empty($retorno[$cont]['imagem'])){
                        $retorno[$cont]['imagem'] = "default-user.png";
                    }
                    
                    $cont++;
                }
            }
            return $retorno;
        } catch (\Exception $e) {
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
                        acesso_usuarios AS tab 
                        INNER JOIN acesso_perfis AS ap ON tab.acesso_perfil_id = ap.id
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
     * Alterar
     *
     * @param array $campos
     * @return void
     */
    public function alterar($campos){
        $agora	= date("Y-m-d H:i:s");
        
        $sql = "UPDATE acesso_usuarios SET ";
        if(!empty($campos['nome']))			{ $sql .= " nome = '".$this->textos->encodeToIso($campos['nome'])."', "; }
        if(!empty($campos['cpf']))		    { $sql .= " cpf = '".$campos['cpf']."', "; }
        if(!empty($campos['email']))		{ $sql .= " email = '".$campos['email']."', "; }
        if(!empty($campos['imagem']))		{ $sql .= " imagem = '".$campos['imagem']."', "; }
        if(!empty($campos['senha']))		{ $sql .= " senha = '".md5($campos['senha'])."', "; }
        if(!empty($campos['dataAcesso']))	{ $sql .= " data_acesso = '".$this->datas->padraoEuaCompleto($campos['dataAcesso'])."', "; }
        if(!empty($campos['acessos']))		{ $sql .= " acessos = '".$campos['acessos']."', "; }
        if(!empty($campos['ipAcesso']))		{ $sql .= " ip_acesso = '".$campos['ipAcesso']."', "; }
        if(!empty($campos['ativo']))		{ $sql .= " ativo = '".$campos['ativo']."', "; }
        if(!empty($campos['liberado']))		{ $sql .= " liberado = '".$campos['liberado']."', "; }
        if(!empty($campos['idPerfil']))		{ $sql .= " acesso_perfil_id = ".$campos['idPerfil'].", "; }		
        $sql .= " updated = '".$agora."', anotador_id = ".$campos['idAnotador']." WHERE id = '".$campos['idUsuario']."'; ";   

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
     * Inserir
     *
     * @param array $campos
     * @return int
     */
    public function inserir($campos){
        $agora   = date("Y-m-d H:i:s");
    
        $sql = "INSERT INTO acesso_usuarios (
                    created,
                    updated,
                    nome,
                    email,
                    imagem,
                    cpf,
                    senha,
                    acesso_perfil_id,
                    ativo,
                    liberado,
                    anotador_id
                ) VALUES (
                    '".$agora."',
                    '".$agora."',
                    '".$this->textos->encodeToIso($campos['nome'])."',
                    '".$campos['email']."',
                    '".$campos['imagem']."',
                    '".$campos['cpf']."',
                    '".md5($campos['senha'])."',
                    '".$campos['idPerfil']."',
                    'true',
                    'false',
                    '".$campos['idAnotador']."'
                )"; 
        try {
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
     * Destruir instância
     *
     * @return void
     */
    public function __destruct(){
            
    }        
    
}// class

