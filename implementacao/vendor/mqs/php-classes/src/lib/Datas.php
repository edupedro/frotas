<?php
namespace MQS\lib;

/**
 * Classe de manipulação de datas
 *
 * @author Eduardo Andrade
 */
class Datas {

	private static $instance = null;

	/**
	 * Construtor
	 */
	function __construct(){

	}

	/**
	 * Retorna instância do singleton
	 * @return object
	 */
	public static function getInstance(){
		if(isset(self::$instance) || is_null(self::$instance)){
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * Calcular diferença entre duas datas
	 * @param string $d1
	 * @param string $d2
	 * @param string $type
	 * @param string $sep
	 * @return int
	 */
	function diferencaDatas($d1, $d2, $type='', $sep='-'){
		$d1 = explode($sep, $d1);
		$d2 = explode($sep, $d2);

		switch ($type){
			case 'A':
				$X = 31536000;
				break;
			case 'M':
				$X = 2592000;
				break;
			case 'D':
				$X = 86400;
				break;
			case 'H':
				$X = 3600;
				break;
			case 'MI':
				$X = 60;
				break;
			default:
				$X = 1;
		}
		$valor = mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]) - mktime(0, 0, 0, $d1[1], $d1[2], $d1[0] );
		return floor( ( ($valor) / $X ) );
	}


	/**
	 * Diferença de datas em dias (AAAA-MM-DD)
	 *
	 * @param string $menor
	 * @param string $maior
	 * @return string
	 */
	function diferencaDias($menor,$maior){
		if (empty($menor) || empty($maior) || $menor == "0000-00-00" || $maior == "0000-00-00") {
			$dias = "";
			return $dias;
		}
		$j = preg_split("/-/",$menor);
		$l = preg_split("/-/",$maior);
		$tempo1 = @mktime(0,0,0,$j[1],$j[2],$j[0]);
		$tempo2 = @mktime(0,0,0,$l[1],$l[2],$l[0]);
		$dias = (((($tempo2 - $tempo1)/60)/60)/24);
		return $dias;
	}


	/**
	 * Diferença de datas em anos (AAAA-MM-DD)
	 *
	 * @param string $menor
	 * @param string $maior
	 * @return string
	 */
	function diferencaAnos($menor,$maior){
		if (empty($menor) || empty($maior)) {
			$dias = "";
			return $dias;
		}
		$j = preg_split("/-/",$menor);
		$l = preg_split("/-/",$maior);
		$tempo1 = @mktime(0,0,0,$j[1],$j[2],$j[0]);
		$tempo2 = @mktime(0,0,0,$l[1],$l[2],$l[0]);
		$anos = (((((($tempo2 - $tempo1)/60)/60)/24)/30)/12);
		$anos = floor($anos);
		return $anos;
	}

	/**
	 * Preenche array com as informações da data (DD/MM/AAAA)
	 *
	 * @param string $dataEnviada
	 * @return array
	 */
	function separaDataIncompleta($dataEnviada){
		if (empty($dataEnviada) || $dataEnviada == '00/00/0000') { return ""; }
		$dia = substr($dataEnviada,0,2);
		$mes = substr($dataEnviada,3,2);
		$ano = substr($dataEnviada,6,4);
		$data   = array();
		$data[0] = $ano;
		$data[1] = $mes;
		$data[2] = $dia;
		return $data;
	}

	/**
	 * Converte a data do formato AAAA-MM-DD em DD/MM/AAAA
	 *
	 * @param string $data
	 * @return string
	 */
	function padraoBrasilIncompleto($data) {
		if (empty($data) || $data == '0000-00-00') { return "";	}
		$mes = substr($data,5,2);
		$dia = substr($data,8,2);
		$ano = substr($data,0,4);

		if(!checkdate($mes, $dia, $ano)){ return ""; }

		$data = $dia."/".$mes."/".$ano;
		return $data;
	}

	/**
	 * Converte a data do formato AAAA-MM-DD HH:MM:SS em DD/MM/AAAA HH:MM:SS
	 *
	 * @param string $data
	 * @return string
	 */
	function padraoBrasilCompleto($data){
		if (empty($data) || $data == '0000-00-00 00:00:00') { return "";	}
		$mes = substr($data,5,2);
		$dia = substr($data,8,2);
		$ano = substr($data,0,4);
		$hora = substr($data,11,2);
		$min = substr($data,14,2);
		$seg = substr($data,17,2);

		if(!checkdate($mes, $dia, $ano)){ return ""; }

		$data = $dia."/".$mes."/".$ano." ".$hora.":".$min.":".$seg;
		return $data;
	}

