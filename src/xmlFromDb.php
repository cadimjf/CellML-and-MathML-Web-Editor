<?php
require_once("Node.php");
require_once("attributes.php");
class XmlFromDb extends DataBase
{
	private $project;
	private $node;
	private $treeRoot;
	private $file;

	function __construct($proj, $tR, $f,$paramiduser){
		global $project, $node, $attribute, $treeRoot,$file;
		parent::__construct();//construtor da classe pai -> database
		$project = $proj;
		$treeRoot = $tR;
		$attribute = new Attribute();
		$node = new Node($paramiduser);
		$file=$f;
	}

	function getXml($tag, $iduser){
		global $project, $node, $treeRoot;
		$items = $node->listByProjectName($project, $tag,$treeRoot, $iduser);
		if(is_null($items)){
			return false;
		}else{
			$this->printNode($items,0);
			return true;
		}
	}


	function getXmlByParent($id){
		global $node;
		$items = $node->listByParent($id);
		$this->printNode($items,0);
	}


	function printNode($elements, $tabulacao){
		global $project, $node, $attribute, $file;
		//,idparent,item_order, idprojeto, name, value
		if(is_Array($elements)){
			foreach($elements as $item){
				for($aux=0;$aux<$tabulacao;$aux++){//formata a tabulação
						//echo ("&nbsp;&nbsp;&nbsp;&nbsp;");
						fwrite($file,"\t");
				}
				//echo ("&lt;<b>".$item["name"]."</b>");
				fwrite($file,"<".$item["name"]);
				$attr = $attribute->listAttributesByNode($item["idnode"]);
				if(is_Array($attr)){
					foreach($attr as $a){
						if(($item["name"]=='map_components')||($item["name"]=='map_variables')){//||($item["name"]=='component_ref')
				//			echo ("&nbsp;<font color='#009900'>".trim($a["attname"])."</font>=<font color='#990000'>\"".$node->getAttrName($a["attvalue"])."\"</font>");
							fwrite($file," ".$a["attname"]."=\"".$node->getAttrName($a["attvalue"])."\"");
						}else{
							//echo ("&nbsp;<font color='#009900'>".trim($a["attname"])."</font>=<font color='#990000'>\"".trim($a["attvalue"])."\"</font>");
							fwrite($file," ".$a["attname"]."=\"".$a["attvalue"]."\"");
						}
					}
				}
				$children = $node->listByParent($item["idnode"]);
					if(is_array($children) ){
					//echo("&gt;");
					//echo ("<br>\n");
					fwrite($file,">\n");
					$this->printNode($children, $tabulacao + 1);
					//echo("&lt;/<b>".$item["name"]."</b>&gt;<br>\n");//fecha tag
					for($aux=0;$aux<$tabulacao;$aux++){//formata a tabulação
						//echo ("&nbsp;&nbsp;&nbsp;&nbsp;");
						fwrite($file,"\t");
					}
					fwrite($file,"</".$item["name"].">\n");
					
				}else{
					if(trim($item["value"])<>NULL){
						//echo("&gt;");
						//echo (trim($item["value"]));
						//echo("&lt;/<b>".$item["name"]."</b>&gt;\n");//fecha tag
						//echo ("<br>\n");
						fwrite($file,">".trim($item["value"])."</".$item["name"].">\n");
					}else{
						//echo("&nbsp;<b>/</b>&gt;<br>\n");//fecha tag
						fwrite($file," />\n");
					}
				}
				
				
			}
		}
	}
}
?>
