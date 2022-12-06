<?php
/**
*********************************************************************
********************Arquivo antigo! Nao utilizado********************
*********************************************************************
*/


/**
The Grammar of the equations
[non-terminal] terminal grammar sintax
[equation] -> [expression] = [expression] |[expression] >= [expression]|[expression] <= [expression]
[expression] -> [expression] + [term] | [expression] - [term] | [term]
[term] -> [term] * [factor] | [term] / [factor] | [factor]
[factor] -> variable | number | ( [expression] )
[factor] -> function ( [expression] ) | function ( [expression], [expression] )
[factor] -> - [expression]
*/
require_once("Node.php");
require_once("attributes.php");
class Grammar
{
	private $project;
	private $node;
	private $code;
	private $currentParent;
	private $attribute;
	private $nm;
	private $expressao;
	private $equation;
	private $erro;
	private $msg;
	/*
	* Construtor
	*/
	function __construct($proj, $tR, $c,$n,$idParent){
		global $project, $node, $attribute, $currentParent,$nm,$expressao,$equation, $erro, $msg,$notacaoCientifica;
		$project = $proj;
		$currentParent=$idParent;
		$code = $c;
		$nm = $n;//id do apply
		$attribute = new Attribute();
		$node = new Node();
		$expressao = "[A-Za-z0-9\/\.\+\*\^\(\)\, _-]";
		$equation = "[A-Za-z0-9\/\.\+\*\^\(\)\,\{\}\=\;\|& <>!:_-]";//><!=
		$notacaoCientifica = "[-|\+| -| \+]?[0-9]+[\.]?[0-9]*[e][-|\+]?[0-9]+";
		$erro=false;
		
		$newNode = $this->estadoEquacao($code, $currentParent);
		if($erro){
			//$a = $node->getNode($newNodem);
			$node->deleteNode($newNodem);//deleta o no criado agora, pois houve erro
			//echo("$tR|Error - $msg");
			echo("$tR|Error");
		}else{
			$a = $node->getNode($tR);
			if(is_numeric($tR)){
				$node->deleteNode($tR);//deleta o nó antigo
			}
			
			//echo("$newNode|Equation saved. $msg");
			echo("$newNode|Equation saved.");
		}
	}
	
	/*
	* Esta função separa duas partes de um expressão qualquer a partir de um sinal, recebido em $param.
	* Ela separa avaliando os parenteses. Por exemplo, a expressão (x+w)+(z+y) divide uma equação assim:
	* parte1: (x+w) parte2:plus parte3:(z+y)
	* Uma expressão regular não é capaz de trabalhar desta forma, podendo dividir a equação assim:
	* parte1: (x parte2: plus parte3: w)+(z+y)
	* 
	*/
	function processaParenteses($expr, $param){
		$tamanho = strlen($expr);
		for($i=$tamanho;$i>=0; $i--){
			if(($expr[$i]==$param[0]) || ($expr[$i]==$param[1])){//encontra um dos sinais 
				$exprEsquerda = substr($expr, 0, $i);
				$exprDireita = substr($expr, $i+1, $tamanho);
				if($this->confereExpressaoValida($exprEsquerda) 
					&& $this->confereExpressaoValida($exprDireita)){
					return array($exprEsquerda, $this->getOperador($expr[$i]), $exprDireita);
				}
			}
		}
		return false;
	}

	function confereExpressaoValida($expr){
		$parentesesAbre=0;
		$parentesesFecha=0;
		for ($i=0; $i<strlen($expr);$i++){
			if ($expr[$i]=="(")
				$parentesesAbre++;
			if($expr[$i]==")") 
				$parentesesFecha++;
		}
		if(($parentesesAbre == $parentesesFecha)){
			return true;
		}else{
			return false;
		}
		
	}


