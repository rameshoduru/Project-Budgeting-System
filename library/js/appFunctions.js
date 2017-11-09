var getDataResp = "";
var getLinksResp = "";

function getData()
{

}

function wtbGanttView()
{

}

function mppGanttView()
{

}

function mcpGanttView()
{

}

function mtpGanttView()
{

}

function wtpGanttView()
{

}

function getLinks()
{

}

function processDataLinks( docXML )
{

}

function fnNew()
{
	location.href = "./pbsHome.php";
}


function fnEdit()
{
	$("#projHideOpa").height($(window).height());
	$("#projHideOpa").width($(window).width());
	var selectedEffect = "fade";
	var options = { percent: 100 };
	$( "#projHideOpa" ).show( selectedEffect , options , 500);
	window.setTimeout("fnEditExt()" , 500)	
}

function fnEditExt()
{
	$("#projMTData").width($(window).width()/2);	
	$("#projMTData").css({left: $(window).width()/4});
	var selectedEffect = "blind";
	var options = { percent: 100 };
	$( "#projMTData" ).show( selectedEffect , options , 500);
}

function mtDataUpdate( varJSON )
{
	alert("Project meta data updated, please submit for the changes to reflect in the functionalities!")
	
	var resType = "";
	var resName = "";
	var resQnty = "";
	var resUnit = "";
	
	varJSON = JSON.parse ( varJSON )
	for( i=0 ; i<varJSON.length ; i++)
	{
		if (resType == "")
		{
			resType = varJSON[i].type;
			resName = varJSON[i].name;
			resQnty = varJSON[i].quantity;
			resUnit = varJSON[i].unit;
		}
		else
		{
			resType = resType + "@^@" + varJSON[i].type;
			resName = resName + "@^@" + varJSON[i].name;
			resQnty = resQnty + "@^@" + varJSON[i].quantity;
			resUnit = resUnit + "@^@" + varJSON[i].unit;
		}
	}
	docProj.txResourceType.value = resType
	docProj.txResourceName.value = resName
	docProj.txResourceQuantity.value = resQnty
	docProj.txResourceUnit.value = resUnit
}


var formatedDate
function stringToDate(_date,_format,_delimiter)
{
	var formatLowerCase=_format.toLowerCase();
	var formatItems=formatLowerCase.split(_delimiter);
	var dateItems=_date.split(_delimiter);
	var monthIndex=formatItems.indexOf("mm");
	var dayIndex=formatItems.indexOf("dd");
	var yearIndex=formatItems.indexOf("yyyy");
	var month=parseInt(dateItems[monthIndex]);
	month-=1;
	formatedDate = new Date(dateItems[yearIndex],month,dateItems[dayIndex]);
	return formatedDate;
}

function fnSubmit2()
{
		alert("saveJSONData");
}

	var tskList = "";
	
