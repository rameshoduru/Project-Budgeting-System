function addWork()
{
	xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
	if (xhttp.readyState == 4 && xhttp.status == 200) {
			processAddWork( xhttp.responseText );
		}
	};
	xhttp.open("GET", "../ajax/getResDetails.php?projid=" + docProj.txProjectID.value, true);
	xhttp.send();	
}

function processAddWork( resDetail )
{
	var strHTMLRet = "";
	try{
		var workDetails = resDetail.split("@~@");
		
		if( resDetail[0].length == 0 )
		{
			alert("Please define resource meta data, before adding resources to the task!")
		}
		else
		{		
			if( workDetails[0].length > 1 )
			{
				resType = workDetails[0].split("@^@");
				resName = workDetails[1].split("@^@");
				resCat = workDetails[2].split("@^@");
				resQnty = workDetails[3].split("@^@");
				resUnit = workDetails[4].split("@^@");
				
				var tempCategory =new Array(); 
				var m=0;				
				for (k=0; k<resType.length; k++)
				{
					
					if( resType[k] == "Work" ){
						
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
			strHTMLRet =  "<div class='panel-group' id='accordionWT'>";
			
				$.each(tempCategory, function(index, value) {
				 strHTMLRet =  strHTMLRet + accordionMenuWT(value);
				});				
				strHTMLRet = strHTMLRet + "</div>"
				document.getElementById("divAddWTCont").innerHTML = strHTMLRet;
			}
			addWorkExt();
		}
	}
	catch(errorStr){
		alert("Error: " + errorStr.message + " while retrieving data.")
		return "";
	}
}

function accordionMenuWT( tempCat){	
	var divId = "WT"+tempCat;
	var strHTMLRet = "<div class='panel panel-default'><div class='panel-heading'><h4 class='panel-title'><a data-toggle='collapse' data-parent='#accordionWT' href='"+"#"+divId+"'>"+tempCat+"</a></h4></div><div id="+divId+" class='panel-collapse collapse'><div class='panel-body'>";
	
	var strHTMLRet = strHTMLRet + "<table class='table'><thead><tr><th></th><th></th><th></th><th>Name</th><th>Unit</th><th>Quantity</th><th>Available Qnty</th></tr></thead><tbody>";
		
		for (j=0; j<resType.length; j++)
		{
			if( resType[j] == "Work" && tempCat == resCat[j] )
			{	
				strHTMLRet = strHTMLRet + "<tr><td><label class='checkbox-inline'><input type='checkbox' value='" + j + "' " + makeItemSelectedWT(resType[j] , resName[j] , resUnit[j]) + " /></label></td><td><input id='Type" + j + "' type='hidden' value=" + resType[j] + "></td><td><input type='hidden' id='Cat" + j + "' value=" + resCat[j] + "></td><td id='Name" + j + "'>" + resName[j] + "</td><td id='Unit" + j + "'>" + resUnit[j] + "</td><td><input id='Qnty" + j + "' type='text' maxlength='6' size='3' value='" + populateItemQntyWT(resType[j] , resName[j] , resUnit[j]) + "'/></td><td><input id='avQnty" + j + "' type='text' maxlength='6' size='3' value='" + populateItemAvlQntyWT(resType[j] , resName[j] , resUnit[j]) + "' READONLY/></td></tr>"			
			}
		}
		strHTMLRet = strHTMLRet + "</tbody></table>";
		strHTMLRet =  strHTMLRet + "</div></div></div>";
		return strHTMLRet;
}


function makeItemSelectedWT( type , name , unit )
{
	for(i=0 ; i<workAddition.length ; i++)
	{
		if(type == workAddition[i].typewt && name == workAddition[i].namewt && unit == workAddition[i].unitwt)
		{
			return "checked=checked";
		}
	}
	return "";
}

function populateItemQntyWT( type , name , unit )
{
	for(i=0 ; i<workAddition.length ; i++)
	{
		if(type == workAddition[i].typewt && name == workAddition[i].namewt && unit == workAddition[i].unitwt)
		{
			return workAddition[i].qntywt;			
		}
	}
	return "";
}

function populateItemAvlQntyWT( type , name , unit )
{
	for(i=0 ; i<workAddition.length ; i++)
	{
		if(type == workAddition[i].typewt && name == workAddition[i].namewt && unit == workAddition[i].unitwt)
		{
			return workAddition[i].avqntywt;
		}
	}
	return "";
}

var workAddition = "";
function mpWorkUpdate()
{
	var allCBs = document.getElementById("divAddWTCont").getElementsByTagName("input")
	var retJSON = "";
	for ( i=0 ; i<allCBs.length ; i++ )
	{
		if ( allCBs[i].type == "checkbox")
		{
			if(allCBs[i].checked == true)
			{
				var indexVal = allCBs[i].value
				if ( retJSON != "" ) { retJSON = retJSON + " , "}
				retJSON = retJSON + "{\"typewt\" : " + "\"" + document.getElementById("Type" + indexVal).value + "\" , ";
				retJSON = retJSON + "\"catwt\" : " + "\"" + document.getElementById("Cat" + indexVal).value + "\" , ";
				retJSON = retJSON + "\"namewt\" : " + "\"" + document.getElementById("Name" + indexVal).innerHTML + "\" , ";
				retJSON = retJSON + "\"unitwt\" : " + "\"" + document.getElementById("Unit" + indexVal).innerHTML + "\" , ";
				retJSON = retJSON + "\"qntywt\" : " + "\"" + document.getElementById("Qnty" + indexVal).value + "\"}";
			}
		}
	}

	document.getElementById("divAddWTCont").innerHTML = "";
	workAddition = JSON.parse("[" + retJSON + "]")
	mpWorkCancel()
}

function addWorkExt()
{
	$("#projHideOpa").height($(window).height());
	$("#projHideOpa").width($(window).width());
	var selectedEffect = "fade";
	var options = { percent: 100 };
	$( "#projHideOpa" ).show( selectedEffect , options , 500);
	$(".dhx_cal_light").hide( selectedEffect , options , 500);
	$(".dhx_cal_cover").hide( selectedEffect , options , 500);
	window.setTimeout("addWorkExtNxt()" , 500)	
}

function addWorkExtNxt()
{
	$("#addWorkDIV").width($(window).width()/2);	
	$("#addWorkDIV").css({left: $(window).width()/4});
	var selectedEffect = "blind";
	var options = { percent: 100 };
	$( "#addWorkDIV" ).show( selectedEffect , options , 500);
}

function mpWorkCancel()
{
	var selectedEffect = "fade";
	var options = { percent: 100 };
	$( "#addWorkDIV" ).hide( selectedEffect , options , 500);
	
	var options = { percent: 100 };
	$( "#projHideOpa" ).hide( selectedEffect , options , 500);
	
	$(".dhx_cal_light").show( selectedEffect , options , 500);
	$(".dhx_cal_cover").show( selectedEffect , options , 500);
}