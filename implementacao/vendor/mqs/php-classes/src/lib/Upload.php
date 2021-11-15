<?php
namespace MQS\lib;

/**
 * Classe de upload de arquivos
 */
class Upload {

	private $arquivo;
	private $altura;
	private $largura;
	private $pasta;
	private $preNome;
	private $extensoes;

	/**
	 * Construtor
	 */
	function __construct($altura = NULL, $largura = NULL){
		if(empty($altura)){ $this->altura = 200; }else{ $this->altura = $altura; }
		if(empty($largura)){ $this->largura = 170; }else{ $this->largura = $largura; }

		$this->extensoes	= array('gif', 'jpeg', 'jpg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx');
	}

	/**
	 * Definição dos valores
	 *
	 * @param array $arquivo
	 * @param string $pasta
	 * @param string $preNome
	 */
	function setValores($arquivo, $pasta, $preNome){
		$this->arquivo	= $arquivo;
		$this->pasta	= $pasta;
		$this->preNome	= $preNome;
	}

	/**
	 * Modificar a largura padrão
	 *
	 * @param int $largura
	 */
	function setLargura($largura){
		$this->largura = $largura;
	}

	/**
	 * Modificar a altura padrão
	 *
	 * @param int $altura
	 */
	function setAltura($altura){
		$this->altura = $altura;
	}

	/**
	 * Modificar as extensões padrão
	 *
	 * @param array $extensoes
	 */
	function setExtensoes($extensoes){
		$this->extensoes = $extensoes;
	}

	/**
	 * Retorna extensão do arquivo
	 *
	 * @return string
	 */
	private function getExtensao(){
		$final  = explode('.', $this->arquivo['name']);
		$extensao = strtolower(array_pop($final));
		return $extensao;
	}

	/**
	 * Valida se o arquivo é uma imagem
	 *
	 * @param string $extensao
	 * @return boolean
	 */
	private function eImagem($extensao){
		$retorno = false;
		if (in_array($extensao, array('gif', 'jpeg', 'jpg', 'png') )){
			$retorno = true;
		}
		return $retorno;
	}

	/**
	 * Redefinir largura, altura, tipo, localizacao da imagem original
	 *
	 * @param int $imgLarg
	 * @param int $imgAlt
	 * @param string $tipo
	 * @param string $img_localizacao
	 * @param string $atributo
	 */
	private function redimensionar($imgLarg, $imgAlt, $tipo, $img_localizacao, $atributo){

		//descobrir novo tamanho sem perder a proporcao
		if ( $imgLarg > $imgAlt ){
			$novaLarg = $this->largura;
			$novaAlt = round( ($novaLarg / $imgLarg) * $imgAlt );

		} else if ( $imgAlt > $imgLarg ){
			$novaAlt = $this->altura;
			$novaLarg = round( ($novaAlt / $imgAlt) * $imgLarg );

		} else {
			// altura == largura
			$novaAlt = max($this->largura, $this->altura);
			$novaLarg = max($this->largura, $this->altura);
		}

		//redimencionar a imagem
		//cria uma nova imagem com o novo tamanho
		$novaimagem = imagecreatetruecolor($novaLarg, $novaAlt);

		switch ($tipo){
			case 1:	// gif
				$origem = imagecreatefromgif($img_localizacao);
				imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0,$novaLarg, $novaAlt, $imgLarg, $imgAlt);
				imagegif($novaimagem, $img_localizacao);
				break;
			case 2:	// jpg
				$origem = imagecreatefromjpeg($img_localizacao);
				imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0,$novaLarg, $novaAlt, $imgLarg, $imgAlt);
				imagejpeg($novaimagem, $img_localizacao);
				break;
			case 3:	// png
				$origem = imagecreatefrompng($img_localizacao);
				imagecopyresampled($novaimagem, $origem, 0, 0, 0, 0,$novaLarg, $novaAlt, $imgLarg, $imgAlt);
				imagepng($novaimagem, $img_localizacao);
				break;
		}

		//destroi as imagens criadas
		imagedestroy($novaimagem);
		imagedestroy($origem);
	}

	/**
	 * Executa o upload para a pasta determinada
	 *
	 * @return string
	 */
	function salvar(){
		$retorno = array();
		$retorno['resultado']	= FALSE;
		$extensao				= $this->getExtensao();

		//gera um nome unico para a imagem em funcao do tempo
		$novo_nome = $this->preNome."".rand(10,99)."".time().".".$extensao;
		//localizacao do arquivo
		$destino = $this->pasta.$novo_nome;

		$retorno['nome']	= $novo_nome;
		$retorno['pasta']	= $destino;

		//move o arquivo
		if (!move_uploaded_file($this->arquivo['tmp_name'], $destino)){
			switch ($this->arquivo['error']){
				case 1:
					$retorno['erro']	= "Tamanho excede o permitido upload_max_filesize";
					break;
				case 2:
					$retorno['erro']	= "Tamanho excede o permitido MAX_FILE_SIZE";
					break;
				case 3:
					$retorno['erro']	= "O upload do arquivo foi feito parcialmente";
					break;
				case 4:
					$retorno['erro']	= "Nenhum arquivo foi enviado";
					break;
				case 6:
					$retorno['erro']	= "Pasta temporária ausênte";
					break;
				case 7:
					$retorno['erro']	= "Falha em escrever o arquivo em disco";
					break;
				case 8:
					$retorno['erro']	= "Uma extensão do PHP interrompeu o upload do arquivo";
					break;
				default:
					$retorno['erro']	= "Upload foi bem sucedido";
			}
		}else{
			$retorno['resultado'] = TRUE;
		}

		if ($this->eImagem($extensao)){
			//pega a largura, altura, tipo e atributo da imagem
			//list($largura, $altura, $tipo, $atributo) = getimagesize($destino);

			// testa se é preciso redimensionar a imagem
			//if(($largura > $this->largura) || ($altura > $this->altura)){
				//$this->redimensionar($largura, $altura, $tipo, $destino, $atributo);
			//}
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
}//class