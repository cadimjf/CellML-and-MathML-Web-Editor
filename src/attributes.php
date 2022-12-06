<?php
require_once("DataBase.php");
class Attribute extends DataBase{
	private $idnode;
	private $name;
	private $value;
	private $table;
	function __construct(){	
		global $table;
		$table = 'attr';
		parent::__construct();//construtor da classe pai -> database

	}
	function getAttr($pId,$pName){
		global $idnode, $name, $value, $table;
		parent::newConnection();
		$pId = trim($pId);
		$pName = trim($pName);
		$str = "SELECT idnode, attname, attvalue FROM attr WHERE (idnode='$pId') AND (attname='$pName')";
		$busca = mysql_query($str);
		parent::logSQL($str);
		if($busca){
			$buscaArray = mysql_fetch_array($busca);
			$idnode = $buscaArray['idnode'];
			$name = $buscaArray['attname'];
			$value = $buscaArray['attvalue'];
			parent::closeConnection();
			return $this->getFields();
		}else{
			parent::closeConnection();
 			return false;
		}
	}
	
	function getFields(){
		global $idnode, $name, $value, $table;
		$attr["idnode"] = $idnode;
		$attr["name"] = $name;
		$attr["value"] = $value;
		return $attr;
	}
	/**
	* Salva novo nó
	* @param $attr
	* $attr array como os índices iguais aos nomes de campo da tabela, exceto o id que é auto_increment
	* @return boolean
	*/
	function insert($attr){
		global $idnode, $name, $value, $table;
		$str = "INSERT INTO attr 
		values ('".$attr["idnode"]."',
		'".$attr["name"]."','".$attr["value"]."')";
		parent::newConnection();
		parent::logSQL($str);
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
	*/
	function update($attr){
		global $table;

		$str = "UPDATE attr SET 
		 attvalue = '".$attr["value"]."'
		 WHERE (idnode='".$attr["idnode"]."') AND (attname='".$attr["name"]."')";
		parent::newConnection();
		$exec = mysql_query($str);
		parent::logSQL($str);
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
	*/
	function deleteAttr($id,$name){
		global $table;
		$str = "DELETE FROM $table
		 WHERE (idnode='".$id."') AND (attname='$name')" ;
		parent::newConnection();
		parent::logSQL($str);
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
	function listAttributesByNode($idnode){
		global $table;
		$sql = "SELECT  idnode, attname, attvalue FROM attr WHERE idnode='".$idnode."' ORDER BY attname";
		parent::newConnection();
		parent::logSQL($str);
		$exec = mysql_query ($sql);
		
		if(!$exec){
			print mysql_errno().": ".mysql_error()." at ".__LINE__." line in ".__FILE__." file<br>";
			parent::closeConnection();
			return false;
		}else{
			$tree = array();
			$i = 0;
			while($row=mysql_fetch_array($exec)){
				$tree["$i"] = $row;
				$i++;
			}
			parent::closeConnection();
			return $tree;
		}
	}

	function deleteAllAttr($id){
		global $table;
		$str = "DELETE FROM attr WHERE idnode='".$id."'" ;
		parent::logSQL($str);
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

?>