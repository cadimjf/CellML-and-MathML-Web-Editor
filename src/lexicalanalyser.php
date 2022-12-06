<?php
include_once("symboltable.php");
include_once("def.php");

/**
* Lexical analyser
*/
class LexicalAnalyser
{
	private $equation;//a string stores the math expression
	private $count;//it counts read chars.
	private $def;
	private $buffer;
	private $numLines;
	/*
	* Construtor
	*/
	function __construct($eq){
		$this->buffer="";
		$this->equation = $eq."\0";
		$this->count=0;
		$this->def = new Def();
// 		echo ($this->equation."<br>");
		$this->numLines=0;
		
		
	}
	function getLexema(){
		return $this->buffer;
	}
	
	function teste(){
		while($this->count<strlen($eq)){
			//echo ($this->count."<br>");
			$token  = $this->getNextToken();
			echo ("token: $token\t$this->buffer<br>");
		}
	}
	/*
	* This function returns the next token
	*/
	function getNextToken(){
		$state='0'; //stores the states' number
		$this->buffer="";
		
		while(true){//infinite loop, it breaks when it finds a complete token
			$char = $this->equation[$this->count];// stores the char at count position in equation			
// 			echo ("state: $state\t$char\t".$this->count."<br> ");
			switch($state){//according to the state
				case '0':
					switch($char){//according to char
						case '/'://division state
							$this->count++;
							return $this->def->TOKEN_DIV;
						case '+'://plus
							$this->count++;
							return $this->def->TOKEN_PLUS;
						case '-'://minus
							$this->count++;
							return $this->def->TOKEN_MINUS;
						case '*'://times
							$this->count++;
							return $this->def->TOKEN_TIMES;
						case '(':
							$this->count++;
							return $this->def->TOKEN_LPAR;
						case ')':
							$this->count++;
							return $this->def->TOKEN_RPAR;
						case '{':
							$this->count++;
							return $this->def->TOKEN_LBRA;
						case '}':
							$this->count++;
							return $this->def->TOKEN_RBRA;
						case ',':
							$this->count++;
							return $this->def->TOKEN_COMMA;
						case ';':
							$this->count++;
							return $this->def->TOKEN_SEMICOLON;
						case '=':
							$this->count++;
							$state="equalsign";
							break;
						case " "://space-it doesn't do anything, only count
							$state="0";
							$this->count++;
							break;
						
						case "\n"://line break-it doesn't do anything, only count
							$this->numLines++;
						case "\t"://line break-it doesn't do anything, only count
							$this->count++;
							$state="0";
							break;
						case '%':
							$this->count++;
							return $this->def->TOKEN_REST;
						case '^':
							$this->count++;
							return $this->def->TOKEN_POWER;
						case '>':
							$this->count++;
							$state="greatersign";
							break;
						case '<':
							$this->count++;
							$state="lesssign";
							break;
						case '!':
							$this->count++;
							$state="exclamation";
							break;
						case '&':
							$this->count++;
							$state="andsign";
							break;
						case '|':
							$this->count++;
							$state="orsign";
							break;
						case '.':
							$this->count++;
							$this->buffer=$char;
							$state="point";
							break;
						case is_numeric($char):
						case '0'://PHP doensn't recognize 0 as number, but recongnize as string. 						
							$this->count++;
							$this->buffer=$char;
							$state="intnumber";
							break;
						case (($char>='a') &&($char<='z')):
						case (($char>='A') &&($char<='Z')):
						case '_':
							$this->count++;
							$this->buffer=$char;
							$state="letter";
							break;
						case "\0":
							return $this->def->TOKEN_EOF;
						case ':':
							$this->count++;
							return $this->def->TOKEN_COLON;
						default:
							//echo "trem<br>";
							$this->count++;
							return $this->def->TOKEN_ERROR;
							break;		
					}//end swicth
							
					break;//end zero state
					
				case "equalsign":
					if($char=='='){
						$this->count++;
						return $this->def->TOKEN_EQUAL;
					}else //another char -it found a assignment
						return $this->def->TOKEN_ASSIGN;	//it doesn't count
				
				case "greatersign":
					if($char=='='){
						$this->count++;
						return $this->def->TOKEN_GREATEREQUAL;
					}else //another char -it found a assignment
						return $this->def->TOKEN_GREATER;
					
				case "lesssign":
					if($char=='='){
						$this->count++;
						return $this->def->TOKEN_LESSEQUAL;
					}else //another char -it found a assignment
						return $this->def->TOKEN_LESS;
					
				case "exclamation":
					if($char=='='){
						$this->count++;
						return $this->def->TOKEN_DIFF;
					}else //another char -it found a assignment
						return $this->def->TOKEN_NEGATION;
				case "orsign":
					if($char=='|'){
						$this->count++;
						return $this->def->TOKEN_OR;
					}else //another char -it found a assignment
						return $this->def->TOKEN_ERROR;
				case "andsign":
					if($char=='&'){
						$this->count++;
						return $this->def->TOKEN_AND;
					}else //another char -it found a assignment
						return $this->def->TOKEN_ERROR;
				case "letter":
					
					switch($char){
						case (($char>='a') &&($char<='z')):
						case (($char>='A') &&($char<='Z')):
 						case is_numeric($char):
						case '_':
// 							echo "estou em letter com $char<br>";
							$this->count++;
							$state="letter";
							$this->buffer=$this->buffer.$char;
							break;
						default://another
// 							echo "saindo  letter com $char<br>";
							$symbolTable = new SymbolTable();
							return $token= $symbolTable->add(new Item($this->def->TOKEN_ID,$this->buffer));
					}
					break;
				case 'intnumber':
					if(is_numeric($char)){
						$this->count++;
						$state="intnumber";
						$this->buffer=$this->buffer.$char;
					}else if($char=="."){
						$this->count++;
						$state="decimal";
						$this->buffer=$this->buffer.$char;
					}else if(($char=="e")||($char=="E")){
						$this->count++;
						$state="e";
						$this->buffer=$this->buffer.$char;
					}else{//another
						return $this->def->TOKEN_NUMBER;
					}
					break;
				
				case "decimal":
					if(is_numeric($char)){
						$this->count++;
						$state="decimal";
						$this->buffer=$this->buffer.$char;
					}else if(($char=="e")||($char=="E")){
						$this->count++;
						$state="e";
						$this->buffer=$this->buffer.$char;
					}else{//another
						return $this->def->TOKEN_NUMBER;
					}
					break;
				
				case "e":
					if(($char=="e")||($char=="E")||(is_numeric($char))||($char=="+")||($char=="-")){
						$this->count++;
						$state="number";
						$this->buffer=$this->buffer.$char;
					}else{//another
						return $this->def->TOKEN_ERROR;
					}
					break;
				case "number":
					if(is_numeric($char)){
						$this->count++;
						$state="number";
						$this->buffer=$this->buffer.$char;
					}else {//another
						return $this->def->TOKEN_NUMBER;
					}
					break;
				case "point":
					if(is_numeric($char)){
						$this->count++;
						$state="decimal";
						$this->buffer=$this->buffer.$char;
					}else {//another
						return $this->def->TOKEN_ERROR;
					}
					break;
				default://unknown state
					return -3;
					break; 
			}//end switch
		if($this->count>strlen($this->equation)) return -2;
		
		}//end while
	}//end getNextChar()

	
	
	
}



?>
