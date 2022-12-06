<?

//+*-+
$notacaoCientifica = "[-|\+]?[0-9]+[\.]?[0-9]*[e][-|\+]?[0-9]+";
$expr2= "-5000000e-5";
eregi("^($notacaoCientifica)$", $expr2, $matches);
var_dump($matches);
echo("<hr>");
var_dump(is_numeric($expr2));
echo("<br>".floatval($expr2));
// $buscaSinal = "\+|-";
// $expressao = "[A-Za-z0-9\/\.\+\*\^\(\)\, _-]";
// $buscaNotacaoCient= "^$notacaoCientifica($buscaSinal)([A-Za-z0-9\/\.\*\^\, _]+)$";
// eregi($buscaNotacaoCient, $expr, $matchesNotacaoCient);
// var_dump($matchesNotacaoCient)
/*
Include("node.php");
$node = new node();
$attr = array();
$attr["idnode"] = '525';
$attr["idparent"] = '0';
$attr["item_order"] = '0';
$attr["idprojeto"] = '702';
$attr["name"] = 'UP';
*/
?>