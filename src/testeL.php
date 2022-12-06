<?php
session_start();
include_once("sintaticanalyser.php");

// 	$eq ="a=a||a||a";
	
// 	$eq="a=r&&rd&&fdh&&hg||td||sfg||fsdds||fsd||fd";

	$eq = "Istim=SELECT{
a CASE:a||a<=a;
OTHE nRWISE(A);
}";
	$eq = "a=A||a";
	$sint = new SintaticAnalyser(0, 0, $eq,$name,0);
	//

?>
