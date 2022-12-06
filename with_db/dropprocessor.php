<?php
	header("Content-type:text/xml");
	require_once('config.php'); 
	print("<?xml version=\"1.0\"?>");
	$id = $_GET["id"];
	$pid = $_GET["parent_id"];

	$link = mysql_pconnect($mysql_host, $mysql_user, $mysql_pasw);
	mysql_select_db ($mysql_db);

	saveNewParent($id,$pid);
	
	mysql_close($link);
	
	//creates xml show item details
	function saveNewParent($id,$pid){
		global $id_out;
        //get last position of item
        $ssql = "SELECT * FROM tree WHERE item_id=$id";
        $res=mysql_query($ssql);
        $sdata=mysql_fetch_array($res);

        //get new position
        $tsql = "SELECT Count(*) FROM tree WHERE item_parent_id=$pid";
        $res=mysql_query($tsql);
        $tdata=mysql_fetch_row($res);

        //update order in source branch
        $usql = "UPDATE tree
                    SET item_order=item_order-1
                    WHERE item_order>".$sdata["item_order"]." AND item_parent_id=".$sdata["item_parent_id"];

        $res=mysql_query($usql);

        //update item
		$sql = "UPDATE tree SET item_parent_id=$pid, item_order=".$tdata[0]." WHERE item_id=$id";
		$res = mysql_query($sql);
		if($res){
			$id_out = $id;
		}else{
			$id_out = "-1";
		}
	}
?>
<succeedded id="<?=$id_out?>"/>
