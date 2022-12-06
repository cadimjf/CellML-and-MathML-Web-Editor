<?	require_once("Node.php");
	$node = new Node($_GET['iduser']);
	require_once("attributes.php");
	$attribute = new Attribute();
	/* ---------Variáveis do $_GET
	idComp
	id
	idparent
	idvar
	idConn
	order
	rootDocConn
	idproj
	idVarConn
	idCompConn
	*/
	
	/***** RETURN****
	idConn
	order
	idVarConn
	idCompConn
	******/
	if($_GET["idCompConn"]==$_GET["idComp"]){
		$msg="está se conectando com o mesmo componente anterior";
		$mapVar = $node->getMapConnection($_GET["id"], $_GET["idVarConn"], 1, "variable");
		if(is_numeric($mapVar["idnode"])){
			$msg =" existe a ligação entre as variáveis";
			$node->deleteNode($mapVar["idnode"]);
		}
		$mapVar = $node->getMapConnection($_GET["id"], $_GET["idVarConn"], 2, "variable");
		if(is_numeric($mapVar["idnode"])){
			$msg = "existe a ligação entre as variáveis";
			$node->deleteNode($mapVar["idnode"]);
		}
		$id=insertNode($_GET["idConn"],'3',$_GET["idproj"],"map_variables","");
		if($_GET['order']==1){
			$value1=$_GET["id"];
			$value2=$_GET["idvar"];
		}elseif($_GET['order']==2){
			$value1=$_GET["idvar"];
			$value2=$_GET["id"];
		}
		$return["idConn"] = $_GET["idConn"];
		$return["order"] = $_GET["order"];
		$return["idVarConn"] = $_GET["idVarConn"];
		$return["idCompConn"] = $_GET["idvar"];
		//insere os atributos
		insertAttr($id,"variable_1",$value1);
		insertAttr($id,"variable_2",$value2);
	}else{
		$msg = "conecta-se com componente diferente do anterior";
		$mapVar = $node->getMapConnection($_GET["id"], $_GET["idVarConn"], $_GET["order"], "variable");
		if(is_numeric($mapVar["idnode"])){
			$msg = "existe a ligação entre as variáveis ".$_GET["id"]." -> ".$_GET["idVarConn"];
			//existe a ligação entre as variáveis
			$node->deleteNode($mapVar["idnode"]);
		}
		$msg = "verifica se o novo componente já está conectado";
		$mapComp  = $node->getMapConnection($_GET["idparent"], $_GET["idComp"],1,  "component");
		$mapComp2 = $node->getMapConnection($_GET["idparent"], $_GET["idComp"],2,  "component");
		if(is_numeric($mapComp["idnode"])){
			$_GET["order"] = 1;
			$idPar = $mapComp["idparent"];
			$return["idConn"] = $idPar;
		}if(is_numeric($mapComp2["idnode"])){
			$_GET["order"] = 2;
			$idPar = $mapComp2["idparent"];
			$return["idConn"] = $idPar;
		}else{
			$msg = "cria o node connection";
			$i=insertNode($_GET["rootDocConn"],'2',$_GET["idproj"],"connection","");
			$idPar=insertNode($i,'2',$_GET["idproj"],"connection","");
			$return["idConn"] = $idPar;
			$msg = "cria o map_components";
			$id=insertNode($idPar,'2',$_GET["idproj"],"map_components","");
			$return["order"] = $_GET["order"];
			if($_GET['order']==1){
				$value1 = $_GET["idparent"];
				$value2 = $_GET["idComp"];
			}elseif($_GET['order']==2){
				$value1 = $_GET["idComp"];
				$value2 = $_GET["idparent"];
			}else{//não existe order
				$value1 = $_GET["idparent"];
				$value2 = $_GET["idComp"];
				$return["order"] = 1;
			}
			$return["idCompConn"] = $_GET["idComp"];
			//insere os atributos
			insertAttr($id,"component_1",$value1);
			insertAttr($id,"component_2",$value2);
		}
		$id=insertNode($idPar,'3',$_GET["idproj"],"map_variables","");
		if($_GET['order']==1){
			$value1=$_GET["id"];
			$value2=$_GET["idvar"];
		}elseif($_GET['order']==2){
			$value1=$_GET["idvar"];
			$value2=$_GET["id"];
		}else{
			$value1=$_GET["id"];
			$value2=$_GET["idvar"];
		}
		$return["idVarConn"] = $_GET["idvar"];
		//insere os atributos
		insertAttr($id,"variable_1",$value1);
		insertAttr($id,"variable_2",$value2);
	}
	echo $return["idConn"]."|".$return["order"]."|".$return["idVarConn"]."|".$return["idCompConn"]."|".$msg;
	
	function insertAttr($idnode, $name, $value){
		$attribute = new Attribute();
		$array =array("idnode"=>$idnode,"name"=> $name, "value"=>$value);
		$attribute->insert($array);
	}
	function insertNode($idparent, $item_order, $idprojeto, $name, $value){
		$node = new Node($_GET['iduser']);
		$array["idparent"] = $idparent;
		$array["item_order"] = $item_order;
		$array["idprojeto"] = $idprojeto;
		$array["name"] = $name;
		$array["value"] = $value;
		$array["tipo"] = "N";
		$n = $node->insert($array);
		return $n["idnode"];
	}
?>
