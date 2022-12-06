<?php
session_start();
	
/*
camada intermediaria
recebe requisicao do do formulario e instancia classes para montar a arvore
*/
	require_once('mount_tree.php');
	
	$codprojeto = $HTTP_GET_VARS['codprojeto'];
	if($codprojeto=='-1'){
	        $arquivo;
		if($_GET["sentfile"]=="-1")  $arquivo = "../files/newModel.xml";
        elseif($_GET["sentfile"]=="1")	$arquivo = $_SESSION['sentfile'];
        $tree = new mount_tree($arquivo, "New model" , $codprojeto, $_GET["tipo"], $_GET['iduser']);
        $tags = explode(",", $_GET["param"]);
        if(is_Array($tags) ){
            foreach($tags as $tag){
                $tree->listTagXml(trim($tag), false);
            }	
            print($tree->getRoot());
        }else{
            print("-1");
        }

	}else{
		$connection = mysql_connect("localhost", "root", "parafisio");
		mysql_select_db("parafisioantigo",$connection);
		$str = "select nome, caminho from Arquivos where codigoprojeto=$codprojeto and tipoarquivo='xml'";
		$busca_arquivo = mysql_query($str);
		if($busca_arquivo){
			$array_arquivo = mysql_fetch_array($busca_arquivo);
			$arquivo = "/home/project/".$array_arquivo["caminho"]."/".$array_arquivo["nome"];
			mysql_close($connection);
			$tree = new mount_tree($arquivo, $array_arquivo["nome"], $codprojeto, $_GET["tipo"],$_GET['iduser']);
			$tags = explode(",", $_GET["param"]);
			if(is_Array($tags) ){
				foreach($tags as $tag){
					$tree->listTagXml(trim($tag), false);
				}	
				print($tree->getRoot());
			}else{
				print("-1");
			}
		}	
	}
?>
