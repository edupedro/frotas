<?php
namespace MQS\ctrl;

use \MQS\PageAdmin;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\FornecedorDAO;
use \Exception;
	    
/** 
 * Classe de CTRL
 * 
 * @author Eduardo Andrade
 */
class FornecedorCTRL extends ModelCTRL {
    
    private $fornecedorDAO;
    private $fornecedorTipoCTRL;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->fornecedorDAO 	= new FornecedorDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
        $this->fornecedorTipoCTRL = null;
    }
        
    /**
     * Get para o objeto
     *
     * @return object
     */
    function getFornecedorTipoCTRL(){
        if(is_null($this->fornecedorTipoCTRL)){
            $this->fornecedorTipoCTRL = new FornecedorTipoCTRL($this->getBanco(), $this->getDatas(), $this->getTextos());
        }
        return $this->fornecedorTipoCTRL;
    }
    
    /**
     * Abrir página de fornecedorTipo
     *
     * @param int $paginaAtual;
     * @return void
     */
    public function getListaRegistro($paginaAtual){
        UsuarioCTRL::verifyLogin();
                
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'fornecedor_manter' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $listaFornecedorTipo = $this->getFornecedorTipoCTRL()->retornaLista(" AND tab.ativo = 'true' ORDER BY tab.descricao ");      
        
        $itensPerPage = ConfigIni::ITENS_TABELAS;
        $start = ($paginaAtual-1)*$itensPerPage;
        $listaGeral = $this->retornaLista(" ORDER BY tab.descricao DESC LIMIT ".$start.",".$itensPerPage." ");
        $qtdRegistros = $this->retornaQTD("");
        $qtdPaginas = ceil( $qtdRegistros / $itensPerPage );        
        if($qtdPaginas == 0){ $qtdPaginas = 1;}

        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("fornecedor_manter", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaGeral" => $listaGeral,
            "paginaAtual" => $paginaAtual,
            "qtdPaginas" => $qtdPaginas,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => ConfigIni::googleAPIkey,
            "listaFornecedorTipo" => $listaFornecedorTipo,
        ));
    }
    
    /**
     * Abrir página de inclusão/alteração
     * 
     * @param int $idRegistro
     * @return void
     */
    function getRegistro($idRegistro = NULL){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'fornecedor_manter' ");
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $isAdmin = $this->isAdmin($sessao);
        
        $listaFornecedorTipo = $this->getFornecedorTipoCTRL()->retornaLista(" AND tab.ativo = 'true' ORDER BY tab.descricao ");
        
        try{
            
            $registro   = array();
            $acao       = "cadastrar";
            if(!is_null($idRegistro)){
                $acao = "alterar";
                $registro = $this->retornaID($idRegistro);
            }
            
        }catch (\Exception $e){
            $registro = array();
        }
        
        $page = new PageAdmin();
        $page->setTpl("fornecedor_registro", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "acao" => $acao,
            "registro" => $registro,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => ConfigIni::googleAPIkey,
            "listaFornecedorTipo" => $listaFornecedorTipo,
        ));        
    }
        
    /**
     * Processamento do formulário
     *
     * @return void
     */
    public function setRegistro($valores,$arquivos){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'fornecedor_manter' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $listaFornecedorTipo = $this->getFornecedorTipoCTRL()->retornaLista(" AND tab.ativo = 'true' ORDER BY tab.descricao ");
        
        $campos = array();
        $acao						   = $valores['acao'];
        $campos['idFornecedor']        = $valores['idRegistro'];
        $campos['fornecedor']          = $valores['fornecedor'];
        $campos['tipo']                = $valores['tipo'];
        $campos['cpf']                 = $valores['cpf'];
        $campos['razaoSocial']         = $valores['razaoSocial'];
        $campos['nomeFantasia']        = $valores['nomeFantasia'];
        $campos['cnpj']                = $valores['cnpj'];
        $campos['inscricaoEstadual']   = $valores['inscricaoEstadual'];
        $campos['inscricaoMunicipal']  = $valores['inscricaoMunicipal'];
        $campos['responsavel']         = $valores['responsavel'];
        $campos['email']               = $valores['email'];
        $campos['telefone']            = $valores['telefone'];
        $campos['endereco']            = $valores['endereco'];
        $campos['numero']              = $valores['numero'];
        $campos['complemento']         = $valores['complemento'];
        $campos['bairro']              = $valores['bairro'];
        $campos['cidade']              = $valores['cidade'];
        $campos['estado']              = $valores['estado'];
        $campos['cep']                 = $valores['cep'];
        $campos['latitude']            = $valores['latitude'];
        $campos['longitude']           = $valores['longitude'];
        $campos['uploadAvatar']        = $arquivos['uploadAvatar'];
        $campos['uploadCapa']          = $arquivos['uploadCapa'];        
        $campos['idFornecedorTipo']	   = $valores['idFornecedorTipo'];
        $campos['idAnotador']		   = $_SESSION['idUsuario'];
        
        try {
            switch($acao){
                case "excluir":
                    
                    $camposExcluir = array();
                    $camposExcluir['idFornecedor']      = $campos['idFornecedor'];
                    $camposExcluir['ativo']			    = 'false';
                    $camposExcluir['idAnotador']	    = $campos['idAnotador'];
                    $camposExcluir['validacao']         = 'false';
                    
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
                    $paginaAtual = 1;

                    $opcaoConsulta = $valores['opcaoConsulta'];
                    $campoConsulta = $valores['campoConsulta'];
                    
                    $where = " AND ".$opcaoConsulta." LIKE '%".$campoConsulta."%' ORDER BY tab.descricao DESC ";                    
                    $listaGeral = $this->retornaLista($where);
                    $qtdRegistros = $this->retornaQTD("");
                    $qtdPaginas = 1;
                    
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
            $itensPerPage = ConfigIni::ITENS_TABELAS;
            
            $start = ($paginaAtual-1)*$itensPerPage;
            $listaGeral = $this->retornaLista(" ORDER BY tab.descricao DESC LIMIT ".$start.",".$itensPerPage." ");
            $qtdRegistros = $this->retornaQTD("");
            $qtdPaginas = ceil( $qtdRegistros / $itensPerPage );
        }
        
        $page = new PageAdmin();
        $page->setTpl("fornecedor_manter", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaGeral" => $listaGeral,
            "processamento" => $processamento,
            "temErro" => $temErro,
            "msgProcessamento" => $msgProcessamento,
            "paginaAtual" => $paginaAtual,
            "qtdPaginas" => $qtdPaginas,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => ConfigIni::googleAPIkey,
            "listaFornecedorTipo" => $listaFornecedorTipo,
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
            return $this->fornecedorDAO->retornaLista($where,$lazy);
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
            return $this->fornecedorDAO->retornaQTD($where);
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
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['fornecedor']); if($erro){ $msgErro[] = "O campo DESCRIÇÃO deve ser preenchido."; } }
            }
            if(!$erro){
                $itens['tabela']	= "fornecedores";
                $itens['where']		= " descricao = '".$this->getTextos()->encodeToIso($campos['fornecedor'])."' ";
                if($tipo == "ALTERAR"){
                    $erro = $this->getValida()->validaVazio($campos['idFornecedor']);
                    if($erro){ $msgErro[] = "Defina o registro para alteração."; }
                    $itens['where']		= " descricao = '".$this->getTextos()->encodeToIso($campos['fornecedor'])."' AND ativo = 'true' AND id <> ".$campos['idFornecedor']." ";
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
                
                //upload
                if(!empty($campos['uploadAvatar']['name'])){
                    $this->getUpload()->setValores($campos['uploadAvatar'],ROOT . DS . "views/images/fornecedores/", "fornecedor_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagemAvatar']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['uploadCapa']['name'])){
                    $this->getUpload()->setValores($campos['uploadCapa'],ROOT . DS . "views/images/fornecedores/", "fornecedor_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagemCapa']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                
                $novoId = $this->fornecedorDAO->inserir($campos);
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
                
                $user = $this->retornaID($campos['idFornecedor']);
                
                if(!empty($campos['uploadAvatar']['name'])){
                    if($user['imagemAvatar'] != "default-avatar.png"){
                        unlink(ROOT . DS . "views/images/fornecedores/".$user['imagemAvatar']);
                    }
                    $this->getUpload()->setValores($campos['uploadAvatar'],ROOT . DS . "views/images/fornecedores/", "fornecedor_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagemAvatar']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                
                if(!empty($campos['uploadCapa']['name'])){
                    if($user['imagemCapa'] != "default-capa.png"){
                        unlink(ROOT . DS . "views/images/proprietarios/".$user['imagemCapa']);
                    }
                    $this->getUpload()->setValores($campos['uploadAvatar'],ROOT . DS . "views/images/fornecedores/", "fornecedor_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagemCapa']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }                
                
                $this->fornecedorDAO->alterar($campos);
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