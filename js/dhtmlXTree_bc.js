 
function myObjTree(a,b,c,d){var a = new dhtmlXTreeObject(a,b,c,d);a.enableCheckBoxes(1);a.enableDragAndDrop("yes");return a;};dhtmlXTreeObject.prototype.disableCheckBoxes=function(){this.enableCheckBoxes(false);};dhtmlXTreeObject.prototype.disableDragAndDrop=function(){this.enableDragAndDrop(false);};dhtmlXTreeObject.prototype.initXMLAutoLoading=function(name){this.setXMLAutoLoading(name);};dhtmlXTreeObject.prototype.getSelectedItems=function(){return this.getSelectedItemText();};dhtmlXTreeObject.prototype.getSelectedItemsId=function(){return this.getSelectedItemId();};dhtmlXTreeObject.prototype.getItemIdByIndex=function(itemId,index){return this.getChildItemIdByIndex(itemId,index);};dhtmlXTreeObject.prototype.insertNewChild=function(pid,id,label,action,im1,im2,im3,optStr,as) {this.insertNewItem(pid,id,label,action,im1,im2,im3,optStr,as);};dhtmlXTreeObject.prototype.collapseAllChildren=function(id){this.closeAllItems(id);};dhtmlXTreeObject.prototype.expandAllChildren=function(id){this.openAllItems(id);};dhtmlXTreeObject.prototype.refreshNode=function(id){this.refreshItem(id);};dhtmlXTreeObject.prototype.setLabel=function(id,text){this.setItemText(id,text);};dhtmlXTreeObject.prototype.getLabel=function(id){return this.getItemText(id);};dhtmlXTreeObject.prototype.setCheckAction=function(func){return this.setOnCheckHandler(func);};dhtmlXTreeObject.prototype.setOpenAction=function(func){return this.setOnOpenHandler(func);};dhtmlXTreeObject.prototype.setDefaultAction=function(func){return this.setOnClickHandler(func);};dhtmlXTreeObject.prototype.setDblClickAction=function(func){return this.setOnDblClickHandler(func);};dhtmlXTreeObject.prototype.setDragFunction=function(func){return this.setDragHandler(func);};
