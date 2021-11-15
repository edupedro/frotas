<?php
namespace MQS\dao;

use \Exception;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class ProprietarioDAO{
    
    private $banco;
    private $datas;
    private $textos;
    
    /**
     * Construtor
     */
    function __construct($banco,$datas,$textos) {
        $this->banco    = $banco;
        $this->datas    = $datas;
        $this->textos   = $textos;
    }
        
    /**
     * Retorna lista de proprietario
     * 
     * @param string $where
     * @param boolean $lazy
     * @return array
     */
    function retornaLista($where,$lazy){
        $retorno    = array();
        
        $sql 	= "	SELECT 
						tab.id AS idProprietario,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS proprietario,
                        tab.tipo,
                        tab.cpf,
                        tab.razao_social,
                        tab.nome_fantasia,
                        tab.cnpj,
                        tab.inscricao_estadual,
                        tab.inscricao_municipal,
                        tab.responsavel ,
                        tab.telefone ,
                        tab.endereco ,
                        tab.email,
                        tab.numero ,
                        tab.complemento ,
                        tab.bairro ,
                        tab.cidade ,
                        tab.estado ,
                        tab.cep ,
                        tab.latitude ,
                        tab.longitude ,
                        tab.imagem_avatar,
                        tab.imagem_capa
        			FROM            		
        				proprietarios AS tab
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
                    $retorno[$cont]['idProprietario']           = $row->idProprietario;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['proprietario']             = $this->textos->encodeToUtf8($row->proprietario);
                    $retorno[$cont]['tipo']                     = $row->tipo;
                    $retorno[$cont]['cpf']                      = $row->cpf;
                    $retorno[$cont]['razaoSocial']              = $this->textos->encodeToUtf8($row->razao_social);
                    $retorno[$cont]['nomeFantasia']             = $this->textos->encodeToUtf8($row->nome_fantasia);
                    $retorno[$cont]['cnpj']                     = $row->cnpj;
                    $retorno[$cont]['inscricaoEstadual']        = $row->inscricao_estadual;
                    $retorno[$cont]['inscricaoMunicipal']       = $row->inscricao_municipal;                   
                    $retorno[$cont]['responsavel']              = $this->textos->encodeToUtf8($row->responsavel);
                    $retorno[$cont]['telefone']                 = $row->telefone;
                    $retorno[$cont]['email']                    = $row->email;
                    $retorno[$cont]['endereco']                 = $this->textos->encodeToUtf8($row->endereco);
                    $retorno[$cont]['numero']                   = $row->numero;
                    $retorno[$cont]['complemento']              = $this->textos->encodeToUtf8($row->complemento);
                    $retorno[$cont]['bairro']                   = $this->textos->encodeToUtf8($row->bairro);
                    $retorno[$cont]['cidade']                   = $this->textos->encodeToUtf8($row->cidade);
                    $retorno[$cont]['estado']                   = $row->estado;
                    $retorno[$cont]['cep']                      = $row->cep;
                    $retorno[$cont]['latitude']                 = $row->latitude;
                    $retorno[$cont]['longitude']                = $row->longitude;
                    $retorno[$cont]['imagemAvatar']             = $row->imagem_avatar;
                    $retorno[$cont]['imagemCapa']               = $row->imagem_capa;
                    
                    if($lazy){
                    }   
                    
                    $retorno[$cont]['tipoPessoa'] = "Pessoa Física"; 
                    if($retorno[$cont]['tipo'] == 'PJ'){ $retorno[$cont]['tipoPessoa'] = "Pessoa Jurídica"; }
                    
                    if(empty($retorno[$cont]['imagemAvatar'])){ $retorno[$cont]['imagemAvatar'] =  "default-avatar.png"; }
                    if(empty($retorno[$cont]['imagemCapa'])){ $retorno[$cont]['imagemCapa'] =  "default-capa.png"; }
                    
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
        				proprietarios AS tab
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
            $sql = "INSERT INTO proprietarios (
                        created,
                        updated,
            			anotador_id,
                        ativo,
                        descricao,
                        tipo,
                        cpf,
                        razao_social,
                        nome_fantasia,
                        cnpj,
                        inscricao_estadual,
                        inscricao_municipal,
                        responsavel ,
                        telefone ,
                        email ,
                        endereco ,
                        numero ,
                        complemento ,
                        bairro ,
                        cidade ,
                        estado ,
                        cep ,
                        latitude ,
                        longitude ,
                        imagem_avatar,
                        imagem_capa
                    ) VALUES (
                        '".$agora."',
                        '".$agora."',
                        '".$campos['idAnotador']."',
                        'true',
                        '".$this->textos->encodeToIso($campos['proprietario'])."',
                        '".$campos['tipo']."',
                        '".$campos['cpf']."',
                        '".$this->textos->encodeToIso($campos['razaoSocial'])."',
                        '".$this->textos->encodeToIso($campos['nomeFantasia'])."',
                        '".$campos['cnpj']."',
                        '".$campos['inscricaoEstadual']."',
                        '".$campos['inscricaoMunicipal']."',             
                        '".$this->textos->encodeToIso($campos['responsavel'])."',
                        '".$campos['telefone']."',
                        '".$campos['email']."',
                        '".$this->textos->encodeToIso($campos['endereco'])."',
                        '".$campos['numero']."',
                        '".$this->textos->encodeToIso($campos['complemento'])."',
                        '".$this->textos->encodeToIso($campos['bairro'])."',
                        '".$this->textos->encodeToIso($campos['cidade'])."',
                        '".$campos['estado']."',
                        '".$campos['cep']."',
                        '".$campos['latitude']."',
                        '".$campos['longitude']."',
                        '".$campos['imagemAvatar']."', 
                        '".$campos['imagemCapa']."'
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

    	$sql = "UPDATE proprietarios SET ";
    	if(!empty($campos['proprietario']))            { $sql .= " descricao = '".$this->textos->encodeToIso($campos['proprietario'])."', "; }
    	if(!empty($campos['tipo']))			           { $sql .= " tipo = '".$campos['tipo']."', "; }
    	if(!empty($campos['cpf']))			           { $sql .= " cpf = '".$campos['cpf']."', "; }
    	if(!empty($campos['nomeFantasia']))            { $sql .= " nomeFantasia = '".$this->textos->encodeToIso($campos['nomeFantasia'])."', "; }
    	if(!empty($campos['cnpj']))                    { $sql .= " cnpj = '".$campos['cnpj']."', "; }
    	if(!empty($campos['inscricaoEstadual']))       { $sql .= " inscricao_estadual = '".$campos['inscricaoEstadual']."', "; }
    	if(!empty($campos['inscricaoMunicipal']))      { $sql .= " inscricao_municipal = '".$campos['inscricaoMunicipal']."', "; }
    	if(!empty($campos['responsavel']))             { $sql .= " responsavel = '".$this->textos->encodeToIso($campos['responsavel'])."', "; }
    	if(!empty($campos['telefone']))        	       { $sql .= " telefone = '".$campos['telefone']."', "; }
    	if(!empty($campos['email']))        	       { $sql .= " email = '".$campos['email']."', "; }
    	if(!empty($campos['endereco']))        	       { $sql .= " endereco = '".$this->textos->encodeToIso($campos['endereco'])."', "; }
    	if(!empty($campos['numero']))        	       { $sql .= " numero = '".$campos['numero']."', "; }
    	if(!empty($campos['complemento']))             { $sql .= " complemento = '".$this->textos->encodeToIso($campos['complemento'])."', "; }
    	if(!empty($campos['bairro']))        	       { $sql .= " bairro = '".$this->textos->encodeToIso($campos['bairro'])."', "; }
    	if(!empty($campos['cidade']))        	       { $sql .= " cidade = '".$this->textos->encodeToIso($campos['cidade'])."', "; }
    	if(!empty($campos['estado']))        	       { $sql .= " estado = '".$campos['estado']."', "; }
    	if(!empty($campos['cep']))        		       { $sql .= " cep = '".$campos['cep']."', "; }
    	if(!empty($campos['latitude']))        	       { $sql .= " latitude = '".$campos['latitude']."', "; }
    	if(!empty($campos['longitude']))               { $sql .= " longitude = '".$campos['longitude']."', "; }
    	if(!empty($campos['imagemAvatar']))            { $sql .= " imagem_avatar = '".$campos['imagemAvatar']."', "; }
    	if(!empty($campos['imagemCapa']))              { $sql .= " imagem_capa = '".$campos['imagemCapa']."', "; }    	
    	if(!empty($campos['ativo']))			       { $sql .= " ativo = '".$campos['ativo']."', "; }
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idProprietario']."'; ";

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