function fnSubmit()
{
	document.getElementById("btnSubmitEnable").style.display = "none";
	document.getElementById("btnSubmitDisabled").style.display = "inline";
	
	// This will reset the assigned resources in the project_resources table. 'resource_assigned_master' --> 0
	var strAgtPath = "/pbtsystem/library/ajax/reset_resource_assigned_master.php?pid=" + docProj.txProjectID.value;
	var agtArgs = "";
	$.ajax({url:strAgtPath, type:"POST", async:false, processData:false, data:agtArgs, dataType:'text'}); 
	//--->
	
	var projEDT;
	var baseLN = parseInt( docProj.numBaseLineNumber.value , 10 ) + 1
	docProj.numBaseLineNumber.value = baseLN.toString()

	var saveJSONData = "";
	var users_data = gantt.serialize();
	for(i = 0 ; i < users_data.data.length; i++)
	{ 
		saveJSONData = "";		
		if (users_data.data[i].type == undefined){
			saveJSONData = saveJSONData + "txTaskType~!^task@$@";
		}
		else{
			saveJSONData = saveJSONData + "txTaskType~!^" + users_data.data[i].type + "@$@";
		}
		
		if(users_data.data[i].type =="project"){
			var	numTaskDuration = users_data.data[i].duration;
			var	dtTPED = users_data.data[i].end_date;
		}
		
		var varDTVal = users_data.data[i].start_date;
		start_date = varDTVal.split( " " )[0];
				
		var varDTVal = users_data.data[i].end_date;
		end_date = varDTVal.split( " " )[0];

		saveJSONData = saveJSONData + "txProjectID~!^" + docProj.txProjectID.value + "@$@";
		saveJSONData = saveJSONData + "numBaseLineNumber~!^" + docProj.numBaseLineNumber.value + "@$@";		
		saveJSONData = saveJSONData + "txTaskID~!^" + users_data.data[i].id + "@$@";
		saveJSONData = saveJSONData + "txTaskName~!^" + users_data.data[i].text + "@$@";
		saveJSONData = saveJSONData + "txTaskParent~!^" + users_data.data[i].parent + "@$@";
		saveJSONData = saveJSONData + "dtTPSD~!^" + start_date + "@$@";
		saveJSONData = saveJSONData + "dtTPED~!^" + end_date + "@$@";
		saveJSONData = saveJSONData + "numTaskDuration~!^" + users_data.data[i].duration + "@$@";		
		saveJSONData = saveJSONData + "proj_manager~!^" + docProj.proj_manager.value + "@$@";
		saveJSONData = saveJSONData + "has_child~!^" + users_data.data[i].has_child + "@$@";
		
		if (users_data.data[i].manpowerw != undefined)
			saveJSONData = saveJSONData + "numManPowerWght~!^" + users_data.data[i].manpowerw + "@$@";
		
		if (users_data.data[i].machineryw != undefined)
			saveJSONData = saveJSONData + "numMachinaryWght~!^" + users_data.data[i].machineryw + "@$@";
		
		if (users_data.data[i].materialw != undefined)
			saveJSONData = saveJSONData + "numMaterialWght~!^" + users_data.data[i].materialw + "@$@";
		
		var resourceJSON = "";
		resourceJSON = resourceJSON + "txTaskID~!^" + users_data.data[i].id + "@$@";
		resourceJSON = resourceJSON + "txTaskName~!^" + users_data.data[i].text + "@$@";
		resourceJSON = resourceJSON + "txProjectID~!^" + docProj.txProjectID.value + "@$@";
		resourceJSON = resourceJSON + "numBaseLineNumber~!^" + docProj.numBaseLineNumber.value + "@$@";
		resourceJSON = resourceJSON + "txTaskParent~!^" + users_data.data[i].parent;
				
		var manpowerJSON = "";	
		if( users_data.data[i].manpowerr != undefined)
		{
			var manpowerAdditionSave = users_data.data[i].manpowerr;
			var dataType = "";
			var dataName = "";
			var dataCat = "";
			var dataUnit = "";
			var dataQnty = "";
			for ( k=0; k < manpowerAdditionSave.length ; k++ )
			{
				dataType = manpowerAdditionSave[k].typemp;
				dataName = manpowerAdditionSave[k].namemp;
				dataCat = manpowerAdditionSave[k].catmp;
				dataUnit = manpowerAdditionSave[k].unitmp;
				dataQnty = manpowerAdditionSave[k].qntymp;
				
				if (dataType == "")
				{
					manpowerJSON = "txResTable~!^task_manpower_consumed"+ "@$@" + resourceJSON + "@$@";	
					manpowerJSON = manpowerJSON + "txResType~!^" + dataType + "@$@";
					manpowerJSON = manpowerJSON + "txResName~!^" + dataName + "@$@";
					manpowerJSON = manpowerJSON + "txResCat~!^" + dataCat + "@$@";
					manpowerJSON = manpowerJSON + "txResUnit~!^" + dataUnit + "@$@";
					manpowerJSON = manpowerJSON + "txResQnty~!^" + dataQnty;
				}
				else
				{
					manpowerJSON = manpowerJSON + "*$~$*" + "txResTable~!^task_manpower_consumed"+ "@$@" + resourceJSON + "@$@";	
					manpowerJSON = manpowerJSON + "txResType~!^" + dataType + "@$@";
					manpowerJSON = manpowerJSON + "txResName~!^" + dataName + "@$@";
					manpowerJSON = manpowerJSON + "txResCat~!^" + dataCat + "@$@";
					manpowerJSON = manpowerJSON + "txResUnit~!^" + dataUnit + "@$@";
					manpowerJSON = manpowerJSON + "txResQnty~!^" + dataQnty;
				}
			}
		}
		
		var workdoneJSON = "";
		if (users_data.data[i].worktyper != undefined)
		{
			var workAdditionSave = users_data.data[i].worktyper;
			var dataType = "";
			var dataName = "";
			var dataCat = "";
			var dataUnit = "";
			var dataQnty = "";
			
			for ( k=0; k < workAdditionSave.length ; k++ )
			{
				dataType = workAdditionSave[k].typewt;
				dataName = workAdditionSave[k].namewt;
				dataCat = workAdditionSave[k].catwt;
				dataUnit = workAdditionSave[k].unitwt;
				dataQnty = workAdditionSave[k].qntywt;
					
				if (dataType == "")
				{
					workdoneJSON = "txResTable~!^task_work_consumed"+ "@$@" + resourceJSON + "@$@";		
					workdoneJSON = workdoneJSON + "txResType~!^" + dataType + "@$@";
					workdoneJSON = workdoneJSON + "txResName~!^" + dataName + "@$@";
					workdoneJSON = workdoneJSON + "txResCat~!^" + dataCat + "@$@";
					workdoneJSON = workdoneJSON + "txResUnit~!^" + dataUnit + "@$@";
					workdoneJSON = workdoneJSON + "txResQnty~!^" + dataQnty;
				}
				else
				{
					workdoneJSON = workdoneJSON + "*$~$*" + "txResTable~!^task_work_consumed"+ "@$@" + resourceJSON + "@$@";	
					workdoneJSON = workdoneJSON + "txResType~!^" + dataType + "@$@";
					workdoneJSON = workdoneJSON + "txResName~!^" + dataName + "@$@";
					workdoneJSON = workdoneJSON + "txResCat~!^" + dataCat + "@$@";
					workdoneJSON = workdoneJSON + "txResUnit~!^" + dataUnit + "@$@";
					workdoneJSON = workdoneJSON + "txResQnty~!^" + dataQnty;
				}
			}
		}

		var machinaryJSON = "";		
		if(users_data.data[i].machineryr != undefined)
		{
			var machineryAdditionSave = users_data.data[i].machineryr;
			var dataType = "";
			var dataName = "";
			var dataCat = "";
			var dataUnit = "";
			var dataQnty = "";
			
			for ( k=0; k < machineryAdditionSave.length ; k++ )
			{
				dataType = machineryAdditionSave[k].typemc;
				dataName = machineryAdditionSave[k].namemc;
				dataCat = machineryAdditionSave[k].catmc;
				dataUnit = machineryAdditionSave[k].unitmc;
				dataQnty = machineryAdditionSave[k].qntymc;
				
				if (dataType == "")
				{
					machinaryJSON = "txResTable~!^task_machinery_consumed"+ "@$@" + resourceJSON + "@$@";	
					machinaryJSON = machinaryJSON + "txResType~!^" + dataType + "@$@";
					machinaryJSON = machinaryJSON + "txResName~!^" + dataName + "@$@";
					machinaryJSON = machinaryJSON + "txResCat~!^" + dataCat + "@$@";
					machinaryJSON = machinaryJSON + "txResUnit~!^" + dataUnit + "@$@";
					machinaryJSON = machinaryJSON + "txResQnty~!^" + dataQnty;
				}
				else
				{
					machinaryJSON = machinaryJSON + "*$~$*" + "txResTable~!^task_machinery_consumed"+ "@$@" + resourceJSON + "@$@";
					machinaryJSON = machinaryJSON + "txResType~!^" + dataType + "@$@";
					machinaryJSON = machinaryJSON + "txResName~!^" + dataName + "@$@";
					machinaryJSON = machinaryJSON + "txResCat~!^" + dataCat + "@$@";
					machinaryJSON = machinaryJSON + "txResUnit~!^" + dataUnit + "@$@";
					machinaryJSON = machinaryJSON + "txResQnty~!^" + dataQnty;
				}
			}
		}

		var materialJSON = "";
		if(users_data.data[i].materialr != undefined)
		{
			var materialAdditionSave = users_data.data[i].materialr;
			var dataType = "";
			var dataName = "";
			var dataCat = "";
			var dataUnit = "";
			var dataQnty = "";
			
			for ( k=0; k < materialAdditionSave.length ; k++ )
			{
				dataType = materialAdditionSave[k].typemt;
				dataName = materialAdditionSave[k].namemt;
				dataCat = materialAdditionSave[k].catmt;
				dataUnit = materialAdditionSave[k].unitmt;
				dataQnty = materialAdditionSave[k].qntymt;
					
				if (dataType == "")
				{	
					materialJSON = "txResTable~!^task_material_consumed"+ "@$@" + resourceJSON + "@$@";			
					materialJSON = materialJSON + "txResType~!^" + dataType + "@$@";
					materialJSON = materialJSON + "txResName~!^" + dataName + "@$@";
					materialJSON = materialJSON + "txResCat~!^" + dataCat + "@$@";
					materialJSON = materialJSON + "txResUnit~!^" + dataUnit + "@$@";
					materialJSON = materialJSON + "txResQnty~!^" + dataQnty ;			
				}
				else
				{
					materialJSON = materialJSON + "*$~$*" + "txResTable~!^task_material_consumed"+ "@$@" + resourceJSON + "@$@";
					materialJSON = materialJSON + "txResType~!^" + dataType + "@$@";
					materialJSON = materialJSON + "txResName~!^" + dataName + "@$@";
					materialJSON = materialJSON + "txResCat~!^" + dataCat + "@$@";
					materialJSON = materialJSON + "txResUnit~!^" + dataUnit + "@$@";
					materialJSON = materialJSON + "txResQnty~!^" + dataQnty ;			
				}
			}
		}		

		if(users_data.data[i].type == "project")
		{
			saveJSONData = saveJSONData + "txResourceType~!^" + docProj.txResourceType.value + "@$@";
			saveJSONData = saveJSONData + "txResourceName~!^" + docProj.txResourceName.value + "@$@";
			saveJSONData = saveJSONData + "txResourceQuantity~!^" + docProj.txResourceQuantity.value + "@$@";
			saveJSONData = saveJSONData + "txResourceUnit~!^" + docProj.txResourceUnit.value + "@$@";			
		}
		saveJSONData = saveJSONData + "*~*@$@" + workdoneJSON + manpowerJSON + materialJSON + machinaryJSON;
		
		if(tskList == "" && i > 0  )
		{
			tskList = users_data.data[i].id;
		}
		else if( i > 0 )
		{
			tskList = tskList + "~" + users_data.data[i].id;
		}
//prompt("", tskList );
		var strAgtPath = "/pbtsystem/library/save_data.php?saveJSONData=" + saveJSONData;
		var agtArgs = saveJSONData;
		$.ajax({url:strAgtPath, type:"POST", async:false, processData:false, data:agtArgs, dataType:'text'});
		
	}
	
	//--- remove existing links...#414
	var saveJSONLinks = "";
	var strAgtPath = "/pbtsystem/library/ajax/remove_links.php?projid=" + docProj.txProjectID.value;
	$.ajax({url:strAgtPath, type:"POST", async:false, processData:false, data:agtArgs, dataType:'text'});

	
	for(i = 0 ; i<users_data.links.length; i++)
	{
		saveJSONLinks = "";
		saveJSONLinks = saveJSONLinks + "txProjectID~!^" + docProj.txProjectID.value + "@$@";
		saveJSONLinks = saveJSONLinks + "numBaseLineNumber~!^" + docProj.numBaseLineNumber.value + "@$@";		
		saveJSONLinks = saveJSONLinks + "txLinkID~!^" + users_data.links[i].id + "@$@";
		saveJSONLinks = saveJSONLinks + "txSourceID~!^" + users_data.links[i].source + "@$@";
		saveJSONLinks = saveJSONLinks + "txTargetID~!^" + users_data.links[i].target + "@$@";
		saveJSONLinks = saveJSONLinks + "txLinkType~!^" + users_data.links[i].type + "@$@";
		saveJSONLinks = saveJSONLinks + "numTaskDuration~!^" + numTaskDuration + "@$@";
		saveJSONLinks = saveJSONLinks + "dtTPED~!^" + dtTPED + "@$@";
		
		//below code is to minimize the hits to project table --- save_links.php
		saveJSONLinks = saveJSONLinks + "i~!^" + i.toString() + "@$@";
		var j = users_data.links.length-1;
		saveJSONLinks = saveJSONLinks + "j~!^" + j.toString() + "@$@";
		var agtArgs = "frmLinks#$#" + saveJSONLinks;
		var strAgtPath = "/pbtsystem/library/save_links.php?saveJSONLinks=" + saveJSONLinks;
		$.ajax({url:strAgtPath, type:"POST", async:false, processData:false, data:agtArgs, dataType:'text'});
	}
	
	//Updating/deleting task/resource list in tasks_master table	
	var strRemTasksPath = "/pbtsystem/library/ajax/remove_tasks.php?pid=" + docProj.txProjectID.value +"&tskList="+tskList;
	var agtArgs = "";	
	if(tskList != "")
	{
		$.ajax({url:strRemTasksPath, type:"POST", async:false, processData:false, data:agtArgs, dataType:'text'});
	}
	
}


