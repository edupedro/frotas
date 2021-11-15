<?php
namespace MQS\ctrl;

if (!isset($_SESSION)) { session_start(); }

use \MQS\PageAdmin;
use \MQS\Mailer;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\dao\UsuarioDAO;
use \Exception;

/**
 * Classe de CTRL
 * 
 * @author Eduardo Andrade
 */
class UsuarioCTRL extends ModelCTRL {
    
    const SESSION   = "User";
    private $usuarioDAO;        
    
    private $perfilCTRL;
    private $localCTRL;
    
    /**
     * Construtor
     */
    public function __construct($banco = NULL, $datas = NULL, $textos = NULL){
        parent::__construct();
        
        if(!is_null($banco)){ $this->setBanco($banco); } else { $this->setBanco(new Banco(ConfigIni::AMBIENTE)); }
        if(!is_null($datas)){ $this->setDatas($datas); } else { $this->setDatas(new Datas()); }
        if(!is_null($textos)){ $this->setTextos($textos); } else { $this->setTextos(new Textos()); }
        
        $this->usuarioDAO = new UsuarioDAO($this->getBanco(), $this->getDatas(), $this->getTextos());
        
        $this->perfilCTRL   = null;
        $this->localCTRL    = null;
    }
    
    /**
     * Get para o objeto
     *
     * @return object
     */
    function getPerfilCTRL(){
        if(is_null($this->perfilCTRL)){
            $this->perfilCTRL = new PerfilCTRL($this->getBanco(), $this->getDatas(), $this->getTextos());
        }
        return $this->perfilCTRL;
    }
    
    /**
     * Get para o objeto
     *
     * @return object
     */
    function getLocalCTRL(){
        if(is_null($this->localCTRL)){
            $this->localCTRL = new LocalCTRL($this->getBanco(), $this->getDatas(), $this->getTextos());
        }
        return $this->localCTRL;
    }
    
    /**
     * Validar a existência de sessão
     * @return void
     */
    public static function verifyLogin(){
        if(!isset($_SESSION[UsuarioCTRL::SESSION]) || !$_SESSION[UsuarioCTRL::SESSION]){
            header("Location: /login");
            exit;
        }
    }
    
    /**
     * Sair do sistema
     * @return void
     */
    public static function logout(){
        $_SESSION[UsuarioCTRL::SESSION] = NULL;
        header("Location: /inicio");
        exit;
    }
    
    /**
     * Abrir página de login
     * @return void
     */
    public function pageLogin(){
        $_SESSION[UsuarioCTRL::SESSION] = NULL;
        
        $pagina = $this->carregarParametrosPagina();
        $chavePublica = $this->getParametroCTRL()->retornaValor("chave_publica_recaptcha");
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("login", array(
            "pagina" => $pagina,
            "chavePublica" => $chavePublica
        ));        
    }
    
    /**
     * Abrir página de cadastro
     * @return void
     */
    public function getCadastreSe(){
        $_SESSION[UsuarioCTRL::SESSION] = NULL;
        
        $pagina = $this->carregarParametrosPagina();
        $chavePublica = $this->getParametroCTRL()->retornaValor("chave_publica_recaptcha");
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("cadastre_se", array(
            "pagina" => $pagina,
            "chavePublica" => $chavePublica
        ));
    }
    
