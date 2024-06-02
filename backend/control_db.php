<?php
namespace control_db;
use Exception;
use \PDO;
use PDOException;
class Database{
    private $HOST = "localhost";     // PostgreSQL sunucu adresi
    private $PORT = "5403";          // PostgreSQL port numarası default 5432
    private $DB_NAME = "webnote";    // Veritabanı adı
    private $USERNAME = "postgres";  // Veritabanı kullanıcı adı
    private $PASSWORD = "abc123";    // Veritabanı şifresi
    private $CHARSET = 'UTF8';
    private $pdo = null;
    private $isConn;
    private $stmt = null; 

    public function __construct(){
        $this->isConn=true;
        $SQL = "pgsql:host=".$this->HOST.";port=".$this->PORT.";dbname=".$this->DB_NAME;

        try {
            $this->pdo = new PDO($SQL, $this->USERNAME, $this->PASSWORD);
			$this->pdo->exec("SET NAMES '" . $this->CHARSET . "'");
			$this->pdo->exec("SET client_encoding = '" . $this->CHARSET . "'");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_OBJ);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);

        } catch (PDOException $e) {
            //die('DB baglantisi kurulamadi. '.$e->getMessage());
			throw new Exception("DB baglantisi kurulamadi (construct): " . $e->getMessage());
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
    public function __destruct()   // Bağlantıyı kapatma 
	{
		$this->pdo=NULL;
		$this->isConn=FALSE;
	}

	//VULNERABLE
	public function vulnLogin($_number, $_username, $_password){
		try {
			$query = "SELECT * FROM accounts WHERE numara = $_number AND username = '$_username' AND password = '$_password'";
			return $this->stmt = $this->pdo->query($query);
		} catch (PDOException $e) {
			throw new Exception($e->getMessage());
		}
	}


	









	
	public function myQuery($query, $params = null)
	{
		if(is_null($params)){
			$this->stmt = $this->pdo->query($query);          
		}else{
			$this->stmt = $this->pdo->prepare($query);    //calistirilacak sorgunun bos hali  (select * from accounts where id = (?))
			
			foreach ($params as $key => $value) {         //$params(array($a,$b))  seklinde olmali  
				$dataType = $this->getDataType($value);
				$this->stmt->bindValue($key + 1, $value, $dataType);
			}
			
			$this->stmt->execute();             
		}
		return $this->stmt;
	}
	
	private function getDataType($value)
	{
		switch (gettype($value)) {
			case 'integer':
				return PDO::PARAM_INT;
			case 'boolean':
				return PDO::PARAM_BOOL;
			case 'NULL':
				return PDO::PARAM_NULL;
			default:
				return PDO::PARAM_STR;
		}
	}

	public function getColumn($query,$params=null)    // Tek bir değer almak için kullanılır (Tek satir tek sutun nokta atisi)
	{   
		try{
		return $this->myQuery($query, $params)->fetchColumn();   
		}catch(PDOException $e){
			//die($e->getMessage()); 
			throw new Exception($e->getMessage());
		}
	}
	public function getRow($query,$params=null)   // Tek bir satır almak için kullanılır
	{	
		try{
		return $this->myQuery($query, $params)->fetch();         
		}catch(PDOException $e){
			//die($e->getMessage()); 
			throw new Exception($e->getMessage());
		}
	}
	public function getRows($query,$params=null)   // Tüm satırları almak için kullanılır
	{	
		try{
		return $this->myQuery($query, $params)->fetchAll();       
		}catch(PDOException $e){
			//die($e->getMessage()); 
			throw new Exception($e->getMessage());
		}		
	}
	public function Insert($query,$params=null)  // Veri Eklemek için
	{   
		try{
		$this->myQuery($query, $params);
		return $this->pdo->lastInsertId();  
		}catch(PDOException $e){
			//die($e->getMessage());
			throw new Exception($e->getMessage());
		}
	}
	public function Update($query,$params=null)    // Veri Güncellemek için
	{   
		try{	
		return $this->myQuery($query, $params)->rowCount();    
		}catch(PDOException $e){
			//die($e->getMessage()); 
			throw new Exception($e->getMessage());
		}
	}
	public function Delete($query,$params=null){	 // Veri Silmek için
		return $this->Update($query,$params);                
		
	}
	public function TableOperations($query){  //tablo operasyonları için		
		$myTable=$this->pdo->query($query);
		return $myTable;
	}

}



// getColumn   SELECT (tek deger)     HATA KONTOL: if (empty($srg)) throw new Exception();  (stdClass $srg)
// getRow      SELECT (tek satir)     HATA KONTOL: if (empty($srg)) throw new Exception();  (stdClass $srg->sutun)        
// getRows     SELECT (tum satirlar)  HATA KONTOL: if (empty($srg)) throw new Exception();  (stdClass $srg->satir->sutun) 
// Insert      INSERT                 HATA KONTOL: if (empty($srg)) throw new Exception();  (string)  (eklenen son serial key degerini verir) 
// Update      UPDATE                 HATA KONTOL: if ($srg === 0) throw new Exception();   (integer) (degisen satir sayisini verir)
// Delete      DELETE                 HATA KONTOL: if ($srg === 0) throw new Exception();   (integer) (degisen satir sayisini verir)


// $srg = $db->getColumn("SELECT username FROM accounts WHERE userid = (?)", array(1));                  // echo $srg;
// $srg = $db->getRow("SELECT * FROM accounts WHERE userid = (?)", array(1));                            // echo $srg->username;
// $srg = $db->getRows("SELECT * FROM accounts");                                                        // echo $srg[0]->username;
// $srg = $db->Insert("INSERT INTO accounts (username, password) VALUES (?,?)", array("ali","veli"));
// $srg = $db->Update("UPDATE accounts SET username = (?), password = (?) WHERE userid = (?)", array("ali", "veli", 1));
// $srg = $db->Delete("DELETE FROM accounts WHERE userid = (?)", array(1));


