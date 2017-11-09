function addMachinery()
{	
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	if (xhttp.readyState == 4 && xhttp.status == 200) {
			processAddMachinary( xhttp.responseText );
		}
	};
	xhttp.open("GET", "../ajax/getResDetails.php?projid=" + docProj.txProjectID.value, true);
	xhttp.send();
}

function processAddMachinary( resDetail )
{
	var strHTMLRet = "";
	try{
		var machDetails = resDetail.split("@~@");
		
		if( machDetails[0].length == 0 )
		{
			alert("Please define resource meta data, before adding resources to the task!")
		}
		else
		{		
			if( machDetails[0].length > 1 )
			{
				resType = machDetails[0].split("@^@");
				resName = machDetails[1].split("@^@");
				resCat = machDetails[2].split("@^@");
				resQnty = machDetails[3].split("@^@");
				resUnit = machDetails[4].split("@^@");
				
				var tempCategory =new Array(); 
				var m=0;				
				for (k=0; k<resType.length; k++)
				{
					
					if( resType[k] == "Machinery" ){
						
						 tempCategory[m] = resCat[k];	
							m = m+1;							
					}
				}
	//Creating unique arry of categories for accordin menu			
				Array.prototype.unique = function () {
				var arr = this;
				return $.grep(arr, function (v, i) {
					return $.inArray(v, arr) === i; });
				}
				tempCategory = tempCategory.unique();
	//for-each JQuery function 
			strHTMLRet =  "<div class='panel-group' id='accordionMC'>";
			
				$.each(tempCategory, function(index, value) {
				 strHTMLRet =  strHTMLRet + accordionMenuMC(value);
				});				
				strHTMLRet = strHTMLRet + "</div>";
				document.getElementById("divAddMCCont").innerHTML = strHTMLRet;
			}
			addMachinaryExt()
		}
	}
	catch(errorStr){
		alert("Error: " + errorStr.message + " while retrieving data.")
		return "";
	}
}

function accordionMenuMC( tempCat){
	var divId = "MC"+tempCat;
	var strHTMLRet = "<div class='panel panel-default'><div class='panel-heading'><h4 class='panel-title'><a data-toggle='collapse' data-parent='#accordionMC' href='"+"#"+divId+"'>"+tempCat+"</a></h4></div><div id="+divId+" class='panel-collapse collapse'><div class='panel-body'>";
	
	var strHTMLRet = strHTMLRet + "<table class='table'><thead><tr><th></th><th></th><th></th><th>Name</th><th>Unit</th><th>Quantity</th><th>Available Qnty</th></tr></thead><tbody>";
	
		for (j=0 ; j<resType.length ; j++)
		{
			if( resType[j] == "Machinery" && tempCat == resCat[j] )
			{
				strHTMLRet = strHTMLRet + "<tr><td><label class='checkbox-inline'><input type='checkbox' value='" + j + "' " + makeItemSelectedMC(resType[j] , resName[j] , resUnit[j]) + " /></label></td><td><input id='Type" + j + "' type='hidden' value=" + resType[j] + "></td><td><input type='hidden' id='Cat" + j + "' value=" + resCat[j] + "></td><td id='Name" + j + "'>" + resName[j] + "</td><td id='Unit" + j + "'>" + resUnit[j] + "</td><td><input id='Qnty" + j + "' type='text' maxlength='6' size='3' value='" + populateItemQntyMC(resType[j] , resName[j] , resUnit[j]) + "'/></td><td><input id='avQnty" + j + "' type='text' maxlength='6' size='3' value='" + populateItemAvlQntyMC(resType[j] , resName[j] , resUnit[j]) + "' READONLY/></td></tr>"
			}
		}
				
	strHTMLRet = strHTMLRet + "</tbody></table>";
	strHTMLRet =  strHTMLRet + "</div></div></div>";
	return strHTMLRet;
}

function makeItemSelectedMC( type , name , unit )
{
	for(i=0 ; i<machineryAddition.length ; i++)
	{
		if(type == machineryAddition[i].typemc && name == machineryAddition[i].namemc && unit == machineryAddition[i].unitmc)
		{
			return "checked=checked";
		}
	}
	return "";
}

function populateItemQntyMC( type , name , unit )
{
	for(i=0 ; i<machineryAddition.length ; i++)
	{
		if(type == machineryAddition[i].typemc && name == machineryAddition[i].namemc && unit == machineryAddition[i].unitmc)
		{
			return machineryAddition[i].qntymc;
		}
	}
	return "";
}

function populateItemAvlQntyMC( type , name , unit )
{
	for(i=0 ; i<machineryAddition.length ; i++)
	{
		if(type == machineryAddition[i].typemc && name == machineryAddition[i].namemc && unit == machineryAddition[i].unitmc)
		{
			return machineryAddition[i].avqntymc;
		}
	}
	return "";
}

var machineryAddition = "";
function mpMachinaryUpdate()
{
	var allCBs = document.getElementById("divAddMCCont").getElementsByTagName("input")
	var retJSON = "";
	for ( i=0 ; i<allCBs.length ; i++ )
	{
		if ( allCBs[i].type == "checkbox")
		{
			if(allCBs[i].checked == true)
			{
				var indexVal = allCBs[i].value
				if ( retJSON != "" ) { retJSON = retJSON + " , "}
				retJSON = retJSON + "{\"typemc\" : " + "\"" + document.getElementById("Type" + indexVal).value + "\" , ";
				retJSON = retJSON + "\"catmc\" : " + "\"" + document.getElementById("Cat" + indexVal).value + "\" , ";
				retJSON = retJSON + "\"namemc\" : " + "\"" + document.getElementById("Name" + indexVal).innerHTML + "\" , ";		
				retJSON = retJSON + "\"unitmc\" : " + "\"" + document.getElementById("Unit" + indexVal).innerHTML + "\" , ";
				retJSON = retJSON + "\"qntymc\" : " + "\"" + document.getElementById("Qnty" + indexVal).value + "\"}";
			}
		}
	}

	document.getElementById("divAddMCCont").innerHTML = "";
	machineryAddition = JSON.parse("[" + retJSON + "]")
	mpMachinaryCancel()
}

function addMachinaryExt()
{
	$("#projHideOpa").height($(window).height());
	$("#projHideOpa").width($(window).width());
	var selectedEffect = "fade";
	var options = { percent: 100 };
	$( "#projHideOpa" ).show( selectedEffect , options , 500);
	$(".dhx_cal_light").hide( selectedEffect , options , 500);
	$(".dhx_cal_cover").hide( selectedEffect , options , 500);
	window.setTimeout("addMachinaryExtNxt()" , 500)	
}

function addMachinaryExtNxt()
{
	$("#addMachineryDIV").width($(window).width()/2);	
	$("#addMachineryDIV").css({left: $(window).width()/4});
	var selectedEffect = "blind";
	var options = { percent: 100 };
	$( "#addMachineryDIV" ).show( selectedEffect , options , 500);
}

function mpMachinaryCancel()
{
	var selectedEffect = "fade";
	var options = { percent: 100 };
	$( "#addMachineryDIV" ).hide( selectedEffect , options , 500);
	
	var options = { percent: 100 };
	$( "#projHideOpa" ).hide( selectedEffect , options , 500);
	
	$(".dhx_cal_light").show( selectedEffect , options , 500);
	$(".dhx_cal_cover").show( selectedEffect , options , 500);
}