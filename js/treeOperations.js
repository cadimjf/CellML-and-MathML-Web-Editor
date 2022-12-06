//tree object
var tree;
//xml loader to load details from database
var xmlLoader = new dtmlXMLLoaderObject(doLoadDetails,window);
//default label for new item
var newItemLabel = "New Item";
//id for new (unsaved) item
var newItemId = "-1";
var idnode;
//load tree on page
function loadTree(){
	tree = new dhtmlXTreeObject("treebox","100%","100%",0);
	tree.setImagePath("../imgs/");
	tree.enableDragAndDrop(false);
	tree.setDragHandler(doOnBeforeDrop);
	tree.setOnClickHandler(doOnSelect);
	tree.loadXMLString("<?$treeFromDB->getXMLFromDb()?>");
	//tree.loadXML
}

function addNewSubUnit(){
	if(tree.getLevel(newItemId)!=0){//check if unsaved item already exists
		alert("New Item (unsaved) already exists")
		return false;
	}
	var selectedId = tree.getSelectedItemId();
	if ( (selectedId!="") && (!(isNaN(selectedId) ) ) ){
		if ( tree.getLevel(selectedId)==2 ){
			newItemLabelU = "unit";
			document.getElementById("details").style.visibility = "visible";
			document.forms["detailsForm"].id_node.value = "-1";
			document.forms["detailsForm"].id_parent.value = selectedId ;
			document.forms["detailsForm"].item_order.value = "2";
			document.forms["detailsForm"].node_name.value = newItemLabelU;
			document.forms["detailsForm"].node_value.value = "";
			document.forms["detailsForm"].codProj.value = "<?php echo($codprojeto);?>";
			tree.insertNewItem(selectedId,"-1",newItemLabelU,"","","","","","");
		}else{
			alert("Error!\n Please select a unit to insert a sub-unit.")
		}
	}else{
		alert("Error!\n Please select a unit to insert a sub-unit.")
	}
}

//add new node next to currently selected (or the first in tree)
function addNewPeer(){
	if(tree.getLevel(newItemId)!=0){//check if unsaved item already exists
		alert("New Item (unsaved) already exists");
		return false;
	}
	newItemLabelU = "units";
	<?php echo("parentid = ". $tree->getRoot().";"); ?>
	parentid = parentid + 1;
	document.getElementById("details").style.visibility = "visible";
	document.forms["detailsForm"].id_node.value = "-1";
	document.forms["detailsForm"].id_parent.value = parentid ;
	document.forms["detailsForm"].item_order.value = "1";
	document.forms["detailsForm"].node_name.value = newItemLabelU;
	document.forms["detailsForm"].node_value.value = " ";
	document.forms["detailsForm"].codProj.value = "<?php echo($codprojeto);?>";
	tree.insertNewItem(parentid,newItemId,newItemLabelU,"","","","","","");
}

//add new child node to selected item (or the first item in tree)
function addNewChild(){
	if(tree.getLevel(newItemId)!=0){//check if unsaved item already exists
		alert("New Item (unsaved) already exists")
		return false;
	}
	var selectedId = tree.getSelectedItemId();
	if ( (selectedId!="") && (!(isNaN(selectedId) ) ) ){
		newItemLabelU = "name";
		document.getElementById("details").style.visibility = "visible";
		document.forms["detailsForm"].id_node.value = "-1_";
		document.forms["detailsForm"].id_parent.value = selectedId ;
		document.forms["detailsForm"].item_order.value = "2";
		document.forms["detailsForm"].node_name.value = newItemLabelU;
		document.forms["detailsForm"].node_value.value = "value";
		document.forms["detailsForm"].codProj.value = "<?php echo($codprojeto);?>";
		newItemLabelU = document.forms["detailsForm"].node_name.value + ":" + document.forms["detailsForm"].node_value.value;
		tree.insertNewItem(selectedId,"-1_",newItemLabelU,"","","","","","");
	}else{
		alert("Error!\n Please select a unit to insert an attribute.")
	}
}

