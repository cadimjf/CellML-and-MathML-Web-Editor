<?	session_start();
	session_cache_expire(0);
	echo ("<html>");
	$ramdom = rand();
	unset($_SESSION['iduser']);
	$_SESSION['id'] = $HTTP_GET_VARS['coduser'];
	$_SESSION['iduser'] = $ramdom;
	require_once("Node.php");
	$arquivo;
    $sentfilecode="-1";
	$codprojeto = $HTTP_GET_VARS['projeto'];
	
    if($codprojeto!='-1'){//projeto a ser editado. Não é um projeto novo
		$connection = mysql_connect("localhost", "root", "parafisio");
		mysql_select_db("parafisioantigo",$connection);
		$str = "select nome, caminho from Arquivos where codigoprojeto=$codprojeto and tipoarquivo='xml'";
		$busca_arquivo = mysql_query($str);
		if($busca_arquivo){
			$array_arquivo = mysql_fetch_array($busca_arquivo);
			$arquivo = "/home/project/".$array_arquivo["caminho"]."/".$array_arquivo["nome"];
		}
		$str = "select nome from Projetos where codigoprojeto=$codprojeto";
		$busca_projeto = mysql_query($str);
		if($busca_projeto){
			$array_projeto = mysql_fetch_array($busca_projeto);
			echo "<title>Editor - ".$array_projeto['nome']."</title>";
			
		}else{
			echo "<title>Editor</title>";
		}
		mysql_close($connection);
	}else{	//new Project
		if($_GET['newfile']=='new'){//opening a new project
            $arquivo = "../files/newModel.xml";
			echo "<title>Editor - New Project</title>";
            
        }elseif($_GET['newfile']=='submit'){//opening a sent file
            $arquivo = $_GET['sentfile'];
            $sentfilecode = "1";
            $_SESSION['sentfile'] = $arquivo;
			echo "<title>Editor - ".$_GET['sentfile']."</title>";
        }
        
	}
	
?>

<style>
.xmlview {
	/*font-family: Verdana,"Bitstream Vera Sans",Arial;
	font-size: 11px;
	font-weight: normal;
	color: #000033;
	background-color: #FFFFFF;
	border-right: 1px solid #000000;
	border-left: 1px solid #000000;
	border-top: 1px solid #000000;
	border-bottom: 1px solid #000000;
	padding: 10px;
	cursor: hand;*/
}

body, table {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #000000;
}

.statusbar {
	font-family: Verdana,"Bitstream Vera Sans",Arial;
	font-size: 9px;
	font-weight: normal;
	color: #000033;
	background-color: #F3F3F3;
	border-right: 1px solid #000000;
	border-left: 1px solid #000000;
	border-bottom: 1px solid #000000;
	
}
.maintable{
	border-left: 1px solid #000;
	/*border-right: 1px solid #000;*/
	
}

.menu {
/* 	font-family: Verdana, Arial, Helvetica, sans-serif; */
	font-family: Verdana,"Bitstream Vera Sans",Arial;
	font-size: 10px;
	font-weight: normal;
	color: #000033;
	background-color: #FFFFFF;
	border-right: 1px solid #000000;
	border-top: 1px solid #000000;
	border-bottom: 1px solid #000000;
	padding: 5px;
	cursor: hand;
}

.menu-sel {
/* 	font-family: Verdana, Arial, Helvetica, sans-serif; */
	font-family: Verdana,"Bitstream Vera Sans",Arial;
	font-size: 10px;
	font-weight: bold;
	color: #000033;
/* 	background-color: #CCCCCC; */
	background-color: #F3F3F3;
	border-right: 1px solid #000000;
	border-top: 1px solid #000000;
/*	border-left: 1px solid #000000;*/

	padding: 5px;
	cursor: hand;
}

.tb-conteudo {
	border-right: 1px solid #000000;
	border-bottom: 1px solid #000000;
}
        