    /**
     * Realizar o cadastro de novo usuário
     * @param array $campos
     * @param array $server
     * @return void
     */
    public function cadastreSe($campos,$server){
        
        $usuario = array();
        $usuario['nome']			= $campos['nome'];
        $usuario['email']			= $campos['email'];
        $usuario['senha']			= $campos['senha'];
        $usuario['concordo']		= $campos['concordo'];
        $usuario['hash']			= md5($campos['email']);
        
        $pagina = $this->carregarParametrosPagina();
        
        // definir a chave secreta
        $chavePublica = $this->getParametroCTRL()->retornaValor("chave_publica_recaptcha");
        $secret = $this->getParametroCTRL()->retornaValor("chave_privada_recaptcha");
        if(isset($campos['g-recaptcha-response'])) { $captcha_data = $campos['g-recaptcha-response']; }
        
        try{
            
            if(ConfigIni::AMBIENTE == 1){
                // Se nenhum valor foi recebido, o usuário não realizou o captcha
                if (!$captcha_data) { throw new \Exception("Erro no ReCaptcha"); }
                
                # Os parâmetros podem ficar em um array
                $vetParametros = array (
                    "secret" => $secret,
                    "response" => $campos["g-recaptcha-response"],
                    "remoteip" => $server["REMOTE_ADDR"]
                );
                
                $vetResposta = $this->respostaReCaptha($vetParametros);
                
                # Analisa o resultado (no caso de erro, pode informar os códigos)
                if (!$vetResposta["success"]){
                    $msgErro = "";
                    foreach ($vetResposta["error-codes"] as $strErro) $msgErro .= "<p>Erro: $strErro</p></br>";
                    throw new \Exception($msgErro);
                }
            }
            
            $resultadoProcessamento = $this->inserir($usuario);
            if($resultadoProcessamento['novoId'] != 0){
                $temErro            = FALSE;
                $msgProcessamento   = "Seja bem-vindo ".$usuario['nome'].", <a href='/admin/login'>clique aqui</a> para acessar .";
                
                $arrayHost = array();
                $arrayHost['HOST'] = $this->getParametroCTRL()->retornaValor("smtp_host");
                $arrayHost['PORT'] = $this->getParametroCTRL()->retornaValor("smtp_port");
                $arrayHost['USER'] = $this->getParametroCTRL()->retornaValor("smtp_login");
                $arrayHost['PASS'] = $this->getParametroCTRL()->retornaValor("smtp_password");
                $arrayHost['FROM'] = $this->getParametroCTRL()->retornaValor("smtp_from");
                
                $arrayMsg = array();
                $arrayMsg['NAME']       = $usuario['nome'];
                $arrayMsg['TO']         = $usuario['email'];
                $arrayMsg['SuBJECT']    = "Seja bem-vindo!";
                                
                $usuario['link']        = $this->getParametroCTRL()->retornaValor("url_instalacao");
                
                $dados = array_merge($pagina, $usuario);
                $mailer = new Mailer($arrayHost, $arrayMsg, "email_entrada", $dados);
                $mailer->send();
                
            }else{
                $temErro    = TRUE;
                $msgProcessamento   = "Erro no seu cadastro tente novamente ou entre em contato com suporte";
            }
            
        } catch (\Exception $e) {
            $temErro    = TRUE;
            $msgProcessamento   = $e->getMessage();
        }
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("cadastre_se", array(
            "pagina" => $pagina,
            "processamento" => TRUE,
            "temErro" => $temErro,
            "chavePublica" => $chavePublica,
            "msgProcessamento" => $msgProcessamento
        ));
    }
    
