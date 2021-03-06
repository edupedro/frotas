<?php
namespace MQS\dao;

use \Exception;

/** 
 * Classe para DAO
 * 
 * @author Eduardo Andrade
 */
class MotoristaDAO{
    
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
     * Retorna lista de motorista
     * 
     * @param string $where
     * @param boolean $lazy
     * @return array
     */
    function retornaLista($where,$lazy){
        $retorno    = array();
        
        $sql 	= "	SELECT 
						tab.id AS idMotorista,
        				tab.created AS dataCadastro,
                        tab.anotador_id,
						tab.ativo,
 						tab.descricao AS motorista,
                        tab.cpf,
                        tab.telefone ,
                        tab.email,
                        tab.endereco ,
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
        				motoristas AS tab
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
                    $retorno[$cont]['idMotorista']              = $row->idMotorista;
                    $retorno[$cont]['dataCadastro']             = $this->datas->padraoBrasilCompleto($row->dataCadastro);
                    $retorno[$cont]['idAnotador']               = $row->anotador_id;
                    $retorno[$cont]['ativo']                    = $row->ativo;
                    $retorno[$cont]['motorista']                = $this->textos->encodeToUtf8($row->motorista);
                    $retorno[$cont]['cpf']                      = $row->cpf;
                    $retorno[$cont]['responsavel']              = $this->textos->encodeToUtf8($row->responsavel);
                    $retorno[$cont]['email']                    = $row->email;
                    $retorno[$cont]['telefone']                 = $row->telefone;
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
        				motoristas AS tab
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
            $sql = "INSERT INTO motoristas (
                        created,
                        updated,
            			anotador_id,
                        ativo,
                        descricao,
                        cpf,
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
                        '".$this->textos->encodeToIso($campos['motorista'])."',
                        '".$campos['cpf']."',
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

    	$sql = "UPDATE motoristas SET ";
    	if(!empty($campos['motorista']))               { $sql .= " descricao = '".$this->textos->encodeToIso($campos['motorista'])."', "; }
    	if(!empty($campos['cpf']))			           { $sql .= " cpf = '".$campos['cpf']."', "; }
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
    	$sql .= " updated = '".$agora."', anotador_id = '".$campos['idAnotador']."'  WHERE id = '".$campos['idMotorista']."'; ";

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
     * Destruir inst??ncia
     *
     * @return void
     */
    function __destruct(){  
    	
    }
    
}// class

?>