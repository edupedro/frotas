<?php
namespace MQS\ctrl;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\MotoristaHabilitacaoDAO;
use \Exception;

/** 
 * Classe para CTRL
 * 
 * @author Eduardo Andrade
 */
class MotoristaHabilitacaoCTRL extends ModelCTRL{
    
    private $motoristaHabilitacaoDAO;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->motoristaHabilitacaoDAO   = new MotoristaHabilitacaoDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
    }
    
    /**
     * Retorna o registro
     *
     * @param int $id
     * @param boolean $lazy
     * @return array
     */
    public function retornaID($id, $lazy = FALSE){
        $retorno = array();
        
        try {
            if(!empty($id)){
                $dados = $this->retornaLista(" AND tab.id = ".$id." ", $lazy);
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
     * @param boolean $lazy
     * @return array
     */
    public function retornaLista($where, $lazy = NULL){
        try {
            if(is_null($lazy)){
                $lazy = FALSE;
            }
            
            return $this->motoristaHabilitacaoDAO->retornaLista($where, $lazy);            
        } catch (\Exception $e) {
            return array();
        }
    }
    
    /**
     * Retorna quantidade
     *
     * @param string $where
     * @return array
     */
    public function retornaQTD($where){
        try {
            return $this->motoristaHabilitacaoDAO->retornaQTD($where);            
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
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['idMotorista']); if($erro){ $msgErro[] = "O campo MOTORISTA deve ser preenchido."; } }
            }
            if(!$erro){
                $itens['tabela']	= "motoristas_habilitacoes";
                $itens['where']		= " numero = '".$campos['numero']."' AND motorista_id = '".$campos['idMotorista']."' ";
                if($tipo == "ALTERAR"){
                    $erro = $this->getValida()->validaVazio($campos['idMotoristaHabilitacao']);
                    if($erro){ $msgErro[] = "Defina o registro para alteção."; }
                    $itens['where']		= " numero = '".$campos['numero']."' AND motorista_id = '".$campos['idMotorista']."'    
                                            AND ativo = 'true' AND id <> ".$campos['idMotoristaHabilitacao']." ";
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
        if(empty($campos['motoristaHabilitacao'])){
            $campos['motoristaHabilitacao'] = $this->gerarCodigo();
        }
        // validação
        if($campos['validacao'] != 'false'){
            $validacao = $this->validacoes($campos, "CADASTRAR");
        }
        
        try {
            // operação
            if(!$validacao['erro']){
                
                //upload
                if(!empty($campos['uploadConteudo']['name'])){
                    $this->getUpload()->setValores($campos['uploadConteudo'],ROOT . DS . "views/images/habilitacoes/", "cnh_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['conteudo']		= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }                
                
                $novoId = $this->motoristaHabilitacaoDAO->inserir($campos);
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
        if($campos['validacao'] != 'false'){
            $validacao = $this->validacoes($campos, "ALTERAR");
        }
        
        try {
            // operação
            if(!$validacao['erro']){

                // upload
                $user = $this->retornaID($campos['idMotoristaHabilitacao']);
                
                if(!empty($campos['uploadConteudo']['name'])){
                    if($user['conteudo'] != "default-avatar.png"){
                        unlink(ROOT . DS . "views/images/habilitacoes/".$user['conteudo']);
                    }
                    $this->getUpload()->setValores($campos['uploadConteudo'],ROOT . DS . "views/images/habilitacoes/", "cnh_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['conteudo']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                
                $this->motoristaHabilitacaoDAO->alterar($campos);
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
    public function gerarCodigo($id = NULL){
        $lista = $this->retornaLista("ORDER BY tab.id DESC LIMIT 1");
        $tam = count($lista);
        
        if($tam>0){
            // pegando o último registro
            $numero		= (int) $lista[0]['motoristaHabilitacao'];
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
     */
    public function __destruct() {

    }
    
}// class