.conteudo {
/* 	font-family: Verdana, Arial, Helvetica, sans-serif; */
	font-family: Verdana,"Bitstream Vera Sans",Arial;
	font-size: 10px;
	font-weight: normal;
	color: #000033;
/* 	background-color: #CCCCCC; */
	background-color: #F3F3F3;
	padding: 5px;
	
/*  	width: 560px;  */
	width: 99%;
/* 	height: 460px; */
}
.titulo{
	font-size:11pt;
	color: #CC4E00;
	text-align: center;
	font-weight: bold;
	font-family: Verdana,"Bitstream Vera Sans",Arial;
}
.titulosetup{
	font-size:10pt;
	color: #707070;
	font-weight: bold;
	font-family: Verdana,"Bitstream Vera Sans",Arial;
}


.itemoption {
	font-family: Verdana,"Bitstream Vera Sans",Arial;
	font-size: 10px;
	font-weight: normal;
	color: #000033;
	background-color: #F3F3F3;
	
	width: 500px;
	padding: 10px;
	
	border-right: thin outset #A0A0A0;
	border-left: thin outset #A0A0A0;
	border-bottom: thin outset #A0A0A0;
	border-top: thin outset #A0A0A0;
}



a:link {
	color: #333;
	text-decoration: none; 
	background-color: transparent;
	}

a:visited {
	color: #333;
	text-decoration: none; 
	background-color: transparent;
	}

 a:hover {
	color: #333;
	text-decoration: underline; 
	background-color: transparent;
	}

 a:active {
	color: #333;
	text-decoration: underline; 
	background-color: transparent;
 }
/*body {font-size:12px}
.{font-family:arial;font-size:12px}*/
h1 {
	cursor:hand;
	font-size:16px;
	margin-left:10px;
	line-height:10px
}

#details span{
	font-size:10px;
	font-family:arial;
	display:block;
}
#details input,#details textarea{
	border: 1px solid gray;
}
#actions a{
	font-size:12px;
	font-family:arial;
	margin:5px;
	text-decoration:none;
}
#actions {
/* 	border-bottom:1px solid silver; */
	padding:2px;
	margin-bottom:10px;
	margin-top:10px;
}
#actions a:hover{
	text-decoration:underline; 
	color: #4E74A6; 
	font-size:12px;
	font-family:arial;
	margin:5px;
}
</style>
<script language="JavaScript">

	function stAba(menu,conteudo)
	{
		this.menu = menu;
		this.conteudo = conteudo;
	}

	var arAbas = new Array();
	arAbas[0] = new stAba("td_units","div_units");
	arAbas[1] = new stAba("td_group","div_group");
	arAbas[2] = new stAba("td_import","div_import");
	arAbas[3] = new stAba("td_xml","div_xml");
	
	function AlternarAbas(menu,conteudo)
	{
		for (i=0;i<arAbas.length;i++)
		{
			m = document.getElementById(arAbas[i].menu);
			m.className = 'menu';
			c = document.getElementById(arAbas[i].conteudo)
			c.style.display = 'none';
		}
		m = document.getElementById(menu)
		m.className = 'menu-sel';
		c = document.getElementById(conteudo)
		c.style.display = '';
		if((menu=="td_xml")&&(conteudo=="div_xml")){
			loadXml();
		}
	}
	function loadXml(){
		proj = <?php echo $codprojeto;?>;
		root = treeRoot;
		//http.open("GET", "viewxml.php?root="+root+"&proj="+proj+"&tag=units,components,group", true);
		rootGroup = document.forms["formHiddenGroup"].rootGroup.value;
		urlGet ="viewCompleteXML.php?root="+root+"&proj="+proj+"&rootGroup="+rootGroup+"&file=<?echo "../models/".$array_projeto['nome']."$ramdom.xml";?>&rootDocConn="+rootDocConn+"&iduser="+<?echo $_SESSION['iduser'];?>;
// 		alert(rootDocConn);
		//alert(urlGet);
		http.open("GET", urlGet, true);
		http.onreadystatechange = ajaxLoadxml;
		document.getElementById("textxml").innerHTML = "<img src='../img/loading.gif'>It can take several minutes, please wait...";
		document.getElementById("xmlOptions").style.visibility = "hidden";
		http.send(null);
		
	}
	
	function ajaxLoadxml(){
		//document.getElementById("xmlview").style.visibility = "visible";//torna o form visivel
		if (http.readyState == 4) {
			results = http.responseText;
			//alert(http.responseText);
			textxml = document.getElementById("textxml")
			textxml.innerHTML = null;
			textxml.innerHTML = results;
			document.getElementById("xmlOptions").style.visibility = "visible";
		}
	}
	function start(control, num){
		AlternarAbas('td_units','div_units');
		if(control){
			ReloadTree(num);
		}else{
			loadTree();
		}

		loadtreeGroup();
	}
