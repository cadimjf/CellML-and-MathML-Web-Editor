<?php
require_once("def.php");
require_once("lexicalanalyser.php");
require_once("sintatictree.php");
require_once("SaveSintaticTree.php");
require_once("Node.php");
require_once("attributes.php");
class SintaticAnalyser{
	private $lex;
	private $currentToken;
	private $def;
	private $flag;
	private $errorMessage;
	private $iduser;
	

    function __construct($proj, $root, $eq,$name,$idParent, $iduser){
        $this->errorMessage = "";
		$this->flag=false;
		$this->lex = new LexicalAnalyser($eq);
		$this->currentToken = $this->lex->getNextToken();
		$this->def = new Def();
		$this->iduser = $iduser;
		$sintaticTree = $this->expr();
//         var_dump($sintaticTree);
        if(!$this->flag){//there were no errors
//             var_dump($sintaticTree);
			$saveSintTree = new SaveSintaticTree($sintaticTree, $idParent, $proj, $this->iduser);
// 			 echo("---------------------------------");
            $node = new Node($this->iduser);
            
            $newRoot = $saveSintTree->readTree($sintaticTree, $idParent);
            $node->deleteNode($root);//deleta o nó antigo
	    $attr = new Attribute();
		//salva o id da equação
	    $a["idnode"] = $newRoot; $a["name"] = "id"; $a["value"] = $name;
	    $attr->insert($a);
        	echo("$newRoot|Equation saved.");
        }else{
            
            echo("$root|Errors:".$this->errorMessage);
        }
// 		echo "<hr>";
// 		var_dump($sintaticTree);
	}
	
	function printSyntacticalError($t){ //imprime o erro com token esperado
		switch($t){
			case $this->def->TOKEN_ERROR:	return "Lexical Error";
			case $this->def->TOKEN_DIV:		return "Missing /";
			case $this->def->TOKEN_PLUS:		return "Missing +";
			case $this->def->TOKEN_MINUS:	return "Missing -";
			case $this->def->TOKEN_TIMES: 	return "Missing +";
			case $this->def->TOKEN_LPAR: 	return "Missing (";
			case $this->def->TOKEN_RPAR: 	return "Missing )";
			case $this->def->TOKEN_LBRA: 	return "Missing {";
			case $this->def->TOKEN_RBRA: 	return "Missing }";
			case $this->def->TOKEN_COMMA: 	return "Missing ,";
			case $this->def->TOKEN_SEMICOLON:return "Missing ;";
			case $this->def->TOKEN_REST: 	return "Missing %";
			case $this->def->TOKEN_POWER: 	return "Missing ^";
			case $this->def->TOKEN_ASSIGN: 	return "Missing =";
			case $this->def->TOKEN_EQUAL: 	return "Missing ==";
			case $this->def->TOKEN_GREATER: 	return "Missing >";
			case $this->def->TOKEN_GREATEREQUAL:return "Missing >=";
			case $this->def->TOKEN_LESS: 	return "Missing <";
			case $this->def->TOKEN_LESSEQUAL:return "Missing <=";
			case $this->def->TOKEN_DIFF: 	return "Missing !=";
			case $this->def->TOKEN_NEGATION: return "Missing !";
			case $this->def->TOKEN_OR:		return "Missing ||";
			case $this->def->TOKEN_AND:		return "Missing &&";
			case $this->def->TOKEN_ID:		return "Missing variable";
			case $this->def->TOKEN_EOF:		return "Unexpected ".$this->def->printChar($this->currentToken);
			case $this->def->TOKEN_NUMBER: 	return "Missing number";
			case $this->def->TOKEN_PI: 		return "Missing pi";
			case $this->def->TOKEN_E: 		return "Missing exponentiable";
			case $this->def->TOKEN_SELECT: 	return "Missing SELECT";
			case $this->def->TOKEN_CASE: 	return "Missing CASE";
			case $this->def->TOKEN_OTHERWISE:return "Missing OTHERWISE";
			case $this->def->TOKEN_TRUE: 	return "Missing true";
			case $this->def->TOKEN_FALSE: 	return "Missing false";
			case $this->def->TOKEN_COLON: 	return "Missing :";
		}
		return "Unknown error";
	}
	
