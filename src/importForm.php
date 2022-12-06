<link rel="STYLESHEET" type="text/css" href="../css/dhtmlXTree.css">
<link rel="STYLESHEET" type="text/css" href="../css/dhtmlXTreeGrid.css">
<script  src="../js/dhtmlXCommon.js"></script>
<script  src="../js/dhtmlXTree.js"></script>
<script  src="../js/dhtmlXTree_gr.js"></script>

<script>
	
	//tree object
	var treeImport;
	var treeRootImport;
	function carregaArvoreImport(){
		document.getElementById("attributeImp").style.visibility = "hidden";
		idprojeto = document.forms["formImportProjects"].idprojetoImport.value;
		if(idprojeto != -1){
			document.getElementById("impSt").innerHTML = "<img src='../img/loading.gif'>Loading tree. Please wait.";
			url = "cellmlToDb.php?codprojeto="+idprojeto+"&tipo=I&param=units,component&iduser="+<?echo $_SESSION['iduser'];?>;
			http.open("GET", url, true);
			http.onreadystatechange = ajaxArvoreImport;
			http.send(null);
		}
	}

	
	function ajaxArvoreImport(){
		if (http.readyState == 4) {
			treeRootImport = http.responseText;
			ReloadTreeImport();
		}
	}

	function ReloadTreeImport(){
		url = "getTreeXMLCode.php?root="+treeRootImport+"&iduser="+<?echo $_SESSION['iduser'];?>;
		http.open("GET", url, true);
		http.onreadystatechange = ajaxReloadTreeImport;
		http.send(null);
		
	}

	function ajaxReloadTreeImport(){
		if (http.readyState == 4) {
			xml = http.responseText.split("\n");
			//xml2 = xml[1].replace("</tree>0", "</tree>");//trata esse erro.
			newTreeImport(xml[1]);
		}
	}

	function newTreeImport(xml){
		document.getElementById("treeboxImport").innerHTML = "";
		treeImport = new dhtmlXTreeObject("treeboxImport","100%","100%",0);
		treeImport.setImagePath("../imgs/");
		treeImport.enableCheckBoxes(1);
		treeImport.setOnClickHandler(doOnSelectImport);
		
		treeImport.enableThreeStateCheckboxes(true);
			
		treeImport.loadXMLString(xml);
		document.getElementById("impSt").innerHTML = "";
	}

	//função disparada ao clicar no nó
	function doOnSelectImport(itemId){
		document.getElementById("impSt").innerHTML="";
		attrForm = document.forms["attrFormImport"];
		document.getElementById("attributeImp").style.visibility = "visible";
		attrForm.attrvalueImp.value = "";
		document.getElementById("messageImp").innerHTML = "<img src='../img/loading.gif'>";
		http.open("GET", "optionsattr.php?id=" + itemId+"&iduser="+<?echo $_SESSION['iduser'];?>, true);
		http.onreadystatechange = ajaxCarregaAtributosImp;
		http.send(null);
	}

	function ajaxCarregaAtributosImp(){
		if (http.readyState == 4) {
			campo_select = document.forms["attrFormImport"].attrnameImp;
			document.getElementById("messageImp").innerHTML= "";
			campo_select.options.length = 0;
			atributos = http.responseText.split("|");
			for( i = 0; i < atributos.length; i++ ){ 
				campo_select.options[i] = new Option( atributos[i], atributos[i] );
			}
			
			if(atributos[0]=="math"){
				id = treeImport.getSelectedItemId();
				loadXmlMath(id, <?php echo($codprojeto);?>)
			}else{
				carregaAttrImp();
			}
		}
	}

	function loadXmlMath(root, proj){
		http.open("GET", "viewxml.php?root="+root+"&proj="+proj+"&tag=math"+"&iduser="+<?echo $_SESSION['iduser'];?>    , true);
		http.onreadystatechange = ajaxLoadxmlMath;
		//document.getElementById("status").innerHTML = "<img src='../../img/loading.gif'>";
		http.send(null);
	}
	var indice=0;
	
	function ajaxLoadxmlMath(){
		if (http.readyState == 4) {
			results = http.responseText.split("___EDITOR_SPLT_");
			strCampos = "<br><textarea rows='10' cols='30' readonly>";
			for(i=0; i < results.length;i++){
				i++;
				strCampos = strCampos + results[i];
				strCampos = strCampos + "\n";
				i++;
			}
			strCampos = strCampos +"</textarea>";
			document.getElementById("equations").innerHTML = strCampos;
			
		}
		
	}

	
	//chamada no change do combo com atributos
	function carregaAttrImp(){
		document.getElementById("messagevalueImp").innerHTML= "<input name='node_value' type='Text' maxlength='50' name='label' style='width:200px;'>";
		document.getElementById("messagevalueImp").innerHTML = "<img src='../img/loading.gif'>";
		url = "loaddetailsAttr.php?id="+treeImport.getSelectedItemId()+"&name="+document.forms["attrFormImport"].attrnameImp.value +"&iduser="+<?echo $_SESSION['iduser'];?>;
       // alert(url);      
		http.open("GET", url, true);
		document.getElementById("equations").innerHTML = "";
		http.onreadystatechange = ajaxGetAttrValueImp;
		http.send(null);
	}
	
	function ajaxGetAttrValueImp(){
		if (http.readyState == 4) {
			document.getElementById("messagevalueImp").innerHTML= "";
			results = http.responseText.split("|");
           // alert(http.responseText);
			document.forms["attrFormImport"].attrvalueImp.value = results[2];
			
		}
	}

	function importCheckedItems(){
		document.getElementById("impSt").innerHTML = "<img src='../img/loading.gif'> Saving...";
		url = "importItems.php?ids="+treeImport.getAllChecked()+"&codProj="+<?echo $codprojeto;?>+"&root="+treeRoot +"&iduser="+<?echo $_SESSION['iduser'];?>;
  //  		alert(url);      
		http.open("GET", url, true);
		http.onreadystatechange = ajaximportCheckedItems;
		http.send(null);
	}

	function ajaximportCheckedItems(){
		if (http.readyState == 4) {
//			alert(http.responseText);
			document.getElementById("impSt").innerHTML = "";
			//document.getElementById("treeboxImport").innerHTML = "";
			//alert("alert imported "+treeRoot);
            alert("Items imported.");
			ReloadTree(treeRoot);
		}
	}

