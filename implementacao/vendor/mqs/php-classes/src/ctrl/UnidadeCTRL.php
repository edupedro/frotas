<?php
namespace MQS\ctrl;

use \MQS\PageAdmin;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\UnidadeDAO;
use \Exception;
	    
/** 
 * Classe de CTRL
 * 
 * @author Eduardo Andrade
 */
class UnidadeCTRL extends ModelCTRL {
    
    private $unidadeDAO;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->unidadeDAO 	= new UnidadeDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
    }
    
    /**
     * Abrir página 
     *
     * @param int $paginaAtual;
     * @return void
     */
    public function getListaRegistro($paginaAtual){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'unidade_cadastro' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $retorno = $this->listaPaginada($paginaAtual, ConfigIni::ITENS_TABELAS, $this, "" ," ORDER BY tab.id DESC ");
        $listaGeral     = $retorno['lista'];
        $qtdRegistros   = $retorno['qtdRegistros'];
        $qtdPaginas     = $retorno['qtdPaginas'];
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("unidade_cadastro", array(
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
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'unidade_cadastro' ");
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $isAdmin = $this->isAdmin($sessao);
        
        try{
            $registro   = array();
            $acao       = "cadastrar";
            $registro['codigo'] = $this->gerarCodigo();
            
            if(!is_null($idRegistro)){
                $acao = "alterar";
                $registro = $this->retornaID($idRegistro);
            }
            
        }catch (\Exception $e){
            $registro = array();
        }
        
        $page = new PageAdmin();
        $page->setTpl("unidade_cadastro_registro", array(
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
     * @param array $valores
     * @param array $arquivos
     * @return void
     */
    public function setRegistro($valores, $arquivos){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'unidade_cadastro' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $campos = array();
        $acao						   = $valores['acao'];
        $campos['idUnidade']           = $valores['idRegistro'];
        $campos['unidade']             = $valores['unidade'];
        $campos['codigo']              = $valores['codigo'];
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
        $campos['upload01']            = $arquivos['upload01'];
        $campos['upload02']            = $arquivos['upload02'];
        $campos['observacoes']         = $valores['observacoes'];
        $campos['idAnotador']		   = $_SESSION['idUsuario'];
        
        try {
            switch($acao){
                case "excluir":
                    
                    $camposExcluir = array();
                    $camposExcluir['idUnidade']           = $campos['idUnidade'];
                    $camposExcluir['ativo']			      = 'false';
                    $camposExcluir['idAnotador']	      = $campos['idAnotador'];
                    $camposExcluir['validacao']           = 'false';
                    
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
        $page->setTpl("unidade_cadastro", array(
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
            return $this->unidadeDAO->retornaLista($where,$lazy);
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
            return $this->unidadeDAO->retornaQTD($where);
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
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['unidade']); if($erro){ $msgErro[] = "O campo DESCRIÇÃO deve ser preenchido."; } }
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['codigo']); if($erro){ $msgErro[] = "O campo CÓDIGO deve ser preenchido."; } }
            }
            if(!$erro){
                $itens['tabela']	= "unidades";
                $itens['where']		= " descricao = '".$this->textos->encodeToIso($campos['unidade'])."' AND codigo = '".$campos['codigo']."' ";
                if($tipo == "ALTERAR"){
                    $erro = $this->getValida()->validaVazio($campos['idUnidade']);
                    if($erro){ $msgErro[] = "Defina o registro para alteração."; }
                    $itens['where']		= " descricao = '".$this->textos->encodeToUtf8($campos['unidade'])."' AND codigo = '".$campos['codigo']."' AND ativo = 'true' AND id <> ".$campos['idUnidade']." ";
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
        $campos['codigo'] = $this->gerarCodigo();
        
        
        // validação
        if($campos['validacao'] != "false"){
            $validacao = $this->validacoes($campos, "CADASTRAR");
        }
        
        try {
            // operação
            if(!$validacao['erro']){
                
                //upload
                if(!empty($campos['upload01']['name'])){
                    $this->getUpload()->setValores($campos['upload01'],ROOT . DS . "views/images/unidades/", "unidade_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem01']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload02']['name'])){
                    $this->getUpload()->setValores($campos['upload02'],ROOT . DS . "views/images/unidades/", "unidade_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem02']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }                
                
                $novoId = $this->unidadeDAO->inserir($campos);
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
                
                $user = $this->retornaID($campos['idUnidade']);
                
                if(!empty($campos['upload01']['name'])){
                    if($user['imagem01'] != "default-unidade.png"){
                        unlink(ROOT . DS . "views/images/unidades/".$user['imagem01']);
                    }
                    $this->getUpload()->setValores($campos['upload01'],ROOT . DS . "views/images/unidades/", "unidade_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem01']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload02']['name'])){
                    if($user['imagem02'] != "default-unidade.png"){
                        unlink(ROOT . DS . "views/images/unidades/".$user['imagem02']);
                    }
                    $this->getUpload()->setValores($campos['upload02'],ROOT . DS . "views/images/unidades/", "unidade_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem02']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }                
                
                $this->unidadeDAO->alterar($campos);
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
     * Geração do código
     *
     * @param int $id
     * @return string
     */
    function gerarCodigo($id = NULL){
        $lista = $this->retornaLista("ORDER BY tab.id DESC LIMIT 1");
        $tam = count($lista);
        
        if($tam>0){
            // pegando o último registro
            $numero		= (int) $lista[0]['codigo'];
            $numero++;
            
            if($id != ""){ $numero = $id++; }
            
            $novoNumero		= str_pad($numero, 5, "0", STR_PAD_LEFT);
        }else{
            $novoNumero 	= str_pad("1", 5, "0", STR_PAD_LEFT);
            if($id != ""){
                $novoNumero = str_pad($id, 5, "0", STR_PAD_LEFT);
            }
        }
        return $novoNumero;
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