	/**
	 * Converte a data do formato DD/MM/AAAA em AAAA-MM-DD
	 *
	 * @param string $data
	 * @return string
	 */
	function padraoEuaIncompleto($data) {
		if (empty($data) || $data == '00/00/0000') { return "";	}
		$dia = substr($data,0,2);
		$mes = substr($data,3,2);
		$ano = substr($data,6,4);
		$data = $ano."-".$mes."-".$dia;
		return $data;
	}

	/**
	 * Converte a data do formato DD/MM/AAAA HH:MM:SS em AAAA-MM-DD HH:MM:SS
	 *
	 * @param string $data
	 * @return string
	 */
	function padraoEuaCompleto($data) {
		if (empty($data) || $data == '00/00/0000 00:00:00') { return "";	}
		$dia = substr($data,0,2);
		$mes = substr($data,3,2);
		$ano = substr($data,6,4);
		$hora = substr($data,11,2);
		$min = substr($data,14,2);
		$seg = substr($data,17,2);
		$data = $ano."-".$mes."-".$dia." ".$hora.":".$min.":".$seg;
		return $data;
	}

	/**
	 * Retorna o data no passado ou futuro
	 *
	 * @param string $operacao
	 * @param int $qtd
	 * @param string $tipo
	 * @param string $dataReferencia
	 * @return string
	 */
	function dataPassadoFuturo($operacao, $qtd, $tipo, $dataReferencia){
		return date("Y-m-d", strtotime(" ".$operacao."".$qtd." ".$tipo." ", strtotime($dataReferencia) ) );
	}

	/**
	 * Retorna o último dia do mês
	 *
	 * @param string $mes
	 * @param string $ano
	 * @return string
	 */
	function ultimoDiaMes($mes,$ano){
		$ultimo_dia = date("t", mktime(0,0,0,$mes,'01',$ano));
		return $ultimo_dia;
	}

	/**
	 * Saber se é fim de semana
	 *
	 * @param string $data
	 * @return boolean
	 */
	function eFimDeSemana($data) {
		return (date('N', strtotime($data)) >= 6);
	}

	/**
	 * Saber se é domingo
	 *
	 * @param string $data
	 * @return boolean
	 */
	function eDomingo($data) {
		return (date('N', strtotime($data)) > 6);
	}

	/**
	 * Saber idade atual
	 *
	 * @param string $data_nasc (Y-m-d)
	 * @return int
	 */
	function calculaIdade($data_nasc){
		$data_nasc  = explode("-", $data_nasc);
		$retorno    = 0;

		$data = date("Y-m-d");
		$data = explode("-", $data);
		$anos = $data[0] - $data_nasc[0];

		//echo $data_nasc[1]." asdf ".$data[1]." <br>";
		//echo $data_nasc[2]." asdf ".$data[2]." <br>";
		//echo $data_nasc[0]." asdf ".$data[0]." <br>";
		//echo $anos." asdfasd ";

		if ( $data_nasc[1] >= $data[1] ){
			if ( ($data_nasc[1] == $data[1]) && ($data_nasc[2] <= $data[2]) ){
				$retorno = $anos;
			}else{
				$retorno = $anos-1;
			}
		}else{
			$retorno = $anos;
		}

		return $retorno;
	}

	/**
	 * Sigla do mês
	 *
	 * @param int $pos
	 * @return string
	 */
	function siglaMes($pos){
		$meses = array("JAN","FEV","MAR","ABR","MAI","JUN","JUL","AGO","SET","OUT","NOV","DEZ");
		return $meses[$pos-1];
	}

