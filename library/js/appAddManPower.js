function addManPower()
{
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	if (xhttp.readyState == 4 && xhttp.status == 200) {
			processAddManpower( xhttp.responseText );
		}
	};
	xhttp.open("GET", "../ajax/getResDetails.php?projid=" + docProj.txProjectID.value, true);
	xhttp.send();
}

function processAddManpower( resDetail )
{
	var strHTMLRet = "";
	try{
		var mpowerDetails = resDetail.split("@~@");
		
		if( mpowerDetails[0].length == 0 )
		{
			alert("Please define resource meta data, before adding resources to the task!")
		}
		else
		{		
			if( mpowerDetails[0].length > 1 )
			{
				resType = mpowerDetails[0].split("@^@");
				resName = mpowerDetails[1].split("@^@");
				resCat = mpowerDetails[2].split("@^@");
				resQnty = mpowerDetails[3].split("@^@");
				resUnit = mpowerDetails[4].split("@^@");
				
				var tempCategory =new Array(); 
				var m=0;				
				for (k=0; k<resType.length; k++)
				{
					
					if( resType[k] == "Manpower" ){
						
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
			strHTMLRet =  "<div class='panel-group' id='accordionMP'>";
			
				$.each(tempCategory, function(index, value) {
				 strHTMLRet =  strHTMLRet + accordionMenuMP(value);
				});

				strHTMLRet = strHTMLRet + "</div>"
				document.getElementById("divAddMPCont").innerHTML = strHTMLRet;
			}
			addManPowerExt()
		}
	}
	catch(errorStr){
		alert("Error: " + errorStr.message + " while retrieving data.")
		return "";
	}
}

function accordionMenuMP( tempCat){
	var divId = "MP"+tempCat;
	var strHTMLRet = "<div class='panel panel-default'><div class='panel-heading'><h4 class='panel-title'><a data-toggle='collapse' data-parent='#accordionMP' href='"+"#"+divId+"'>"+tempCat+"</a></h4></div><div id="+divId+" class='panel-collapse collapse'><div class='panel-body'>";
	
	var strHTMLRet = strHTMLRet + "<table class='table'><thead><tr><th></th><th></th><th></th><th>Name</th><th>Unit</th><th>Quantity</th><th>Available Qnty</th></tr></thead><tbody>";
	
		for (j=0 ; j<resType.length ; j++)
		{
			if( resType[j] == "Manpower" && tempCat == resCat[j] )
			{
				strHTMLRet = strHTMLRet + "<tr><td><label class='checkbox-inline'><input type='checkbox' value='" + j + "' " + makeItemSelectedMP(resType[j] , resName[j] , resUnit[j]) + " /></label></td><td><input id='Type" + j + "' type='hidden' value=" + resType[j] + "></td><td><input type='hidden' id='Cat" + j + "' value=" + resCat[j] + "></td><td id='Name" + j + "'>" + resName[j] + "</td><td id='Unit" + j + "'>" + resUnit[j] + "</td><td><input id='Qnty" + j + "' type='text' maxlength='6' size='3' value='" + populateItemQntyMP(resType[j] , resName[j] , resUnit[j]) + "'/></td><td><input id='avQnty" + j + "' type='text' maxlength='6' size='3' value='" + populateItemAvlQntyMP(resType[j] , resName[j] , resUnit[j]) + "' READONLY/></td></tr>";
			}
		}
				
	strHTMLRet = strHTMLRet + "</tbody></table>";
	strHTMLRet =  strHTMLRet + "</div></div></div>";
	return strHTMLRet;
}


function makeItemSelectedMP( type , name , unit )
{
	for(i=0 ; i<manpowerAddition.length ; i++)
	{
		if(type == manpowerAddition[i].typemp && name == manpowerAddition[i].namemp && unit == manpowerAddition[i].unitmp)
		{
			return "checked=checked";
		}
	}
	return "";
}

function populateItemQntyMP( type , name , unit )
{
	for(i=0 ; i<manpowerAddition.length ; i++)
	{
		if(type == manpowerAddition[i].typemp && name == manpowerAddition[i].namemp && unit == manpowerAddition[i].unitmp)
		{
			return manpowerAddition[i].qntymp;
		}
	}
	return "";
}

function populateItemAvlQntyMP( type , name , unit )
{
	for(i=0 ; i<manpowerAddition.length ; i++)
	{
		if(type == manpowerAddition[i].typemp && name == manpowerAddition[i].namemp && unit == manpowerAddition[i].unitmp)
		{
			return manpowerAddition[i].avqntymp;
		}
	}
	return "";
}

var manpowerAddition = "";
function mpManpowerUpdate()
{
	var allCBs = document.getElementById("divAddMPCont").getElementsByTagName("input")
	var retJSON = "";
	for ( i=0 ; i<allCBs.length ; i++ )
	{
		if ( allCBs[i].type == "checkbox")
		{
			if(allCBs[i].checked == true)
			{
				var indexVal = allCBs[i].value
				if ( retJSON != "" ) { retJSON = retJSON + " , "}
				retJSON = retJSON + "{\"typemp\" : " + "\"" + document.getElementById("Type" + indexVal).value + "\" , ";
				retJSON = retJSON + "\"catmp\" : " + "\"" + document.getElementById("Cat" + indexVal).value + "\" , ";
				retJSON = retJSON + "\"namemp\" : " + "\"" + document.getElementById("Name" + indexVal).innerHTML + "\" , ";
				retJSON = retJSON + "\"unitmp\" : " + "\"" + document.getElementById("Unit" + indexVal).innerHTML + "\" , ";
				retJSON = retJSON + "\"qntymp\" : " + "\"" + document.getElementById("Qnty" + indexVal).value + "\"}";
			}
		}
	}

	document.getElementById("divAddMPCont").innerHTML = "";
	manpowerAddition = JSON.parse("[" + retJSON + "]")
	mpManpowerCancel()
}

function addManPowerExt()
{
	$("#projHideOpa").height($(window).height());
	$("#projHideOpa").width($(window).width());
	var selectedEffect = "fade";
	var options = { percent: 100 };
	$( "#projHideOpa" ).show( selectedEffect , options , 500);
	$(".dhx_cal_light").hide( selectedEffect , options , 500);
	$(".dhx_cal_cover").hide( selectedEffect , options , 500);
	window.setTimeout("addManPowerExtNxt()" , 500)	
}

function addManPowerExtNxt()
{
	$("#addManPowerDIV").width($(window).width()/2);	
	$("#addManPowerDIV").css({left: $(window).width()/4});
	var selectedEffect = "blind";
	var options = { percent: 100 };
	$( "#addManPowerDIV" ).show( selectedEffect , options , 500);
}

function mpManpowerCancel()
{
	var selectedEffect = "fade";
	var options = { percent: 100 };
	$( "#addManPowerDIV" ).hide( selectedEffect , options , 500);
	
	var options = { percent: 100 };
	$( "#projHideOpa" ).hide( selectedEffect , options , 500);
	
	$(".dhx_cal_light").show( selectedEffect , options , 500);
	$(".dhx_cal_cover").show( selectedEffect , options , 500);
}