<?php

class Def{
	public static $TOKEN_ERROR;
	public static $TOKEN_DIV;
	public static $TOKEN_PLUS;
	public static $TOKEN_MINUS;
	public static $TOKEN_TIMES;
	public static $TOKEN_LPAR;
	public static $TOKEN_RPAR;
	public static $TOKEN_LBRA;
	public static $TOKEN_RBRA;
	public static $TOKEN_COMMA;
	public static $TOKEN_SEMICOLON;
	public static $TOKEN_REST ;
	public static $TOKEN_POWER;
	public static $TOKEN_ASSIGN;
	public static $TOKEN_EQUAL;
	public static $TOKEN_GREATER;
	public static $TOKEN_GREATEREQUAL;
	public static $TOKEN_LESS;
	public static $TOKEN_LESSEQUAL;
	public static $TOKEN_DIFF;
	public static $TOKEN_NEGATION;
	public static $TOKEN_OR;
	public static $TOKEN_AND;
	public static $TOKEN_ID;
	public static $TOKEN_EOF;
	public static $TOKEN_NUMBER;
	public static $TOKEN_COLON;
	public static $TOKEN_NAN;
	public static $TOKEN_INF;
	
	
	function __construct(){
		$this->TOKEN_ERROR =-1;
		$this->TOKEN_DIV=0;
		$this->TOKEN_PLUS=1;
		$this->TOKEN_MINUS=2;
		$this->TOKEN_TIMES=3;
		$this->TOKEN_LPAR=4;
		$this->TOKEN_RPAR=5;
		$this->TOKEN_LBRA=6;
		$this->TOKEN_RBRA=7;
		$this->TOKEN_COMMA=8;
		$this->TOKEN_SEMICOLON=9;
		$this->TOKEN_REST =10;
		$this->TOKEN_POWER=11;
		$this->TOKEN_ASSIGN=12;
		$this->TOKEN_EQUAL=13;
		$this->TOKEN_GREATER=14;
		$this->TOKEN_GREATEREQUAL=15;
		$this->TOKEN_LESS=16;
		$this->TOKEN_LESSEQUAL=17;
		$this->TOKEN_DIFF=18;
		$this->TOKEN_NEGATION=19;
		$this->TOKEN_OR=20;
		$this->TOKEN_AND=21;
		$this->TOKEN_ID=22;
		$this->TOKEN_EOF=23;
		$this->TOKEN_NUMBER=24;
		$this->TOKEN_PI=25;
		$this->TOKEN_E=26;
		$this->TOKEN_SELECT=27;
		$this->TOKEN_CASE=28;
		$this->TOKEN_OTHERWISE=29;
		$this->TOKEN_TRUE=30;
		$this->TOKEN_FALSE=31;
		$this->TOKEN_COLON=32;
		$this->TOKEN_NAN=33;
		$this->TOKEN_INF=34;
	}
	
	function printChar($code){
		switch($code){
			case $this->TOKEN_ERROR:	return -1;
			case $this->TOKEN_DIV:		return "/";
			case $this->TOKEN_PLUS:		return "+";
			case $this->TOKEN_MINUS:	return "-";
			case $this->TOKEN_TIMES: 	return "*";
			case $this->TOKEN_LPAR: 	return "(";
			case $this->TOKEN_RPAR: 	return ")";
			case $this->TOKEN_LBRA: 	return "{";
			case $this->TOKEN_RBRA: 	return "}";
			case $this->TOKEN_COMMA: 	return ",";
			case $this->TOKEN_SEMICOLON:return ";";
			case $this->TOKEN_REST: 	return "%";
			case $this->TOKEN_POWER: 	return "^";
			case $this->TOKEN_ASSIGN: 	return "=";
			case $this->TOKEN_EQUAL: 	return "==";
			case $this->TOKEN_GREATER: 	return ">";
			case $this->TOKEN_GREATEREQUAL:return ">=";
			case $this->TOKEN_LESS: 	return "<";
			case $this->TOKEN_LESSEQUAL:return "<=";
			case $this->TOKEN_DIFF: 	return "!=";
			case $this->TOKEN_NEGATION: return "!";
			case $this->TOKEN_OR:		return "||";
			case $this->TOKEN_AND:		return "&&";
			case $this->TOKEN_ID:		return "variable";
			case $this->TOKEN_EOF:		return "EOF";
			case $this->TOKEN_NUMBER: 	return "number";
			case $this->TOKEN_PI: 		return "pi";
			case $this->TOKEN_E: 		return "e";
			case $this->TOKEN_SELECT: 	return "SELECT";
			case $this->TOKEN_CASE: 	return "CASE";
			case $this->TOKEN_OTHERWISE:return "OTHERWISE";
			case $this->TOKEN_TRUE: 	return "true";
			case $this->TOKEN_FALSE: 	return "false";
			case $this->TOKEN_COLON: 	return ":";
			case $this->TOKEN_NAN:		return "notanumber";
			case $this->TOKEN_INF: 	return "infinity";
		}
	}
	
	function getTag($code){
        
		switch($code){
			case $this->TOKEN_DIV:		return "divide";
			case $this->TOKEN_PLUS:		return "plus";
			case $this->TOKEN_MINUS:	return "minus";
			case $this->TOKEN_TIMES: 	return "times";
			case $this->TOKEN_REST: 	return "module";
			case $this->TOKEN_POWER: 	return "power";
			case $this->TOKEN_ASSIGN: 	return "eq";
			case $this->TOKEN_EQUAL: 	return "eq";
			case $this->TOKEN_GREATER: 	return "gt";
			case $this->TOKEN_GREATEREQUAL:return "geq";
			case $this->TOKEN_LESS: 	return "lt";
			case $this->TOKEN_LESSEQUAL:return "leq";
			case $this->TOKEN_DIFF: 	return "neq";
			case $this->TOKEN_NEGATION: return "not";
			case $this->TOKEN_OR:		return "or";
			case $this->TOKEN_AND:		return "and";
			case $this->TOKEN_ID:		return "ci";
			case $this->TOKEN_NUMBER: 	return "cn";
			case $this->TOKEN_PI: 		return "pi";
			case $this->TOKEN_E: 		return "exponentiale";
			case $this->TOKEN_SELECT: 	return "piecewise";
			case $this->TOKEN_CASE: 	return "piece";
			case $this->TOKEN_OTHERWISE:return "otherwise";
			case $this->TOKEN_TRUE: 	return "true";
			case $this->TOKEN_FALSE: 	return "false";
			case $this->TOKEN_NAN: 	return "notanumber";
			case $this->TOKEN_INF: 	return "infinity";
		}
	}
}

?>