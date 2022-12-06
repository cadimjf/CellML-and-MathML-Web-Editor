<?php
/**Deprecated**/
/*
require_once("DataBase.php");
class MathCode extends DataBase{
	private $idnode;
	private $id;
	private $code;
	private $table;
	function __construct(){	
		global $table;
		$table = 'mathcode';
		parent::__construct();//construtor da classe pai -> database

	}
	function getMathCode($pId){
		global $idnode, $code, $id, $table;
		parent::newConnection();
		$pId = trim($pId);
		$str = "SELECT id, idnode, code FROM mathcode WHERE idnode='$pId'";
		$busca = mysql_query($str);
		if($busca){
			$buscaArray = mysql_fetch_array($busca);
			$idnode = $buscaArray['idnode'];
			$id = $buscaArray['id'];
			$code = $buscaArray['code'];
			parent::closeConnection();
 			return $this->getFields();
		}else{
			parent::closeConnection();
 			return false;
		}
	}
	
	function getFields(){
		global $idnode, $code, $id, $table;
		$attr["idnode"] = $idnode;
		$attr["code"] = $code;		
		$attr["id"] = $id;
		return $attr;
	}
	/**
	* Salva novo nó
	* @param $attr
	* $attr array como os índices iguais aos nomes de campo da tabela, exceto o id que é auto_increment
	* @return boolean
	*
	function insert($attr){
		var_dump($attr);
		global $idnode, $name, $value, $table;
		$str = "INSERT INTO $table 
		values ('".$attr["id"]."',
		'".$attr["idnode"]."','".$attr["code"]."')";
		parent::newConnection();
		echo $str;
		$exec = mysql_query($str);
		if(!$exec){
			parent::closeConnection();
			return mysql_errno().": ".mysql_error()." at ".__LINE__." line in ".__FILE__." file<br>";;
		}else{
			parent::closeConnection();
			return $attr;
		}

	}
	/**
	* Altera novo nó
	* @param $attr
	* $attr array como os índices iguais aos nomes de campo da tabela
	*  $attr["id_node"]
	*  $attr["name"]
	*  $attr["oldName"]
	* $attr["value"]
	if($idnode==-1){
	* @return boolean
	*
	function update($attr){
		global $table;

		$str = "UPDATE attr SET 
		 idnode = '".$attr["idnode"]."',
		 code = '".$attr["code"]."'
		 WHERE id = '".$attr["id"]."'";
		parent::newConnection();
		$exec = mysql_query($str);
		if(!$exec){
			print mysql_errno().": ".mysql_error()." at ".__LINE__." line in ".__FILE__." file<br>";
			parent::closeConnection();
			return false;
		}else{
			parent::closeConnection();
			return $attr;
		}

	}

	/**
	* Deleta nó
	* @param $id,$name
	* @return boolean
	*
	function deleteAttr($id,$name){
		global $table;
		$str = "DELETE FROM $table
		 WHERE (id='".$id."')";
		parent::newConnection();
		$exec = mysql_query($str);
		if(!$exec){
			print mysql_errno().": ".mysql_error()." at ".__LINE__." line in ".__FILE__." file<br>";
			parent::closeConnection();
			return false;
		}else{
			parent::closeConnection();
			return true;
		}

	}

}
*/
?>