    /**
     * Abrir página de usuário
     *
     * @return void
     */
    public function getListaRegistro(){
        UsuarioCTRL::verifyLogin();
        
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'usuario_manter' ");
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $isAdmin = $this->isAdmin($sessao);
        
        $idPerfilAdministrador = $this->getParametroCTRL()->retornaValor("perfil_administrador_id");
        $retorno = $this->listaGeral($this, " AND tab.acesso_perfil_id <> ".$idPerfilAdministrador." AND tab.acesso_perfil_id <> 1 " ," ORDER BY tab.id DESC ");
        $listaGeral     = $retorno['lista'];
        $qtdRegistros   = $retorno['qtdRegistros'];
        $qtdPaginas     = $retorno['qtdPaginas'];
        
        $listaPerfis    = $this->getPerfilCTRL()->retornaLista(" AND tab.id <> ".$idPerfilAdministrador." ORDER BY tab.id DESC ");
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("usuario_manter", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaPerfis" => $listaPerfis,
            "listaUsuarios" => $listaGeral,
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
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'usuario_manter' ");
        $isAdmin = $this->isAdmin($sessao);
        
        $idPerfilAdministrador = $this->getParametroCTRL()->retornaValor("perfil_administrador_id");  
        $listaPerfis    = $this->getPerfilCTRL()->retornaLista(" AND tab.id <> ".$idPerfilAdministrador." ORDER BY tab.id DESC ");
        
        $campos = array();
        $acao						= $valores['acao'];
        $campos['idUsuario']		= $valores['idUsuario'];
        $campos['nome']				= $valores['nome'];
        $campos['cpf']		        = $valores['cpf'];        
        $campos['email']			= $valores['email'];
        $campos['senha']			= $valores['senha'];
        $campos['confirmacao']		= $valores['confirmacao'];
        $campos['idPerfil']			= $valores['idPerfil'];
        $campos['liberado']			= $valores['liberado'];
        $campos['upload']			= $arquivos['upload'];
        $campos['idAnotador']		= $_SESSION['idUsuario'];
        
        try {
            switch($acao){
                case "excluir":
                    
                    $camposExcluir = array();
                    $camposExcluir['idUsuario']	    = $campos['idUsuario'];
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
                case "liberar":
                    
                    $camposLiberar = array();
                    $camposLiberar['idUsuario']	    = $campos['idUsuario'];
                    $camposLiberar['liberado']	    = $campos['liberado'];
                    $camposLiberar['idAnotador']	= $campos['idAnotador'];
                    $camposLiberar['validacao']     = 'false';
                    
                    $resultadoProcessamento = $this->alterar($camposLiberar);
                    $qtdErro = $resultadoProcessamento['qtdErro'];
                    
                    if($qtdErro == 0){
                        
                        $arrayHost = array();
                        $arrayHost['HOST'] = $this->getParametroCTRL()->retornaValor("smtp_host");
                        $arrayHost['PORT'] = $this->getParametroCTRL()->retornaValor("smtp_port");
                        $arrayHost['USER'] = $this->getParametroCTRL()->retornaValor("smtp_login");
                        $arrayHost['PASS'] = $this->getParametroCTRL()->retornaValor("smtp_password");
                        $arrayHost['FROM'] = $this->getParametroCTRL()->retornaValor("smtp_from");
                        
                        $arrayMsg = array();
                        $arrayMsg['NAME']           = $campos['nome'];
                        $arrayMsg['TO']             = $campos['email'];
                        $arrayMsg['SuBJECT']        = "Liberação de Acesso";
                        
                        $mensagem = $this->getParametroCTRL()->retornaValor("mensagem_liberado");
                        if($camposLiberar['liberado'] == "false"){
                            $mensagem = $this->getParametroCTRL()->retornaValor("mensagem_bloqueado");
                        }
                        $usuario['msg']            = $mensagem;
                        $usuario['emailContato']   = $this->getParametroCTRL()->retornaValor("email_contato_padrao");
                        $usuario['senha']          = $this->getParametroCTRL()->retornaValor("senha_inicio");
                        
                        $dados = array_merge($pagina, $usuario);
                        $mailer = new Mailer($arrayHost, $arrayMsg, "email_liberacao", $dados);
                        $mailer->send();
                        
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
                    
                    $where = " AND tab.id <> ".$idPerfilAdministrador." AND tab.acesso_perfil_id <> 1 ";
                    if(!empty($opcaoConsulta) && !empty($campoConsulta)){
                        $where .= " AND ".$opcaoConsulta." LIKE '%".$campoConsulta."%' ";
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
        } catch (\Exception $e) {
            $processamento = TRUE;
            $temErro       = TRUE;
            $msgProcessamento = $e->getMessage();
        }
        
        if($acao != "consultar"){
            $paginaAtual = 1;
            $retorno = $this->listaGeral($this, "  AND tab.acesso_perfil_id <> ".$idPerfilAdministrador." AND tab.acesso_perfil_id <> 1 " ," ORDER BY tab.id DESC ");
            $listaGeral     = $retorno['lista'];
            $qtdRegistros   = $retorno['qtdRegistros'];
            $qtdPaginas     = $retorno['qtdPaginas'];
            
        }
        
        $page = new PageAdmin();
        $page->setTpl("usuario_manter", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "listaPerfis" => $listaPerfis,
            "listaUsuarios" => $listaGeral,
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
     * Abrir página de perfil
     * @return void
     */
    public function getPerfil(){
        UsuarioCTRL::verifyLogin();
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("usuario_perfil", array(
            "pagina" => $pagina,
            "sessao" => $sessao
        ));
    }
    
    /**
     * Realizar alteração de senha do usuário
     * @param array $campos
     * @param array $arquivos
     * @return void
     */
    public function setPerfil($campos, $arquivos){
        UsuarioCTRL::verifyLogin();
        
        $usuario = array();
        $usuario['email']			= $campos['email'];
        $usuario['telefone']		= $campos['telefone'];
        $usuario['senha']			= $campos['senha'];
        $usuario['confirmacao']		= $campos['confirmacao'];
        $usuario['idUsuario']		= $campos['idUsuario'];
        $usuario['upload']			= $arquivos['uploadBtn'];
        $usuario['idAnotador']		= $campos['idUsuario'];
        
        try{
            
            $resultadoProcessamento = $this->alterar($usuario);
            if($resultadoProcessamento['qtdErro'] == 0){
                $temErro            = FALSE;
                $msgProcessamento   = "Atualização realizada com sucesso.";
            }else{
                $temErro    = TRUE;
                $msgProcessamento   = "Erro no seu cadastro tente novamente ou entre em contato com suporte@habiliter.com.br";
            }
            
        } catch (\Exception $e) {
            $temErro    = TRUE;
            $msgProcessamento   = $e->getMessage();
        }
        
        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $usuario = $this->retornaID($sessao['idUsuario']);
        
        $page = new PageAdmin([
            "header" => false,
            "footer" => false
        ]);
        $page->setTpl("usuario_perfil", array(
            "pagina" => $pagina,
            "processamento" => TRUE,
            "temErro" => $temErro,
            "msgProcessamento" => $msgProcessamento,
            "usuario" => $usuario,
            "sessao" => $sessao
        ));
    }
        
    /**
     * Abrir página Dashboard
     * @return void
     */
    public function pageDashboard(){
        UsuarioCTRL::verifyLogin();

        $pagina = $this->carregarParametrosPagina();
        $sessao = $_SESSION[UsuarioCTRL::SESSION];
        $isAdmin = $this->isAdmin($sessao);
        $funcionalidade = $this->getAcessoriosCTRL()->retornaFuncionalidades(" AND tab.codigo = 'acessar_sistema' ");
        
        $latitudePadrao     = $this->getParametroCTRL()->retornaValor("latitude_padrao");
        $longitudePadrao    = $this->getParametroCTRL()->retornaValor("longitude_padrao");
                
        // lista de locais
        $listaLocais = $this->getLocalCTRL()->retornaLista(" AND tab.ativo = 'true' ORDER BY tab.descricao ASC ");
        $qtdLocais = count($listaLocais);
        
        $page = new PageAdmin();
        $page->setTpl("dashboard", array(
            "pagina" => $pagina,
            "sessao" => $sessao,
            "funcionalidade" => $funcionalidade[0],
            "isAdmin" => $isAdmin,
            "qtdMeses" => ConfigIni::MESES_CONSULTA_GRAFICO,
            "latitudePadrao" => $latitudePadrao,
            "longitudePadrao" => $longitudePadrao,
            "googleAPIkey" => ConfigIni::googleAPIkey,
            "API_GOOGLE_MARKERCLUSTERER" => ConfigIni::API_GOOGLE_MARKERCLUSTERER,
            "API_GOOGLE_INFOBUBBLE" => ConfigIni::API_GOOGLE_INFOBUBBLE,
            "listaLocais" => $listaLocais,
            "qtdLocais" => $qtdLocais,
        ));
        
    }
    /**
     * Capturando a resposta do ReCaptcha
     *
     * @param array $vetParametros
     * @return array
     */
    private function respostaReCaptha($vetParametros){
        
        # Abre a conexão e informa os parâmetros: URL, método POST, parâmetros e retorno numa string
        $curlReCaptcha = curl_init();
        curl_setopt($curlReCaptcha, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($curlReCaptcha, CURLOPT_POST, true);
        curl_setopt($curlReCaptcha, CURLOPT_POSTFIELDS, http_build_query($vetParametros));
        curl_setopt($curlReCaptcha, CURLOPT_RETURNTRANSFER, true);
        
        # A resposta é um objeto json em uma string, então só decodificar em um array (true no 2º parâmetro)
        $vetResposta = json_decode(curl_exec($curlReCaptcha), true);
        
        # Fecha a conexão
        curl_close($curlReCaptcha);
        return $vetResposta;
    }
    
    /**
     * Validação de acesso por meio de login do atendimento
     *
     * @param array $campos
     * @param array $server
     * @return void
     */
    public function validaAcesso($campos, $server){

        $login      = addslashes(trim($campos['cpf']));
        $senha      = addslashes(trim($campos['senha']));
        $ip         = $server['REMOTE_ADDR'];
        
        // definir a chave secreta
        $chavePublica = $this->getParametroCTRL()->retornaValor("chave_publica_recaptcha");        
        $secret = $this->getParametroCTRL()->retornaValor("chave_privada_recaptcha");
        if(isset($campos['g-recaptcha-response'])) { $captcha_data = $campos['g-recaptcha-response']; }
        
        $config = new ConfigIni();
        
        try {
            
            if(ConfigIni::AMBIENTE == 1){            
                // Se nenhum valor foi recebido, o usuário não realizou o captcha
                if (!$captcha_data) { throw new \Exception("Erro no ReCaptcha"); }
                
                # Os parâmetros podem ficar em um array
                $vetParametros = array (
                    "secret" => $secret,
                    "response" => $campos["g-recaptcha-response"],
                    "remoteip" => $server["REMOTE_ADDR"]
                );
                
                $vetResposta = $this->respostaReCaptha($vetParametros);
                
                # Analisa o resultado (no caso de erro, pode informar os códigos)
                if (!$vetResposta["success"]){
                    $msgErro = "";
                    foreach ($vetResposta["error-codes"] as $strErro) $msgErro .= "<p>Erro: $strErro</p></br>";
                    throw new \Exception($msgErro);
                }
            }
            
            $where = " AND tab.ativo = 'true' AND tab.cpf = '".$login."' AND tab.liberado = 'true' ";
            $usuarios   = $this->usuarioDAO->retornaLista($where);
            
            if (count($usuarios) > 0) {
                
                if($usuarios[0]['senha'] == md5($senha)){
                    $this->registrarSessao($usuarios[0]);
                    $this->registrarLog($usuarios[0],$ip);
                    $this->registrarAcesso($usuarios[0],$ip);
                }else{
                    throw new \Exception($config->getConstante('MSG_LOGIN_ERRO2',"MSG"));
                }
                header("Location: /dashboard");
                exit;
                
            }else{
                throw new \Exception($config->getConstante('MSG_LOGIN_ERRO2',"MSG"));
            }
            
        } catch (\Exception $e) {
            $pagina = $this->carregarParametrosPagina();
            
            $page = new PageAdmin([
                "header" => false,
                "footer" => false
            ]);
            $page->setTpl("login", array(
                "pagina" => $pagina,
                "chavePublica" => $chavePublica,
                "msgErro" => $e->getMessage()
            ));
        }
    }	

    /**
     * Registrando a sessão do usuário
     *
     * @param array usuario
     * @return void
     */
    private function registrarSessao($usuario){        
        try {
            
            $where = " AND tab.ativo = 'true' ";
            $idPerfilAdministrador = $this->getParametroCTRL()->retornaValor("perfil_administrador_id");
            if($usuario['idPerfil'] != $idPerfilAdministrador){
                $where .= " AND tab.acesso_perfil_id = ".$usuario['idPerfil']." ORDER BY af.ordem ASC ";
                $listaFuncionalidades = $this->getAcessoriosCTRL()->retornaFuncionalidadePerfil($where);
            }else{
                $where .= " ORDER BY tab.ordem ASC ";
                $listaFuncionalidades = $this->getAcessoriosCTRL()->retornaFuncionalidades($where);
            }
            $usuario['Funcionalidades'] = $listaFuncionalidades;
            
            $lista = array();
            $usuario['Paciente'] = $lista[0];
            
            $_SESSION[UsuarioCTRL::SESSION] = $usuario;
        } catch (\Exception $e) {
            //do nothing
        }
    }    
    
    /**
     * Registrando o log de acessos
     *
     * @param array $usuario
     * @return void
     */
    private function registrarLog($usuario,$ip){
        $agora	= date("Y-m-d H:i:s");
        $txt	= $usuario['nome']." CPF: ".$usuario['cpf']." Data-Hora: ".$agora ." IP: ".$ip;
    
        try{
            $sql	= "INSERT INTO log_acessos (
                            descricao,
                            data_acesso,
                            ip_acesso,
                            acesso_usuario_id
                        ) VALUES (
                            '".$this->getTextos()->encodeToIso($txt)."',
                            '".$agora."',
                            '".$ip."',
                            '".$usuario['idUsuario']."'
                        )";
            $this->getBanco()->executarSql($sql);
        } catch (\Exception $e) {
            // do nothing
        }
        // limpando memória
        unset($txt,$ip,$usuario);
    }
    
    /**
     * Registrando o acesso do usuário
     *
     * @param array $usuario
     * @param string $ip
     * @return void
     */
    private function registrarAcesso($usuario,$ip){
        $campos = array();
        
        $campos['idUsuario']	= $usuario['idUsuario'];
        $campos['dataAcesso']	= date("d/m/Y H:i:s");
        $campos['acessos']		= $usuario['acessos'] + 1;
        $campos['ipAcesso']		= $ip;
        $campos['validacao']	= 'false';
        
        try{
            
            $this->alterar($campos);
            
        } catch (\Exception $e) {
            // do nothing
        }
    }
    
    /**
     * Retorna item
     *
     * @param int $id
     * @return array
     */
    public function retornaID($id, $lazy = FALSE){
        
        try{
            $dados = $this->usuarioDAO->retornaLista(" AND tab.id = ".$id." ");
            if (count($dados) > 0){
                return $dados[0];
            }        	    
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
    public function retornaLista($where, $lazy = FALSE){
        try {
            return $this->usuarioDAO->retornaLista($where);            
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
            return $this->usuarioDAO->retornaQTD($where);
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
        $retorno = array();
        
        try{
            $erro		= FALSE;
            $msgErro	= array();
            if($tipo == "CADASTRAR"){
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['nome']); if($erro){ $msgErro[] = "O campo NOME deve ser preenchido."; } }
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['email']); if($erro){ $msgErro[] = "O campo EMAIL deve ser preenchido."; } }
                if(!$erro){ $erro = $this->getValida()->validaVazio($campos['idPerfil']); if($erro){ $msgErro[] = "O campo PERFIL deve ser preenchido."; } }
            }
            
            if(!$erro){
                $itens = array();
                
                $itens['tabela']	= "acesso_usuarios";
                $itens['where']		= " email = '".$campos['email']."' ";
                if($tipo == "ALTERAR"){
                    $erro = $this->getValida()->validaVazio($campos['idUsuario']);
                    if($erro){ $msgErro[] = "Defina o registro para alteração."; }
                    $itens['where']		= " email = '".$campos['email']."' AND id <> ".$campos['idUsuario']." ";
                }
                if($this->getValida()->validaExiste($itens)){
                    $erro = TRUE;
                    if($erro){ $msgErro[] = "Registro já existente na base de dados."; }
                }
            }
            $retorno['erro']	= $erro;
            $retorno['msgErro']	= $msgErro;
        }catch (\Exception $e){
            $retorno['erro']	= TRUE;
            $retorno['msgErro']	= $e->getMessage();
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
        
        // operação
        try{
            if(!$validacao['erro']){

                $user = $this->retornaID($campos['idUsuario']);
                
                //upload
                if(!empty($campos['upload']['name'])){
                    // excluindo imagem anterior
                    if($user['imagem'] != "default-avatar.png"){
                        @unlink(ROOT . DS . "views/images/pessoas/".$user['imagem']);
                    }
                    $this->getUpload()->setValores($campos['upload'],ROOT . DS . "views/images/pessoas/", $campos['idUsuario']."_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                
                $this->usuarioDAO->alterar($campos);
            }
            
            $retorno['erro'] 	= $validacao['erro'];
            $retorno['msgErro']	= $validacao['msgErro'];
            $retorno['qtdErro']	= 0;                            
        }catch (\Exception $e){
            $retorno['erro'] 	= TRUE;
            $retorno['msgErro'][]= $e->getMessage();
            $retorno['qtdErro']	= 1;
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
        if(empty($campos['idPerfil'])){
            $campos['idPerfil'] = $this->getParametroCTRL()->retornaValor("perfil_padrao_id");
        }
        if(empty($campos['senha'])){
            $campos['senha']	= $this->getParametroCTRL()->retornaValor("senha_inicio");
        }
        if(empty($campos['imagem'])){
            $campos['imagem']	= $this->getParametroCTRL()->retornaValor("imagem_avatar_padrao_pessoa");
        }
        if(empty($campos['cadastrarPessoa'])){
            $campos['cadastrarPessoa']	= "true";
        }
        
        // validação
        $this->valida   = $this->getValida();
        $validacao = $this->validacoes($campos, "CADASTRAR");
        
        // operação            
        try{
            if(!$validacao['erro']){
                
                //upload
                if(!empty($campos['upload']['name'])){
                    
                    $this->getUpload()->setValores($campos['upload'],ROOT . DS . "views/images/pessoas/",$campos['email']."_");
                    $retorno	= $this->getUpload()->salvar();
                    if($retorno['resultado']){
                        $campos['imagem']	= $retorno['nome'];
                    }else{
                        $validacao['erro']		= TRUE;
                        $validacao['msgErro'][]	= $retorno['erro'];
                    }
                }
                
                $novoId = $this->usuarioDAO->inserir($campos);
                if($novoId == 0){
                    $validacao['erro']		= TRUE;
                    $validacao['msgErro'][] = "Erro no processamento do cadastro.";
                }
            }
            
            $retorno['erro'] 	  = $validacao['erro'];
            $retorno['msgErro'][] = $validacao['msgErro'];
            $retorno['novoId']	  = $novoId;
        }catch (\Exception $e){
            $retorno['erro'] 	  = TRUE;
            $retorno['msgErro'][]= $e->getMessage();
            $retorno['novoId']	= 0;
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
    
} // class

