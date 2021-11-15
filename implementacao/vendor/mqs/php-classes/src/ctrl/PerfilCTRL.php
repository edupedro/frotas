<?php
namespace MQS\ctrl;

use \MQS\PageAdmin;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\PerfilDAO;
use \Exception;
	    
/** 
 * Classe de CTRL
 * 
 * @author Eduardo Andrade
 */
class PerfilCTRL extends ModelCTRL {
    
    private $perfilDAO;
    private $usuarioCTRL;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->perfilDAO 	= new PerfilDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
    }
    
    /**
     * Get para o objeto
     *
     * @return object
     */
    function getUsuarioCTRL(){
        if(is_null($this->usuarioCTRL)){
            $this->usuarioCTRL = new UsuarioCTRL($this->getBanco(), $this->getDatas(), $this->getTextos());
        }
        return $this->usuarioCTRL;
    }
    
    /**
     * Abrir página de perfil
     *
     * @return void
     */
    public function getListaRegistro(){
        UsuarioCTRL::verifyLogin();
        
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'perfil_manter' ");
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $isAdmin = $this->isAdmin($sessao);
        
        $idPerfilAdministrador = $this->getParametroCTRL()->retornaValor("perfil_administrador_id");        
        $retorno = $this->listaGeral($this, " AND tab.id <> ".$idPerfilAdministrador." " ," ORDER BY tab.id DESC ");
        $listaGeral     = $retorno['lista'];
        $qtdRegistros   = $retorno['qtdRegistros'];
        $qtdPaginas     = $retorno['qtdPaginas'];
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("perfil_manter", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaPerfis" => $listaGeral,
            "paginaAtual" => 1,
            "qtdRegistros" => $qtdRegistros,
            "qtdPaginas" => $qtdPaginas,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => ConfigIni::googleAPIkey,
        ));
    }
    /**
     * Processamento do formulário
     *
     * @return void
     */
    public function setRegistro($valores,$arquivos){
        UsuarioCTRL::verifyLogin();
        
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'perfil_manter' ");
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $isAdmin = $this->isAdmin($sessao);
        
        $idPerfilAdministrador = $this->getParametroCTRL()->retornaValor("perfil_administrador_id");  
        
        $campos = array();
        $acao						= $_POST['acao'];
        $campos['idPerfil']			= $_POST['idPerfil'];
        $campos['perfil']			= $_POST['perfil'];
        $campos['permissoes']		= $_POST['idFuncionalidades'];
        $campos['idAnotador']		= $_SESSION['idUsuario'];
        
        try {
            switch($acao){
                case "excluir":
                    
                    $camposExcluir = array();
                    $camposExcluir['idPerfil']	    = $campos['idPerfil'];
                    $camposExcluir['ativo']			= 'false';
                    $camposExcluir['idAnotador']	= $campos['idAnotador'];
                    $camposExcluir['validacao']     = 'false';
                    
                    $resultadoProcessamento = $this->alterar($camposExcluir);
                    $qtdErro = $resultadoProcessamento['qtdErro'];
                    
                    if($qtdErro == 0){
                        $temErro = FALSE;
                        $msgProcessamento = "Registro desativado com sucesso.";
                    }else{
                        $temErro = TRUE;
                        $msgProcessamento = "Erro no processamento da desativação do registro.";
                    }
                    $processamento = TRUE;
                    break;
                    // fim da processamento da exclusão
                case "alterar":
                    
                    $resultadoProcessamento = $this->alterar($campos);
                    $qtdErro = $resultadoProcessamento['qtdErro'];
                    
                    if($qtdErro == 0){
                        $temErro = FALSE;
                        $msgProcessamento = "Registro alterado com sucesso.";
                    }else{
                        $temErro = TRUE;
                        $msgProcessamento = "Erro no processamento da alteração do registro.";
                    }
                    $processamento = TRUE;
                    break;
                    // fim da processamento da alteração
                case "cadastrar":
                    
                    $resultadoProcessamento = $this->inserir($campos);
                    $novoId = $resultadoProcessamento['novoId'];
                    if($novoId == 0){ $qtdErro++; }
                    
                    if($qtdErro == 0){
                        $temErro = FALSE;
                        $msgProcessamento = "Registro inserido com sucesso.";
                    }else{
                        $temErro = TRUE;
                        $msgProcessamento = "Erro no processamento do cadastro do registro.";
                    }
                    $processamento = TRUE;
                    break;
                    // fim da processamento do cadastro
                case "consultar":
                    
                    $opcaoConsulta = $valores['opcaoConsulta'];
                    $campoConsulta = $valores['campoConsulta'];
                    
                    $where = " AND tab.id <> ".$idPerfilAdministrador." ";
                    if(!empty($opcaoConsulta) && !empty($campoConsulta)){
                        $where .= " AND ".$opcaoConsulta." LIKE '%".$campoConsulta."%' ";
                    }
                    
                    $paginaAtual = 1;
                    $retorno = $this->listaGeral($this, $where ," ORDER BY tab.id DESC ");
                    $listaGeral     = $retorno['lista'];
                    $qtdRegistros   = $retorno['qtdRegistros'];
                    $qtdPaginas     = $retorno['qtdPaginas'];
                    
                    $processamento = FALSE;
                    $temErro       = FALSE;
                    $msgProcessamento = "";
                    break;
                    // fim da processamento da consulta
            }
        } catch (Exception $e) {
            $processamento = TRUE;
            $temErro       = TRUE;
            $msgProcessamento = $e->getMessage();
        }
        
        if($acao != "consultar"){
            $paginaAtual = 1;
            $retorno = $this->listaGeral($this, " AND tab.id <> ".$idPerfilAdministrador." " ," ORDER BY tab.id DESC ");
            $listaGeral     = $retorno['lista'];
            $qtdRegistros   = $retorno['qtdRegistros'];
            $qtdPaginas     = $retorno['qtdPaginas'];
            
        }
        
        $page = new PageAdmin();
        $page->setTpl("perfil_manter", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaPerfis" => $listaGeral,
            "processamento" => $processamento,
            "temErro" => $temErro,
            "msgProcessamento" => $msgProcessamento,
            "paginaAtual" => $paginaAtual,
            "qtdRegistros" => $qtdRegistros,
            "qtdPaginas" => $qtdPaginas,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => ConfigIni::googleAPIkey,
        ));
    }
    
    /**
     * Retorna o registro
     *
     * @param int $id
     * @return array
     */
    function retornaID($id, $lazy = FALSE){
        $retorno = array();
        
        try {
            if(!empty($id)){
                $dados = $this->retornaLista(" AND tab.id = ".$id." ",$lazy);
                if(count($dados)>0){
                    $retorno = $dados[0];
                }
            }
            return $retorno;
        } catch (\Exception $e) {
            return array();
        }
    }
    
    /**
     * Retorna listagem
     *
     * @param string $where
     * @return array
     */
    function retornaLista($where, $lazy = FALSE){
        try {
            if(is_null($lazy)){
                $lazy = FALSE;
            }
            return $this->perfilDAO->retornaLista($where,$lazy);
        } catch (\Exception $e) {
            return array();
        }
    }
    
    /**
     * Retorna QTD
     *
     * @param string $where
     * @return array
     */
    function retornaQTD($where){
        try {
            return $this->perfilDAO->retornaQTD($where);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Executa validações da entidade
     *
     * @param array $campos
     * @param array $tipo
     * @return array
     */
    function validacoes($campos,$tipo){
        $retorno	= array();
        $itens		= array();
        
        try {
            $erro		= FALSE;
            $msgErro	= array();
            if($tipo == "CADASTRAR"){
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['perfil']); if($erro){ $msgErro[] = "O campo DESCRIÇÃO deve ser preenchido."; } }
            }
            if(!$erro){
                $itens['tabela']	= "acesso_perfis";
                $itens['where']		= " descricao = '".$this->textos->encodeToIso($campos['perfil'])."' ";
                if($tipo == "ALTERAR"){
                    $erro = $this->getValida()->validaVazio($campos['idPerfil']);
                    if($erro){ $msgErro[] = "Defina o registro para alteração."; }
                    $itens['where']		= " descricao = '".$this->textos->encodeToUtf8($campos['perfil'])."' AND ativo = 'true' AND id <> ".$campos['idPerfil']." ";
                }
                if($this->getValida()->validaExiste($itens)){
                    $erro = TRUE;
                    if($erro){ $msgErro[] = "Registro já existente na base de dados."; }
                }
            }
            $retorno['erro']	= $erro;
            $retorno['msgErro']	= $msgErro;
        } catch (\Exception $e) {
            $retorno['erro']	= TRUE;
            $retorno['msgErro'][]	= $e->getMessage();
        }finally {
            return $retorno;
        }
    }
    
    /**
     * Retorna as permissões do perfil
     *
     * @param string $where
     * @return array
     */
    function getPermissoes($where){
    	return $this->perfilDAO->getPermissoes($where);
    }
    
    /**
     * Modifica as permissões do perfil
     *
     * @param array $campos
     * @return array
     */
    function setPermissoes($campos){
        $retorno = array();
        
        try {
            $erro = FALSE;
            $msgErro = array();
            
            $qtd = count($campos);
            for($i=0;$i<$qtd;$i++){
                
                if(empty($campos[$i]['idAnotador'])){
                    $campos[$i]['idAnotador'] = $this->parametroCTRL->retornaValor("usuario_sistema");
                }
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos[$i]['idPerfil']); if($erro){ $msgErro[] = "O campo PERFIL deve ser preenchido."; } }
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos[$i]['idFuncionalidade']); if($erro){ $msgErro[] = "O campo FUNCIONALIDADE deve ser preenchido."; } }
                
                if($erro){
                    throw new \Exception($msgErro[0]." ".print_r($campos,TRUE));
                }
            }
            
            // operação
            if(!$erro){
                $this->perfilDAO->setPermissoes($campos);
            }
            
        } catch (\Exception $e) {
            $retorno['erro'] 	= TRUE;
            $retorno['msgErro'][]	= $e->getMessage();
            $retorno['qtdErro']	= 1;
        }finally {
            return $retorno;
        }
    }
    
    /**
     * Inserir
     *
     * @param array $campos
     * @return array
     */
    function inserir($campos){
        $novoId		= 0;
        $retorno	= array();
        
        if(empty($campos['idAnotador'])){
            $campos['idAnotador'] = $this->getParametroCTRL()->retornaValor("usuario_sistema");
        }
        
        // validação
        if($campos['validacao'] != "false"){
            $validacao = $this->validacoes($campos, "CADASTRAR");
        }
        
        try {
            // operação
            if(!$validacao['erro']){
                
                $novoId = $this->perfilDAO->inserir($campos);
                if($novoId == 0){
                    $validacao['erro']		= TRUE;
                    $validacao['msgErro'][] = "Erro no processamento do cadastro.";
                }
            }
            
            $retorno['erro'] 	= $validacao['erro'];
            $retorno['msgErro']	= $validacao['msgErro'];
            $retorno['novoId']	= $novoId;
        } catch (\Exception $e) {
            $retorno['erro'] 	= TRUE;
            $retorno['msgErro'][]	= $e->getMessage();
            $retorno['novoId']	= 0;
        }finally {
            return $retorno;
        }
    }
    
    /**
     * Alterar
     *
     * @param array $campos
     * @return array
     */
    function alterar($campos){
        $retorno	= array();
        
        if(empty($campos['idAnotador'])){
            $campos['idAnotador'] = $this->getParametroCTRL()->retornaValor("usuario_sistema");
        }
        
        // validação
        if($campos['validacao'] != "false"){
            $validacao = $this->validacoes($campos, "ALTERAR");
        }
        
        try {
            // operação
            if(!$validacao['erro']){
                $this->perfilDAO->alterar($campos);
            }
            
            $retorno['erro'] 	= $validacao['erro'];
            $retorno['msgErro']	= $validacao['msgErro'];
            $retorno['qtdErro']	= 0;
        } catch (\Exception $e) {
            $retorno['erro'] 	= TRUE;
            $retorno['msgErro'][]	= $e->getMessage();
            $retorno['qtdErro']	= 1;
        }finally {
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
    
?>