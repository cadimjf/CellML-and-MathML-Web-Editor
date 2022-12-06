<?
//this class reads the sintatic tree and save its nodes in XML
require_once("Node.php");
require_once("sintatictree.php");
require_once("def.php");
class SaveSintaticTree{
    private $sintaticTree;
    private $node;
	private $project;
    private $def;
    function __construct($sT, $root, $proj, $iduser){
        $this->node = new Node($iduser);
        $this->sintaticTree = $sT;
        $this->project=$proj;
        $this->def = new Def();
	}

    function readTree($sTree, $parent){//read a node from tree and finds out about the kind of class, so calls a specific function to save each node
        if($sTree instanceof BinaryOperation) return $this->saveBinOp($sTree, $parent);
        elseif($sTree instanceof UnaryOperation) return $this->saveUnOp($sTree, $parent);
        elseif($sTree instanceof ReservedWord) return $this->saveReservedWord($sTree, $parent);
        elseif($sTree instanceof Variable) return $this->saveVariable($sTree, $parent);
        elseif($sTree instanceof Functionn) return $this->saveFunctionn($sTree, $parent);
        elseif($sTree instanceof OtherWise) return $this->saveOtherWise($sTree, $parent);
        elseif($sTree instanceof Casee) return $this->saveCasee($sTree, $parent);
        elseif($sTree instanceof Select) return $this->saveSelect($sTree, $parent);
        elseif($sTree instanceof Identifier) return $this->saveIdentifier($sTree, $parent);
        elseif($sTree instanceof Operation) return $this->saveOperation($sTree, $parent);
        elseif($sTree instanceof Number) return $this->saveNumber($sTree, $parent);
        elseif($sTree instanceof Expression) return $this->saveExpression($sTree, $parent);
    }
 	
    function saveBinOp($sTree, $parent){
        $newParent = $this->insertNode("apply", $parent, "");
//         echo "estou salvando ". $this->def->getTag($sTree->idOp)."\n";
        $this->insertNode($this->def->getTag($sTree->idOp), $newParent, "");
     	$this->readTree($sTree->term1, $newParent);
        $this->readTree($sTree->term2, $newParent);
        return $newParent;
    }
    
    function saveUnOp($sTree, $parent){
//         echo "estou salvando ".$this->def->getTag($sTree->idOp)."\n";
        $newParent = $this->insertNode("apply", $parent, "");
        $this->insertNode($this->def->getTag($sTree->idOp), $newParent, "");
     	$this->readTree($sTree->term, $newParent);
        return $newParent;
    }
    
    function saveReservedWord($sTree, $parent){
        return $this->insertNode($this->def->getTag($sTree->name), $parent, "");
    }
    
    function saveVariable($sTree, $parent){
        return $this->insertNode("ci", $parent, $sTree->name);
    }
    
    function saveFunctionn($sTree, $parent){
		if(strtolower($sTree->name)=="ode"){

            $newId = $this->insertNode("apply", $parent, "");
            $this->insertNode("diff", $newId, "");
            $bvar = $this->insertNode("bvar", $newId, "");
            $this->readTree($sTree->expressionList[1], $bvar);//save the under variable			dt
            $this->readTree($sTree->expressionList[0], $newId);//save the variable above	dV
            return $newId;
        }else{
            $newParent = $this->insertNode("apply", $parent, "");
            $this->insertNode(strtolower($sTree->name), $newParent, "");
            for($i=0; $i<sizeof($sTree->expressionList); $i++)
                $this->readTree($sTree->expressionList[$i], $newParent);
            return $newParent;
        }
        
    }
    
    function saveOtherWise($sTree, $parent){
        $newParent = $this->insertNode("otherwise", $parent, "");
        $this->readTree($sTree->expr, $newParent);
        return $newParent;
    }
    
    function saveCasee($sTree, $parent){
        $newParent = $this->insertNode("piece", $parent, "");
        $this->readTree($sTree->value, $newParent);
        $this->readTree($sTree->expression, $newParent);
    	return $newParent;
    }
    
    function saveSelect($sTree, $parent){
        $newParent = $this->insertNode("piecewise", $parent, "");
        for($i=0; $i<sizeof($sTree->listCase); $i++)
                $this->readTree($sTree->listCase[$i], $newParent);
		$this->readTree($sTree->otherwise, $newParent); 
        return $newParent;
    }	

    function saveIdentifier($sTree, $parent){
    	return $this->insertNode("ci", $parent, $sTree->name);
    }

    function saveOperation($sTree, $parent){
        echo "SaveOperation<br>";
    }

    function saveNumber($sTree, $parent){
        return $this->insertNode("cn", $parent, $sTree->value);
    }

    function saveExpression($sTree, $parent){
     	echo "SaveExpression<br>";
    }

    function insertNode($operador, $parent,$value){
//         echo $this->project."\n";
    	$attr["idparent"]=$parent;
		$attr["item_order"]=0;
		$attr["idprojeto"]= $this->project;
		$attr["name"] = trim($operador);
		$attr["value"] =$value;
		$attr["tipo"]= "N";
		$valores = $this->node->insert($attr);
		return $valores["idnode"];
	
    }
}
?>
