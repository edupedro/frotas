<?php
namespace MQS\ctrl;

use \MQS\PageAdmin;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\ParametroDAO;
use \Exception;
            
/** 
 * Classe para CTRL
 * 
 * @author Eduardo Andrade
 */
class ParametroCTRL extends ModelCTRL {
    
    private $parametroDAO;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->parametroDAO 	= new ParametroDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
    }
    
    /**
     * Abrir página de perfil
     *
     * @return void
     */
    public function getListaRegistro(){
        UsuarioCTRL::verifyLogin();
        
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'parametro_manter' ");
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];             
        $isAdmin = $this->isAdmin($sessao);
        
        $retorno = $this->listaGeral($this, " " ," ORDER BY tab.id DESC ");
        $listaGeral     = $retorno['lista'];
        $qtdRegistros   = $retorno['qtdRegistros'];
        $qtdPaginas     = $retorno['qtdPaginas'];
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("parametro_manter", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaParametros" => $listaGeral,
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
        
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'parametro_manter' ");
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $isAdmin = $this->isAdmin($sessao);
        
        $campos = array();
        $acao                          = $valores['acao'];
        $campos['idParametro']         = $valores['idParametro'];
        $campos['parametro']		   = $valores['parametro'];
        $campos['codigo']			   = $valores['codigo'];
        $campos['valor']			   = $valores['valor'];
        $campos['idAnotador']          = $sessao['idUsuario'];
        
        try {
            switch($acao){
                case "excluir":
                    
                    $camposExcluir = array();
                    $camposExcluir['idParametro']	= $campos['idParametro'];
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
                case "consultar":
                    
                    $opcaoConsulta = $valores['opcaoConsulta'];
                    $campoConsulta = $valores['campoConsulta'];
                    
                    $where = "";
                    if(!empty($opcaoConsulta) && !empty($campoConsulta)){
                        $where = " AND ".$opcaoConsulta." LIKE '%".$campoConsulta."%' ".$where;
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
            $retorno = $this->listaGeral($this, "" ," ORDER BY tab.id DESC ");
            $listaGeral     = $retorno['lista'];
            $qtdRegistros   = $retorno['qtdRegistros'];
            $qtdPaginas     = $retorno['qtdPaginas'];
            
        }
        
        $page = new PageAdmin();
        $page->setTpl("parametro_manter", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaParametros" => $listaGeral,
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
     * Retorna item
     *
     * @param int $id
     * @return array
     */
    function retornaID($id, $lazy = FALSE){
        $retorno = array();

        try {
            if(!empty($id)){
                $dados = $this->retornaLista(" AND tab.id = ".$id." ");
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
     * Retorna lista
     *
     * @param string $where
     * @return array
     */
    function retornaLista($where, $lazy = FALSE){
        try {
            return $this->parametroDAO->retornaLista($where);            
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
            return $this->parametroDAO->retornaQTD($where);
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Retorna item
     * 
     * @param string $codigo
     * @return string
     */
    function retornaValor($codigo){
        try {
            if(empty($codigo)){
                return "";
            }else{
                return $this->parametroDAO->retornaValor($codigo);
            }            
        } catch (\Exception $e) {
            return array();
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
        $retorno = array();
    
        try {
            $erro		= FALSE;
            $msgErro	= array();
            if($tipo == "CADASTRAR"){
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['parametro']); if($erro){ $msgErro[] = "O campo DESCRIÇÃO deve ser preenchido."; } }
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['valor']); if($erro){ $msgErro[] = "O campo VALOR deve ser preenchido."; } }
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['codigo']); if($erro){ $msgErro[] = "O campo CÓDIGO deve ser preenchido."; } }
            }
            if(!$erro){
                $itens = array();
                
                $itens['tabela']	= "parametros";
                $itens['where']		= " codigo = '".$campos['codigo']."' ";
                if($tipo == "ALTERAR"){
                    $erro = $this->getValida()->validaVazio($campos['idParametro']);
                    if($erro){ $msgErro[] = "Defina o registro para alteração."; }
                    $itens['where']	= " codigo = '".$campos['codigo']."' AND id <> ".$campos['idParametro']." ";
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
     * Alterar
     *
     * @param array $campos
     * @return array
     */
    function alterar($campos){
        $retorno	= array();
        $qtdErro	= 0;
            
        if(empty($campos['idAnotador'])){
            $campos['idAnotador'] = $this->parametroCTRL->retornaValor("usuario_sistema");
        }
    
        // validação
        if($campos['validacao'] != "false"){
            $validacao = $this->validacoes($campos, "ALTERAR");
        }
        
        try {
            // operação
            if(!$validacao['erro']){
                $this->parametroDAO->alterar($campos);
            }
            
            $retorno['erro'] 	= $validacao['erro'];
            $retorno['msgErro']	= $validacao['msgErro'];
            $retorno['qtdErro']	= $qtdErro;            
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
