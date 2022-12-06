<?php 
/*
* chamado por unitscompForm.php
* pela função carregaAttr()
* Recebe valores via get e repassa para a classe Node.php
* lista todos os atributos de um nó
*/

	$selnode = $_GET["selnode"];
	$name = $_GET["name"];
	require_once("attributes.php");
	$attribute = new Attribute();
	$result = $attribute->getAttr($selnode,$name);
	

//	var_dump($_GET);
	if(is_array($result)){
	
//		echo ("$name-----------$id");
		echo ( implode("|", $result) );
	}else{
		echo ("ERROR|ERROR|ERROR");
	}

?>
