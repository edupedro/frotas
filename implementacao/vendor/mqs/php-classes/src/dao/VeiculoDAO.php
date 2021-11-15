<?php
namespace MQS\dao;

use \Exception;
use \MQS\ctrl\ProprietarioCTRL;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class VeiculoDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    private $proprietarioCTRL;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
        
        $this->proprietarioCTRL = null;        
    }
    
    /**
     * Retorna o objeto
     * @return object
     */
    function getProprietarioCTRL(){
        if(is_null($this->proprietarioCTRL)){
            $this->proprietarioCTRL = new ProprietarioCTRL($this->banco, $this->datas, $this->textos);
        }
        return $this->proprietarioCTRL;
    }
        
    /**
     * Retorna lista
     * 
     * @param string $where
     * @param boolean $lazy
     * @return array
     */
    function retornaLista($where,$lazy){
        $retorno    = array();
        
        $sql 	= "	SELECT 
						tab.id AS idVeiculo,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
                        tab.proprietario_id AS idProprietario,
                        tab.descricao,
                        tab.placa,
                        tab.ano_fabricacao,
                        tab.ano_modelo,
                        tab.renavan,
                        tab.chassi,
                        tab.combustivel,
                        tab.gnv,
                        tab.seguro,
                        tab.cor,
                        tab.km_inicial,
                        tab.patrimonio,
                        tab.valor_fipe,
                        tab.qtd_passageiros,
                        tab.observacoes,
                        tab.imagem01,
                        tab.imagem02,
                        tab.imagem03,
                        tab.imagem04,
                        tab.imagem05,
                        tab.imagem06,
                        p.descricao AS proprietario,
                        p.imagem_avatar 
        			FROM            		
        				veiculos AS tab
                        INNER JOIN proprietarios AS p ON tab.proprietario_id = p.id
        			WHERE
        				tab.id IS NOT NULL ";
        if(!empty($where)){
            $sql .= $where;
        }
        
        
        try {
            $resultSet = $this->banco->executarSql($sql);
            $qtd = $this->banco->totalDeRegistros($resultSet);
            
            if ($qtd > 0) {
                $cont = 0;
                while($row = $this->banco->registroComoObjeto($resultSet)){
                    $retorno[$cont]['idVeiculo']                = $row->idVeiculo;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['veiculo']                  = $this->textos->encodeToUtf8($row->descricao);
                    $retorno[$cont]['placa']                    = $row->placa;
                    $retorno[$cont]['anoFabricacao']            = $row->ano_fabricacao;
                    $retorno[$cont]['anoModelo']                = $row->ano_modelo;
                    $retorno[$cont]['renavan']                  = $row->renavan;
                    $retorno[$cont]['chassi']                   = $row->chassi;
                    $retorno[$cont]['combustivel']              = $row->combustivel;
                    $retorno[$cont]['gnv']                      = $row->gnv;
                    $retorno[$cont]['seguro']                   = $row->seguro;
                    $retorno[$cont]['cor']                      = $row->cor;
                    $retorno[$cont]['kmInicial']                = $row->km_inicial;
                    $retorno[$cont]['patrimonio']               = $row->patrimonio;
                    $retorno[$cont]['valorFipe']                = $row->valor_fipe;
                    $retorno[$cont]['qtdPassageiros']           = $row->qtd_passageiros;
                    $retorno[$cont]['observacoes']              = $this->textos->encodeToUtf8($row->observacoes);
                    $retorno[$cont]['imagem01']                 = $row->imagem01;
                    $retorno[$cont]['imagem02']                 = $row->imagem02;
                    $retorno[$cont]['imagem03']                 = $row->imagem03;
                    $retorno[$cont]['imagem04']                 = $row->imagem04;
                    $retorno[$cont]['imagem05']                 = $row->imagem05;
                    $retorno[$cont]['imagem06']                 = $row->imagem06;
                    $retorno[$cont]['idProprietario']           = $row->idProprietario;
                    $retorno[$cont]['proprietario']             = $this->textos->encodeToUtf8($row->proprietario);
                    $retorno[$cont]['imagemAvatar']             = $row->imagem_avatar;
                    
                    if($lazy){
                        $retorno[$cont]['Proprietario']       =  $this->getProprietarioCTRL()->retornaID($retorno[$cont]['idProprietario']);                        
                    }

                    if(empty($retorno[$cont]['imagem01'])){ $retorno[$cont]['imagem01'] =  "default-veiculo.png"; }
                    if(empty($retorno[$cont]['imagem02'])){ $retorno[$cont]['imagem02'] =  "default-veiculo.png"; }
                    if(empty($retorno[$cont]['imagem03'])){ $retorno[$cont]['imagem03'] =  "default-veiculo.png"; }
                    if(empty($retorno[$cont]['imagem04'])){ $retorno[$cont]['imagem04'] =  "default-veiculo.png"; }
                    if(empty($retorno[$cont]['imagem05'])){ $retorno[$cont]['imagem05'] =  "default-veiculo.png"; }
                    if(empty($retorno[$cont]['imagem06'])){ $retorno[$cont]['imagem06'] =  "default-veiculo.png"; }
                    if(empty($retorno[$cont]['imagemAvatar'])){ $retorno[$cont]['imagemAvatar'] =  "default-avatar.png"; }
                    
                    $retorno[$cont]['temGnv'] = "Possui GNV";
                    if($retorno[$cont]['gnv'] == "false"){ $retorno[$cont]['temGnv'] = "Não possui GNV"; }

                    $retorno[$cont]['temSeguro'] = "Possui seguro";
                    if($retorno[$cont]['seguro'] == "false"){ $retorno[$cont]['temSeguro'] = "Não possui seguro"; }

                    $retorno[$cont]['valorFipeExib'] = number_format($retorno[$cont]['valorFipe'],2,",",".");
                    $retorno[$cont]['kmInicialExib'] = number_format($retorno[$cont]['kmInicial'],2,",",".");
                    
                    $cont++;
                }
            }
            return $retorno;
        }catch (\Exception $e){
            throw $e;
        } finally {
            $this->banco->liberar($resultSet);
            unset($sql, $resultSet, $qtd);
        }
    }

    /**
     * Retorna QTD
     *
     * @param string $where
     * @return array
     */
    function retornaQTD($where){
        
        $sql 	= "	SELECT
						COUNT(tab.id) AS qtd
        			FROM
        				veiculos AS tab
        			WHERE
        				tab.id IS NOT NULL ";
        if(!empty($where)){
            $sql .= $where;
        }
        
        try {
            
            $resultSet = $this->banco->executarSql($sql);
            $linha = $this->banco->registroComoObjeto($resultSet);
            $qtd = $linha->qtd;
            
            return $qtd;
        }catch (\Exception $e){
            throw $e;
        } finally {
            $this->banco->liberar($resultSet);
            unset($sql, $resultSet, $qtd);
        }
    }
    
    /**
     * Inserir
     * 
     * @param array $campos
     * @return int
     */
    function inserir($campos){
        $agora	= date("Y-m-d H:i:s");
        
        try {
            $sql = "INSERT INTO veiculos (
                    created,
                    updated,
        			anotador_id,
                    ativo,
                    proprietario_id,
                    descricao,
                    placa,
                    ano_fabricacao,
                    ano_modelo,
                    renavan,
                    chassi,
                    combustivel,
                    gnv,
                    seguro,
                    cor,
                    km_inicial,
                    patrimonio,
                    valor_fipe,
                    qtd_passageiros,
                    observacoes,
                    imagem01,
                    imagem02,
                    imagem03,
                    imagem04,
                    imagem05,
                    imagem06
                ) VALUES (
                    '".$agora."',
                    '".$agora."',
                    '".$campos['idAnotador']."',
                    'true',
                    '".$campos['idProprietario']."',
                    '".$this->textos->encodeToIso($campos['veiculo'])."',
                    '".$campos['placa']."',
                    '".$campos['anoFabricacao']."',
                    '".$campos['anoModelo']."',
                    '".$campos['renavan']."',
                    '".$campos['chassi']."',
                    '".$campos['combustivel']."',
                    '".$campos['gnv']."',
                    '".$campos['seguro']."',
                    '".$campos['cor']."',
                    '".$this->textos->moeda($campos['kmInicial'])."',
                    '".$campos['patrimonio']."',
                    '".$this->textos->moeda($campos['valorFipe'])."',
                    '".$campos['qtdPassageiros']."',
                    '".$this->textos->encodeToIso($campos['observacoes'])."',
                    '".$campos['imagem01']."',
                    '".$campos['imagem02']."',
                    '".$campos['imagem03']."',
                    '".$campos['imagem04']."',
                    '".$campos['imagem05']."',
                    '".$campos['imagem06']."'
                );";

            $novoId = $this->banco->executarSql($sql,true,true,true);
            $this->banco->confirmar();
            return $novoId;
        } catch (\Exception $e) {
            $this->banco->reverter();
            throw $e;
        } finally {
            unset($campos, $sql);
        }
    }
      
    /**
     * Alterar
     *
     * @param array $campos
     * @return int
     */
    function alterar($campos){
    	$agora	= date("Y-m-d H:i:s");

    	$sql = "UPDATE veiculos SET ";
    	if(!empty($campos['veiculo']))          { $sql .= " descricao = '".$this->textos->encodeToIso($campos['veiculo'])."', "; }
    	if(!empty($campos['idProprietario']))	{ $sql .= " proprietario_id = '".$campos['idProprietario']."', "; }
    	if(!empty($campos['placa']))      		{ $sql .= " placa = '".$campos['placa']."', "; }
    	if(!empty($campos['anoFabricacao']))    { $sql .= " ano_fabricacao = '".$campos['anoFabricacao']."', "; }
    	if(!empty($campos['anoModelo']))      	{ $sql .= " ano_modelo = '".$campos['anoModelo']."', "; }
    	if(!empty($campos['renavan']))      	{ $sql .= " renavan = '".$campos['renavan']."', "; }
    	if(!empty($campos['chassi']))      		{ $sql .= " chassi = '".$campos['chassi']."', "; }
    	if(!empty($campos['combustivel']))      { $sql .= " combustivel = '".$campos['combustivel']."', "; }
    	if(!empty($campos['gnv']))      		{ $sql .= " gnv = '".$campos['gnv']."', "; }
    	if(!empty($campos['seguro']))      		{ $sql .= " seguro = '".$campos['seguro']."', "; }
    	if(!empty($campos['cor']))      		{ $sql .= " cor = '".$campos['cor']."', "; }
    	if(!empty($campos['kmInicial']))      	{ $sql .= " km_inicial = '".$this->textos->moeda($campos['kmInicial'])."', "; }
    	if(!empty($campos['patrimonio']))      	{ $sql .= " patrimonio = '".$campos['patrimonio']."', "; }
    	if(!empty($campos['valorFipe']))      	{ $sql .= " valor_fipe = '".$this->textos->moeda($campos['valorFipe'])."', "; }
    	if(!empty($campos['qtdPassageiros']))   { $sql .= " qtd_passageiros = '".$campos['qtdPassageiros']."', "; }
    	if(!empty($campos['observacoes']))      { $sql .= " observacoes = '".$this->textos->encodeToIso($campos['observacoes'])."', "; }
    	if(!empty($campos['imagem01']))      	{ $sql .= " imagem01 = '".$campos['imagem01']."', "; }
    	if(!empty($campos['imagem02']))      	{ $sql .= " imagem02 = '".$campos['imagem02']."', "; }
    	if(!empty($campos['imagem03']))      	{ $sql .= " imagem03 = '".$campos['imagem03']."', "; }
    	if(!empty($campos['imagem04']))      	{ $sql .= " imagem04 = '".$campos['imagem04']."', "; }
    	if(!empty($campos['imagem05']))      	{ $sql .= " imagem05 = '".$campos['imagem05']."', "; }
    	if(!empty($campos['imagem06']))      	{ $sql .= " imagem06 = '".$campos['imagem06']."', "; }
    	if(!empty($campos['ativo']))			{ $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idVeiculo']."'; ";
    	
    	try {
    	    $this->banco->executarSql($sql,true,true,false);
    	    $this->banco->confirmar();
    	} catch (\Exception $e) {
    	    $this->banco->reverter();
    	    throw $e;
    	} finally {
    	    unset($campos, $sql);
    	}
    }        
    
    /**
     * Destruir instância
     *
     * @return void
     */
    function __destruct(){  
    	
    }
    
}// class

?>