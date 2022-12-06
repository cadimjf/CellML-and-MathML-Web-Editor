<?php
	require_once('config.php'); 

	$link = mysql_pconnect($mysql_host, $mysql_user, $mysql_pasw);
	mysql_select_db ($mysql_db);
	deleteNode($_POST["item_id"]);
	mysql_close($link);
	
	//insert item
	function deleteNode($id){
		deleteBranch($id);
        deleteAndUpdateOrder($id,$order);
		print("<script>top.doDeleteTreeItem('$id');</script>");
	}
    function deleteAndUpdateOrder($id){
    	$i_sql = "SELECT item_order,item_parent_id FROM tree WHERE item_id=".$id;
		$d_sql = "DELETE FROM tree WHERE item_id=".$id;

		$res = mysql_query($i_sql);
        if ($res){
            $data=mysql_fetch_array($res);
            $order=$data['item_order'];
            $pid=$data['item_parent_id'];

            $u_sql = "UPDATE tree
                        SET item_order=item_order-1
                        WHERE item_order>".$order." AND item_parent_id=".$pid;
            $ures=mysql_query($u_sql);
            $dres=mysql_query($d_sql);
        }
    };
	function deleteSingleNode($id){
		$d_sql = "Delete from tree where item_id=".$id;
		$resDel = mysql_query($d_sql);
	}
	function deleteBranch($pid){
		$s_sql = "Select item_id,item_order from tree where item_parent_id=$pid";
		$res = mysql_query($s_sql);

		if($res){
			while($row=mysql_fetch_array($res)){
				deleteBranch($row['item_id']);
				deleteSingleNode($row['item_id']);
			}
		}

	}
?>
