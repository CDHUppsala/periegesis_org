function getTableNames(tblName,currentTable) 
{ 
	select	= document.getElementById(tblName)
	count	= 1; 
	select.options.length = count; 
	for( i = 0; i < arrayTables.length; i++ )
	{ 
		var strOption = select.options[count++] = new Option(arrayTables[i]);
		strOption.value  = arrayTables[i];
//		if(arrayTables[i] == currentTable){strOption.selected  = "selected";}
	} 
//	select.focus(); 
}

function getFields(tableID,fieldsID,currentField) 
{ 
	select	= document.getElementById(fieldsID)
	strSplit = ""; 
	count	= 1; 
	select.options.length = count; 
	for( i = 0; i < arrayFields.length; i++ )
	{ 
		strSplit = arrayFields[i].split( "|" ); 
		if( strSplit[0] == tableID)
		{ 
			var strOption = select.options[count++] = new Option(strSplit[2]);
			strOption.title  = strSplit[1];
			strOption.value  = strSplit[2];
//			if(strSplit[2] == currentField)
//			{
//				strOption.selected  = "selected";
//			}
		} 
	} 
//	select.focus(); 
} 
function getMultipleFields(selectorID,tableID,relationType,currentTextarea,currentField,currentTable)
{
	if(relationType == 3)
	{
		getFields(tableID,'distinct','')
		getFields(tableID,'fieldOrderBy','')
		getFields(tableID,'fieldWhere','')
	}
	if(relationType == 10 )
	{
		getFields(tableID,'fieldID','')
		getFields(tableID,'fieldName','')
		getFields(tableID,'fieldCat','')
	}
	if(relationType == 1 || relationType == 2)
	{
		getFields(tableID,'fieldID','')
		getFields(tableID,'fieldName','')
		getFields(tableID,'fieldOrderBy','')
		getFields(tableID,'fieldWhere','')
		getFields(tableID,'fieldOrderBySecond','')
		getFields(tableID,'fieldWhereSecond','')
	}
	if(relationType == 11)
	{
		getFields(tableID,'firstField','')
		getFields(tableID,'secondField','')
		getFields(tableID,'relatedID_Name','')
		getFields(currentTable,'firstField1','')
		getFields(currentTable,'secondField1','')
		getFields(currentTable,'currentID_Name','')
	}
	if(relationType == 12)
	{
		if(selectorID == 'tableList')
		{
			getFields(tableID,'relatedField','')
			getFields(currentTable,'thisField','')
			getFields(tableID,'relatedID','')
			getFields(currentTable,'relatedName','')
		}
	}
}

