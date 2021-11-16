<?php
namespace MQS\ctrl;

use \MQS\PageAdmin;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\VeiculoDAO;
use \Exception;
	    
/** 
 * Classe de CTRL
 * 
 * @author Eduardo Andrade
 */
class VeiculoCTRL extends ModelCTRL {
    
    private $veiculoDAO;
    private $proprietarioCTRL;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->veiculoDAO 	= new VeiculoDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
        $this->proprietarioCTRL = null;
    }
        
    /**
     * Get para o objeto
     *
     * @return object
     */
    private function getProprietarioCTRL(){
        if(is_null($this->proprietarioCTRL)){
            $this->proprietarioCTRL = new ProprietarioCTRL($this->getBanco(), $this->getDatas(), $this->getTextos());
        }
        return $this->proprietarioCTRL;
    }
    
    /**
     * Abrir página da ficha do veículo
     * 
     * @return void
     */
    public function getFichaVeiculo(){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'veiculo_ficha' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("veiculo_ficha", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => $this->getParametroCTRL()->retornaValor("key_api_google_maps"),
        ));        
    }
    
    /**
     * Abrir página de proprietario
     *
     * @param int $paginaAtual;
     * @return void
     */
    public function getListaRegistro($paginaAtual){
        UsuarioCTRL::verifyLogin();
                
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'veiculo_cadastro' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $listaProprietario = $this->getProprietarioCTRL()->retornaLista(" AND tab.ativo = 'true' ORDER BY tab.descricao ");      
        
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
        $page->setTpl("veiculo_cadastro", array(
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
            "listaProprietario" => $listaProprietario,
        ));
    }
    
    /**
     * Abrir página de inclusão/alteração
     * 
     * @param int $idRegistro
     * @return void
     */
    public function getRegistro($idRegistro = NULL){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'veiculo_cadastro' ");
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $isAdmin = $this->isAdmin($sessao);
        
        $listaProprietario = $this->getProprietarioCTRL()->retornaLista(" ORDER BY tab.descricao ");
        
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
        $page->setTpl("veiculo_cadastro_registro", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "acao" => $acao,
            "registro" => $registro,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => ConfigIni::googleAPIkey,
            "listaProprietario" => $listaProprietario,
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
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'veiculo_cadastro' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $listaProprietario = $this->getProprietarioCTRL()->retornaLista(" ORDER BY tab.descricao ");
        
        $campos = array();
        $acao						= $valores['acao'];
        $campos['idVeiculo']        = $valores['idRegistro'];
        $campos['veiculo']          = $valores['veiculo'];
        $campos['idProprietario']   = $valores['idProprietario'];
        $campos['veiculo']          = $valores['veiculo'];
        $campos['placa']			= $valores['placa'];
        $campos['anoFabricacao']    = $valores['anoFabricacao'];
        $campos['anoModelo']        = $valores['anoModelo'];
        $campos['renavan']          = $valores['renavan'];
        $campos['chassi']           = $valores['chassi'];
        $campos['combustivel']      = $valores['combustivel'];
        $campos['gnv']              = $valores['gnv'];
        $campos['seguro']           = $valores['seguro'];
        $campos['cor']              = $valores['cor'];
        $campos['kmInicial']        = $valores['kmInicial'];
        $campos['patrimonio']       = $valores['patrimonio'];
        $campos['valorFipe']        = $valores['valorFipe'];
        $campos['qtdPassageiros']   = $valores['qtdPassageiros'];
        $campos['observacoes']      = $valores['observacoes'];        
        $campos['upload01']         = $arquivos['upload01'];
        $campos['upload02']         = $arquivos['upload02'];
        $campos['upload03']         = $arquivos['upload03'];
        $campos['upload04']         = $arquivos['upload04'];
        $campos['upload05']         = $arquivos['upload05'];
        $campos['upload06']         = $arquivos['upload06'];
        $campos['idAnotador']		= $_SESSION['idUsuario'];
        
        try {
            switch($acao){
                case "excluir":
                    
                    $camposExcluir = array();
                    $camposExcluir['idVeiculo']      = $campos['idVeiculo'];
                    $camposExcluir['ativo']			 = 'false';
                    $camposExcluir['idAnotador']	 = $campos['idAnotador'];
                    $camposExcluir['validacao']      = 'false';
                    
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
        $page->setTpl("veiculo_cadastro", array(
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
            "listaProprietario" => $listaProprietario,
        ));
    }
    
    /**
     * Retorna o registro
     *
     * @param int $id
     * @return array
     */
    public function retornaID($id, $lazy = FALSE){
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
    public function retornaLista($where, $lazy = FALSE){
        try {
            if(is_null($lazy)){
                $lazy = FALSE;
            }
            return $this->veiculoDAO->retornaLista($where,$lazy);
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
    public function retornaQTD($where){
        try {
            return $this->veiculoDAO->retornaQTD($where);
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
    private function validacoes($campos,$tipo){
        $retorno	= array();
        $itens		= array();
        
        try {
            $erro		= FALSE;
            $msgErro	= array();
            if($tipo == "CADASTRAR"){
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['placa']); if($erro){ $msgErro[] = "O campo PLACA deve ser preenchido."; } }
            }
            if(!$erro){
                $itens['tabela']	= "veiculos";
                $itens['where']		= " placa = '".$campos['placa']."' ";
                if($tipo == "ALTERAR"){
                    $erro = $this->getValida()->validaVazio($campos['idVeiculo']);
                    if($erro){ $msgErro[] = "Defina o registro para alteração."; }
                    $itens['where']		= " placa = '".$campos['placa']."' AND ativo = 'true' AND id <> ".$campos['idVeiculo']." ";
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
    public function inserir($campos){
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
                if(!empty($campos['upload01']['name'])){
                    $this->getUpload()->setValores($campos['upload01'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem01']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload02']['name'])){
                    $this->getUpload()->setValores($campos['upload02'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem02']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload03']['name'])){
                    $this->getUpload()->setValores($campos['upload03'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem03']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload04']['name'])){
                    $this->getUpload()->setValores($campos['upload04'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem04']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload05']['name'])){
                    $this->getUpload()->setValores($campos['upload05'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem05']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload06']['name'])){
                    $this->getUpload()->setValores($campos['upload06'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem06']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                
                $novoId = $this->veiculoDAO->inserir($campos);
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
    public function alterar($campos){
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
                
                $user = $this->retornaID($campos['idVeiculo']);
                
                if(!empty($campos['upload01']['name'])){
                    if($user['imagem01'] != "default-veiculo.png"){
                        unlink(ROOT . DS . "views/images/veiculos/".$user['imagem01']);
                    }
                    $this->getUpload()->setValores($campos['upload01'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem01']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload02']['name'])){
                    if($user['imagem02'] != "default-veiculo.png"){
                        unlink(ROOT . DS . "views/images/veiculos/".$user['imagem02']);
                    }
                    $this->getUpload()->setValores($campos['upload02'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem02']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload03']['name'])){
                    if($user['imagem03'] != "default-veiculo.png"){
                        unlink(ROOT . DS . "views/images/veiculos/".$user['imagem03']);
                    }
                    $this->getUpload()->setValores($campos['upload03'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem03']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload04']['name'])){
                    if($user['imagem04'] != "default-veiculo.png"){
                        unlink(ROOT . DS . "views/images/veiculos/".$user['imagem04']);
                    }
                    $this->getUpload()->setValores($campos['upload04'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem04']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload05']['name'])){
                    if($user['imagem05'] != "default-veiculo.png"){
                        unlink(ROOT . DS . "views/images/veiculos/".$user['imagem05']);
                    }
                    $this->getUpload()->setValores($campos['upload05'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem05']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                if(!empty($campos['upload06']['name'])){
                    if($user['imagem06'] != "default-veiculo.png"){
                        unlink(ROOT . DS . "views/images/veiculos/".$user['imagem06']);
                    }
                    $this->getUpload()->setValores($campos['upload06'],ROOT . DS . "views/images/veiculos/", "veiculo_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem06']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                
                $this->veiculoDAO->alterar($campos);
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
    public function __destruct(){            
        
    }        
    
}// class
    
?>