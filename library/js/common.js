

 //Go-live date picker function
	$(document).ready(function(){
		var date = new Date();
        var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

		var date_input=$('input[name="golivedate"]'); //our date input has the name "date"
		var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
		date_input.datepicker({
			format: 'mm/dd/yyyy',
			container: container,
			todayHighlight: true,
			autoclose: true,
			startDate: today,
			minDate: today,
			changeMonth: true
		})
	});

//Get app id from app inventory for new version apps	
		function getAppid(){
			var str = document.getElementById('appname').value;
			var store = document.forms[0].appstore;
			for (var i = 0; i < store.length; i++) {
				if (store[i].checked) {
					var str2 = store[i].value;
					}
				}
			var xhttp;
			  if (str.length == 0) { 
				document.getElementById("appid").value = "";
				return;
			  }
			  xhttp = new XMLHttpRequest();
			  xhttp.onreadystatechange = function() {
			if (xhttp.readyState == 4 && xhttp.status == 200) {
			  document.forms[0].appid.value = xhttp.responseText;
			}
		  };
		  xhttp.open("GET", "lkpappid.php?q="+str+"&x="+str2, true);
		  xhttp.send(); 
		}

		
//Territories list values enable/disable selections 			
		 function territories(){
			var cboxes = document.getElementsByName('territories[]');
			var len = cboxes.length;	
			for (var i=1; i< len; i++) {
				if( cboxes[0].checked == true){
					cboxes[i].disabled = true;
					cboxes[i].checked = false;						
				}else{
					cboxes[i].disabled = false;
				}					
			}							
		}


//hiding accept button for security
		function adminPrivilage(){
			var secaccept = document.getElementById("secaccept");
			var secrem = document.getElementById("secrem");
			if( "<?php echo $statvalue; ?>" != "2" ){
				secaccept.style.display = 'none';				
			}
			if( "<?php echo $statvalue; ?>" == "2" ){
				secrem.style.display = 'none';				
			}
		}


//form manipulation for iOS request		
		function expComp() {
			var eCompilance = document.forms[0].elements["exportcomp"];
			for(var i=0;i< eCompilance.length;i++){
				if(eCompilance[i].checked){
					var eComp = eCompilance[i].value;
				}
			}	
			if(eComp=="Yes"){
				document.getElementById("exc1").style.display="block";
				document.getElementById("exc2").style.display="block";
			} else if(eComp=="No"){
				document.getElementById("exc1").style.display="none";
				document.getElementById("exc2").style.display="none";
					var crypto1 = document.forms[0].elements["cryptography"];
					for(var i=0;i<crypto1.length;i++){
					crypto1[i].checked = false
					}
					var crypto2 = document.forms[0].elements["cryptography2"];
					for(var i=0;i<crypto2.length;i++){
					crypto2[i].checked = false
					}
				}	
		};
		
		function idfavalidation(){
			var idfa = document.forms[0].elements["IDFA"];
			for(var i=0;i<idfa.length;i++){
				if(idfa[i].checked){
					var idfaval = idfa[i].value;
				}
			}
			if(idfaval =="Yes"){
				document.getElementById("idfadiv").style.display="block";
			}else if(idfaval=="No"){
			
				var cboxes = document.getElementsByName('idfaoptions[]');
				var len = cboxes.length;	
				for (var i=0; i< len; i++) {
					cboxes[i].checked = false;
				}
				document.getElementById("idfadiv").style.display="none";
			}
			
		};
		

	function ulogHideWhen(){
		var ulog = document.forms[0].hasuserlogin;			
		for (var i = 0; i < ulog.length; i++) {
			if (ulog[i].checked) {
				var ulogvalue = ulog[i].value;
				}
			}		
		if( ulogvalue == "Yes" ){
			document.getElementById("demoAccount").style.visibility = "visible";
		}else{
			document.getElementById("demoAccount").style.visibility = "hidden";
		}
			
	}
		


	function piiHideWhen(){
		var pii = document.forms[0].piiinformation;			
		for (var i = 0; i < pii.length; i++) {
			if (pii[i].checked) {
				var piivalue = pii[i].value;
				}
			}		
			
		if( piivalue == "Yes" ){
			document.getElementById("piidiv").style.visibility = "visible";
		}else{
			document.getElementById("piidiv").style.visibility = "hidden";
		}				
	}


