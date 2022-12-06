<?php
	header("Content-type:text/xml");
	require_once('config.php'); 
	print("<?xml version=\"1.0\"?>");
	$id = $_GET["id"];
	$pid = $_GET["parent_id"];

	$link = mysql_pconnect("localhost", "root", "parafisio");
	mysql_select_db ("parafisioantigo");

	saveNewParent($id,$pid);
	
	mysql_close($link);
	
	//creates xml show item details
	function saveNewParent($id,$pid){
		global $id_out;
		//get last position of item
		$ssql = "SELECT * FROM node WHERE idnode=$id";
		$res=mysql_query($ssql);
		$sdata=mysql_fetch_array($res);
	
		//get new position
		$tsql = "SELECT Count(*) FROM node WHERE idparent=$pid";
		$res=mysql_query($tsql);
		$tdata=mysql_fetch_row($res);
	
		//update order in source branch
		$usql = "UPDATE node
			SET item_order=item_order-1
			WHERE item_order>".$sdata["item_order"]." AND idparent=".$sdata["idparent"];
	
		$res=mysql_query($usql);
	
		//update item
		$sql = "UPDATE node SET idparent=$pid, item_order=".$tdata[0]." WHERE idnode=$id";
		$res = mysql_query($sql);
		if($res){
			$id_out = $id;
		}else{
			$id_out = "-1";
		}
	}
?>
<succeedded id="<?=$id_out?>"/>
