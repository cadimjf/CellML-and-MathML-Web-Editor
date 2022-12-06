<?php

	/*
	* chamado por unitscompForm.php
	* pela função paste()
	* copiar um nó
	*/
	require_once("Node.php");
	require_once("attributes.php");
	//copyNodeId -> nó que o usuário copiou
	//pasteNodeId -> nó que será o pai
	copia(trim($_GET["copyNodeId"]), trim($_GET["pasteNodeId"]));

	function copia($copy, $paste){
		//$copy -> nó que o usuário copiou
		//$paste -> nó que será o pai
		$node = new Node($_GET['iduser']);
		$result = $node->getNode($copy);
		$oldAttr = $node->listAllAttributes($copy);
		if(is_array($result)){
			$at["idparent"] = $paste;//troca o idparent
			$at["item_order"] = $result["item_order"];
			$at["idprojeto"] = $result["idprojeto"];
			$at["name"] = $result["name"];
			$at["value"] = $result["value"];
			$newNodeValores = $node->insert($at);//cria um novo nó com idparent diferente
			if(is_array($oldAttr)){
				foreach($oldAttr as $a){
					$attr["idnode"] = $newNodeValores["idnode"];
					$attr["name"] = $a["attname"];
					$attr["value"] = $a["attvalue"];
					$attribute = new Attribute();
					$attribute->insert($attr);
					if($attr["name"]=='name'){
						$name = $a["attvalue"];
					}
				}
			}
			//echo (implode("|",$newNodeValores));
			$op = false;
			if(
				($newNodeValores["name"]=='variable') ||
				($newNodeValores["name"]=='component') ||
				($newNodeValores["name"]=='units') ||
				($newNodeValores["name"]=='unit') ||
				($newNodeValores["name"]=='math')
			){
				$op = true;
				if(is_null($name)){
					echo ($newNodeValores["idparent"]."|".$newNodeValores["idnode"]."|".$newNodeValores["name"]);
				}else{
					echo ($newNodeValores["idparent"]."|".$newNodeValores["idnode"]."|".$name);
				}
			}
			
			$children = $node->listByParent($copy);
			if(is_Array($children)){
				foreach($children as $child){
					if(($op)&&($newNodeValores["name"]!='math')){
						echo ("|");
					}
					copia($child["idnode"], $newNodeValores["idnode"]);
				}
			}
		}else{
			echo ("ERROR|ERROR|ERROR");
		}
	}
?>