//--- File size display --
		function fileUploadExt( elementID, fldLabel ){				
			var txtUpload="";
			var uploadObj = document.getElementById(elementID);

			if(fldLabel == "PIA Proforma"){
				var allowedFiles = [".xls", ".xlsx"];
				txtUpload = "\nPIA proforma should be of type [ " + allowedFiles.join(', ') + " ] only";
			}else if( fldLabel == "Web Server Scanning"){								
				var allowedFiles = [".xls", ".xlsx"];
				txtUpload = "\nWeb server scanning proforma should be of type [ " + allowedFiles.join(', ') + " ] only";

			}else if( fldLabel == "Google Play" ){			
				var allowedFiles = [".apk", ".zip"];
				txtUpload = "\nUpload project file of type [ " + allowedFiles.join(', ') + " ] only";
			}else if(fldLabel == "iTunes"){
				var allowedFiles = [".ipa", ".zip"];
				txtUpload = "\nUpload project file of type [ " + allowedFiles.join(', ') + " ] only";
			}else if( fldLabel == "appscreens" ){
				var allowedFiles = [".zip"];
				txtUpload = "\nUpload app screens in a compressed [ " + allowedFiles.join(', ') + " ] format";				
			}else if( fldLabel == "appicon" ){
				var allowedFiles = [".png"];
				txtUpload = "\nApp icon should be of type [ " + allowedFiles.join(', ') + " ] only";				
			}else if( fldLabel == "featuredGraphic" ){
				var allowedFiles = [".png", ".jpg"];
				txtUpload = "\nFeatured graphic should be of type [ " + allowedFiles.join(', ') + " ] only";				
			}

			if( uploadObj.files.length != 0){
				var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + allowedFiles.join('|') + ")$");
				
				if (!regex.test(uploadObj.value.toLowerCase())) {
					stopSubmit = true;
					msgtxt = msgtxt + txtUpload;
				}
			}
		}

//save as draft - validation for file formats
			var stopSubmit;
			var msgtxt="";
		function fileUploadValidation( store ){
			stopSubmit = false;
			msgtxt="Please verify the file types before upload..\n";
			
			var piival = $('input:radio[name=piiinformation]:checked').val(); 
			if(piival == "Yes"){
				fileUploadExt( "webappproforma", "Web Server Scanning" ); //Web Server Scanning proforma App screens file type validation		
				fileUploadExt( "piaform", "PIA Proforma" ); //Proforma file type validation
			}	
		
			fileUploadExt( "appicon", "appicon" ); //App icon file type validation
			fileUploadExt( "appscreens", "appscreens" ); //App screens file type validation	
			fileUploadExt( "projectFile", "Google Play" ); //Project file type validation
			if( store == "Google Play" ){ 
				fileUploadExt( "featuredGraphic", "featuredGraphic" ); //App icon file type validation  
			}
			
		}
		
//email validation function
		function validateEmail(sEmail, errmsg) {
			var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
			if(sEmail != ""){
				if (!filter.test(sEmail)) {
					stopSubmit = true;
					msgtxt = msgtxt+errmsg;	
				}	
			}

		}

//save as draft - validation for file formats
		function draftValidation(appstore){
			//file extension validation
			fileUploadValidation(appstore);		

			//email validation for Unilever contact
			var sEmail = $('#unileveremail').val();
			var errmsg = "\nWrong email format for Unilever contact mail id";
			validateEmail(sEmail, errmsg);
			if(stopSubmit==true){
				alert(msgtxt);				
				return false;
			}	
		}	
		
		