	function getOperador($sinal){
		$s = trim($sinal);
		switch($s){
			case "+":
				$operador = "plus";
			break;
			case "-":
				$operador = "minus";
			break;
			case "*":
				$operador = "times";
			break;
			case "/":
				$operador = "divide";
			break;
			case ">=":
				$operador = "geq";
			break;
			case "<=": 
				$operador = "leq";
			break;
			case ">": 
				$operador = "gt";
			break;
			case "<": 
				$operador = "lt";
			break;
			case "!=": 
				$operador = "neq";
			break;
			case "<>": 
				$operador = "neq";
			break;
			case "=":
				$operador = "eq";
			break;
			case "&":
				$operador = "and";
			break;
			case "|":
				$operador = "or";
			break;
			case "%":
				$operador = "module";
			break;
			default:
				$operador = null;
			break;
		}
		return $operador;
	}
	
	/*
	* Verifica se o último parenteses está fechado o primeiro
	*(((Y-(4-(v/(hr*(t-w)))))-y)-((z/r)/b)) -> retorna true
	*((Y-(4-(v/(hr*(t-w)))))-y)-(z/r/b) ->retorn false
	*/
	function confereParenteses($expr){
		$parentesesAbre=0;
		$parentesesFecha=0;
		for ($i=0; $i<strlen($expr);$i++){
			if ($expr[$i]=="(")
				$parentesesAbre++;
			if($expr[$i]==")") 
				$parentesesFecha++;
			if(($parentesesAbre == $parentesesFecha) && ($i != (strlen($expr)-1) )){
				return false;
			}
		}
		return true;
	}

	/*
		insere um nó
	*/
	function insereNode($operador, $parent,$value){
		global $project, $node;
		$attr["idparent"]=$parent;
		$attr["item_order"]=0;
		$attr["idprojeto"]= $project;
		$attr["name"] = trim($operador);
		$attr["value"] =$value;
		$attr["tipo"]= "N";
		$valores = $node->insert($attr);
		return $valores["idnode"];
	}


	/*
	*	[equation] -> [expression] = [expression]
	*	Separa a equacao pelo sinal '='.
	*/
	function estadoEquacao($code,$currentP){
		global $node, $attribute,$nm,$expressao,$equation, $msg;
		$expr= trim($expr);
		$msg = $msg."<hr>[equation] -> [expression] = [expression]<Br>Recebi:$code";
		$busca= "^(\($equation+\))$";
		eregi($busca, $code, $matches);
		
		$conferePar = $this->confereParenteses($code);
		if(is_array($matches)&&($conferePar)){
			$tamanho =  strlen($matches[1])-2;
			$code = substr($matches[1],1,$tamanho);// ([expressao])
			//echo "<br>--------->>>>>>>>>>>>>>>>>>{$code}<<<<<<<<<<<<<<<<<<<<<----------------<br>";
		}
		
		$buscaSinal = "<=|>=|!=|<>|=|<|>";
		$busca= "^($expressao+)($buscaSinal)($expressao+)$";
		eregi($busca, $code, $matches);

		$currentParent = $this->insereNode("apply", $currentP, null);
		$a['idnode']= $currentParent;
		$a['name'] = 'id';
		$a['value'] = $nm;
		$nm=null;
		$t = $attribute->insert($a);
		if(is_array($matches)){
			//identifica o sinal da equação
			$operador = $this->getOperador($matches[2]);
			$msg = $msg."<br>Passando para estado Expressao";
			$this->insereNode($operador, $currentParent,null);
			$this->estadoExpressao($matches[1],$currentParent);
			$this->estadoExpressao($matches[3],$currentParent);
			require_once("xmlFromDb.php");
			$xml = new XmlFromDb(43,43, null);
			//echo ($xml->getXmlByParent(43));
			
		}else{
			//select
			$msg = $msg."<br>Passando para estado Fator- $code";
			$this->estadoFator($code,$currentParent);
		}
		return $currentParent;
	}

