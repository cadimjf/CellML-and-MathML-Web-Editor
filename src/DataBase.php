<?php
// session_start();
class DataBase{
	private $local;
	private $user;
	private $password;
	private $dataBase;
	private $connection;
	private $iduser;
	function __construct(){
		global $local, $user, $password, $dataBase,$iduser;
		$local = "localhost";
		$user = "root";
		$password = "";
		$dataBase = "parafisioantigo";
		$iduser=$_SESSION['iduser'];
	}
	function newConnection(){
		global $local, $user, $password, $dataBase,$connection;
		$connection = mysql_connect($local, $user, $password);
		mysql_select_db($dataBase,$connection);
		return $connection;
	}
	
	function closeConnection(){
		global $connection;
		mysql_close($connection);
		return true;
	}

	function execSQL($str){
		$query = str_replace("\n", " ",$str);
		$query = str_replace("\t", " ",$query);
		$this->newConnection();
		//LOg
		$this->logSQL($query);
		$exec = mysql_query($query);
		if(!$exec){
			$error= mysql_errno().": ".mysql_error()." at ".__LINE__." line in ".__FILE__." file<br>";
			$this->logSQL($error);
			return false;
		}else{
			$tree = array();
			$i = 0;
			while($row=mysql_fetch_array($exec)){
				$tree["$i"] = $row;
				$i++;
			}
			return $tree;
		}
		$this->closeConnection();
	}
	function logSQL($content){
         return;
		global $iduser;
		$content = str_replace("\n", " ",$content);
		$content = str_replace("\t", " ",$content);
		$filename= "../log/queries_".$iduser."_".date("d_m_y").".sql";
		// Tendo certeza que o arquivo existe e que há permissão de escrita primeiro.
		
		if (!$handle = fopen($filename, 'a')) {
		//	print "Erro abrindo arquivo ($filename)";
			return;
		}else{
			$comando = "chmod 777 $fileName";
			$resultado = system($comando, $retval);
		
			// Escrevendo $somecontent para o arquivo aberto.
			$cont = "[".date("d/m/y H:i:s")."] ".$content.";\n";
			if (!fwrite($handle, $cont)) {
				print "Erro escrevendo no arquivo ($filename)";
			}
			fclose($handle);
		}
	}
}
?>
