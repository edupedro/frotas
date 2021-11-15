<?php 

if (!isset($_SESSION)) { session_start(); }

ini_set('memory_limit', '512M');
ini_set('error_reporting', FALSE);

require_once("vendor/autoload.php");

if(!defined('DS')) { define('DS',DIRECTORY_SEPARATOR); }
if(!defined('ROOT')) { define('ROOT',dirname(__FILE__)); }

use \MQS\lib\ConfigIni;
use \MQS\lib\Banco;
use \MQS\lib\Datas;
use \MQS\lib\Textos;
use \MQS\lib\PdfCreator;

use \Fpdf\Fpdf;

use \MQS\ctrl\ParametroCTRL;
use \MQS\ctrl\UsuarioCTRL;
use \MQS\ctrl\LocalCTRL;
use \MQS\ctrl\AgendaPacienteCTRL;

$banco		= new Banco(ConfigIni::AMBIENTE);
$datas		= new Datas();
$textos		= new Textos();
$pdf		= new PdfCreator('relatorio',ConfigIni::TITULO_RELATORIO);

$parametroCTRL      = new ParametroCTRL();
$localCTRL          = new LocalCTRL();
$agendaPacienteCTRL = new AgendaPacienteCTRL();

$sessao = $_SESSION[UsuarioCTRL::SESSION];


if($_SERVER['REQUEST_METHOD'] == "POST"){
    $campos['idLocal']          = $_POST['idLocal'];
    $campos['dataReferencia']   = $_POST['dataReferencia'];
}


$local = array();
if(!empty($campos['idLocal'])){
    $local = $localCTRL->retornaID($campos['idLocal']);
}

$where = " AND tab.ativo = 'true' ";
if(!empty($campos['idLocal'])){
    $where .= " AND agendaDia.agenda_id IN ( SELECT ag.id FROM agendas AS ag WHERE ag.local_id = ".$campos['idLocal']." ) ";
}
if(!empty($campos['dataReferencia'])){
    $where .= " AND agendaDia.data_referencia >= '".$datas->padraoEuaIncompleto($campos['dataReferencia'])."' ";
}
$where = "  ORDER BY agendaDia.data_referencia, paciente.nome ";

$listaAgendamentos = $agendaPacienteCTRL->retornaLista($where, TRUE);


//abertura da página
$pdf->SetAutoPageBreak(true,40);
$pdf->AliasNbPages('{nb}');

// capa
$pdf->AddPage();
$pdf->SetXY(5, 5);

//$pdf->Image(ROOT . DS. 'views/images/template_topo.jpg', 0, 0, 290, 40);
$pdf->Ln(40);
$pdf->SetFont('Arial','B','20');
$pdf->Cell(0, 5, utf8_decode('Agendamentos do Dia'),0,1,'C');
$pdf->Ln(5);


$pdf->SetFont('Arial','B','12');
$pdf->Cell(30, 8, "CPF" ,1,0,'L');
$pdf->Cell(100, 8, "Nome" ,1,0,'L');
$pdf->Cell(30, 8, "Telefone" ,1,0,'C');
$pdf->Cell(30, 8, "Agendamento" ,1,0,'C');
$pdf->Cell(0, 8, "Assinatura" ,1,1,'L');

$pdf->SetFont('Arial','','9');
foreach ($listaAgendamentos as $item) {
    $pdf->Cell(30, 8, $textos->encodeToIso($item['cpf']) ,1,0,'L');
    $pdf->Cell(100, 8, $textos->encodeToIso($item['paciente']) ,1,0,'L');
    $pdf->Cell(30, 8, $textos->encodeToIso($item['Paciente']['telefone']) ,1,0,'C');
    $pdf->Cell(30, 8, $textos->encodeToIso($item['dataReferencia']) ,1,0,'C');
    $pdf->Cell(0, 8, "" ,1,1,'L');    
}


$filename = "relatorio-agenda-dia-".date("Y-m-d").".pdf";

//fechamento
//$pdf->Output('imprimir_registro.pdf', 'D');
$pdf->Output($filename, 'I');

?>