function getTableOptions(relationType,elementID,currentTextarea,currentField,currentTable)
{
	if(relationType == 1 || relationType == 2 || relationType == 3 || relationType == 10 || relationType == 11 || relationType == 12)
	{
		floatingMenu.show()
	}else{
		floatingMenu.hide()
		return false
	}
	var strElement = document.getElementById(elementID)
	var currentInfo = '<b>Current Field</b>: '+currentField+' <b><br>Current Table</b>: '+currentTable+'<br>'
	var strAdd = '';
	var tblAdd = '<br><select id="tableList" name="tableList"'
	+ 'onChange="javascript:getMultipleFields(this.id,this.options[selectedIndex].value,\''+relationType+'\',\''+currentTextarea+'\',\''+currentField+'\',\''+currentTable+'\');">' 
	+ '<option value="">Select Related Table</option></select>'
	+ '<input type="hidden" name="currentTextarea" id="currentTextarea" value="'+currentTextarea+'">'
	+ '<input type="hidden" name="relationType" id="relationType" value="'+relationType+'">'
	+ '<input type="hidden" name="currentTable" id="currentTable" value="'+currentTable+'">'
	+ '<input type="hidden" name="currentField" id="currentField" value="'+currentField+'">'

	if(relationType == 3)
 	{
		strAdd += currentInfo +'<b>Relation Type 3</b>: Get Distinct Values from a Field.<br>' 
		+ tblAdd +'<select id="distinct" name="distinct">'
		+ '<option value="">Select Distinct Field</option></select>'

		+ '<br><select id="fieldWhere" name="fieldWhere">'
		+ '<option value="">WHERE</option></select>'
		+ '<select id="compare" name="compare">'
			+ '<option value="=">	= 	</option>'
			+ '<option value="<>">	<>	</option>'
			+ '<option value=">">	>	</option>'
			+ '<option value="<">	<	</option>'
			+ '<option value=">=">	>=	</option>'
			+ '<option value="<=">	<=	</option></select>'
		+ '<input type="text" id="fieldWhereTo" name="fieldWhereTo">'

		+ '<br><select id="fieldOrderBy" name="fieldOrderBy">'
		+ '<option value="">ORDER BY</option></select>'
		+ '<select id="orderType" name="orderType">'
			+ '<option value="ASC">Ascending</option>'
			+ '<option value="DESC">Descending</option></select>'

	}
	if(relationType == 10)
	{
		strAdd += currentInfo +'<b>Relation Typ 10</b>: Replace ID Field by Name Field. '
		+ 'Get only the subcategories of a category.<br>'
		+ tblAdd 
		+ '<select id="fieldCat" name="fieldCat">'
		+ '<option value="">Select Category Field</option></select>'
		+ '<br><select id="fieldID" name="fieldID">'
		+ '<option value="">Subcategory ID Field</option></select>'
		+ '<select id="fieldName" name="fieldName">'
		+ '<option value="">Subcategory Name Field</option></select>'
	}
	if(relationType == 1 || relationType == 2)
	{
		var relationText = '';
		if (relationType == 2)
			{relationText =  currentInfo +'<b>Relation Typ 2</b>: Replace ID Field by Name Field. '
			+ 'A new record can be added to the Related Table.<br>'}
		else{relationText =  currentInfo +'<b>Relation Typ 1</b>: Replace ID Field by Name Field .<br>'}
		strAdd += relationText + tblAdd +'<br>'
		+ '<select id="fieldID" name="fieldID">'
		+ '<option value="">Select ID Field</option></select>'
		+ '<select id="fieldName" name="fieldName">'
		+ '<option value="">Select Name Field</option></select>'
		+ '<br><select id="fieldWhere" name="fieldWhere">'
		+ '<option value="">Where Field</option></select>'
		+ '<select id="compare" name="compare">'
			+ '<option value="=">	= 	</option>'
			+ '<option value="<>">	<>	</option>'
			+ '<option value=">">	>	</option>'
			+ '<option value="<">	<	</option>'
			+ '<option value=">=">	>=	</option>'
			+ '<option value="<=">	<=	</option></select>'
		+ '<input type="text" id="fieldWhereTo" name="fieldWhereTo">'

		+ '<br><select id="fieldWhereSecond" name="fieldWhereSecond">'
		+ '<option value="">Where Field</option></select>'
		+ '<select id="compareSecond" name="compareSecond">'
			+ '<option value="=">	= 	</option>'
			+ '<option value="<>">	<>	</option>'
			+ '<option value=">">	>	</option>'
			+ '<option value="<">	<	</option>'
			+ '<option value=">=">	>=	</option>'
			+ '<option value="<=">	<=	</option></select>'
		+ '<input type="text" id="fieldWhereToSecond" name="fieldWhereToSecond">'
	
		+ '<br><select id="fieldOrderBy" name="fieldOrderBy">'
		+ '<option value="">Order By</option></select>'
		+ '<select id="orderType" name="orderType">'
			+ '<option value="ASC">Ascending</option>'
			+ '<option value="DESC">Descending</option></select>'
	
		+ '<br><select id="fieldOrderBySecond" name="fieldOrderBySecond">'
		+ '<option value="">Order By</option></select>'
		+ '<select id="orderTypeSecond" name="orderTypeSecond">'
			+ '<option value="ASC">Ascending</option>'
			+ '<option value="DESC">Descending</option></select>'
	}


	if(relationType == 11)
	{
		var relationText =  currentInfo +'<b>Relation Typ 11</b>: Adds Pair Field Values from Current Table '
			+ 'to Related Table and get the New ID of the Related Table to Add in Current Table.<br>'
		strAdd += relationText + tblAdd +'<br>'
		+ '<b>Pair Fields from Related Table</b><br>'
		+ '<select id="firstField" name="firstField">'
		+ '<option value="">Select First Field</option></select>'
		+ '<select id="secondField" name="secondField">'
		+ '<option value="">Select Second Field</option></select><br>'
		+ '<select id="relatedID_Name" name="relatedID_Name">'
		+ '<option value="">ID-Field Name to Get from Related Table</option></select><br>'

		+ '<b>Pair Fields from Current Table</b><br>'
		+ '<select id="firstField1" name="firstField1">'
		+ '<option value="">Select First Replacer</option></select>'
		+ '<select id="secondField1" name="secondField1">'
		+ '<option value="">Select Second Replacer</option></select><br>'
		+ '<select id="currentID_Name" name="currentID_Name">'
		+ '<option value="">ID-Field Name to Add in Current Table</option></select><br>'
	}

	if(relationType == 12)
	{
		strAdd += currentInfo +'<b>Relation Typ 12</b>: Multiple Update of Field Values in Related Tables '
			+ 'with Field Values from Current Table.<br>'
		+ tblAdd 
		+ '<br><select id="relatedField" name="relatedField">'
		+ '<option value="">Select Related Field</option></select>'
		+ '<select id="thisField" name="thisField">'
		+ '<option value="">Replace with this Field</option></select><br>'

		+ '<select id="relatedID" name="relatedID">'
		+ '<option value="">Select Related ID Field</option></select>'
		+ '<select id="relatedName" name="relatedName">'
		+ '<option value="">Select Curent ID Field</option></select>'

	}

	strAdd += '<div style="text-align: right;"><input type=button onclick="addRelations()" value="Add to Textarea"></div>'
	strElement.innerHTML = '';
	strElement.innerHTML = strAdd;
	getTableNames('tableList','');
	if(relationType == 12)
	{
	getTableNames('tableListb','');
	getTableNames('tableListc','');
	}
	return false;
}

