<?php
	require_once('mount_tree.php');
	$treeGroup = new mount_tree($arquivo, $array_arquivo["nome"], $codprojeto,"N", $_SESSION['iduser']);
 	$treeGroup->listTagXml('group', false);
	require_once("treefromdb.php");
	$treeFromDBGroup = new treeFromDb($treeGroup->getRoot()+1,$_SESSION['iduser']);
//	var_dump($treeFromDBGroup );
?>
<form name='formHiddenGroup'>
	<input type='hidden' value="<? echo $treeGroup->getRoot(); ?>" name='rootGroup'>
	<input type='hidden' value="<? echo $codprojeto; ?>" name='codprojetoGroup'>
</form>


<link rel="STYLESHEET" type="text/css" href="../css/dhtmlXTree.css">
<script  src="../js/dhtmlXCommon.js"></script>
<script  src="../js/dhtmlXTree.js"></script>	
<script>
	
	//tree object
	var treeGroup;
	//xml loader to load details from database
	
	//load tree on page
	function loadtreeGroup(){
	//	alert(<? echo $_SESSION['iduser'];?>);
		treeGroup = new dhtmlXTreeObject("treeboxGroup","100%","100%",0);
		treeGroup.setImagePath("../imgs/");
		treeGroup.enableDragAndDrop(true);
		treeGroup.setDragHandler(doOnBeforeDropGroup);
		treeGroup.loadXMLString("<?$treeFromDBGroup->getXMLFromDb(true)?>");
	}
	//save moved (droped) item to db. Cancel drop if save failed or item is new
	function doOnBeforeDropGroup(id,parentId){
		if(id==newItemId)
			return false;
		else{
			var dropSaverGroup = new dtmlXMLLoaderObject(null,null,false);//sync mode
			urlGroup = "dropprocessor.php?id="+id+"&parent_id="+parentId;
			dropSaverGroup.loadXML(urlGroup);
			var rootGroup = dropSaverGroup.getXMLTopNode("succeedded");
			var idGroup = rootGroup.getAttribute("id");
			if(idGroup==-1){
				alert("Save failed");
				return false;
			}else{
				if(treeGroup.getSelectedItemId()==id){//update details (really need only parent id)
// 					loadDetails(id);
				}
			}
			
			return true;
		}
	}

	function addComponent(){
		//form variable
//		alert("Aqui");
		document.getElementById("groupStatus").innerHTML="";
		
// 		if(treeGroup.getLevel(newItemId)!=0){//check if unsaved item already exists
// 			alert("New Item (unsaved) already exists");
// 			return false;
// 		}
		
		formComponents = document.forms["formComponents"];
		parentid = treeGroup.getSelectedItemId();
		if(parentid !=""){
			document.getElementById("divComponents").style.visibility = "visible";
			formComponents.id_node.value = "-1";
			formComponents.id_parent.value = parentid;
			formComponents.item_order.value = "1";
			formComponents.node_name.value = "";
			formComponents.id_projeto.value = "<?php echo($codprojeto);?>";
			treeGroup.insertNewItem(parentid,"-1","New Tag","","","","","","");
			document.getElementById("groupStatus").innerHTML= "<img src='../img/loading.gif'>";
			http.open("GET", "listComponentsByProject.php?codprojeto='<?php echo($codprojeto);?>'", true);
			http.onreadystatechange = ajaxAddComponent;
			http.send(null);
		}else{
			alert("Please choose a node to insert.");
		}
	}
	//carrega o combo com os nomes da tags possíveis
	function ajaxAddComponent(){
		if (http.readyState == 4) {
			campo_select = document.forms["formComponents"].selectComponents;
			document.getElementById("messagenode").innerHTML= "";
			campo_select.options.length = 0;
			atributos = http.responseText.split("|");
			for( i = 0; i < atributos.length; i++ ){ 
				campo_select.options[i] = new Option( atributos[i], atributos[i] );
			}
		}
	}

	function deleteComponent(){
		alert("To do...");
	}
	function voltarPagina(){
		if( confirm("Are you sure?\n") ){
			window.location = "index.php";
		}
	}
	
</script>	
<table width="100%">
	
	<tr>	
		<td width="20%" valign='top'>
			<div id="actions">
				<div class="titulosetup">Actions</div>
				<img src="../img/linha.gif" alt="" height="3" width="100%"><br>
<!-- 				<a href="javascript:void(0);" onclick="addComponent()">Insert component</a><br> -->
<!-- 				<img src="../img/linha.gif" alt="" height="3" width="100%"><br> -->
<!-- 				<a href="javascript:void(0);" onclick="deleteComponent()">Delete</a><br> -->
<!-- 				<img src="../img/linha.gif" alt="" height="3" width="100%"><br> -->
				<a href="javascript:void(0);" onclick="treeGroup.closeAllItems(0);">Collapse all</a><br>
				<img src="../img/linha.gif" alt="" height="3" width="100%"><br>
				<a href="javascript:void(0);" onclick="treeGroup.openAllItems(0);">Expand all</a><br>
				<img src="../img/linha.gif" alt="" height="3" width="100%"><br>
				<a href="javascript:void(0);" onclick="treeGroup.closeItem(treeGroup.getSelectedItemId());">Close selected item</a><br>
				<img src="../img/linha.gif" alt="" height="3" width="100%"><br>
				<a href="javascript:void(0);" onclick="treeGroup.openItem(treeGroup.getSelectedItemId());">Open selected item</a><br>
				<img src="../img/linha.gif" alt="" height="3" width="100%"><br>
				<a href="javascript:void(0);" onclick="treeGroup.closeAllItems(treeGroup.getSelectedItemId());">Collapse selected branch</a><br>
				<img src="../img/linha.gif" alt="" height="3" width="100%"><br>
				<a href="javascript:void(0);" onclick="treeGroup.openAllItems(treeGroup.getSelectedItemId());">Expand selected branch</a><br>
				<img src="../img/linha.gif" alt="" height="3" width="100%"><br>
<!-- 				<a href="javascript:void(0);" onclick="voltarPagina()">Exit</a><br> -->
<!-- 				<img src="../img/linha.gif" alt="" height="3" width="100%"><br> -->
			</div>
		</td>
		<!--- tree area --->
		<td width="50%" valign='top'>
			<div  id="treeboxGroup" style="width:100%; height:400px; background-color:#f5f5f5;border:1px solid Silver;"/>
		</td>
		<div name='groupStatus'>
		</div>
		<td width="30%" valign='top'>
		<div name='divComponents' style="visibility:hidden;">
		<form name='formComponents'>
			<input type='hidden' name="id_node.value">
			<input type='hidden' name="id_parent.value">
			<input type='hidden' name="item_order.value">
			<input type='hidden' name="node_name.value">
			<input type='hidden' name="id_projeto.value">
			<select name='selectComponents'></select>
		</form>
		</div>
		</td>
	</tr>
</table>

	
