<?php
namespace DB_control;
use Exception;
use \PDO;
use PDOException;
class Database{
    private $HOST = "localhost";     // PostgreSQL sunucu adresi
    private $PORT = "5403";          // PostgreSQL port numarası genellikle 5432'dir
    private $DB_NAME = "webnote";   // Veritabanı adı
    private $USERNAME = "postgres";  // Veritabanı kullanıcı adı
    private $PASSWORD = "abc123";    // Veritabanı şifresi
    private $CHARSET = 'UTF8';
    private $COLLATION = 'utf8_general_ci';
    private $pdo = null;
    private $isConn;
    private $stmt = null; 

    public function __construct(){
        $this->isConn=true;
        $SQL = "pgsql:host=".$this->HOST.";port=".$this->PORT.";dbname=".$this->DB_NAME;

        try {
            $this->pdo = new PDO($SQL, $this->USERNAME, $this->PASSWORD);
            // $this->pdo->exec("SET NAMES '" . $this->CHARSET . "' COLLATE '" . $this->COLLATION . "'");
            // $this->pdo->exec("SET CHARACTER SET '" . $this->CHARSET . "'");
			$this->pdo->exec("SET NAMES '" . $this->CHARSET . "'");
			$this->pdo->exec("SET client_encoding = '" . $this->CHARSET . "'");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

        } catch (PDOException $e) {
            die('Cannot the connect to Database with PDO. '.$e->getMessage());
        }
    }
    public function MyTransaction(){
		$this->pdo->beginTransaction();
	}
	public function MyCommit(){
		$this->pdo->commit();
	}
	public function MyRollBack(){
		$this->pdo->rollBack();
	}
    public function __destruct()
	{
		// Bağlantıyı kapatma
		$this->pdo=NULL;
		$this->isConn=FALSE;
	}

    public function myQuery($query, $params=null)
	{
		//Aşağıdaki functionlardaki kodu kısaltmak için hazırladığım function
	  if(is_null($params)){
			$this->stmt=$this->pdo->query($query);
			
		}else{
			$this->stmt=$this->pdo->prepare($query);
			$this->stmt->execute($params);
		}
		return $this->stmt;
	}

	public function getColumn($query,$params=null)   
	{   // Tek bir değer almak için kullanılır (Tek satir tek sutun nokta atisi)
		try{
		return $this->myQuery($query, $params)->fetchColumn();
		}catch(PDOException $e){
			die($e->getMessage()); 
		}
	}
	public function getRow($query,$params=null)
	{	// Tek bir satır almak için kullanılır
		try{
		return $this->myQuery($query, $params)->fetch();
		}catch(PDOException $e){
			die($e->getMessage()); 
		}
	}
	public function getRows($query,$params=null)
	{	// Tüm satırları almak için kullanılır
		try{
		return $this->myQuery($query, $params)->fetchAll();
		}catch(PDOException $e){
			die($e->getMessage()); 
		}
		
	}
	public function Insert($query,$params=null)
	{   // Veri Eklemek için
		try{
		$this->myQuery($query, $params);
		return $this->pdo->lastInsertId();
		}catch(PDOException $e){
			if (strpos($e->getMessage(), 'accounts_username_key') !== false) {    // Unique olmasi gerekirse bu hatayi istek yapilan yere iteler
				throw new Exception("UNIQUE_USERNAME"); 
			}else if (strpos($e->getMessage(), 'accounts_numara_key') !== false) {
				throw new Exception("UNIQUE_NUMARA");     
			}else if ($e->getCode() == '22001') {
				throw new Exception("TOOLONG");      // Karakter sayisi sinirini asinca
			}else {				
				die($e->getMessage());
				//throw new Exception($e->getMessage());
			}
		}
	}
	public function Update($query,$params=null)
	{   // Veri Güncellemek için
		try{	
		return $this->myQuery($query, $params)->rowCount();
		}catch(PDOException $e){
			die($e->getMessage()); 
		}
	}
	public function Delete($query,$params=null)
	{	// Veri Silmek için
		return $this->Update($query,$params);
		
	}
	public function TableOperations($query){ 
		//tablo operasyonları için
		$myTable=$this->pdo->query($query);
		return $myTable;
	}

}