function addRelations()
{
/*	Relation Types and Fields
All:	tableList,currentTextarea,currentTable,
3:		distinct,fieldWhere,compare,fieldWhereTo,fieldOrderBy,orderType
10:		fieldID,fieldName,fieldCat
1,2:	fieldID,fieldName,fieldWhere,compare,fieldWhereTo,
		fieldWhereSecond,compareSecond,fieldWhereToSecond,fieldOrderBy,orderType,fieldOrderBySecond,orderTypeSecond
11:		firstField,secondField,relatedID_Name,firstField1,secondField1,currentID_Name
12: 	relatedField,thisField,relatedID,relatedName
*/
	var addStr = "";
	var x = document.getElementById('relationType').value;
	var tableList = document.getElementById('tableList').value;
	var currentTextarea = document.getElementById('currentTextarea').value;
	var currentTable = document.getElementById('currentTable').value;

	if (x ==10 || x ==1 || x ==2)
	{
		var fieldID = document.getElementById('fieldID').value;
		var fieldName = document.getElementById('fieldName').value;
	}
	if (x ==3 || x ==1 || x ==2)
	{
		var fieldWhere = document.getElementById('fieldWhere').value;
		var compare = document.getElementById('compare').value;
		var fieldWhereTo = document.getElementById('fieldWhereTo').value;
		var fieldOrderBy = document.getElementById('fieldOrderBy').value;
		var orderType = document.getElementById('orderType').value;
	}
	if (x ==1 || x ==2)
	{
		var fieldWhereSecond = document.getElementById('fieldWhereSecond').value;
		var compareSecond = document.getElementById('compareSecond').value;
		var fieldWhereToSecond = document.getElementById('fieldWhereToSecond').value;
		var fieldOrderBySecond = document.getElementById('fieldOrderBySecond').value;
		var orderTypeSecond = document.getElementById('orderTypeSecond').value;
	}
	if (x == 1 || x == 2)
	{
		if (tableList == '' || fieldID == '' || fieldName == '' || fieldID == fieldName)
		{
			alert("Selected ID Field or Name Field are not correct")
			return false;
		}else{
			addStr = 'SELECT '+fieldID+', '+fieldName+' FROM '+ tableList
			if (fieldWhere != '' && compare != '')
			{
				if (fieldWhereTo == '' || fieldWhereTo == '""' || fieldWhereTo == '\'\''){fieldWhereTo = "\"\"\"\""}
				addStr += ' WHERE '+fieldWhere+' '+compare+' '+fieldWhereTo 
			}
			if (fieldWhereSecond != '' && compareSecond != '' && fieldWhere != '')
			{
				if (fieldWhereToSecond == ''|| fieldWhereToSecond == '""' || fieldWhereToSecond == '\'\''){fieldWhereToSecond = "\"\"\"\""}
				addStr += ' AND '+fieldWhereSecond+' '+compareSecond+' '+fieldWhereToSecond 
			}
			if (fieldOrderBy != '')
			{
				addStr += ' ORDER BY '+fieldOrderBy+' '+orderType
			}
			if (fieldOrderBySecond != '' && fieldOrderBy != '')
			{
				addStr += ', '+fieldOrderBySecond+' '+orderTypeSecond
			}
		}
	}

	if (x == 10)
	{
		var fieldCat = document.getElementById('fieldCat').value;
		if (tableList == '' || fieldID == '' || fieldName == '' || fieldCat == '' || fieldID == fieldName || fieldID == fieldCat || fieldName == fieldCat)
		{
			alert("Selected ID Field, Name Field or Category Field are not correct")
			return false;
		}else
		{
			addStr = 'SELECT '+fieldID+', '+fieldName+', '+fieldCat+' FROM '+ tableList
				+ ' ORDER BY '+fieldCat
		}
	}

	if (x == 3)
	{
		var distinct = document.getElementById('distinct').value;
		if (tableList == '' || distinct == '')
		{
			alert("Selected distinct value is not correct")
			return false;
		}else{
			addStr = 'SELECT DISTINCT '+distinct+' FROM '+ tableList
			if (fieldWhere != '' && compare != '')
			{
				if (fieldWhereTo == '' || fieldWhereTo == '""' || fieldWhereTo == '\'\''){fieldWhereTo = "\"\"\"\""}
				addStr += ' WHERE '+fieldWhere+' '+compare+' '+fieldWhereTo 
			}
			if (fieldOrderBy != '')
			{
				addStr += ' ORDER BY '+fieldOrderBy+' '+orderType
			}
		}
	}

	if (x == 11)
	{
		var firstField = document.getElementById('firstField').value;
		var secondField = document.getElementById('secondField').value;
		var firstField1 = document.getElementById('firstField1').value;
		var secondField1 = document.getElementById('secondField1').value;
		var relatedID_Name = document.getElementById('relatedID_Name').value;
		var currentID_Name = document.getElementById('currentID_Name').value;

		if (firstField == '' || secondField == '' || firstField1 == '' || secondField1 == '')
		{
			alert("Selected Pair Fields are not correct")
			return false;
		}else{
			addStr = 'SELECT '+ relatedID_Name +' FROM '+ tableList
				+' WHERE '+ firstField +' = ? AND '+ secondField +' = ?;'
			addStr += 'INSERT INTO '+ tableList +' ('+ firstField +','+ secondField +')'
				+' VALUES(?,?);'+ firstField1 +';'+ secondField1 +';'+ currentID_Name
		}
	}
	if (x == 12)
	{
		var relatedField = document.getElementById('relatedField').value;
		var thisField = document.getElementById('thisField').value;
		var relatedID = document.getElementById('relatedID').value;
		var relatedName = document.getElementById('relatedName').value;
		if (relatedField == '' || thisField == '' || relatedID == '' || relatedName == '')
		{
			alert("Selected Field Relations are not correct")
			return false;
		}else{
			addStr = 'UPDATE '+ tableList +' SET '+ relatedField +' = ?'
			+' WHERE '+ relatedID +' = ?; '+ thisField +'; '+ relatedName
		}
	}

	var output = document.getElementById(currentTextarea);
	output.value = "";
	output.value += addStr;
}
