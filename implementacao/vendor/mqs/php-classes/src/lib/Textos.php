<?php
namespace MQS\lib;

/**
 * Classe de manipulação de textos
 * 
 * @author Eduardo Andrade
 */
class Textos{
    
    private static $instance = null;

    /**
     * Construtor
     */
    function __construct(){
        
    }
    
    /**
     * Retorna instância do singleton
     * @return string
     */
    public static function getInstance(){
        if(isset(self::$instance) || is_null(self::$instance)){
            $c = __CLASS__;
            self::$instance = new $c;
        }
        return self::$instance;
    }                
    
    /**
     * Converter para UTF8
     * 
     * @param string $string
     * @return string
     */
    function encodeToUtf8($string) {
        if (preg_match('!!u', $string)) { // Se jÃ¡ for UTF-8, apenas retorna.
            return $string;
        } else { // Se nÃ£o for UTF-8, converte
            return utf8_encode($string);
        }
    }		

    /**
     * Converter para ISO-8859-1
     * 
     * @param string $string
     * @return string
     */
    function encodeToIso($string) {
        //return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
        return utf8_decode(addslashes($string));
    }
    
    /**
     * Converte decimal para hexadecimal
     * @param string $number
     * @return string
     */
    function dec2hex($number){
        $hexvalues = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F');
        $hexval = '';
        while($number != '0'){
        	$hexval = $hexvalues[bcmod($number,'16')].$hexval;
        	$number = bcdiv($number,'16',0);
        }
        return $hexval;
    }        
	

    /**
     * Retirar acentos
     * 
     * @param string $string
     * @return string
     */
    function retirarAcentos($string){
        return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/","/(ç)/","/(Ç)/"),explode(" ","a A e E i I o O u U n N c C"),$string);
    }

    /**
     * Retirar pontuação
     *
     * @param string $string
     * @return string
     */
    function retirarPontuacao($string){
        // matriz de entrada
        $what = array( 'ä','ã','à','á','â','ê','ë','è','é','ï','ì','í','ö','õ','ò','ó','ô','ü','ù','ú','û','À','Á','É','Í','Ó','Ú','ñ','Ñ','ç','Ç',' ','-','(',')',',',';',':','|','!','"','#','$','%','&','/','=','?','~','^','>','<','ª','º' );
        // matriz de saída
        $by   = array( 'a','a','a','a','a','e','e','e','e','i','i','i','o','o','o','o','o','u','u','u','u','A','A','E','I','O','U','n','n','c','C','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_','_' );
        // devolver a string
        return str_replace($what, $by, $string);
    }
    
    /**
     * Conversão em float
     * 
     * @param string $str
     * @return float
     */
    function moeda($str) {
        if(preg_match("/([0-9\.,-]+)/", $str, $match)){
            $value = $match[0];
            if( preg_match("/(\.\d{1,2})$/", $value, $dot_delim) ){
                $value = (float)str_replace(',', '', $value);
            }
            else if( preg_match("/(,\d{1,2})$/", $value, $comma_delim) ){
                $value = str_replace('.', '', $value);
                $value = (float)str_replace(',', '.', $value);
            }
            else
                $value = (int)$value;
        }
        else {
            $value = 0;
        }
        return $value;
    }

    /**
     * Captarando parte do nome
     * 
     * @param string $nom
     * @param string $qtd
     * @return string
     */
    function separaNome($nom,$qtd){
        $t = strlen($nom);
        $b = 0;
        $nomed = "";

        for($i=0;$i<=$t;$i++) {
            $c = substr($nom,$i,1);
            if ($i == $t || $c == " "){
                $b++;
                if($b == $qtd){
                    break;
                }
            }
            $nomed = $nomed.$c; 
        }
        return $nomed;
    }		

    /**
     * Colocar a formatação do CPF
     * 
     * @param string $cpf
     * @return string
     */
    function formataCPF($cpf){
        $parte1 = substr($cpf,0,3);
        $parte2 = substr($cpf,3,3);
        $parte3 = substr($cpf,6,3);
        $parte4 = substr($cpf,9,2);

        return $parte1.".".$parte2.".".$parte3."-".$parte4;
    }


    /**
     * Escrever número pot extenso
     * 
     * @param string $valor
     * @param string $maiusculas
     * @return string
     */
    function extenso($valor=0, $maiusculas=false) {
        // verifica se tem virgula decimal
        if (strpos($valor, ",") > 0) {
            $valor = str_replace(".", "", $valor);
            $valor = str_replace(",", ".", $valor);
        }

        $singular = array("centavo", "real", "mil", "milh&atilde;o", "bilh&atilde;o", "trilh&atilde;o", "quatrilh&atilde;o");
        $plural = array("centavos", "reais", "mil", "milh&otilde;es", "bilh&otilde;es", "trilh&otilde;es","quatrilh&otilde;es");

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "tr&ecirc;s", "quatro", "cinco", "seis","sete", "oito", "nove");

        $z = 0;

        $valor = number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        $cont = count($inteiro);
        for ($i = 0; $i < $cont; $i++){
            for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++){
                $inteiro[$i] = "0" . $inteiro[$i];
            }
        }

        $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
        $rt = '';
        for ($i = 0; $i < $cont; $i++) {
            $valor = $inteiro[$i];
            $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
            $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
            $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

            $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd && $ru) ? " e " : "") . $ru;
            $t = $cont - 1 - $i;
            $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
            if ($valor == "000"){
                $z++; 
            }else if ($z > 0){
                $z--;
            }
            if (($t == 1) && ($z > 0) && ($inteiro[0] > 0)){
                $r .= ( ($z > 1) ? " de " : "") . $plural[$t];
            }
            if ($r){
                $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
            }
        }

        if (!$maiusculas) {
            return($rt ? $rt : "zero");
        } else if ($maiusculas == "2") {
            return (strtoupper($rt) ? strtoupper($rt) : "Zero");
        } else {
            return (ucwords($rt) ? ucwords($rt) : "Zero");
        }
    }
    
    /**
     * Converte um simplexml_load_string em array
     * 
     * @param object $object
     * @return array
     */
    function objectToArray($object) { 
        return @json_decode(@json_encode($object),1);
    }
    
    /**
     * Destruir instância
     *
     * @return void
     */
    function __destruct(){
        
    }

}//class