	/**
	 * Escreve a data por extenso
	 *
	 * @param string $data
	 * @return string
	 */
	function escreveData($data) {
		$vardia = substr($data, 8, 2);
		$varmes = substr($data, 5, 2);
		$varano = substr($data, 0, 4);

		$convertedia = date ("w", mktime (0,0,0,$varmes,$vardia,$varano));
		$diaSemana = array("Domingo", "Segunda-feira", utf8_decode("Terça-feira"), "Quarta-feira", utf8_decode("Quinta-feira"), "Sexta-feira", utf8_decode("Sábado"));
		$mes = array("Janeiro","Fevereiro",utf8_decode("Março"),"Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

		return $diaSemana[$convertedia] . ", " . $vardia  . " de " . $mes[$varmes-1] . " de " . $varano;
	}

	/**
	 * Escreve data de referência
	 *
	 * @param string $data
	 * @return string
	 */
	function escreveDataReferencia($data) {
		$varmes = substr($data, 5, 2);
		$varano = substr($data, 0, 4);

		$mes = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

		return $mes[$varmes-1] . " de " . $varano;
	}

	/**
	 * Identifica o dia da semana
	 *
	 * @param string $data
	 * @return string
	 */
	function capturaDia($data){
		$vardia = substr($data, 8, 2);
		$varmes = substr($data, 5, 2);
		$varano = substr($data, 0, 4);

		$convertedia = date ("w", mktime (0,0,0,$varmes,$vardia,$varano));
		$diaSemana = array("Domingo", "Segunda-feira", utf8_decode("Terça-feira"), "Quarta-feira", "Quinta-feira", "Sexta-feira", utf8_decode("Sábado"));

		return $diaSemana[$convertedia];
	}

	/**
	 * Como usar: echo semana(0) // Sairá o dia referente ao mês no domingo
	 *
	 *  0 = Domingo
	 *  1 = Segunda
	 *  2 = Terça
	 *  3 = Quarta
	 *  4 = Quinta
	 *  5 = Sexta
	 *  6 = Sábado
	 *
	 * @param string $dia
	 * @return string
	 */
	function semana($dia){
		$func_dia = date(j);
		$func_semana = date(w);
		$func_ano = date(Y);
		$func_mes = date(m);
		$resto = $func_ano % 4;

		//Determina final do mês para ano bissexto
		if($resto == 0){
			$bissexto = 29;
		}else{
			$bissexto = 28;
		}

		$mes = array();
		$sem = array();

		switch($func_mes){
			// $mes[0] corresponde ao mes anterior e o $mes[1] ao atual
			case"01": $mes[0] = 31; $mes[1] = 31; break;
			case"02": $mes[0] = 31; $mes[1] = $bissexto; break;
			case"03": $mes[0] = $bissexto; $mes[1] = 31; break;
			case"04": $mes[0] = 31; $mes[1] = 30; break;
			case"05": $mes[0] = 30; $mes[1] = 31; break;
			case"06": $mes[0] = 31; $mes[1] = 30; break;
			case"07": $mes[0] = 30; $mes[1] = 31; break;
			case"08": $mes[0] = 31; $mes[1] = 31; break;
			case"09": $mes[0] = 31; $mes[1] = 30; break;
			case"10": $mes[0] = 30; $mes[1] = 31; break;
			case"11": $mes[0] = 31; $mes[1] = 30; break;
			case"12": $mes[0] = 30; $mes[1] = 31; break;
		}

		//Checa se não for domingo porque é o começo da tabela de 0 a 6
		if ($func_semana != 0){
			//Contagem regressiva do ponto atual da semana
			$s = 0; //somatório
			for ($n=$func_semana;$n>=0;$n--){
				//Checa se um dos dias for 1
				if($sem[$n+1] == 1){
					$sem[$n] = $mes[0];
					$func_dia = $mes[0];
					$s=1;
				}else{
					$sem[$n] = $func_dia - $s++;
				}
			}
			//Contagem normal do ponto atual da semana
			$s=1; //somatório
			$func_dia = date(j);
			for ($n=$func_semana+1;$n<=6;$n++){
				//Checa se é fim do mês
				if($sem[$n-1] == $mes[1]){
					$sem[$n] = 1;
					$func_dia = 1;
					$s = 1;
				}else{
					$sem[$n] = $func_dia + $s++;
				}
			}
		} else {
			$sem[0] = $func_dia;
			$s=1;
			for ($n=1;$n<=6;$n++){
				//Checa se é fim do mês
				if     ($sem[$n-1] == $mes[1]){
					$sem[$n] = 1;
					$func_dia = 1;
					$s = 1;
				}else{
					$sem[$n] = $func_dia + $s++;
				}
			}
		}
		return $sem[$dia];
	}

	/**
	 * Somar horas
	 *
	 * @param string $hora1
	 * @param string $hora2
	 * @return string
	 */
	function somaHora($hora1,$hora2){
		//if(empty($hora1)){ $hora1 = "0:00:00:00"; }
		//if(empty($hora2)){ $hora2 = "0:00:00:00"; }
		if(empty($hora1)){ $hora1 = "00:00:00"; }
		if(empty($hora2)){ $hora2 = "00:00:00"; }

		//if(strlen($hora1) <= 8){ $hora1 = "0:".$hora1; }
		//if(strlen($hora2) <= 8){ $hora2 = "0:".$hora2; }

		$h1 = explode(":",$hora1);
		$h2 = explode(":",$hora2);

		/*
		 $segundo = $h1[3] + $h2[3];
		 $minuto  = $h1[2] + $h2[2];
		 $horas   = $h1[1] + $h2[1];
		 $dia     = $h1[0] + $h2[0];
		*/

		$segundo = $h1[2] + $h2[2];
		$minuto  = $h1[1] + $h2[1];
		$horas   = $h1[0] + $h2[0];

		if($segundo > 59){
			$segundodif = $segundo - 60;
			$segundo = $segundodif;
			$minuto = $minuto + 1;
		}

		if($minuto > 59){
			$minutodif = $minuto - 60;
			$minuto = $minutodif;
			$horas = $horas + 1;
		}
		/*
		 if($horas > 24){
		 $num = 0;
		 (int)$num = $horas / 24;
		 $horaAtual = (int)$num * 24;
		 $horasDif = $horas - $horaAtual;
		 $horas = $horasDif;

		 for($i = 1; $i <= (int)$num; $i++){
		 $dia +=  1 ;
		 }
		 }
		 */
		if(strlen($horas) == 1){ $horas = "0".$horas; }
		if(strlen($minuto) == 1){ $minuto = "0".$minuto; }
		if(strlen($segundo) == 1){ $segundo = "0".$segundo; }

		//return  $dia.":".$horas.":".$minuto.":".$segundo;
		return  $horas.":".$minuto.":".$segundo;
	}

	/**
	 * Definir horário
	 *
	 * @param string $data YYYY-mm-dd H:i:s
	 * @param string $tempo
	 * @param string $operacao
	 * @return string
	 */
	function calculaHorario($data,$tempo,$operacao){
		$mes 	= substr($data,5,2);
		$dia 	= substr($data,8,2);
		$ano 	= substr($data,0,4);
		$hora 	= substr($data,11,2);
		$min 	= substr($data,14,2);
		$seg 	= substr($data,17,2);

		if($operacao == "soma"){
			$data = date('Y-m-d H:i:s',mktime($hora+$tempo,$min,$seg,$mes,$dia,$ano));
		}else{
			$data = date('Y-m-d H:i:s',mktime($hora-$tempo,$min,$seg,$mes,$dia,$ano));
		}
		return $data;
	}

	/**
	 * Saber a data de vencimento
	 *
	 * @param string $data
	 * @param string $tempo
	 * @param string $tipo
	 * @return string
	 */
	function calculaVencimentos($data,$tempo,$tipo){
		$novadata = explode("-",$data);
		$ano = $novadata[0];
		$mes = $novadata[1];
		$dia = $novadata[2];

		//PARA DESCOBRIR QUAL DATA SERÁ DAQUI A 5 DIAS
		//echo date('d/m/Y',mktime(0,0,0,$mes,$dia+5,$ano));
		//PARA DESCOBRIR QUAL SERÁ O DIA AMANHÃ
		//echo date('d/m/Y',mktime(0,0,0,$mes,$dia+1,$ano));
		//PARA MÊS QUE VEM
		//echo date('d/m/Y',mktime(0,0,0,$mes + 1,$dia,$ano));
		//PARA ANO QUE VEM
		//echo date('d/m/Y',mktime(0,0,0,$mes,$dia,$ano + 1));

		if ($tempo==0 && $tipo == "meses"){
			return date('Y-m-d',mktime(0,0,0,$mes,$dia,$ano));
		}else if($tempo>0 && $tipo == "meses"){
			return date('Y-m-d',mktime(0,0,0,$mes+$tempo,$dia,$ano));
		}else if($tempo==0 && $tipo == "dias"){
			return date('Y-m-d',mktime(0,0,0,$mes,$dia,$ano));
		}else{
			return date('Y-m-d',mktime(0,0,0,$mes,$dia+$tempo,$ano));
		}
	}

	/**
	 * Saber qual o número do mês
	 *
	 * @param string $nome
	 * @return string
	 */
	function retornaNumeroMes($nome){
		$retorno	= "";
		$tam 	= strlen($nome);
		$meses	= array("","Janeiro","Fevereiro",utf8_decode("Março"),"Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		$abrev	= array("","Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez");

		if($tam == 3){
			$retorno = array_search($nome, $abrev);
		}else{
			$retorno = array_search($nome, $meses);
		}

		if($retorno < 10){ $retorno = "0".$retorno; }
		return $retorno;
	}

	/**
	 * Saber qual o nome do mês
	 *
	 * @param string $pos
	 * @return string
	 */
	function retornaNomeMes($pos){
		$meses	= array("Janeiro","Fevereiro",utf8_decode("Março"),"Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
		return $meses[$pos-1];
	}

	/**
	 * Destruir instância
	 *
	 * @return void
	 */
	function __destruct(){
	}

}//class
