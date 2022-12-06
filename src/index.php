<?php 
ini_set("memory_limit","24M");
session_start();
?>
<html>
<link type="text/css" rel="stylesheet" href="../css/estiloparafisio.css" />
<head>
<title>Editor</title>

</head>
<body >
<center>
<div class="titulo"><h2 align="center">Open Project</h2></div>

<table aling="center" border='0' cellspacing="3">
<form action='editor.php' method='get'>
<tr>
<?
	include('config.php');
	if(isset($HTTP_GET_VARS['iu'])){
		$codpesquisador = $HTTP_GET_VARS['iu'];
		$_SESSION['iu'] = $codpesquisador;
	}elseif(isset($_SESSION['iu'])){
		$codpesquisador = $_SESSION['iu'];
	}
    echo  "<input type='hidden' name='coduser' value='".$HTTP_GET_VARS['iu']."'>";
    
	//$_SESSION['iduser'] = $codpesquisador;
	$_SESSION['iduser'] = rand();
	$str = "SELECT codigoprojeto,nome FROM Projetos WHERE codigopesquisador=$codpesquisador ORDER BY nome";
	$busca_projetos = mysql_query($str);
	$i=0;
	echo "<td><div class='titulosetup'>\n";
	echo "<input type='radio' name='projeto' value='1410'>New project<br>";
	echo "</div></td>\n";
	$i++;
    if($busca_projetos){
        while($array_proj = mysql_fetch_array($busca_projetos)){
	        echo "<td><div class='titulosetup'>\n";
	        echo "<input type='radio' name='projeto' value='".$array_proj['codigoprojeto']."'>".$array_proj['nome']." <br>";
	        echo "</div></td>\n";
	        $i++;
	        if(($i % 2)==0){
		        echo "</tr> <tr>\n";
	        }
        }
        echo "<tr><td colspan='2' align='center'><input type=\"submit\" value=\"Submit\"></td></tr>";
    }else{
            echo "<td><div class='titulosetup'>\n"; 
            echo "Nenhum projeto encontrado.<br>No projects found.<br>";
            echo "</div></td>\n";
    }
	echo "</tr>";

?>


</tr></td>

</form>
</table>

</center>

</body></html>

