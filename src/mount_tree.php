<?php
/**
*Esta classe recebe o XML e guardas as tags no banco de dados
*/

//ini_set("memory_limit","28M");

class mount_tree{
	private $lastId;
	private $parser;
	private $nomeProj;
	private $codProj;
	private $rootNode;
	private $tipo;
	private $iduser;
	function __construct($file, $nomeProjeto, $codprojeto, $t, $param_iduser){
		global $lastId,$parser, $nomeProj, $codProj,$rootNode,$tipo, $iduser;
		require_once("parser_DOM.php");
		$this->iduser = $param_iduser;
		$parser = new parser_DOM($file);
		$nomeProj = $nomeProjeto;
		$codProj = $codprojeto;
		$tipo = $t;
		$rootNode = $this->newNode($nomeProj,'0','0');
		//TODO
		/*
		*	colocar model
		*	$rootNode = $this->newNode($nomeProj,'0','0');
		*
		*/
		$lastId = $rootNode;
	}

	function getRoot(){
		global $rootNode;
		return $rootNode;
	}
	function newNode($name,$parent,$order,$value=NULL){
		global $lastId,$codProj,$tipo, $iduser;
		require_once("Node.php");
		$node = new Node($this->iduser);
		$attr = array();
		$attr["idparent"] = $parent;
		$attr["item_order"] = $order;
		$attr["idprojeto"] = $codProj;
		$attr["name"] = $name;
		$attr["value"] = $value;
		$attr["tipo"] = $tipo;
		$insertedNode = $node->insert($attr);
		
		if(is_array($insertedNode)){
			$lastId = $insertedNode['idnode'];
		}
		return $lastId;
	} 
	function listTagXml($tag, $tipo){
		global $lastId, $parser, $codProj,$rootNode;
		$array_xml= $parser->listNodesByTag($tag,$tipo);
//	if($tag=="component")var_dump($array_xml);
		$lastId = $this->newNode($tag,$rootNode, '1');
		$this->saveDataBase($array_xml, $lastId);
	}


	function saveDataBase($array_xml, $idParent){
		global $lastId,$codProj, $comp1,$comp2;
		require_once("Node.php");
//		$f = fopen("saida.txt", 'a');
		$node = new Node($idnode);
		if (is_array($array_xml))
		foreach($array_xml as $tag){
			if (is_array($tag))
			foreach($tag  as $item){
				
				if(trim($item["tagname"])=="rdf:RDF"){
					break;
				}elseif((trim($item["tagname"])!="apply")&&(trim($item["tagname"])!="math")&&(trim($item["tagname"])!="component") &&(trim($item["tagname"])!="piece")&&(trim($item["tagname"])!="piecewise")){
					$lastId = $this->newNode( trim($item["tagname"]) ,$idParent, '2', $item["value"]);
				}else{
					$lastId = $this->newNode( trim($item["tagname"]) ,$idParent, '2', null);
				}
			
//				fwrite($f, "tagname:".trim($item["tagname"])." - ".trim($item["value"])."\n");
			
				if (is_array($item["attributes"] ) )
				foreach($item["attributes"] as $attr){
					$a = array();
					$a["idnode"] = $lastId;
					$a["name"] = $attr["name"];

//					fwrite($f, "--".trim($attr["name"]).": ".trim($attr["value"])."\n");

					if($item["tagname"]=='map_components'){
						if($attr["name"]=="component_1"){
							//$comp1 = $attr["value"];
							$comp1 = $node->getNodeByProjectName($codProj,$attr["value"],'component');
							$a["value"] = $comp1;
						}elseif($attr["name"]=="component_2"){
							$comp2 = $node->getNodeByProjectName($codProj,$attr["value"],'component');
							$a["value"] = $comp2;
						}
					}elseif($item["tagname"]=='map_variables'){
						if($attr["name"]=="variable_1"){
							$a["value"] = $node->getVarByProjectName($codProj, $attr["value"], $comp1);
						}elseif($attr["name"]=="variable_2"){
							$a["value"] = $node->getVarByProjectName($codProj, $attr["value"], $comp2);
						}
					}else{
						$a["value"] = $attr["value"];
					}
					require_once("attributes.php");
					$attribute = new Attribute();
					$attribute->insert($a);
				}
				if(is_array($item["children"])){//tem filhos
					$this->saveDataBase($item["children"], $lastId);
				}
			}
		}
//		fclose($f);
	}
	function insertEspecialAttr($value,$tag, $comp){
		global $codProj,$idnode;
		require_once("Node.php");
		$node = new Node($this->idnode);
		return $node->getNodeByProjectName($codProj,$value,$tag);
	}
}
?>
