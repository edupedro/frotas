<?php
namespace MQS\ctrl;

use \MQS\PageAdmin;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\VeiculoViagemDAO;
use \Exception;



/** 
 * Classe de CTRL
 * 
 * @author Eduardo Andrade
 */
class VeiculoViagemCTRL extends ModelCTRL {
    
    private $veiculoViagemDAO;
    private $unidadeCTRL;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->veiculoViagemDAO = new VeiculoViagemDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
        $this->unidadeCTRL      = null;
    }
    
    /**
     * Retorna o objeto
     * 
     * @return object
     */
    public function getUnidade(){
        if(is_null($this->unidadeCTRL)){
            $this->unidadeCTRL = new UnidadeCTRL($this->getBanco(), $this->getDatas(), $this->getTextos());
        }
        return $this->unidadeCTRL;
    }
    
    /**
     * Abrir página
     *
     * @return void
     */
    public function getListaRegistro($paginaAtual){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'veiculo_viagem_lista' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $retorno = $this->listaPaginada($paginaAtual, ConfigIni::ITENS_TABELAS, $this, "" ," ORDER BY tab.id DESC ");
        $listaGeral     = $retorno['lista'];
        $qtdRegistros   = $retorno['qtdRegistros'];
        $qtdPaginas     = $retorno['qtdPaginas'];
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("veiculo_viagem_lista", array(
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
     * Processamento do formulário
     *
     * @return void
     */
    public function setRegistro($valores,$arquivos){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'veiculo_viagem_manter' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $listaPrincipal = $this->retornaLista(" ORDER BY tab.descricao DESC ");        
        
        $campos = array();
        $acao						        = $_POST['acao'];
        $campos['idVeiculoViagem']          = $_POST['idVeiculoViagem'];
        $campos['veiculoViagem']            = $_POST['veiculoViagem'];
        $campos['dataInicio']               = $_POST['data_inicio'];
        $campos['dataTermino']              = $_POST['data_termino'];
        $campos['situacao']                 = $_POST['situacao'];
        $campos['marcador']                 = $_POST['marcador'];
        $campos['observacoes']              = $_POST['observacoes'];
        $campos['idVeiculoMotorista']       = $_POST['idVeiculoMotorista'];
        $campos['idAnotador']		        = $_SESSION['idUsuario'];
        
        try {
            switch($acao){
                case "excluir":
                    
                    $camposExcluir = array();
                    $camposExcluir['idVeiculoViagem']           = $campos['idVeiculoViagem'];
                    $camposExcluir['ativo']			            = 'false';
                    $camposExcluir['idAnotador']	            = $campos['idAnotador'];
                    $camposExcluir['validacao']                 = 'false';
                    
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
                    $itensPerPage = ConfigIni::ITENS_TABELAS;
                    
                    $start = ($paginaAtual-1)*$itensPerPage;
                    $listaIncidente = $this->retornaLista(" LIMIT ".$start.",".$itensPerPage." ORDER BY tab.descricao DESC ");
                    $qtdRegistros = count($listaIncidente);
                    $qtdPaginas = ceil( $qtdRegistros / $itensPerPage );
                    
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
            $listaIncidente = $this->retornaLista(" LIMIT ".$start.",".$itensPerPage." ORDER BY tab.descricao DESC ");
            $qtdRegistros = count($listaIncidente);
            $qtdPaginas = ceil( $qtdRegistros / $itensPerPage );
        }
        
        $page = new PageAdmin();
        $page->setTpl("veiculo_viagem_manter", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaPrincipal" => $listaPrincipal,
            "processamento" => $processamento,
            "temErro" => $temErro,
            "msgProcessamento" => $msgProcessamento,
            "qtdPaginas" => $qtdPaginas,
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
            return $this->veiculoViagemDAO->retornaLista($where,$lazy);
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
            return $this->veiculoViagemDAO->retornaQTD($where);
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
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['idVeiculoMotorista']); if($erro){ $msgErro[] = "O campo MOTORISTA deve ser preenchido."; } }
            }
            if(!$erro){
                $itens['tabela']	= "veiculos_viagens";
                $itens['where']		= " descricao = '".$this->textos->encodeToIso($campos['veiculoViagem'])."' ";
                if($tipo == "ALTERAR"){
                    $erro = $this->getValida()->validaVazio($campos['idVeiculoViagem']);
                    if($erro){ $msgErro[] = "Defina o registro para alteração."; }
                    $itens['where']		= " descricao = '".$this->textos->encodeToUtf8($campos['veiculoViagem'])."' AND ativo = 'true' AND id <> ".$campos['idVeiculoViagem']." ";
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
                
                $novoId = $this->veiculoViagemDAO->inserir($campos);
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
                $this->veiculoViagemDAO->alterar($campos);
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