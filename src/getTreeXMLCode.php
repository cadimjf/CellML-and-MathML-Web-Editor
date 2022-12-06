<?
	require_once("treefromdb.php");
	$treeFromDB = new treeFromDb($_GET["root"],$_GET['iduser']);
	$response = $treeFromDB->getXMLFromDb(false);
	print(trim($response));
?>
