function addMaterial()
{
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	if (xhttp.readyState == 4 && xhttp.status == 200) {

			processAddMaterial( xhttp.responseText );
		}
	};
	xhttp.open("GET", "../ajax/getResDetails.php?projid=" + docProj.txProjectID.value, true);
	xhttp.send();
}

function processAddMaterial( resDetail )
{
	var strHTMLRet = "";
	try{
		var mattDetails = resDetail.split("@~@");
		
		if( mattDetails[0].length == 0 )
		{
			alert("Please define resource meta data, before adding resources to the task!")
		}
		else
		{		
			if( mattDetails[0].length > 1 )
			{
				resType = mattDetails[0].split("@^@");
				resName = mattDetails[1].split("@^@");
				resCat = mattDetails[2].split("@^@");
				resQnty = mattDetails[3].split("@^@");
				resUnit = mattDetails[4].split("@^@");

				var tempCategory =new Array(); 
				var m=0;				
				for (k=0; k<resType.length; k++)
				{
					
					if( resType[k] == "Material" ){
						
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
			strHTMLRet =  "<div class='panel-group' id='accordionMT'>";
			
				$.each(tempCategory, function(index, value) {
				 strHTMLRet =  strHTMLRet + accordionMenuMT(value);
				});				
				strHTMLRet = strHTMLRet + "</div>";
				document.getElementById("divAddMTCont").innerHTML = strHTMLRet;
			}
			addMaterialExt()
		}
	}
	catch(errorStr){
		alert("Error: " + errorStr.message + " while retrieving data.")
		return "";
	}
}

function accordionMenuMT( tempCat){
	var divId = "MT"+tempCat;
	var strHTMLRet = "<div class='panel panel-default'><div class='panel-heading'><h4 class='panel-title'><a data-toggle='collapse' data-parent='#accordionMT' href='"+"#"+divId+"'>"+tempCat+"</a></h4></div><div id="+divId+" class='panel-collapse collapse'><div class='panel-body'>";
	
	var strHTMLRet = strHTMLRet + "<table class='table'><thead><tr><th></th><th></th><th></th><th>Name</th><th>Unit</th><th>Quantity</th><th>Available Qnty</th></tr></thead><tbody>";
	
		for (j=0 ; j<resType.length ; j++)
		{
			if( resType[j] == "Material" && tempCat == resCat[j] )
			{
				strHTMLRet = strHTMLRet + "<tr><td><label class='checkbox-inline'><input type='checkbox' value='" + j + "' " + makeItemSelectedMT(resType[j] , resName[j] , resUnit[j]) + " /></label></td><td><input id='Type" + j + "' type='hidden' value=" + resType[j] + "></td></td><td><input type='hidden' id='Cat" + j + "' value=" + resCat[j] + "></td><td id='Name" + j + "'>" + resName[j] + "</td><td id='Unit" + j + "'>" + resUnit[j] + "</td><td><input id='Qnty" + j + "' type='text' maxlength='6' size='3' value='" + populateItemQntyMT(resType[j] , resName[j] , resUnit[j]) + "'/></td><td><input id='avQnty" + j + "' type='text' maxlength='6' size='3' value='" + populateItemAvlQntyMT(resType[j] , resName[j] , resUnit[j]) + "' READONLY/></td></tr>"
			}
		}
				
	strHTMLRet = strHTMLRet + "</tbody></table>";
	strHTMLRet =  strHTMLRet + "</div></div></div>";
	return strHTMLRet;
}


function makeItemSelectedMT( type , name , unit )
{
	for(i=0 ; i<materialAddition.length ; i++)
	{
		if(type == materialAddition[i].typemt && name == materialAddition[i].namemt && unit == materialAddition[i].unitmt)
		{
			return "checked=checked";
		}
	}
	return "";
}

function populateItemQntyMT( type , name , unit )
{
	for(i=0 ; i<materialAddition.length ; i++)
	{
		if(type == materialAddition[i].typemt && name == materialAddition[i].namemt && unit == materialAddition[i].unitmt)
		{
			return materialAddition[i].qntymt;
		}
	}
	return "";
}

function populateItemAvlQntyMT( type , name , unit )
{
	for(i=0 ; i<materialAddition.length ; i++)
	{
		if(type == materialAddition[i].typemt && name == materialAddition[i].namemt && unit == materialAddition[i].unitmt)
		{
			return materialAddition[i].avqntymt;
		}
	}
	return "";
}

var materialAddition = "";
function mpMaterialUpdate()
{
	var allCBs = document.getElementById("divAddMTCont").getElementsByTagName("input")
	var retJSON = "";
	for ( i=0 ; i<allCBs.length ; i++ )
	{
		if ( allCBs[i].type == "checkbox")
		{
			if(allCBs[i].checked == true)
			{
				var indexVal = allCBs[i].value
				if ( retJSON != "" ) { retJSON = retJSON + " , "}
				retJSON = retJSON + "{\"typemt\" : " + "\"" + document.getElementById("Type" + indexVal).value + "\" , ";
				retJSON = retJSON + "\"catmt\" : " + "\"" + document.getElementById("Cat" + indexVal).value + "\" , ";
				retJSON = retJSON + "\"namemt\" : " + "\"" + document.getElementById("Name" + indexVal).innerHTML + "\" , ";				
				retJSON = retJSON + "\"unitmt\" : " + "\"" + document.getElementById("Unit" + indexVal).innerHTML + "\" , ";
				retJSON = retJSON + "\"qntymt\" : " + "\"" + document.getElementById("Qnty" + indexVal).value + "\"}";
			}
		}
	}

	document.getElementById("divAddMTCont").innerHTML = "";
	materialAddition = JSON.parse("[" + retJSON + "]")
	mpMaterialCancel()
}

function addMaterialExt()
{
	$("#projHideOpa").height($(window).height());
	$("#projHideOpa").width($(window).width());
	var selectedEffect = "fade";
	var options = { percent: 100 };
	$( "#projHideOpa" ).show( selectedEffect , options , 500);
	$(".dhx_cal_light").hide( selectedEffect , options , 500);
	$(".dhx_cal_cover").hide( selectedEffect , options , 500);
	window.setTimeout("addMaterialExtNxt()" , 500)	
}

function addMaterialExtNxt()
{
	$("#addMaterialDIV").width($(window).width()/2);	
	$("#addMaterialDIV").css({left: $(window).width()/4});
	var selectedEffect = "blind";
	var options = { percent: 100 };
	$( "#addMaterialDIV" ).show( selectedEffect , options , 500);
}

function mpMaterialCancel()
{
	var selectedEffect = "fade";
	var options = { percent: 100 };
	$( "#addMaterialDIV" ).hide( selectedEffect , options , 500);
	
	var options = { percent: 100 };
	$( "#projHideOpa" ).hide( selectedEffect , options , 500);
	
	$(".dhx_cal_light").show( selectedEffect , options , 500);
	$(".dhx_cal_cover").show( selectedEffect , options , 500);
}