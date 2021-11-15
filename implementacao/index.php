<?php
session_start();
require_once("vendor/autoload.php");

if(!defined('DS')) { define('DS',DIRECTORY_SEPARATOR); }
if(!defined('ROOT')) { define('ROOT',dirname(__FILE__)); }

use \Slim\Slim;

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;

use \MQS\ctrl\SiteCTRL;
use \MQS\ctrl\UsuarioCTRL;
use \MQS\ctrl\ParametroCTRL;
use \MQS\ctrl\PerfilCTRL;
use \MQS\ctrl\LocalCTRL;
use \MQS\ctrl\FornecedorTipoCTRL;
use \MQS\ctrl\VeiculoIncidenteTipoCTRL;
use \MQS\ctrl\FornecedorCTRL;
use \MQS\ctrl\ProprietarioCTRL;
use \MQS\ctrl\VeiculoCTRL;
use \MQS\ctrl\MotoristaCTRL;
use \MQS\ctrl\VeiculoViagemCTRL;
use \MQS\ctrl\VeiculoAbastecimentoCTRL;
use \MQS\ctrl\VeiculoIncidenteCTRL;
use \MQS\ctrl\UnidadeCTRL;
use \MQS\ctrl\RotaCTRL;

$app = new Slim();
$app->config('debug', true);


/** Rotas do site **/
$app->get('/', function() use ($app){ (new SiteCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->index(); });
$app->get('/inicio', function() use ($app){ (new SiteCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->index(); });
/** Fim rotas do site **/

/** Rotas do painel **/
$app->get('/login', function() use ($app) { (new UsuarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->pageLogin(); });
$app->post('/login', function() use ($app) { (new UsuarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->validaAcesso($_POST,$_SERVER); });
$app->get('/dashboard', function() use ($app){ (new UsuarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->pageDashboard(); });
$app->get('/logout', function(){ (new UsuarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->logout(); });
$app->get('/usuario_perfil', function() use ($app) { (new UsuarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getPerfil(); });
$app->post('/usuario_perfil', function() use ($app) { (new UsuarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setPerfil($_POST,$_FILES); });
$app->get('/ativar/:idRegistro', function($idRegistro = NULL) use ($app) { (new UsuarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->ativar($idRegistro); });
/** Fim rotas do painel **/


/** Rotas da área administrativa **/
// menu tabelas
$app->get('/admin/parametro_tabelas', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro(); });
$app->post('/admin/parametro_tabelas', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });
$app->get('/admin/usuario_tabelas', function() use ($app) { (new UsuarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro(); });
$app->post('/admin/usuario_tabelas', function() use ($app) { (new UsuarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });
$app->get('/admin/perfil_tabelas', function() use ($app) { (new PerfilCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro(); });
$app->post('/admin/perfil_tabelas', function() use ($app) { (new PerfilCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });
$app->get('/admin/fornecedor_tipo_tabelas', function() use ($app) { (new FornecedorTipoCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro(); });
$app->post('/admin/fornecedor_tipo_tabelas', function() use ($app) { (new FornecedorTipoCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });
$app->get('/admin/tabelas_veiculo_incidente_tipo', function() use ($app) { (new VeiculoIncidenteTipoCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro(); });
$app->post('/admin/tabelas_veiculo_incidente_tipo', function() use ($app) { (new VeiculoIncidenteTipoCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });

//menu cadastros
$app->get('/admin/unidade_cadastro(/:pagina)', function($pagina = 1) use ($app) { (new UnidadeCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/unidade_cadastro_registro(/:idRegistro)', function($idRegistro = NULL) use ($app) { (new UnidadeCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getRegistro($idRegistro); });
$app->post('/admin/unidade_cadastro', function() use ($app) { (new UnidadeCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });
$app->get('/admin/local_cadastro(/:pagina)', function($pagina = 1) use ($app) { (new LocalCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/local_cadastro_registro(/:idRegistro)', function($idRegistro = NULL) use ($app) { (new LocalCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getRegistro($idRegistro); });
$app->post('/admin/local_cadastro', function() use ($app) { (new LocalCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });
$app->get('/admin/rota_cadastro(/:pagina)', function($pagina = 1) use ($app) { (new RotaCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/rota_cadastro_registro(/:idRegistro)', function($idRegistro = NULL) use ($app) { (new RotaCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getRegistro($idRegistro); });
$app->post('/admin/rota_cadastro', function() use ($app) { (new RotaCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });
$app->get('/admin/proprietario_cadastro(/:pagina)', function($pagina = 1) use ($app) { (new ProprietarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/proprietario_cadastro_registro(/:idRegistro)', function($idRegistro = NULL) use ($app) { (new ProprietarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getRegistro($idRegistro); });
$app->post('/admin/proprietario_cadastro', function() use ($app) { (new ProprietarioCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });

$app->get('/admin/motorista_cadastro(/:pagina)', function($pagina = 1) use ($app) { (new MotoristaCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/motorista_cadastro_registro(/:idRegistro)', function($idRegistro = NULL) use ($app) { (new MotoristaCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getRegistro($idRegistro); });
$app->post('/admin/motorista_cadastro', function() use ($app) { (new MotoristaCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_SERVER,$_POST,$_FILES); });


$app->get('/admin/cadastro-veiculo(/:pagina)', function($pagina = 1) use ($app) { (new VeiculoCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/cadastro-veiculo-registro(/:idRegistro)', function($idRegistro = NULL) use ($app) { (new VeiculoCTRL())->getRegistro($idRegistro); });
$app->post('/admin/cadastro-veiculo', function() use ($app) { (new VeiculoCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });




$app->get('/admin/cadastro-fornecedor(/:pagina)', function($pagina = 1) use ($app) { (new FornecedorCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/cadastro-fornecedor-registro(/:idRegistro)', function($idRegistro = NULL) use ($app) { (new FornecedorCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getRegistro($idRegistro); });
$app->post('/admin/cadastro-fornecedor', function() use ($app) { (new FornecedorCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->setRegistro($_POST,$_FILES); });


$app->get('/admin/rotas-locais', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getPagina("rotas-locais"); });
$app->get('/admin/unidades-veiculos', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getPagina("unidades-veiculos"); });
$app->get('/admin/motoristas-veiculos', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getPagina("motoristas-veiculos"); });
$app->get('/admin/veiculos-rotas', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getPagina("veiculos-rotas"); });

$app->get('/admin/ficha-veiculo', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getPagina("ficha-veiculo"); });
$app->get('/admin/ficha-motorista', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getPagina("ficha-motorista"); });
$app->get('/admin/ficha-proprietario', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getPagina("ficha-proprietario"); });
$app->get('/admin/ficha-unidade', function() use ($app) { (new ParametroCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getPagina("ficha-unidade"); });

//menu registros
$app->get('/admin/viagem_lista(/:pagina)', function($pagina = 1) use ($app) { (new VeiculoViagemCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/abastecimento_lista', function($pagina = 1) use ($app) { (new VeiculoAbastecimentoCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/incidente_lista', function($pagina = 1) use ($app) { (new VeiculoIncidenteCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getListaRegistro($pagina); });
$app->get('/admin/mapa_deslocamento', function() use ($app) { (new LocalCTRL(new Banco(ConfigIni::AMBIENTE), new Datas(), new Textos()))->getMapaDeslocamento(); });

/** Fim rotas da área administrativa **/

$app->run();

?>