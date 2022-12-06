<?php

	/*
	* chamado por unitscompForm.php
	* pela função addNewTag()
	* o objetivo é listar todos as tags do CellML possíveis para inseri-las na árvore
	*/
	require_once("Node.php");
	$node = new Node();
	$result = $node->listComponentsByProject($_GET["codprojeto"]);
	if(is_array($result)){
		echo ( implode("|", $result) );
	}else{
		echo ("ERROR");
	}
?>