//delete item (from database)
function deleteNode(){
	var f = document.forms["detailsForm"];
	if(tree.getSelectedItemId()!=newItemId){//delete node from db
		if(!confirm("Are you sure you want to delete selected node?")){
			return false;
		}else{
			http.open("GET", "deletenode.php?id_node="+f.id_node.value+"&node_name="+f.node_name.value+"&id_parent="+f.id_parent.value, true);
			http.onreadystatechange = teste;
			http.send(null);
//			alert(tree.getSelectedItemId());
//			f.action = "deletenode.php";
//			f.submit();
			doDeleteTreeItem(tree.getSelectedItemId());
		}
	}else{//delete unsaved node
		doDeleteTreeItem(newItemId);
	}
}
function teste(){
	
}
//remove item from tree
function doDeleteTreeItem(id){
	document.getElementById("details").style.visibility = "hidden";
	var pId = tree.getParentId(id);
	tree.deleteItem(id);
	if(pId!="0")
		tree.selectItem(pId,true);
	
}

//save item
function saveItem(){
	var f = document.forms["detailsForm"];
	
	id = document.forms["detailsForm"].id_node.value;
	label = document.forms["detailsForm"].node_name.value;
	if(id==-1){//o nó é novo
		actionStr = "savenewnode.php?id_node="+document.forms["detailsForm"].id_node.value+"&id_parent="+document.forms["detailsForm"].id_parent.value + "&item_order=" + document.forms["detailsForm"].item_order.value + "&codProj=<?php echo($codprojeto);?>&node_name=" + document.forms["detailsForm"].node_name.value;
		http.open("GET", actionStr, true);
		http.onreadystatechange = handleHttpResponseNewNode;
		http.send(null);
	}else if(id=="-1_"){//o attributo é novo
		idaux = document.forms["detailsForm"].id_parent.value+"_" + document.forms["detailsForm"].node_name.value;
		//alert(tree.getItemText(idaux));
		if(tree.getItemText(idaux)==0){
			actionStr = "savenewattr.php?id_node="+document.forms["detailsForm"].id_parent.value+"&node_name=" + document.forms["detailsForm"].node_name.value+"&node_value=" + document.forms["detailsForm"].node_value.value;
			http.open("GET", actionStr, true);
			http.onreadystatechange = handleHttpResponseNewAttr;
			http.send(null);
		}else{
			alert("Error!\nThe attribute '"+document.forms["detailsForm"].node_name.value+ "' already exists in this node.\nPlease type another name.")
			return false;
		}
	}else{
		f.action = "savenode.php";
		f.submit();
		doUpdateItem(id, label);
	}
}
function handleHttpResponseNewNode(){
	if (http.readyState == 4) {
		results = http.responseText.split("|");
		document.forms["detailsForm"].id_node.value = results[0];
		tree.changeItemId("-1",results[0]);
		tree.setItemText(results[0],results[4]);
	}
}
function handleHttpResponseNewAttr(){
	if (http.readyState == 4) {
		results = http.responseText.split("|");
		document.forms["detailsForm"].id_node.value = results[0]+"_"+results[1];
		document.forms["detailsForm"].id_parent.value = results[0];
		document.forms["detailsForm"].node_name.value = results[1];
		document.forms["detailsForm"].node_value.value = results[2];
		tree.changeItemId("-1_", document.forms["detailsForm"].id_node.value);
		tree.setItemText(document.forms["detailsForm"].id_node.value,results[1]+":"+results[2]);
	}
}

//save moved (droped) item to db. Cancel drop if save failed or item is new
function doOnBeforeDrop(id,parentId){
	if(id==newItemId)
		return false;
	else{
		
		var dropSaver = new dtmlXMLLoaderObject(null,null,false);//sync mode
		dropSaver.loadXML("dropprocessor.php?id="+id+"&parent_id="+parentId);
		var root = dropSaver.getXMLTopNode("succeedded");
		var id = root.getAttribute("id");
		if(id==-1){
			alert("Save failed");
			
			return false;
		}else{
			if(tree.getSelectedItemId()==id){//update details (really need only parent id)
				loadDetails(id);
			}
		}
		
		return true;
	}
}

