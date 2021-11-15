<?php
namespace MQS\ctrl;

use \MQS\Page;

/**
 * Classe para CTRL
 * 
 * @author Eduardo
 */
class AdminCTRL{

    /**
     * Construtor
     */
    public function __construct(){
        
    }
    
    /**
     * Método para acesso a página inicial
     * @return void
     */
    public static function index(){
        //global $app;
        $page = new Page();
        $page->setTpl("index");
    }

    /**
     * Método para acesso as páginas do site
     * @param string $pagina
     * @return void
     */
    public static function getPage($pagina){
        //global $app;
        $page = new Page();
        $page->setTpl($pagina);
    }
    
    /**
     * Destrutor
     */
    public function __destruct(){
        
    }
}