	/*
	 * Esta função verifica se o sinal + ou - que foi encontrado na fuction estadoExpressao()  pertence a uma numeral em notação cientifica, com por exemplo, 3.4545e-4
	*/
	function confereSinalExpressao($arrayBusca){
		global $expressao;
		$notacaoCientifica1 = "[-|\+]?[0-9]+[\.]?[0-9]*[e]";
		$notacaoCientifica2 = "[0-9]+";

		//se a primeira parte é semelhante a notação cientifica.
		$parte1= "^($expressao*)($notacaoCientifica1)$";
 		eregi($parte1, $arrayBusca[1], $matches1);
		//$msg= $msg."<br>---=====>>".$arrayBusca[1]."____".$arrayBusca[2]."____".$arrayBusca[3];
		//se a segunda parte é semelhante a notação cientifica.
		$parte2= "^($notacaoCientifica2)($expressao*)$";
 		eregi($parte2, $arrayBusca[3], $matches2);
		if(is_array($matches1) && is_array($matches2)){
			return true; //este sinal é parte de uma notação científica
		}else{
			return false; //é um sinal de operação.
		}
	}




	/*
	* [expression] -> [expression] + [term] | [expression] - [term] | [term]
	* Separa os termos por + e -, que estao FORA dos parentesis
	*/
	function estadoExpressao($expr,$id){
		global $expressao, $msg,$notacaoCientifica;
		$expr= trim($expr);
		$msg =$msg."<hr><br> [expression] -> [expression] + [term] or [expression] - [term] or [term]<br>Recebi: $expr<br>";
		$buscaSinal = "\+|-";
		//[expression] -> [expression] / -[expression] | [expression] *-[expression]
//i_Leak=1e1+2.2e-2*3.3e3-4.444e-4+5.5e+5

		$msg =$msg."<br>$expr<br>";

		$buscaNotacaoCient= "^($expressao+)($buscaSinal)($notacaoCientifica)$";
 		eregi($buscaNotacaoCient, $expr, $matchesNotacaoCient);

		$buscaNotacaoCient2= "^($notacaoCientifica)($buscaSinal)($expressao+)$";
 		eregi($buscaNotacaoCient2, $expr, $matchesNotacaoCient2);
		
		$busca= "^($expressao+)([\*|\/|\^])($buscaSinal)([A-Za-z0-9\/\.\*\^\, _]+)$";
		eregi($busca, $expr, $matches);
		
		//[expression] -> [expression] + [term] | [expression] - [term]
		$busca1= "^($expressao+)($buscaSinal)([A-Za-z0-9\/\.\*\^\, _]+)$";
		eregi($busca1, $expr, $matches1);
		$confereBusca1 = $this->confereSinalExpressao($matches1);

		//[expression] -> ([expression])
		$conferePar = $this->confereParenteses($expr);
	
		//[expression] -> [expression] + [term] | [expression] - [term]
		$busca2= "^($expressao+)($buscaSinal)($expressao+)$";
		eregi($busca2, $expr, $matches2);
		$exprPar = $this->processaParenteses($expr, array("+","-"));
		$arrayAux[0]=null;
		$arrayAux[1]=$exprPar[0];
		$arrayAux[2]=$exprPar[1];
		$arrayAux[3]=$exprPar[2];
		$confereBusca2 = $this->confereSinalExpressao($arrayAux);

		if(is_numeric(trim($expr))){
		
			$this->estadoFator($expr,$id);
		}elseif(is_array($matchesNotacaoCient)){
			$operador = $this->getOperador($matchesNotacaoCient[2]);
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode($operador, $id,null);
			$this->estadoExpressao($matchesNotacaoCient[1],$id);
			$this->estadoTermo($matchesNotacaoCient[3], $id);

		}elseif(is_array($matchesNotacaoCient2)){

			$operador = $this->getOperador($matchesNotacaoCient2[2]);
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode($operador, $id,null);
			$this->estadoTermo($matchesNotacaoCient2[1], $id);
			$this->estadoExpressao($matchesNotacaoCient2[3],$id);

		}elseif(is_array($matches)){
			$msg=$msg."<br>[expression] -> [expression][*\^] [+|-][term]<br>";
			$this->estadoTermo($matches[0],$id);
		}elseif((is_array($matches1)) && !($confereBusca1) ){
			$msg=$msg."<br>[expression] -> [expression]+-[term]<br>";
			$operador = $this->getOperador($matches1[2]);
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode($operador, $id,null);
			$this->estadoExpressao($matches1[1],$id);
			$this->estadoTermo($matches1[3],$id);

		}elseif($conferePar){
			$msg=$msg."<br>[expression] -> ([expression])";
			$this->estadoTermo($expr,$id);
		
		}elseif((is_array($matches2)) && (is_array($exprPar)) && !($confereBusca2) ){
			$msg=$msg."<br>[expression] -> ([expression])+([expression])";
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode($exprPar[1], $id,null);
			$this->estadoExpressao($exprPar[0],$id);
			$this->estadoExpressao($exprPar[2],$id);
		
		}else{
			//echo ("passando: $expr<br>");
			$this->estadoTermo($expr,$id);
			
		}
	}

	

