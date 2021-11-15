<?php
namespace MQS\ctrl;

use \MQS\PageAdmin;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\MotoristaDAO;
use \Exception;
	    
/** 
 * Classe de CTRL
 * 
 * @author Eduardo Andrade
 */
class MotoristaCTRL extends ModelCTRL {
    
    private $motoristaDAO;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->motoristaDAO 	= new MotoristaDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
    }
    
    /**
     * Abrir página de motorista
     *
     * @param int $paginaAtual;
     * @return void
     */
    public function getListaRegistro($paginaAtual){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'motorista_cadastro' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $retorno = $this->listaPaginada($paginaAtual, ConfigIni::ITENS_TABELAS, $this, "" ," ORDER BY tab.id DESC ");
        $listaGeral     = $retorno['lista'];
        $qtdRegistros   = $retorno['qtdRegistros'];
        $qtdPaginas     = $retorno['qtdPaginas'];
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("motorista_cadastro", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaGeral" => $listaGeral,
            "paginaAtual" => $paginaAtual,
            "qtdRegistros" => $qtdRegistros,
            "qtdPaginas" => $qtdPaginas,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => ConfigIni::googleAPIkey,
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
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'motorista_cadastro' ");
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $isAdmin = $this->isAdmin($sessao);
        
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
        $page->setTpl("motorista_cadastro_registro", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "acao" => $acao,
            "registro" => $registro,
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
    public function setRegistro($servidor, $valores, $arquivos){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'motorista_cadastro' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $campos = array();
        $acao						   = $valores['acao'];
        $campos['idMotorista']         = $valores['idRegistro'];
        $campos['motorista']           = $valores['motorista'];
        $campos['cpf']                 = $valores['cpf'];
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
        $campos['idAnotador']		   = $_SESSION['idUsuario'];
        
        try {
            switch($acao){
                case "excluir":
                    
                    $camposExcluir = array();
                    $camposExcluir['idMotorista']       = $campos['idMotorista'];
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
                    
                    $opcaoConsulta = $valores['opcaoConsulta'];
                    $campoConsulta = $valores['campoConsulta'];
                    
                    $where = "";
                    if(!empty($opcaoConsulta) && !empty($campoConsulta)){
                        $where = " AND ".$opcaoConsulta." LIKE '%".$campoConsulta."%' ".$where;
                    }
                    
                    $paginaAtual = 1;
                    $retorno = $this->listaPaginada($paginaAtual, ConfigIni::ITENS_TABELAS, $this, $where, " ORDER BY tab.descricao DESC ");
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
            $retorno = $this->listaPaginada($paginaAtual, ConfigIni::ITENS_TABELAS, $this, "" ," ORDER BY tab.id DESC ");
            $listaGeral     = $retorno['lista'];
            $qtdRegistros   = $retorno['qtdRegistros'];
            $qtdPaginas     = $retorno['qtdPaginas'];
            
        }
        
        $page = new PageAdmin();
        $page->setTpl("motorista_cadastro", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaGeral" => $listaGeral,
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
            return $this->motoristaDAO->retornaLista($where,$lazy);
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
            return $this->motoristaDAO->retornaQTD($where);
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
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['motorista']); if($erro){ $msgErro[] = "O campo DESCRIÇÃO deve ser preenchido."; } }
            }
            if(!$erro){
                $itens['tabela']	= "motoristas";
                $itens['where']		= " descricao = '".$this->getTextos()->encodeToIso($campos['motorista'])."' ";
                if($tipo == "ALTERAR"){
                    $erro = $this->getValida()->validaVazio($campos['idMotorista']);
                    if($erro){ $msgErro[] = "Defina o registro para alteração."; }
                    $itens['where']		= " descricao = '".$this->getTextos()->encodeToIso($campos['motorista'])."' AND ativo = 'true' AND id <> ".$campos['idMotorista']." ";
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
                    $this->getUpload()->setValores($campos['uploadAvatar'],ROOT . DS . "views/images/motoristas/", "motorista_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagemAvatar']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['uploadCapa']['name'])){
                    $this->getUpload()->setValores($campos['uploadCapa'],ROOT . DS . "views/images/motoristas/", "motorista_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagemCapa']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }               
                
                $novoId = $this->motoristaDAO->inserir($campos);
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
                
                $user = $this->retornaID($campos['idMotorista']);
                
                if(!empty($campos['uploadAvatar']['name'])){
                    if($user['imagemAvatar'] != "default-avatar.png"){
                        unlink(ROOT . DS . "views/images/motoristas/".$user['imagemAvatar']);
                    }
                    $this->getUpload()->setValores($campos['uploadAvatar'],ROOT . DS . "views/images/motoristas/", "motorista_");
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
                        unlink(ROOT . DS . "views/images/motoristas/".$user['imagemCapa']);
                    }
                    $this->getUpload()->setValores($campos['uploadCapa'],ROOT . DS . "views/images/motoristas/", "motorista_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagemCapa']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                
                $this->motoristaDAO->alterar($campos);
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