</script>
</head>

<body onload="start(false, null);">
<div class="titulo"><h2 align="center">Editor</h2></div>


<table class = "maintable" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody width="100%"><tr>
		<td class="menu-sel" id="td_units" onclick="AlternarAbas('td_units','div_units')" height="20" width="100">
			Units&amp;Components
		</td>
		<td class="menu" id="td_group" onclick="AlternarAbas('td_group','div_group')" height="20" width="100">
			Group
		</td>
		<td class="menu" id="td_import" onclick="AlternarAbas('td_import','div_import')" height="20" width="100">
			Import
		</td>
		<td class="menu" id="td_xml" onclick="AlternarAbas('td_xml','div_xml')" height="20" width="100">
			Saving
		</td>

		<td style="border-bottom: 1px solid rgb(0, 0, 0);" width="100%">
			&nbsp;
		</td><td>
	</td></tr>
	<tr>
	
		<td class="tb-conteudo" colspan="5" valign='top'> 
			<div id="div_units" class="conteudo" style="display: none;">
			<? require_once("unitscompForm.php");?>
			</div>
			<div id="div_group" class="conteudo" style="display: none;">
			<? require_once("groupForm.php");?>
			</div>
			<div id="div_import" class="conteudo" style="display: none;">
			<? require_once("importForm.php");?>
			</div>
			<div id="div_xml" class="conteudo" style="display: none;">

                    
				<div class='xmlview' id='textxml'></div>
				
               	<div class='xmlOptions' id='xmlOptions' >
                	<h2>Available Options:</h2>
                    <div  class="itemoption" id="opt1">
                  		<b>Download the model (XML file):</b>
                  		<form action="<?echo "../models/".$array_projeto['nome']."$ramdom.xml";?>" method="post" target="blank">
							<input type='submit' value='Download'>
						</form>
                  	</div>
                  	<br>
					<div  class="itemoption" id="opt2">
                     <b>Create a new project in your repository with this edited model:</b>
                     <form action="savenewproject.php" method="post" target="blank">
                           <input type="hidden" value="<?echo $array_projeto['nome']."$ramdom";?>" name="nomeproj">
                           <input type="hidden" value="<?echo  $HTTP_GET_VARS['coduser'];?>" name="coduser">
                           Project Name:<input type="text" value="<?echo $array_projeto['nome']."$ramdom";?>" name="nomeprojusuario"><br>
                           <input type='submit' value='Save'>
                     </form>
                  </div>
                        
				</div>
			</div>
		</td>
	</tr>
</tbody></table>
<table class="statusbar" width="100%" cellpadding="1" cellspacing="1" border='1'>
<tbody>
<tr>
<td width="34%"><b>Project:&nbsp;</b><? echo $array_projeto['nome'];?></td>
<td width="33%">Fisiocomp</td>
<td width="33%"><b>File:&nbsp;</b><? echo $array_arquivo['nome'];?></td>
</tr>
</tbody></table>
</body></html>