	function match($t){
	
		if ($this->currentToken == $t) { //token casado
			$this->currentToken = $this->lex->getNextToken();
		} else { //erro - token nao casou
			$this->printError($this->printSyntacticalError($t));
		}
	}

	function printError($msg){
// 		echo("<font color=\"FF0000\">Sintatic error: $msg</font><br>");
		$this->errorMessage = $this->errorMessage . $msg."<br>";
        $this->flag=true;
	}
	
	function expr( ){//expr -> attrib
		$term = $this->attrib( );
		$this->match($this->def->TOKEN_EOF);
		return $term;
	}
	
	function attrib( ){//attrib -> or attrib'
		$term1 = $this->orr( );
		if(is_null($term1)) $this->printError("Missing left side, before = ");
		$term2 = $this->attrib2($term1);
		if(is_null($term2)) $this->printError("Missing right side, after = ");
		else return $term2;
	}
	
	function attrib2( $expr ){//attrib' -> = or attrib' | E |= select
		$term2;$term3;
		if($this->currentToken==$this->def->TOKEN_ASSIGN){
			$this->match($this->def->TOKEN_ASSIGN);
			if($this->currentToken==$this->def->TOKEN_SELECT)
				$term1 = $this->select();
			else{
				$term1 = $this->orr();
				if(is_null($term1)){
					$this->printError("Expression Error: Missing expression after =");
					return null;
				}
				$term = $this->attrib2($term1);
				if(!(is_null($term))) $term1 = $term;
			}
			return new BinaryOperation($this->def->TOKEN_ASSIGN, $expr, $term1);
		}
		if(is_null($term3)) return $term2;
		else return $term3;
	}
	
	function orr( ){//or -> and or'
//         echo "orr<br>\n";
		$term1 = $this->andd();
		if(is_null($term1)) {$this->printError("Expression Error: Missing expression before ||");}
		$term2 = $this->orr2($term1);
		if(is_null($term2)) return $term1;
		else return $term2;
	}
	
