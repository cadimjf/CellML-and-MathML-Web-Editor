<?
	require_once("Node.php");
	$node = new Node($_GET["iduser"]);
//	print($_GET["iduser"]);
	$components = $node->listComponents($_GET['name'],$_GET['idproj'],$_GET["idcomp"]);
	$connection = $node->getConnectionByVar($_GET["idvar"], $_GET["idcomp"]);
	if(is_array($components) ||  is_array($connection)){
		print(implode("|", $components));
		print ("_______________________________");
		print(implode("|", $connection));
	}else{
		print ("ERROR");
	}
?>
