<?
	require_once("Node.php");
	$node = new Node($_GET['iduser']);
	$vars = $node->listVariables($_GET['idComp']);
	if(is_array($vars)){
		print(implode("|", $vars));
	}else{
		print ("ERROR");
	}
?>