//update item
function doUpdateItem(id, label){
	var f = document.forms["detailsForm"];
	
	if(isNaN(id)){//true se o id não é numérica
	//attr
		idParent = tree.getParentId(id);
		newId = idParent + "_" + f.node_name.value;
		newLabel = f.node_name.value + ": " + f.node_value.value;
		if(newId==id){
			f.id_node.value = id;
//			tree.changeItemId(id,newId);
			tree.setItemText(id,newLabel);
		}else{
			f.id_node.value = newId;
			tree.changeItemId(id,newId);
			tree.setItemText(newId,newLabel);
		}
	}else{//node
		tree.setItemText(id,label);
		f.id_node.value = id;
	}
	tree.setItemColor(id,"black","white");
}

//what to do when item selected
function doOnSelect(itemId){
	if ((itemId !=-1) || (itemId !="-1_")){//new unit or new attribute
	//if(itemId!=newItemId){
		if(tree.getLevel(newItemId)!=0){
			if(confirm("Do you want to save changes?")){//save changes to new item
				tree.selectItem(newItemId,false)
				saveItem();
				return;
			}
			tree.deleteItem(newItemId);
		}	
	}else{//set color to new item label
		tree.setItemColor(itemId,"red","pink")
		
	}
	loadDetails(itemId);//load details for selected item
	
}
//send request to the server to load details
function loadDetails(id){
	t= isNaN(id);//se for atributo a chave não é numerica, retorna true
	if(t){	//está na tabela ATTR
		idParent = tree.getParentId(id);
		nameAttr = tree.getItemText(id);
		nameAttr = nameAttr.split(":");
		nameAttr =nameAttr[0];
		http.open("GET", "loaddetailsAttr.php?id="+idParent+"&name="+nameAttr, true);
		http.onreadystatechange = handleHttpResponseAttr;
		http.send(null);
	}else{	//está na tabela node
		http.open("GET", "loaddetails.php?id=" + id, true);
		http.onreadystatechange = handleHttpResponseNode;
		http.send(null);
	}
}

function handleHttpResponseNode(){
	document.getElementById("details").style.visibility = "visible";//torna o form visivel
	if (http.readyState == 4) {
		results = http.responseText.split("|");
		document.forms["detailsForm"].id_node.value = results[0];
		document.forms["detailsForm"].id_parent.value = results[1];
		document.forms["detailsForm"].item_order.value =results[2];// itemorder
		document.forms["detailsForm"].codProj.value =results[3];// idprojeto
		document.forms["detailsForm"].node_name.value = results[4];
		document.forms["detailsForm"].node_value.value = " ";
	}
}

function handleHttpResponseAttr(){
	document.getElementById("details").style.visibility = "visible";//torna o form visivel
	if (http.readyState == 4) {
		results = http.responseText.split("|");
		document.forms["detailsForm"].id_node.value = results[0]+"_"+results[1];
		document.forms["detailsForm"].id_parent.value = results[0];
		document.forms["detailsForm"].node_name.value = results[1];
		document.forms["detailsForm"].node_value.value = results[2];
		document.forms["detailsForm"].oldName.value = results[1];
	}
}

function getHTTPObject() {
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
var http = getHTTPObject();
//populate form of details
function doLoadDetails(obj){
	/*var f = document.forms["detailsForm"];
	var root = xmlLoader.getXMLTopNode("details")
	var id = root.getAttribute("id");
	document.getElementById("details").style.visibility = "visible";
	if(id==newItemId){
		f.item_nm.value = tree.getItemText(id);
		f.item_desc.value = "";
	}else{
		f.item_nm.value = root.getElementsByTagName("name")[0].firstChild.nodeValue;
		if(root.getElementsByTagName("desc")[0].firstChild!=null)
			f.item_desc.value = root.getElementsByTagName("desc")[0].firstChild.nodeValue;
		else
			f.item_desc.value = "";
	}
	f.item_id.value = id
	f.item_order.value = get_order(id);
	f.item_parent_id.value = tree.getParentId(id);
*/
}
function get_order(itemId){
var z=tree._globalIdStorageFind(itemId);
var z2=z.parentObject;
for (var i=0; i<z2.childsCount; i++)
	if (z2.childNodes[i]==z) return i;
return -1;
}

