<?php
namespace MQS\ctrl;

use MQS\Page;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

/**
 * Classe de CTRL
 * 
 * @author Eduardo
 */
class SiteCTRL extends ModelCTRL {
    
    private $localCTRL;

    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->localCTRL = null;
    }        
    
    /**
     * Método para acesso a página inicial
     * @return void
     */
    public function index(){
        //global $app;
        
        $params = $this->carregarParametrosPagina();
        
        // lista de locais
        $listaLocais = array();
        //$listaLocais = $this->getLocalCTRL()->retornaLista(" AND tab.ativo = 'true'");
        
        $page = new Page([
            "header" => false,
            "footer" => false
        ], "/views/site/");
        $page->setTpl("index", array(
            "pagina" => $params,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => ConfigIni::googleAPIkey,            
            "cidadeInstalacao" => $this->getParametroCTRL()->retornaValor("cidade_instalacao"),
            "estadoInstalacao" => $this->getParametroCTRL()->retornaValor("estado_instalacao"),
            "listaLocais" => $listaLocais,
        ));
    }

    /**
     * Método para acesso as páginas do site
     * @param string $pagina
     * @param string $local
     * @return void
     */
    public function getPage($pagina, $local = "site"){
        //global $app;
        
        $params = $this->carregarParametrosPagina();
        
        if($local == "site"){
            $tplEndereco = "/views/site/";
        }else{
            $tplEndereco = "/views/admin/";            
        }
        
        // lista de locais
        $listaLocais = $this->getLocalCTRL()->retornaLista(" AND tab.ativo ");
        
        $page = new Page([
            "header" => false,
            "footer" => false
        ], $tplEndereco);        
        $page->setTpl($pagina, array(
            "pagina" => $params,
            "latitudePadrao" => $this->getParametroCTRL()->retornaValor("latitude_padrao"),
            "longitudePadrao" => $this->getParametroCTRL()->retornaValor("longitude_padrao"),
            "googleAPIkey" => ConfigIni::googleAPIkey,
            "cidadeInstalacao" => $this->getParametroCTRL()->retornaValor("cidade_instalacao"),
            "estadoInstalacao" => $this->getParametroCTRL()->retornaValor("estado_instalacao"),
            "listaLocais" => $listaLocais,
        ));
    }
    
    /**
     * Destrutor
     */
    public function __destruct(){
        
    }
}

