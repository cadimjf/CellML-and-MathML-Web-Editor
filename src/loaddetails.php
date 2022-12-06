<?php 
	$id = $_GET["id"];
	require_once("Node.php");
	$node = new Node();
	$result = $node->getNode($id);
	if(is_array($result)){
		echo (implode("|",$result));
	}else{
		echo ("ERROR|ERROR|ERROR");
	}
	
?>
