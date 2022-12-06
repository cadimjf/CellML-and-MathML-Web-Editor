<?php
/*
************************************************************
********************Formulário antigo***********************
************************************************************
*/
	require_once('mount_tree.php');
	$treecomp = new mount_tree($arquivo, $array_arquivo["nome"], $codprojeto);
	$treecomp->listTagXml('component');
	require_once("treefromdb.php");
	$treecompFromDB = new treeFromDb($treecomp->getRoot());
?>
<form name='formHidden'>
	<input type='hidden' value="<? echo $treecomp->getRoot(); ?>" name='root'>
	<input type='hidden' value="<? echo $codprojeto; ?>" name='codprojeto'>
</form>
<link rel="STYLESHEET" type="text/css" href="../css/dhtmlXTree.css">
<script  src="../js/dhtmlXCommon.js"></script>
<script  src="../js/dhtmlXTree.js"></script>	

<script>
	alert("Está em desuso");
	//treecomp object
	var treecomp;
	//xml loader to load details from database
	var xmlLoader_comp = new dtmlxmlLoaderObject(doloadDetails_comp,window);
	//default label for new item
	var newItemLabel = "New Item";
	//id for new (unsaved) item
	var newitemId_comp_comp = "-1";
	var idnode_comp;
	//load treecomp on page
	function loadtreecomp(){
		treecomp = new dhtmlXTreeObject("treecompbox","100%","100%",0);
		treecomp.setImagePath("../imgs/");
		treecomp.enableDragAndDrop(true);
		treecomp.setDragHandler(doOnBeforeDrop_comp);
		treecomp.setOnClickHandler(doOnSelect_comp);
		treecomp.loadXMLString("<?$treecompFromDB->getXMLFromDb()?>");
		//treecomp.loadXML
	}
	
	function addNewVariable(){
		if(treecomp.getLevel(newitemId_comp_comp)!=0){//check if unsaved item already exists
			alert("New Item (unsaved) already exists")
			return false;
		}
		var selectedId_comp = treecomp.getSelecteditemId_comp();
		if ( (selectedId_comp!="") && (!(isNaN(selectedId_comp) ) ) ){
			if ( treecomp.getLevel(selectedId_comp)==2 ){
				newItemLabelU = "unit";
				document.getElementById("details_comp").style.visibility = "visible";
				document.forms["detailsFormComp"].id_node.value = "-1";
				document.forms["detailsFormComp"].id_parent.value = selectedId_comp ;
				document.forms["detailsFormComp"].item_order.value = "2";
				document.forms["detailsFormComp"].node_name.value = newItemLabelU;
				document.forms["detailsFormComp"].node_value.value = "";
				document.forms["detailsFormComp"].codProj.value = "<?php echo($codprojeto);?>";
				treecomp.insertNewItem(selectedId_comp,"-1",newItemLabelU,"","","","","","");
			}else{
				alert("Error!\n Please select a component to insert a variable.")
			}
		}else{
			alert("Error!\n Please select a component to insert a variable.")
		}
	}

	//add new node next to currently selected (or the first in treecomp)
	function addNew_comp(){
		if(treecomp.getLevel(newitemId_comp_comp)!=0){//check if unsaved item already exists
			alert("New Item (unsaved) already exists");
			return false;
		}
		newItemLabelU = "units";
		<?php echo("parentid = ". $treecomp->getRoot().";"); ?>
		parentid = parentid + 1;
		document.getElementById("details_comp").style.visibility = "visible";
		document.forms["detailsFormComp"].id_node.value = "-1";
		document.forms["detailsFormComp"].id_parent.value = parentid ;
		document.forms["detailsFormComp"].item_order.value = "1";
		document.forms["detailsFormComp"].node_name.value = newItemLabelU;
		document.forms["detailsFormComp"].node_value.value = " ";
		document.forms["detailsFormComp"].codProj.value = "<?php echo($codprojeto);?>";
		treecomp.insertNewItem(parentid,newitemId_comp_comp,newItemLabelU,"","","","","","");
	}
	
	//add new child node to selected item (or the first item in treecomp)
	function addNewChild_comp(){
		if(treecomp.getLevel(newitemId_comp_comp)!=0){//check if unsaved item already exists
			alert("New Item (unsaved) already exists")
			return false;
		}
		var selectedId_comp = treecomp.getSelecteditemId_comp();
		if ( (selectedId_comp!="") && (!(isNaN(selectedId_comp) ) ) ){
			newItemLabelU = "name";
			document.getElementById("details_comp").style.visibility = "visible";
			document.forms["detailsFormComp"].id_node.value = "-1_";
			document.forms["detailsFormComp"].id_parent.value = selectedId_comp ;
			document.forms["detailsFormComp"].item_order.value = "2";
			document.forms["detailsFormComp"].node_name.value = newItemLabelU;
			document.forms["detailsFormComp"].node_value.value = "value";
			document.forms["detailsFormComp"].codProj.value = "<?php echo($codprojeto);?>";
			newItemLabelU = document.forms["detailsFormComp"].node_name.value + ":" + document.forms["detailsFormComp"].node_value.value;
			treecomp.insertNewItem(selectedId_comp,"-1_",newItemLabelU,"","","","","","");
		}else{
			alert("Error!\n Please select a unit to insert an attribute.")
		}
	}
	
	//delete item (from database)
	function deleteNode_comp(){
		var f = document.forms["detailsFormComp"];
		if(treecomp.getSelecteditemId_comp()!=newitemId_comp_comp){//delete node from db
			if(!confirm("Are you sure you want to delete selected node?")){
				return false;
			}else{
				http.open("GET", "deleteNode.php?id_node="+f.id_node.value+"&node_name="+f.node_name.value+"&id_parent="+f.id_parent.value, true);
				http.onreadystatechange = teste_comp;
				http.send(null);
// 				alert(treecomp.getSelecteditemId_comp());
// 				f.action = "deleteNode_comp.php";
// 				f.submit();
				doDeletetreecompItem(treecomp.getSelecteditemId_comp());
			}
		}else{//delete unsaved node
			doDeletetreecompItem(newitemId_comp_comp);
		}
	}
	function teste_comp(){
		
	}
	//remove item from treecomp
	function doDeletetreecompItem(id){
		document.getElementById("details").style.visibility = "hidden";
		var pId = treecomp.getParentId(id);
		treecomp.deleteItem(id);
		if(pId!="0")
			treecomp.selectItem(pId,true);
		
	}
	
	//save item
	function saveItem_comp(){
		var f = document.forms["detailsFormComp"];
		
		id = document.forms["detailsFormComp"].id_node.value;
		label = document.forms["detailsFormComp"].node_name.value;
		if(id==-1){//o nó é novo
			actionStr = "savenewnode.php?id_node="+document.forms["detailsFormComp"].id_node.value+"&id_parent="+document.forms["detailsFormComp"].id_parent.value + "&item_order=" + document.forms["detailsFormComp"].item_order.value + "&codProj=<?php echo($codprojeto);?>&node_name=" + document.forms["detailsFormComp"].node_name.value;
			http.open("GET", actionStr, true);
			http.onreadystatechange = handleHttpResponseNewNode_comp;
			http.send(null);
		}else if(id=="-1_"){//o attributo é novo
			idaux = document.forms["detailsFormComp"].id_parent.value+"_" + document.forms["detailsFormComp"].node_name.value;
			//alert(treecomp.getItemText(idaux));
			if(treecomp.getItemText(idaux)==0){
				actionStr = "savenewattr.php?id_node="+document.forms["detailsFormComp"].id_parent.value+"&node_name=" + document.forms["detailsFormComp"].node_name.value+"&node_value=" + document.forms["detailsFormComp"].node_value.value;
				http.open("GET", actionStr, true);
				http.onreadystatechange = handleHttpResponseNewAttr_comp;
				http.send(null);
			}else{
				alert("Error!\nThe attribute '"+document.forms["detailsFormComp"].node_name.value+ "' already exists in this node.\nPlease type another name.")
				return false;
			}
		}else{
			f.action = "savenode.php";
			f.submit();
			doUpdateItem_comp(id, label);
		}
	}
	function handleHttpResponseNewNode_comp(){
		if (http.readyState == 4) {
			results = http.responseText.split("|");
			document.forms["detailsFormComp"].id_node.value = results[0];
			treecomp.changeitemId_comp("-1",results[0]);
			treecomp.setItemText(results[0],results[4]);
		}
	}
	function handleHttpResponseNewAttr_comp(){
		if (http.readyState == 4) {
			results = http.responseText.split("|");
			document.forms["detailsFormComp"].id_node.value = results[0]+"_"+results[1];
			document.forms["detailsFormComp"].id_parent.value = results[0];
			document.forms["detailsFormComp"].node_name.value = results[1];
			document.forms["detailsFormComp"].node_value.value = results[2];
			treecomp.changeitemId_comp("-1_", document.forms["detailsFormComp"].id_node.value);
			treecomp.setItemText(document.forms["detailsFormComp"].id_node.value,results[1]+":"+results[2]);
		}
	}

	//save moved (droped) item to db. Cancel drop if save failed or item is new
	function doOnBeforeDrop_comp(id,parentId){
		if(id==newitemId_comp_comp)
			return false;
		else{
			
			var dropSaver = new dtmlxmlLoader_compObject(null,null,false);//sync mode
			dropSaver.loadXML("dropprocessor.php?id="+id+"&parent_id="+parentId);
			var root = dropSaver.getXMLTopNode("succeedded");
			var id = root.getAttribute("id");
			if(id==-1){
				alert("Save failed");
				
				return false;
			}else{
				if(treecomp.getSelecteditemId_comp()==id){//update details (really need only parent id)
					loadDetails_comp(id);
				}
			}
			
			return true;
		}
	}
	
	//update item
	function doUpdateItem_comp(id, label){
		var f = document.forms["detailsFormComp"];
		
		if(isNaN(id)){//true se o id não é numérica
		//attr
			idParent = treecomp.getParentId(id);
			newId = idParent + "_" + f.node_name.value;
			newLabel = f.node_name.value + ": " + f.node_value.value;
			if(newId==id){
				f.id_node.value = id;
// 				treecomp.changeitemId_comp(id,newId);
				treecomp.setItemText(id,newLabel);
			}else{
				f.id_node.value = newId;
				treecomp.changeitemId_comp(id,newId);
				treecomp.setItemText(newId,newLabel);
			}
		}else{//node
			treecomp.setItemText(id,label);
			f.id_node.value = id;
		}
		treecomp.setItemColor(id,"black","white");
	}
	
	//what to do when item selected
	function doOnSelect_comp(itemId_comp){
		if ((itemId_comp !=-1) || (itemId_comp !="-1_")){//new unit or new attribute
		//if(itemId_comp!=newitemId_comp_comp){
			if(treecomp.getLevel(newitemId_comp_comp)!=0){
				if(confirm("Do you want to save changes?")){//save changes to new item
					treecomp.selectItem(newitemId_comp_comp,false)
					saveItem_comp();
					return;
				}
				treecomp.deleteItem(newitemId_comp_comp);
			}	
		}else{//set color to new item label
			treecomp.setItemColor(itemId_comp,"red","pink")
			
		}
		loadDetails_comp(itemId_comp);//load details for selected item
		
	}
	//send request to the server to load details
	function loadDetails_comp(id){
		t= isNaN(id);//se for atributo a chave não é numerica, retorna true
		if(t){	//está na tabela ATTR
			idParent = treecomp.getParentId(id);
			nameAttr = treecomp.getItemText(id);
			nameAttr = nameAttr.split(":");
			nameAttr =nameAttr[0];
			http.open("GET", "loaddetailsAttr.php?id="+idParent+"&name="+nameAttr, true);
			http.onreadystatechange = handleHttpResponseAttr_comp;
			http.send(null);
		}else{	//está na tabela node
			http.open("GET", "loaddetails.php?id=" + id, true);
			http.onreadystatechange = handleHttpResponseNode_comp;
			http.send(null);
		}
	}
	
	function handleHttpResponseNode_comp(){
		document.getElementById("details_comp").style.visibility = "visible";//torna o form visivel
		if (http.readyState == 4) {
			results = http.responseText.split("|");
			document.forms["detailsFormComp"].id_node.value = results[0];
			document.forms["detailsFormComp"].id_parent.value = results[1];
			document.forms["detailsFormComp"].item_order.value =results[2];// itemorder
			document.forms["detailsFormComp"].codProj.value =results[3];// idprojeto
			document.forms["detailsFormComp"].node_name.value = results[4];
 			document.forms["detailsFormComp"].node_value.value = " ";
		}
	}
	
	function handleHttpResponseAttr_comp(){
		document.getElementById("details_comp").style.visibility = "visible";//torna o form visivel
		if (http.readyState == 4) {
			results = http.responseText.split("|");
			document.forms["detailsFormComp"].id_node.value = results[0]+"_"+results[1];
			document.forms["detailsFormComp"].id_parent.value = results[0];
			document.forms["detailsFormComp"].node_name.value = results[1];
			document.forms["detailsFormComp"].node_value.value = results[2];
			document.forms["detailsFormComp"].oldName.value = results[1];
		}
	}

	function getHTTPObject_comp() {
		var req;
		try { 
			if (window.XMLHttpRequest) {
				req = new XMLHttpRequest();
				if (req.readyState == null) {
					req.readyState = 1;
					req.addEventListener("load", function () {
						req.readyState = 4;
	
						if (typeof req.onReadyStateChange == "function")
						req.onReadyStateChange();
					}, false);
				}
				return req;
 			}

			if (window.ActiveXObject) {
				var prefixes = ["MSXML2", "Microsoft", "MSXML", "MSXML3"];
		
				for (var i = 0; i < prefixes.length; i++) {
					try {
						req = new ActiveXObject(prefixes[i] + ".XmlHttp");
						return req;
					} catch (ex) {};
		
				}
			}
		} catch (ex) {}

		alert("XmlHttp Objects not supported by client browser");
	}
	var http = getHTTPObject_comp();
	//populate form of details
	function doloadDetails_comp(obj){
		/*var f = document.forms["detailsFormComp"];
		var root = xmlLoader_comp.getXMLTopNode("details")
		var id = root.getAttribute("id");
		document.getElementById("details").style.visibility = "visible";
		if(id==newitemId_comp_comp){
			f.item_nm.value = treecomp.getItemText(id);
			f.item_desc.value = "";
		}else{
			f.item_nm.value = root.getElementsByTagName("name")[0].firstChild.nodeValue;
			if(root.getElementsByTagName("desc")[0].firstChild!=null)
				f.item_desc.value = root.getElementsByTagName("desc")[0].firstChild.nodeValue;
			else
				f.item_desc.value = "";
		}
		f.item_id.value = id
		f.item_order.value = get_order_comp(id);
		f.item_parent_id.value = treecomp.getParentId(id);
	*/
	}
	function get_order_comp(itemId_comp){
            var z=treecomp._globalIdStorageFind(itemId_comp);
            var z2=z.parentObject;
            for (var i=0; i<z2.childsCount; i++)
                if (z2.childNodes[i]==z) return i;
            return -1;
        }
