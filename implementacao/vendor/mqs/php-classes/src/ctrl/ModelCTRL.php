<?php
namespace MQS\ctrl;

use \MQS\lib\ValidaCampos;
use \MQS\lib\Upload;

/**
 * Classe CTRL modelo 
 * 
 * @author Eduardo
 */
class ModelCTRL{

    private $banco;
    private $datas;
    private $textos;
    private $valida;
    private $upload;

    private $parametroCTRL;
    private $acessoriosCTRL;
    
    /**
     * Construtor
     */
    public function __construct(){
        $this->banco           = null;
        $this->datas           = null;
        $this->textos          = null;
        $this->upload          = null;        
        $this->valida          = null;
        
        $this->parametroCTRL   = NULL;
        $this->acessoriosCTRL  = NULL;
    }
    
    /**
     * Retorna o objeto
     * 
     * @return object
     */
    public function getBanco(){
        return $this->banco;
    }
    
    /**
     * Retorna o objeto
     *
     * @return object
     */
    public function getDatas(){
        return $this->datas;
    }
    
    /**
     * Retorna o objeto
     *
     * @return object
     */
    public function getTextos(){
        return $this->textos;
    }
    
    /**
     * Retorna o objeto
     *
     * @return object
     */
    public function getUpload(){
        if(is_null($this->upload)){
            $this->upload = new Upload();
        }
        return $this->upload;
    }    

    /**
     * Retorna o objeto
     *
     * @return object
     */
    public function getValida(){
        if(is_null($this->valida)){
            $this->valida = new ValidaCampos($this->banco, $this->datas, $this->textos);
        }
        return $this->valida;
    }
    
    /**
     * Get para o objeto
     *
     * @return object
     */
    public function getParametroCTRL(){
        if(is_null($this->parametroCTRL)){
            $this->parametroCTRL = new ParametroCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->parametroCTRL;
    }    
    
    /**
     * Get para o objeto
     *
     * @return object
     */
    public function getAcessoriosCTRL(){
        if(is_null($this->acessoriosCTRL)){
            $this->acessoriosCTRL = new AcessoriosCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->acessoriosCTRL;
    }
        
    /**
     * Carregamentos das informações padrão das páginas
     * 
     * @return string[]
     */
    public function carregarParametrosPagina(){
        $pagina = array();
        $pagina['titulo']      = $this->textos->encodeToUtf8($this->getParametroCTRL()->retornaValor("pagina_titulo"));
        $pagina['descricao']   = $this->textos->encodeToUtf8($this->getParametroCTRL()->retornaValor("pagina_descricao"));
        $pagina['autor']       = $this->textos->encodeToUtf8($this->getParametroCTRL()->retornaValor("pagina_autor"));
        $pagina['palavras']    = $this->textos->encodeToUtf8($this->getParametroCTRL()->retornaValor("pagina_keywords"));
        $pagina['rodape']      = $this->textos->encodeToUtf8($this->getParametroCTRL()->retornaValor("pagina_rodape"));
        return $pagina;
    } 
    
    /**
     * Altera o objeto
     *
     * @param object $banco
     * @return void
     */
    public function setBanco($banco){
        $this->banco = $banco;
    }
    
    /**
     * Altera o objeto
     *
     * @param object $datas
     * @return void
     */
    public function setDatas($datas){
        $this->datas = $datas;
    }
    
    /**
     * Altera o objeto
     *
     * @param object $textos
     * @return void
     */
    public function setTextos($textos){
        $this->textos = $textos;
    }
        
    /**
     * Retorna se o usuário é administrador
     * 
     * @param array $sessao
     * @return boolean
     */
    public function isAdmin($sessao) {
        $isAdmin = FALSE;
        $idPerfilAdministrador = $this->getParametroCTRL()->retornaValor("perfil_administrador_id");
        if($sessao['idPerfil'] == $idPerfilAdministrador){
            $isAdmin = TRUE;
        }
        return $isAdmin;
    }
    
    /**
     * Retorna a lista conforme a paginação
     * 
     * @param int $pagina
     * @param int $itensPorPagina
     * @param object $controller
     * @param string $condicao
     * @param string $order
     * @return array
     */
    public function listaPaginada($pagina, $itensPorPagina, $controller, $condicao, $order){
        $retorno = array();
        
        $start = ($pagina-1)*$itensPorPagina;
        
        $lista = $controller->retornaLista($condicao." ".$order." LIMIT ".$start.",".$itensPorPagina);
        $qtdRegistros = $controller->retornaQTD($condicao);
        $qtdPaginas = ceil( $qtdRegistros / $itensPorPagina );
        
        $retorno['lista']           = $lista;
        $retorno['qtdRegistros']    = $qtdRegistros;
        $retorno['qtdPaginas']      = $qtdPaginas;
        return $retorno;
    }
    
    /**
     * Retorna a lista sem paginação (geral)
     *
     * @param object $controller
     * @param string $condicao
     * @param string $order
     * @return array
     */
    public function listaGeral($controller, $condicao, $order){
        $retorno = array();
        
        $lista = $controller->retornaLista($condicao." ".$order);
        $qtdRegistros = $controller->retornaQTD($condicao);
        
        $retorno['lista']           = $lista;
        $retorno['qtdRegistros']    = $qtdRegistros;
        $retorno['qtdPaginas']      = 1;
        return $retorno;
    }

    /**
     * Destrutor
     */
    function __destruct(){
        
    }
    
}//class