</script>	
<table width="100%">
	<tr>	
		<td width="30%" valign='top'>
			<div id='importProjects' style="width:100%; height:400px; background-color:#f5f5f5;border:1px solid Silver;">
			<div class="titulosetup"><p>Choose a project to import</p></div>

			<form name='formImportProjects'>
			<p><select name="idprojetoImport" onchange='carregaArvoreImport()'>
			<option value='-1'>Options...</option>
			<?
				include('config.php');
			// 	$codpesquisador = $HTTP_GET_VARS['codpesquisador'];
				$codpesquisador = $_SESSION['id'];
				$str = "SELECT codigoprojeto,nome FROM Projetos WHERE codigopesquisador=$codpesquisador AND codigoprojeto<>'$codprojeto' order by nome" ;
				$busca_projetos = mysql_query($str);
				$i=0;
				while($array_proj = mysql_fetch_array($busca_projetos)){
					echo "<option value='".$array_proj['codigoprojeto']."'>".$array_proj['nome']." </option>";
					$i++;
				}
			?>
			</select></p>
			</form>
			<input type='button' value='Import checked items' onclick='importCheckedItems()'>
			</div>
			
		</td>
		<!--- tree area --->
		<td width="40%" valign='top'>
		<div  id="treeboxImport" style="width:100%; height:400px; background-color:#f5f5f5;border:1px solid Silver;visibility:visible;"/>
		</td>
		<div id='impSt'></div>
		
		<!--Terceira coluna-->
		<td width="30%" valign='top'>

		<div  id="attributeImp" style="width:100%; height:400px; background-color:#f5f5f5;border:1px solid Silver;visibility:hidden" >
		<form name="attrFormImport" action="" target="actionframe" method="post">
		<span>Attribute:</span><div id="messageImp"></div><select name="attrnameImp" onchange="carregaAttrImp()"></select><br>
		<span>Value:</span><div id="messagevalueImp"> </div><input name="attrvalueImp" readonly type="Text" maxlength="50" style="width:200px;"><br>
		<div id="equations"> </div>
		</form>
		</div>

		</td>
	</tr>
</table>

	