function monthlyGanttView()
{
	gantt.config.scale_unit = "month";
	gantt.config.date_scale = "%F, %Y";
	gantt.config.subscales = [{unit:"week", step:1, date:"Week #%W"}];
	gantt.render();
}

function dailyGanttView()
{
	gantt.config.scale_unit = "day"; 
	gantt.config.date_scale = "%d %M"; 
	gantt.templates.scale_cell_class = function(date){
	    if(date.getDay()==0||date.getDay()==6){
	        return "weekend";
	    }
	};	
	gantt.config.subscales = [{unit:"week", step:1, date:"Week #%W"}];
	gantt.render();
}

function weeklyGanttView()
{
	gantt.config.scale_unit = "week";
	gantt.config.date_scale = "Week #%W";
	gantt.config.subscales = [{unit:"month", step:1, date:"%F, %Y"}];
	gantt.render();
}

function getInnerText (node) {
	if (typeof node.textContent != 'undefined')
	{
		return node.textContent;
	}
	else if (typeof node.innerText != 'undefined')
	{
		return node.innerText;
	}
	else if (typeof node.text != 'undefined')
	{
		return node.text;
	}
	else
	{
		switch (node.nodeType)
		{
			case 3:
			case 4:
				return node.nodeValue;
				break;
			case 1:
			case 11:
				var innerText = '';
				for (var i = 0; i < node.childNodes.length; i++)
				{
					innerText += getInnerText(node.childNodes[i]);
				}
				return innerText;
				break;
			default:
				return '';
		}
	}
}


