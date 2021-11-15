<?php
namespace MQS\lib;


/**
 * Classe para validação de campos
 * O padrão de para os métodos é havendo erro retorna TRUE
 *
 * @author Eduardo Andrade
 */
class ValidaCampos{

	private $banco;
	private $datas;
	private $textos;

	/**
	 * Construtor
	 */
	function __construct($banco, $datas, $textos){
	    $this->banco        = $banco;
	    $this->datas        = $datas;
	    $this->textos       = $textos;
	}

	/**
	 * Validar existência de um registro
	 *
	 * @param array $itens
	 * @return boolean
	 */
	public function validaExiste($itens){
		$retorno = 0;
			
		$sql = "SELECT COUNT(id) AS qtd FROM {$itens['tabela']} WHERE {$itens['where']}";

		try {
			$resultSet = $this->banco->executarSql($sql);
			$retorno = $this->banco->registroComoObjeto($resultSet)->qtd;

			return $retorno;
		} finally {
			unset($sql);
		}
	}

	/**
	 * Verifica se o CPF é válido
	 *
	 * @param string $cpf
	 * @return boolean
	 */
	public static function validaCPF($cpf){
		$retorno = FALSE;

		// Verifiva se o número digitado contém todos os digitos
		$cpf = str_pad(preg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);
		// Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
		if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111'
				|| $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444'
				|| $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777'
				|| $cpf == '88888888888' || $cpf == '99999999999'){
			$retorno = TRUE;
		}else{
			// Calcula os números para verificar se o CPF é verdadeiro
			for ($t = 9; $t < 11; $t++) {
				for ($d = 0, $c = 0; $c < $t; $c++) {
					$d += $cpf[$c] * (($t + 1) - $c);
				}
				$d = ((10 * $d) % 11) % 10;

				if ($cpf[$c] != $d) {
					$retorno =  TRUE;
				}
			}
		}
		return $retorno;
	}

	/**
	 * Verifica o preenchimento do campo
	 *
	 * @param string $campo
	 * @return boolean
	 */
	public static function validaVazio($campo){
		$retorno = FALSE;

		if(empty($campo)){ $retorno = TRUE; }
		return $retorno;
	}

	/**
	 * Verifica se a data é válida dd/MM/AAAA
	 *
	 * @param string $data
	 * @return boolean
	 */
	public static function validaData($data){
		$retorno = FALSE;

		list($dia, $mes, $ano) = explode('/', $data);
		if(!checkdate($mes,$dia,$ano)){
			$retorno = TRUE;
		}
		return $retorno;
	}

	/**
	 * Verifica se o email é válido
	 *
	 * @param string $email
	 * @return boolean
	 */
	public static function validaEmail($email){
		$retorno = FALSE;
		$er = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";

		if (!preg_match($er, $email)){
			$retorno = TRUE;
		}
		return $retorno;
	}


	/**
	 * Compara o conteúdo dos campos
	 *
	 * @param string $campoA
	 * @param string $campoB
	 * @return boolean
	 */
	public static function comparaCampos($campoA, $campoB){
		$retorno = FALSE;

		if(empty($campoA) || $campoA != $campoB){
			$retorno = TRUE;;
		}
		return $retorno;
	}

	/**
	 * Destruir instância
	 *
	 * @return void
	 */
	function __destruct(){

	}

}// class