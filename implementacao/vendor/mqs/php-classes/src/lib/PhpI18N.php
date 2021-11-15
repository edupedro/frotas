<?php 

namespace MQS\lib;

/**
* Classe simples para internacionalizar sua aplicação em diversos idiomas, para cada idioma deve existir um arquivo Xml contendo as traduçõees.
* O script é muito simples(porém eficiente), o arquivo Xml de tradução, faz um loop e adiciona no array($arrayLabel) os índices(atributo <name>) e seus valores(atributo <value>).
* Através desse índice ele retorna a tradução do idioma selecionado.
* O padrão de nomenclatura dos arquivos Xmls é:
* 	phpi18n.xml -> Linguagem padrão
*	phpi18n_xx_XX.xml -> Ondes xxXX são a abreviações dos idiomas e países.
* Para maiores detalhes sobre abreviaturas dos pa�ses acessem(https://ftp.ics.uci.edu/pub/ietf/http/related/iso639.txt, https://userpage.chemie.fu-berlin.de/diverse/doc/ISO_3166.html, https://www.iso.org/iso/en/prods-services/iso3166ma/index.html).
* Autor: Rodrigo Rodrigues
* Email: web-rodrigo@bol.com.br
* Versão: 1
* IMPORTANTE: PRECISA TER INSTALADO O PHP 5 PORQ USA O COMPONENTE SimpleXml(https://br.php.net/manual/pt_BR/ref.simplexml.php).
*/
class PhpI18N {
	
	/**
	 * Valores da tradução
	 * @var array
	 */
	private $arrayLabel = array();
	
	/**
	 * Nome do arquivo XML
	 * @var string
	 */
	private $xmlFile;
	
	/**
	 * Nome do idioma
	 * @var string
	 */
	private $language;
	
	/**
	 * Nome do país
	 * @var string
	 */
	private $country;
	
	/**
	 * Nome do arquivo de tradução
	 * @var string
	 */
	private $xml;
	
	/**
	 * Contrutor
	 * @param string $xmlFile
	 * @param string $language
	 * @param string $country
	 */
	function __construct($xmlFile = "", $language = "", $country = "") {
		
		if(empty($xmlFile)){
			$xmlFile = "phpi18n";
		}
		
		$this->language = $language;
		$this->country = $country;
		$this->xml = $xmlFile;
		
		if(!(empty($this->language) && empty($this->country))){
			$this->xml .= "_".$this->language."_".$this->country;
		}
		$this->xml .= ".xml";
		
		$this->loadXml($this->xml);
	}
	
	/**
	 * Carregar o XML
	 * @param string $xml
	 */
	private function loadXml($xml) {
		$xml = ROOT . DS . "res/phpi18n/".$xml;
		
		if(!file_exists($xml)){
			$xml = ROOT . DS . "res/phpi18n/phpi18n.xml"; // Language Default
		}
		
		$simpleXml = @simplexml_load_file($xml);
		if(!$simpleXml){
			echo "Language file not found";
			exit();
		}

		foreach($simpleXml->label as $loadLabel){
			$this->arrayLabel["$loadLabel->name"] = utf8_encode($loadLabel->value);
		}
	}
	
	/**
	 * Nome do arquivo 
	 * @return string
	 */
	public function getXml() {
		return $this->xml;
	}
	
	/**
	 * Valor da tradução
	 * @param string $keyName
	 */
	public function getLabel($keyName) {
		return utf8_decode($this->label($keyName));
	}
	
	/**
	 * Retorna o valor da chave
	 * @param string $keyName
	 * @return string|multitype:
	 */
	private function label($keyName) {
		if($this->arrayLabel == null || $this->arrayLabel[$keyName] == null ){
			return "empty";
		}
		return $this->arrayLabel[$keyName];
	}
	
	/**
	 * Destrutor
	 */
	function __destruct() {
		unset($this->arrayLabel);
	}
	
}
?>