<?
	require_once("xmlFromDb.php");
	$file = fopen($_GET["file"],"wb");
//	var_dump($iduser);
// 	$comando = "chmod 666 $fileName";
// 	$resultado = system($comando, $retval);
	$xml = new XmlFromDb($_GET["proj"],$_GET["root"], $file, $_GET['iduser']);
	//echo "<b>&lt;?xml</b> <font color='#009900'>version</font>=<font color='#990000'>\"1.0\"</font> <font color='#009900'>encoding</font>=<font color='#990000'>\"UTF-8\"</font>?<b>&gt;</b>";
	//echo "<br>";
	//echo "<b>&lt;model&gt;</b>";
	fwrite($file, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<model>\n");	
	//$xml->getXml('rdf:RDF');
//	echo "teste $iduser";
//	echo "$iduser";
	$varD = $xml->getXml('documentation', $_GET['iduser']);
	$varU = $xml->getXml('units', $_GET['iduser']);
	$varC = $xml->getXml('component', $_GET['iduser']);
	$varCn = $xml->getXml('connection', $_GET['iduser']);
	
	if((!$varU)||(!$varC)){
		$xml->getXmlByParent($_GET["root"]+1);
	}
	if((!$varD)||(!$varCn)){
		$xml->getXmlByParent($_GET["rootDocConn"]);
	}
	
	
	$xml->getXmlByParent($_GET["rootGroup"]+1);
	//$xml->getXml('group');
	fwrite($file, "</model>\n");
	//echo "<b>&lt;/model&gt;</b>";
	fclose($file);
?>