	/*
	*[term] -> [term] * [factor] | [term] / [factor] | [factor]
	*
	*/
	function estadoTermo($expr,$id){
		global $expressao, $msg;
		$expr= trim($expr);
		//$msg = $msg."<hr><br> [expression] -> [expression] * [term] or [expression] / [term] or [term]<br>Recebi:$expr";
		//var_dump($expr);
		//echo("<br>");
		$buscaSinal = "\*|\/";
		//[expression] -> [expression] + [term] | [expression] - [term]
		$busca1= "^($expressao+)($buscaSinal)([A-Za-z0-9\.\^\, _]+)$";
		eregi($busca1, $expr, $matches1);

		//[expression] -> ([expression])
		$conferePar = $this->confereParenteses($expr);
	
		//[expression] -> [expression] + [term] | [expression] - [term]
		$busca2= "^($expressao+)($buscaSinal)($expressao+)$";
		eregi($busca2, $expr, $matches2);
		$exprPar = $this->processaParenteses($expr, array("*","/"));
		//$msg=$msg."<br>[Term] ".$expr;	

		if(is_numeric(trim($expr))){
			//$msg=$msg." ".$expr;	
			$this->estadoFator($expr,$id);
		}elseif(is_array($matches1)){
			//echo "[expression] -> [expression]+-[term]<br>";
			$operador = $this->getOperador($matches1[2]);
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode($operador, $id,null);
			$this->estadoExpressao($matches1[1],$id);
			$this->estadoFator($matches1[3],$id);

		}elseif($conferePar){
			//echo "[expression] -> ([expression])";	
			$this->estadoFator($expr,$id);
		
		}elseif((is_array($matches2)) && (is_array($exprPar))){
			//echo "[expression] -> ([expression])";	
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode($exprPar[1], $id,null);
			$this->estadoExpressao($exprPar[0],$id);
			$this->estadoExpressao($exprPar[2],$id);
		
		}else{
			//echo ("passando: $expr<br>");
			$this->estadoFator($expr,$id);
			
		}
	}