</script>	
<div id="actions">
	<a href="javascript:void(0)" onclick="addNew_comp()">Add component</a>|
	<a href="javascript:void(0)" onclick="addNewVariable()">Add variable</a>|
	<a href="javascript:void(0)" onclick="addNewChild_comp()">Add attribute</a>|
	<a href="javascript:void(0)" onclick="deleteNode_comp()">Delete</a>
</div>
<table width="100%">
	<tr>
	
		<!--- treecomp area --->
		<td width="50%" valign='top'>
			<div id="treecompbox" style="width:100%; background-color:#f5f5f5;border:1px solid Silver;"/>
		</td>
		<!--- details area. visible if any node selected --->
		<td id="details_comp" style="padding:5px;visibility:hidden;" valign="top" align='left'>
			<form name="detailsFormComp" action="" target="actionframe" method="post">
			<span>Node ID:</span>
			<input  name="id_node" type="Text" style="background-color:lightgrey;width:70px;text-align:right;" name="label" readonly="true"><br>
			<span>Parent ID:</span>
			<input  name="id_parent" type="Text" style="background-color:lightgrey;width:70px;text-align:right;" name="label" readonly="true"><br>
			<span>Name:</span>
			<input name="node_name" type="Text" maxlength="50" name="label" style="width:200px;"><br>
			<span>Value:</span>
			<input name="node_value" type="Text" maxlength="50" name="label" style="width:200px;"><br>
			<button type="button" onclick="saveItem_comp()" style="width:200px;">Save</button>
			<input type='hidden' name='item_order' value='0'>
			<input type='hidden' name='codProj' value='0'>
			<input type='hidden' name='oldName' value='0'>
			</form>
		</td>
	</tr>
</table>
<iframe name="actionframe" id="actionframe" frameborder="0" width="100%" height="0"></iframe>