	function orr2( $expr ){//or' -> || and or' | E
//         echo "orr2<br>\n";
		$term2;$term3;
		if($this->currentToken==$this->def->TOKEN_OR){
			$this->match($this->def->TOKEN_OR);
			$term1 = $this->andd();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after ||");
				return null;
			}		
			$term2 = new BinaryOperation($this->def->TOKEN_OR, $expr, $term1);
			$term3 = $this->orr2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}
		if(is_null($term3)) return $term2;
		else return $term3;

	}
	
	function andd( ){//and -> equaldiff and'
//         echo "And<br>\n";
		$term1 = $this->equaldiff();
		if(is_null($term1)) {$this->printError("Expression Error: Missing expression before &&");}
		$term2 = $this->andd2($term1);
		if(is_null($term2)) return $term1;
		else return $term2;
	}
	
	function andd2( $expr){// and' -> && equaldiff and' | E
//         echo "and2<br>\n";
		$term2;$term3;
		if($this->currentToken==$this->def->TOKEN_AND){
			$this->match($this->def->TOKEN_AND);
            $term1 = $this->equaldiff();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after &&");
				return null;
			}	
			$term2 = new BinaryOperation($this->def->TOKEN_AND, $expr, $term1);
			$term3 = $this->andd2($term2);
            
			if(is_null($term3)) return $term2;
			else return $term3;
		}
		if(is_null($term3)) return $term2;
		else return $term3;
	}
	
    function equaldiff( ){// equaldiff -> lessthan equaldiff'
//         echo "equaldiff<br>\n";
		$term1 = $this->lessthan();
		if(is_null($term1)) {$this->printError("Expression Error: ");}
		$term2 = $this->equaldiff2($term1);
		if(is_null($term2)) return $term1;
		else return $term2;
	}
	
    function equaldiff2( $expr ){// equaldiff' -> == lessthan equaldiff' | != lessthan equaldiff' | E
//         echo "equaldiff2<br>\n";
		$term2;$term3;
		if($this->currentToken==$this->def->TOKEN_EQUAL){
			$this->match($this->def->TOKEN_EQUAL);
			$term1 = $this->lessthan();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after ==");
				return null;
			}	
			$term2 = new BinaryOperation($this->def->TOKEN_EQUAL, $expr, $term1);
			$term3 = $this->equaldiff2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}elseif($this->currentToken==$this->def->TOKEN_DIFF){
			$this->match($this->def->TOKEN_DIFF);
			$term1 = $this->lessthan();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after !=");
				return null;
			}	
			$term2 = new BinaryOperation($this->def->TOKEN_DIFF, $expr, $term1);
			$term3 = $this->equaldiff2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}
		if(is_null($term3)) return $term2;
		else return $term3;
	}
	
    function lessthan( ){// lessthan -> sum lessthan'
		$term1 = $this->sum();
		if(is_null($term1)) {$this->printError("Expression Error");}
		$term2 = $this->lessthan2($term1);
		if(is_null($term2)) return $term1;
		else return $term2;
	}
	
    function lessthan2($expr ){// lessthan' -> < sum lessthan' | <= sum lessthan' | >= sum lessthan' | > sum lessthan' | E
		$term3;$term2;
		if($this->currentToken==$this->def->TOKEN_LESS){
			$this->match($this->def->TOKEN_LESS);
			$term1 = $this->sum();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after <");
				return null;
			}	
			$term2 = new BinaryOperation($this->def->TOKEN_LESS, $expr, $term1);
			$term3 = $this->lessthan2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}elseif($this->currentToken==$this->def->TOKEN_LESSEQUAL){
			$this->match($this->def->TOKEN_LESSEQUAL);
			$term1 = $this->sum();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after <=");
				return null;
			}	
			$term2 = new BinaryOperation($this->def->TOKEN_LESSEQUAL, $expr, $term1);
			$term3 = $this->lessthan2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}elseif($this->currentToken==$this->def->TOKEN_GREATEREQUAL){
			$this->match($this->def->TOKEN_GREATEREQUAL);
			$term1 = $this->sum();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after >=");
				return null;
			}	
			$term2 = new BinaryOperation($this->def->TOKEN_GREATEREQUAL, $expr, $term1);
			$term3 = $this->lessthan2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}elseif($this->currentToken==$this->def->TOKEN_GREATER){
			$this->match($this->def->TOKEN_GREATER);
			$term1 = $this->sum();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after >");
				return null;
			}	
			$term2 = new BinaryOperation($this->def->TOKEN_GREATER, $expr, $term1);
			$term3 = $this->lessthan2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}
		if(is_null($term3)) return $term2;
		else return $term3;
	}
	
    function sum( ){// sum -> mult sum'
		$term1 = $this->mult();
		if(is_null($term1)) {$this->printError("Expression Error");}
		$term2 = $this->sum2($term1);
		if(is_null($term2)) return $term1;
		else return $term2; 
	}
	
    function sum2( $expr ){// sum' -> + mult sum' | - mult sum' | E
		$term3;$term2;
		if($this->currentToken==$this->def->TOKEN_PLUS){
			$this->match($this->def->TOKEN_PLUS);
			$term1 = $this->mult();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after +");
				return null;
			}
			$term2 = new BinaryOperation($this->def->TOKEN_PLUS, $expr, $term1);
			$term3 = $this->sum2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}elseif($this->currentToken==$this->def->TOKEN_MINUS){
			$this->match($this->def->TOKEN_MINUS);
			$term1 = $this->mult();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after -");
				return null;
			}
			$term2 = new BinaryOperation($this->def->TOKEN_MINUS, $expr, $term1);
			$term3 = $this->sum2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}
		if(is_null($term3)) return $term2;
		else return $term3;
	}
	
    function mult( ){// mult -> not mult'
		$term1 = $this->not();
		if(is_null($term1)) {$this->printError("Expression Error");}
		$term2 = $this->mult2($term1);
		if(is_null($term2)) return $term1;
		else return $term2;
	}
	function mult2( $expr){// mult' -> * not mult' | / not mult' | % not mult' | & not mult' | E | ^ not mult' 
		$term3;$term2;
		if($this->currentToken==$this->def->TOKEN_TIMES){
			$this->match($this->def->TOKEN_TIMES);
			$term1 = $this->not();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after *");
				return null;
			}
			$term2 = new BinaryOperation($this->def->TOKEN_TIMES, $expr, $term1);
			$term3 = $this->mult2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}elseif($this->currentToken==$this->def->TOKEN_REST){
			$this->match($this->def->TOKEN_REST);
			$term1 = $this->not();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after %");
				return null;
			}
			$term2 = new BinaryOperation($this->def->TOKEN_REST, $expr, $term1);
			$term3 = $this->mult2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}elseif($this->currentToken==$this->def->TOKEN_DIV) {
			$this->match($this->def->TOKEN_DIV);
			$term1 = $this->not();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after /");
				return null;
			}
			$term2 = new BinaryOperation($this->def->TOKEN_DIV, $expr, $term1);
			$term3 = $this->mult2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}elseif($this->currentToken==$this->def->TOKEN_POWER) {
			$this->match($this->def->TOKEN_POWER);
			$term1 = $this->not();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after ^");
				return null;
			}
			$term2 = new BinaryOperation($this->def->TOKEN_POWER, $expr, $term1);
			$term3 = $this->mult2($term2);
			if(is_null($term3)) return $term2;
			else return $term3;
		}
		if(is_null($term3)) return $term2;
		else return $term3;
		
	}
	function not( ){// not -> not'|value 
		$term1 = $this->not2(null);
		if(is_null($term1)) { 
				$term2 = $this->value();
				return $term2;
		}else return $term1;
	}
	
    function not2( $expr){// not' -> ! value  | + value  | - value  | E
		$term1;$term2;
        if($this->currentToken==$this->def->TOKEN_NEGATION){
			$this->match($this->def->TOKEN_NEGATION);
			$term1 = $this->value();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after !");
				return null;
			}
			$term2 = new UnaryOperation($this->def->TOKEN_NEGATION, $term1);
		}elseif($this->currentToken==$this->def->TOKEN_PLUS){
			$this->match($this->def->TOKEN_PLUS);
			$term1 = $this->value();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after +");
				return null;
			}
			$term2 = new UnaryOperation($this->def->TOKEN_PLUS, $term1);
		}elseif($this->currentToken==$this->def->TOKEN_MINUS){
//             echo "\nestou em not-------2\n";
			$this->match($this->def->TOKEN_MINUS);
			$term1 = $this->value();
			if(is_null($term1)){
				$this->printError("Expression Error: Missing expression after -");
				return null;
			}
			$term2 = new UnaryOperation($this->def->TOKEN_MINUS, $term1);
		}
		if(is_null($term2)) return $term1;
		else return $term2;
	}
	function value( ){// value -> ID | NUM | ID () | ID ( exprlist | ( expr )| reservedWords
		$term1;$term2;
		if($this->currentToken==$this->def->TOKEN_ID){
			$name = $this->lex->getLexema();
			$this->match($this->def->TOKEN_ID);
			if($this->currentToken==$this->def->TOKEN_LPAR){
				$this->match($this->def->TOKEN_LPAR);
				$function = new Functionn($name);
				$f = $this->exprlist($function);
				return $f;
			}else{ return new Variable(strval($name));}	
		}elseif($this->currentToken==$this->def->TOKEN_NUMBER){
			$number = new Number($this->lex->getLexema());
			$this->match($this->def->TOKEN_NUMBER);
			return $number;
		}elseif($this->currentToken==$this->def->TOKEN_LPAR){
			$this->match($this->def->TOKEN_LPAR);
			$term1 = $this->orr( );
			$this->match($this->def->TOKEN_RPAR);
			return $term1;
		}elseif(//reservedWords
			($this->currentToken==$this->def->TOKEN_E)||//expontiable
			($this->currentToken==$this->def->TOKEN_PI) || //pi constant
			($this->currentToken==$this->def->TOKEN_INF)|| //infinity
			($this->currentToken==$this->def->TOKEN_TRUE)|| //true
			($this->currentToken==$this->def->TOKEN_NAN) ||//not a number
			($this->currentToken==$this->def->TOKEN_FALSE) //false
		){
			$aux = $this->currentToken;
			$this->match($this->currentToken);
			return new ReservedWord($aux);
		}
		return null;
	}
	
	function exprlist($function){// exprlist -> ) | expr exprlisttail )
		if($this->currentToken==$this->def->TOKEN_RPAR){
			$this->match($this->def->TOKEN_RPAR);
		}else{
			$function->add($this->orr());
			$function = $this->exprlisttail($function);
			$this->match($this->def->TOKEN_RPAR);
		}
		return $function;
	}
	
    function exprlisttail( $function ){// exprlisttail -> , expr exprlisttail | E
		if($this->currentToken==$this->def->TOKEN_COMMA){
			$this->match($this->def->TOKEN_COMMA);
			$function->add($this->orr());
			$function = $this->exprlisttail($function);
		}
		return $function;
	}
	
	
	function select( ){// select -> SELECT{ case+ otherwise}
//         echo "select<br>\n";
		$select =null;
		if($this->currentToken==$this->def->TOKEN_SELECT){
			$this->match($this->def->TOKEN_SELECT);
			$select = new Select();
			$this->match($this->def->TOKEN_LBRA);
			$value = $this->orr();
			if(is_null($value)) $this->printError("Expression Error: Missing expression before case");
			if($this->currentToken==$this->def->TOKEN_CASE){
				$this->match($this->def->TOKEN_CASE);
				$this->match($this->def->TOKEN_COLON);
				$condExpr = $this->orr();
				$select->addCase(new Casee($value, $condExpr));
				$this->match($this->def->TOKEN_SEMICOLON);
			}else 	$this->printError("Expression Error: Missing case clause");
			$select = $this->casee($select);
			$select = $this->otherwise($select);
			$this->match($this->def->TOKEN_RBRA);
		}
		return $select;
	}
	function casee($select ){// case -> value CASE: orr;
//         echo "case<br>\n";
        if(	($this->currentToken==$this->def->TOKEN_OTHERWISE)||
			($this->currentToken==$this->def->TOKEN_RBRA)) return $select; //exit this function
		$value = $this->orr();
		if($this->currentToken==$this->def->TOKEN_CASE){
			if(is_null($value)) $this->printError("Expression Error: Missing expression before case");
			$this->match($this->def->TOKEN_CASE);
			$this->match($this->def->TOKEN_COLON);
			$condExpr = $this->orr();
			$select->addCase(new Casee($value, $condExpr));
			$this->match($this->def->TOKEN_SEMICOLON);
			$select = $this->casee($select);			
		}
		return $select;
		
	}
	function otherwise( $select ){// otherwise -> OTHERWISE(orr);
//         echo "otherwise<br>\n";
        
		if($this->currentToken==$this->def->TOKEN_OTHERWISE){
			$this->match($this->def->TOKEN_OTHERWISE);
			$this->match($this->def->TOKEN_LPAR);
			$otherwise = new Otherwise($this->orr());
			$select->setOtherwise($otherwise);
			$this->match($this->def->TOKEN_RPAR);
			$this->match($this->def->TOKEN_SEMICOLON);
		}
		return $select;
	}
}

?>
