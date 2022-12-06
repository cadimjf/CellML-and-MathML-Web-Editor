<?php
require_once("DataBase.php");
class AttrNode extends DataBase{
	private $nodename;
	private $attrname;
	function __construct(){	
		global $table;
		$table = 'attrnode';
		parent::__construct();//construtor da classe pai -> database

	}
	function getAttr($nn,$an){
		global $nodename, $attrname, $table;
		parent::newConnection();
		$nn = trim($nn);
		$an = trim($an);
		$str = "SELECT nodename, attrname FROM attrnode WHERE (nodename='$nn') AND (attrname='$an')";
		$busca = mysql_query($str);
		if($busca){
			$buscaArray = mysql_fetch_array($busca);
			$nodename = $buscaArray['nodename'];
			$attrname = $buscaArray['attrname'];
			parent::closeConnection();
 			return $this->getFields();
		}else{
			parent::closeConnection();
 			return false;
		}
	}
	
	function getFields(){
		global $idnode, $name, $value, $table;
		$attr = array();
		$attr["nodename"] = $nodename;
		$attr["attrname"] = $attrname;		
		return $attr;
	}

	function listTags(){
		global $table;
		parent::newConnection();
		$str = "SELECT distinct(nodename) FROM $table ORDER BY nodename";
		$busca = mysql_query($str);
		if($busca){
			$i = 0;
			while($row=mysql_fetch_array($busca)){
				$tree["$i"] = $row["nodename"];
				$i++;
			}
			parent::closeConnection();
 			return $tree;
		}else{
			parent::closeConnection();
 			return false;
		}
	}

	
}

?>