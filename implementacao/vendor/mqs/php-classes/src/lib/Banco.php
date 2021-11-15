<?php
namespace MQS\lib;

use Exception;

error_reporting(E_ALL ^ E_NOTICE);
ini_set('max_execution_time','36000');

/**
 * Classe de conexão a banco e manipulação de dados
 * 
 * @author Carlos Feitoza Filho
 */
class Banco {
    const ERRO_DE_CONEXAO = "Não foi possível conectar ao banco de dados. %s";
    const ERRO_DE_DESCONEXAO = "Não foi possível desconectar ao banco de dados. %s";
    const ERRO_DE_AUTOCOMMIT = "Não foi possível configurar o uso de transações. %s";
    const ERRO_DE_EXECUCAO_DE_SQL = "A execução do SQL não foi bem sucedida. %s";
    const ERRO_DE_CONFIRMACAO = "Não foi possível confirmar as alterações realizadas no banco de dados. %s";
    const ERRO_DE_REVERSAO = "Não foi possível reverter as alterações realizadas no banco de dados. %s";
    
    private $host; 
    private $user;
    private $pass;
    private $db;

    private $conn;
    
    /**
     * Construtor
     * 
     * Cria a instância da classe Banco
     * 
     * @param integer $aAmbiente Indica a qual ambiente este banco deve se 
     * conectar
     */
    function __construct($aAmbiente){
        switch ($aAmbiente) {
            case 0: // desenvolvimento
                $this->host = "localhost";
                $this->user = "root";
                $this->pass = "";
                $this->db = "db_demo_frotas";
            break;
            case 1: // produção
                $this->host = "216.172.172.48";
                $this->user = "galor697_user";
                $this->pass = "Q(_6,40Ub1Ck";
                $this->db = "galor697_demo_frotas";
            break;
        }
        
        $this->conn = null;
    }
    
    /**
     * Conectar ao banco de dados
     * 
     * @param boolean $aUsarTransacao Indica se a conexão será feita no modo 
     * autocommit (false) ou transacional (true)
     * 
     * @return void
     */
    function conectar($aUsarTransacao) {
        if (is_null($this->conn) && !($this->conn = mysqli_connect($this->host,$this->user,$this->pass,$this->db))) {
            throw new Exception(sprintf(Self::ERRO_DE_CONEXAO,mysqli_connect_error()),mysqli_connect_errno());
        }
        
        if (!mysqli_autocommit($this->conn, !$aUsarTransacao)) {
            throw new Exception(sprintf(Self::ERRO_DE_AUTOCOMMIT,mysqli_error($this->conn)),mysqli_errno($this->conn));
        }
    }

    /**
     * Fecha uma conexão, caso a instância atual possua uma conexão aberta
     *
     * @return void
     */
    function desconectar(){
        if (!is_null($this->conn) && !mysqli_close($this->conn)) {
            throw new Exception(sprintf(Self::ERRO_DE_DESCONEXAO,mysqli_error($this->conn)),mysqli_errno($this->conn));
        }
        
        $this->conn = null;
    }
    
    /**
     * Executar consultas SQL 
     * 
     * Caso uma conexão ainda não tenha sido explicitamente aberta era será 
     * aberta usando a opção de usar transação (segundo parâmetro)
     *
     * @param string $aSql SQL a ser executado
     * @param boolean $aUsarTransacao Caso true, uma transação será aberta 
     * e, em caso de um sql de inserção, exclusão ou alteração, será 
     * necessário encerrar a transação de forma conveniente. O padrão é 
     * false, isto é, o autocommit será usado por padrão
     * @param boolean $aOperacaoCritica Quando este parâmetro é true, uma 
     * exceção será levantada.
     * @param boolean $aRetornarId Quando este parâmetro é true, será 
     * retornado o Id da inserção realizada. Obviamente esta opção só faz 
     * sentido quando $aSql contém um comando que realiza uma inserção no 
     * banco de dados
     * 
     * @return mixed ResultSet (object) ou o id (integer) da última operação 
     * de inserção, dependendo do parâmetro $aRetornarId
     * 
     * @see $confirmar
     * @see $reverter
     */
    function executarSql($aSql, $aUsarTransacao = false, $aOperacaoCritica = true, $aRetornarId = False) {
        $this->conectar($aUsarTransacao);
        
        if (!($resultSet = mysqli_query($this->conn, $aSql)) && $aOperacaoCritica) {
            throw new Exception(sprintf(Self::ERRO_DE_EXECUCAO_DE_SQL,mysqli_error($this->conn)),mysqli_errno($this->conn));
        }
        
        if ($aRetornarId) {
            return mysqli_insert_id($this->conn);
        } 
        
        return $resultSet;
    }
    
    function confirmar() {
        if (!is_null($this->conn) && !mysqli_commit($this->conn)) {
            throw new Exception(sprintf(Self::ERRO_DE_CONFIRMACAO,mysqli_error($this->conn)),mysqli_errno($this->conn));
        }
    }
    
    function reverter() {
        if (!is_null($this->conn) && !mysqli_rollback($this->conn)) {
            throw new Exception(sprintf(Self::ERRO_DE_REVERSAO,mysqli_error($this->conn)),mysqli_errno($this->conn));
        }
    }

    /**
     * Retorna a quantidade de linhas do ResultSet 
     *
     * @param object $query
     * 
     * @return int
     */
    function totalDeRegistros($aResultSet){
        return mysqli_num_rows($aResultSet);
    }
    
    function linhasAfetadas() {
        return mysqli_affected_rows($this->conn);
    }

    /**
     * Obtém um registro do ResultSet como objeto
     *
     * @param object Result Set
     * 
     * @return object Objeto onde cada atributo é uma coluna do resultado
     */
    function registroComoObjeto($aResultSet) {
        return mysqli_fetch_object($aResultSet);
    }        

    /**
     * Obtém um registro do ResultSet como array associativo
     *
     * @param object Result Set
     * 
     * @return array Array associativo onde cada atributo é uma chave do 
     * array
     */
    function registroComoArrayAssociativo($aResultSet){
        return  mysqli_fetch_assoc($aResultSet);
    }

    /**
     * Obtém um registro do ResultSet como array tradicional
     *
     * @param object Result Set
     * 
     * @return array Array indexado por número começando por zero
     */
    function registroComoArray($aResultSet){
        return mysqli_fetch_row($aResultSet);
    }

    /**
     * Liberar o ResultSet
     *
     * @param object $query
     * 
     * @return void
     */
    function liberar($aResultSet){
        mysqli_free_result($aResultSet);
    }

    /**
     * Destrutor
     */
    function __destruct() {
        $this->desconectar();
    }        
}//class