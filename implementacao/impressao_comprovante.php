<?php 

	//ini_set('memory_limit', '512M');
	ini_set('error_reporting', FALSE);
	
	require_once("vendor/autoload.php");
	
	use Fpdf\Fpdf;
    //use MQS\ctrl\ParametroCTRL;
	
	//$parametroCTRL = new ParametroCTRL();
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
	
		//$idPagamento  = $_POST['idPagamento'];
		//$idProjeto    = $parametroCTRL->retornaValor("produto_padrao");
	
	
	}
	
	/*
	//removendo imagens anteriores
	if(file_exists("images/level_L.png")){ unlink("level_L.png"); }
	if(file_exists("images/codbarras.gif")){ unlink("codbarras.gif"); }
	
	$URL_VALID_CODE = $parametroCTRL->retornaValor("url_validacao_qrcode");
	$transacao		= str_replace("T", "", $pagamento['transacao']);
	$transacao		= str_replace("D", "", $transacao);
	
	$codeContents = $transacao;
	$codeContents = $URL_VALID_CODE."?yk=".md5($codeContents)."";
	QRcode::png($codeContents, 'images/level_L.png', QR_ECLEVEL_M);
	
	//$barCode 		 = new barCodeGenrator($transacao, 1 ,"images/codbarras.gif", 130, 135, false);
	*/
	// construindo PDF
	$pdf = new FPDF('P', 'mm', 'A4');
	//$pdf = new FPDF('L','cm',array(20,10));
	
	$pdf->SetCreator("Grupo MQS. Todos os direitos reservados.");
	$pdf->SetAuthor("Grupo MQS");
	$pdf->SetTitle(utf8_decode("Impressão Voucher"));
	$pdf->SetSubject("Vouvher");
	
	$pdf->AddPage();
	$pdf->SetXY(5, 5);
	
	//Imagem('nome do arquivo', alinhamento eixo x, alinhamento eixo y, witdh, heigth, jpg - jpeg - png);
	//$pdf->Image("res/compra/images/voucher_01.png", 1, 1, 210, 90);
	//$pdf->Image("res/compra/images/marca_corte.png", 1, 95, 208);

	//$pdf->Image("res/compra/images/level_L.png", 175, 1, 32);
	//$pdf->Image("res/compra/images/codbarras.gif", 170, 60, 40, 20);
		
	/*
	$pdf->SetFont('Arial', '', 12);
	$pdf->Cell(60,7,'',0,0,'L');
	$pdf->Cell(130,7, utf8_decode($projeto['titulo']) ,0,1,'L');	
	$pdf->Cell(55,7,'',0,0,'L');
	$pdf->Cell(130,7, utf8_decode($pagamento['nome']) ,0,1,'L');
	$pdf->Cell(55,7,'',0,0,'L');
	$pdf->Cell(130,7,utf8_decode($pagamento['cpf']),0,1,'L');

	$pdf->SetFont('Arial', '', 9);
	$pdf->Cell(60,32,'',0,1,'L');
	$pdf->Cell(80,6,'',0,0,'L');
	$pdf->Cell(100,6, utf8_decode("TRANSAÇÃO ".$pagamento['transacao']) ,0,1,'L');
	$pdf->Cell(80,6,'',0,0,'L');
	$pdf->Cell(100,6, utf8_decode("SOLICITAÇÃO ".$pagamento['dataSolicitacao']) ,0,1,'L');
	$pdf->Cell(80,6,'',0,0,'L');
	$pdf->Cell(100,6, utf8_decode("STATUS ".$pagamento['pagamentoStatus']) ,0,1,'L');
	$pdf->Cell(80,6,'',0,0,'L');
	$pdf->Cell(100,6, utf8_decode("PAGAMENTO ".$pagamento['dataPagamento']) ,0,1,'L');
	
	$pdf->Cell(60,20,'',0,1,'L');
	$pdf->SetFont('Arial', 'B', 14);
	$pdf->Cell(190,20, $parametroCTRL->retornaValor("msg_voucher") ,1,1,'C');
	*/
	// attachment name
	$filename = "voucher-.pdf";
	$pdf->Output($filename, 'I');

?>