	/*	
	*[factor] -> variable | number | ( [expression] )
	*[factor] -> function ( [expression] ) | function ( [expression], [expression] )
	*[factor] -> - [expression]
	*/
	function estadoFator($expr,$id){
		global $expressao,$equation,$erro, $msg;
		//$msg = $msg."<hr><br> [factor]<br>Recebi: $expr<br>";
		$expr= trim($expr);
		//[factor] -> number
		$busca1 = is_numeric(trim($expr));
		
		//[factor] -> variable
		$busca2= "^([a-zA-Z0-9_ ]+)$";
		eregi($busca2, $expr, $matches2);
		
		//[factor] -> ( [expression] )
		$busca3= "^(\($equation+\))$";
		eregi($busca3, $expr, $matches3);
		$conferePar = $this->confereParenteses($expr);
		
		//[factor] -> function ( [expression], [expression] )
		$busca4= "^([a-zA-Z_ ]+)(\()($expressao+)(\,)($expressao+)(\))$";
		eregi($busca4, $expr, $matches4);
		$parametroFuncao = $matches4[3].$matches4[4].$matches4[5];
		$exprParamFunc = $this->processaParenteses($parametroFuncao, array(",", ","));


		//[factor] -> function ( [expression] )
		$busca5 = "^([a-zA-Z_ ]+)(\($expressao+\))$";
		eregi($busca5, $expr, $matches5);

		//[factor] -> [expression]^[var]
		$busca6= "^($expressao+)(\^)([0-9A-Za-z ]+)$";
		eregi($busca6, $expr, $matches6);
		
		//[factor] -> [expression]^[expression]
		$busca9= "^($expressao+)(\^)($expressao+)$";
		eregi($busca9, $expr, $matches9);
		$exprPowPar = $this->processaParenteses($expr, array("^", "^"));
		
		//[factor] -> - [expression]
		$busca7= "^(-)(\($expressao+\))$";
		eregi($busca7, $expr, $matches7);


		$buscaSinal = "<=|>=|!=|<>|=|<|>";
		$busca8= "^([a-zA-Z0-9_ ]+)($buscaSinal)(SELECT\{)";
		$caseCount = substr_count(strtoupper($expr), "CASE");
		for($i=0; $i<$caseCount; $i++){
			$busca8 = $busca8 . "($expressao+)(CASE:)($equation+);";
		}
		$otherwiseCount = substr_count(strtoupper($expr), "OTHERWISE");
		for($i=0; $i<$otherwiseCount; $i++){
			$busca8 = $busca8 . "(OTHERWISE)($expressao+);";
		}
	
		//$busca8= "^([a-zA-Z0-9_ ]+)(\=)(SELECT\{)($expressao+)(CASE:)($equation+)(OTHERWISE)($expressao)(\})$";
		$busca8 = $busca8 . "(\})$";
		eregi($busca8, $expr, $matches8);
		//var_dump($matches8);
		
		if($busca1){//[factor]->number
			//$msg = $msg. "[number]<br>";
			$id = $this->insereNode("cn", $id, $expr);
						
		}elseif(is_array($matches2)){//[factor]->variable
			//$msg = $msg."[var]<br>";
			//var_dump($matches2);
			$id = $this->insereNode("ci", $id, $expr);//variable
			
		}elseif(is_array($matches3)&&($conferePar)){//[factor] -> ( [expression] )
			$tamanho =  strlen($matches3[1])-2;
			$expressaoSemParenteses = substr($matches3[1],1,$tamanho);// ([expressao])
			//$msg = $msg."<br>--------->>$expressaoSemParenteses<<----------------<br>";
			$this->estadoExpressao($expressaoSemParenteses,$id);//[term]	

		}elseif(is_array($matches4) && is_array($exprParamFunc)){//[factor] -> function ( [expression], [expression] )
			$msg = $msg. "<br>[factor] -> function ( [expression], [expression] )";
			if(($matches4[1]!="ODE")&&($matches4[1]!="ode")){
				$id = $this->insereNode("apply", $id,null);
				$this->insereNode($matches4[1],$id,null);//[term]
				$this->estadoExpressao($exprParamFunc[0],$id);//[term1]
				$this->estadoExpressao($exprParamFunc[2],$id);//[term2]
			}else{
				//colocar bvar
				$id = $this->insereNode("apply", $id,null);
				$this->insereNode("diff", $id,null);
				$this->estadoFator($exprParamFunc[0],$id);//[term1]
				$id = $this->insereNode("bvar", $id,null);
				$this->estadoFator($exprParamFunc[2],$id);//[term2]
			}

		}elseif(is_array($matches5)){//[factor] -> function ( [expression] )
			//$msg = $msg."[factor] -> function ( [expression] )<br>";
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode($matches5[1],$id,null);//[term]
			$this->estadoFator($matches5[2],$id);//[term]

		}elseif(is_array($matches6)){
			$msg = $msg."<br>[expr]^[var]";
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode('power', $id,null);
			$this->estadoExpressao($matches6[1],$id);//[term]
			$this->estadoExpressao($matches6[3],$id);//[term]
		}elseif(is_array($matches9) && is_array($exprPowPar) ){//[factor] -> [expressao] ^ [expressao]
			//echo "[expression] -> ([expression])";	
			$msg = $msg."<br>[expr]^[expr]";
			$msg = $msg."<br>".$exprPowPar[0]."^".$exprPowPar[2];
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode('power', $id,null);
			$this->estadoExpressao($exprPowPar[0],$id);//[term]
			$this->estadoExpressao($exprPowPar[2],$id);//[term]

		}elseif(is_array($matches7)){//[factor] ->-[expressao]
			$msg = $msg. "<br>>>>>-[expressao]<br>";
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode('minus', $id,null);
			//var_dump($matches7);
			$this->estadoExpressao($matches7[2],$id);//[term]

		}elseif(is_array($matches8)){//[factor] = piecewise
			//$msg = $msg."[factor] = piecewise";
			//$id = $this->insereNode("apply", $id,null);
			$this->insereNode($this->getOperador($matches8[2]), $id,null);
			$this->estadoFator($matches8[1], $id);
			$id = $this->insereNode("piecewise", $id,null);
			for($i=0; $i<$caseCount; $i++){
				$idPiece = $this->insereNode("piece", $id,null);
				$this->estadoExpressao($matches8[4+$i*3],$idPiece);
				$msg = $msg ."<br>####passando estadoOperadorLogico".$matches8[6+$i*3];
				$this->estadoOperadorLogico($matches8[6+$i*3],$idPiece);
			}
			for($i=0; $i<$otherwiseCount; $i++){
				$idOtherwise = $this->insereNode("otherwise", $id,null);
				$this->estadoExpressao($matches8[5+$caseCount*3+$i*3],$idOtherwise);
			}

		}else{
			
			if($code!=null){
				$erro=true;
				$msg=$msg."Error";
			}
		}
	}
	/*
	*[term] -> [expression]&&[expression]|[expression]||[expression]
	*
	*/
	function estadoOperadorLogico($expr,$id){
		global $expressao,$equation, $msg;
		$expr= trim($expr);
			
		$buscaSinal = "&|\|";
		//[equation] | [equation] | [equation] & [equation]
		$busca1= "^($equation+)($buscaSinal)($equation+)$";
		eregi($busca1, $expr, $matches1);
		//[expression] -> ([expression])
		$conferePar = $this->confereParenteses($expr);
		$exprPar = $this->processaParenteses($expr, array("&","|"));
		//[factor] -> ( [expression] )
		
		$busca2= "^(\($equation+\))$";
		eregi($busca2, $expr, $matches2);
		$conferePar = $this->confereParenteses($expr);
		
		if(is_array($matches2)&&$conferePar){
			//echo "[expression] -> ([expression])";	
			$tamanho =  strlen($matches2[1])-2;
			$expressaoSemParenteses = substr($matches2[1],1,$tamanho);// ([expressao])
			//retirando parenteses excedentes
			$msg=$msg."<br>[OPERADOR LOGICO] retira parenteses: ".$expressaoSemParenteses;
			$this->estadoOperadorLogico($expressaoSemParenteses,$id);//[term]	
		
		}elseif(is_array($matches1) &&is_array($exprPar) ){
			$msg=$msg."<br>[OPERADOR LOGICO] aceito: ".$expr;
			$id = $this->insereNode("apply", $id,null);
			$this->insereNode($exprPar[1], $id,null);
			$this->estadoOperadorLogico($exprPar[0],$id);
			$this->estadoOperadorLogico($exprPar[2],$id);
		
		}else{
			//echo ("passando: $expr<br>");
			$msg=$msg."<br>[OPERADOR LOGICO] só repassando: ".$expr;
			$this->estadoEquacao($expr,$id);
			
		}
	}
	
}



?>
