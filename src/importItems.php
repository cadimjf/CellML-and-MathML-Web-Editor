<?php
	require_once("Node.php");
	require_once("attributes.php");
	$arrayIds = explode(",",$_GET["ids"]);
	$tree=null;
	saveImported($arrayIds, $_GET["root"]);

	function saveImported($arrayIds,$PARENT){
		$node = new Node($_GET['iduser']);
		$objAttribute = new Attribute();
		$idParentOld;
		$idNew;
		foreach($arrayIds as $id){
			$attr = $node->getNode($id);
			$attr["idprojeto"] = $_GET["codProj"];
			if($attr["idparent"]==$idOld){
				$idparent = $idNew;
			}else{
				$idparent = $PARENT;
			}
			if(($attr['name']=='component')||($attr['name']=='units')){
				$idOld = $attr["idnode"];
				$idNew = copia($id, $idparent, false);
			}elseif($attr['name']=='math'){
				copia($id, $idparent, true);
			}else{
				copia($id, $idparent, false);
			}
		}
	}

	function copia($copy, $paste, $copiaFilhos){
		//$copy -> nó que o usuário copiou
		//$paste -> nó que será o pai
		$node = new Node($_GET['iduser']);
		$result = $node->getNode($copy);
		$oldAttr = $node->listAllAttributes($copy);
		if(is_array($result)){
			$at["idparent"] = $paste;//troca o idparent
			$at["item_order"] = $result["item_order"];
			$at["idprojeto"] =$_GET["codProj"];
			$at["name"] = $result["name"];
			$at["value"] = $result["value"];
			$at["tipo"] = '2';
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
			$children = $node->listByParent($copy);
			if((is_Array($children)) && ($copiaFilhos) ){
				foreach($children as $child){
					copia($child["idnode"], $newNodeValores["idnode"], true);
				}
			}
		}else{
			return -1;
		}
		return $newNodeValores["idnode"];
	}
/*
$attr["idnode"] = $idnode;
$attr["idparent"] = $idparent;
$attr["item_order"] = $item_order;
$attr["idprojeto"] = $idprojeto;
$attr["name"] = $name;
$attr["value"] = $value;
$attr["tipo"] = $tipo;
*/
?>
