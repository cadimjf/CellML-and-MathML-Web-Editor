<?php
/*
* chamado por unitscompForm.php
* pela função deleteNode()
* Recebe o id do no via get e passa para a deleção no bd na classe Node
*/
	//deletar todos os filhos e seus atributos
	require_once("Node.php");
	$node = new Node($_GET['iduser']);
	$node->deleteNode($_GET["id_node"]);
?>
