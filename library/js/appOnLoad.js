function loadProject()
{
	docProj = document.forms[0];
	document.getElementById("btnSubmitEnable").style.display = "inline";
	document.getElementById("btnSubmitDisabled").style.display = "none";
	
	var strDATA = getData()
	strDATA = getDataResp
	if (strDATA == "")
	{
		strDATA = "\"data\" : []"
	}
	else
	{
		strDATA = "\"data\":[" + strDATA + "]"
	}
	
	var strLinks = getLinks()
	strLinks = getLinksResp
	if (strLinks == "")
	{
		strLinks = "\"links\" : []"
	}
	else
	{
		strLinks = "\"links\":[" + strLinks + "]"
	}
	
	if ( strDATA != "\"data\" : []" )
	{
		users_data = JSON.parse("{" + strDATA + " , " + strLinks + "}")
	}

	
	//Define columns and required width...
	gantt.config.grid_width = 500;
	gantt.config.columns =  [
	    {name:"text", label:"Task/Activity",  tree:true, width:230 },
	    {name:"start_date", label:"Start Date", align: "center", width:100  },
	    {name:"end_date",   label:"End Date",   align: "center", width:100 },
	    {name:"duration",   label:"Duration",   align: "center", width:70  },
	    {name:"add", label:"", width:44 }
	];

	//Set up the header and the the secondary scale...
	gantt.config.subscales = [
	    {unit:"week", step:1, date:"Week #%W"}
	];
	gantt.config.scale_height = 40;

	//Set up scale weekend colors...
	gantt.templates.scale_cell_class = function(date){
	    if(date.getDay()==0||date.getDay()==6){
	        return "weekend";
	    }
	};
	gantt.templates.task_cell_class = function(item,date){
	    if(date.getDay()==0||date.getDay()==6){ 
	        return "weekend" ;
	    }
	};

	gantt.templates.rightside_text = function(start, end, task){
		if(task.type == gantt.config.types.milestone){
			return task.text;
		}
		return "";
	};

	//Add fields to the dialog...
	gantt.form_blocks["my_editor"] = {
		render:function(sns) {
		var addFields = "<table style='font-size:11px;width:100%'><tr><td style='width:20%'>Name</td> <td><input type='text' 		style='height:25px;' class='form-control input-xs'></td></tr></table>";
		
			addFields = addFields + "<table style='font-size:11px;width:100%'><tr><td style='width:20%'>Manpower (Wtg)</td><td style='width:13%'><input type='number' class='form-control input-xs' size='1' maxlength='2' style='height:25px;' min='0' max='99' oninput='this.value=this.value.slice(0,this.maxLength)' /></td><td align='center' >&nbsp;Material (Wtg)</td><td style='width:13%'><input value='' type='number' class='form-control input-xs' size='1' maxlength='2' style='height:25px;' min='0' max='99' oninput='this.value=this.value.slice(0,this.maxLength)' /></td><td  align='center'>&nbsp;Machinery (Wtg)</td><td style='width:13%'><input type='number' class='form-control input-xs' size='1' maxlength='2' style='height:25px;' min='0' max='99' oninput='this.value=this.value.slice(0,this.maxLength)' /></td></tr></table><br>";
						
			addFields = addFields +"<table style='font-size:11px;width:100%'><tr><td align='center' style='padding-top: 1px;'><button type='button' class='btn btn-primary btn-xs' onClick='addWork()'>Add Work</button>&nbsp;<button type='button' class='btn btn-primary btn-xs' onClick='addManPower()'>Add Manpower</button>&nbsp;<button type='button' class='btn btn-primary btn-xs' onClick='addMaterial()'>Add Material</button>&nbsp;<button type='button' class='btn btn-primary btn-xs' onClick='addMachinery()'>Add Machinary</button></td></tr></table>";
			
			return "<div class='dhx_cal_ltext' style='height:100px;'>" + addFields + "</div>";
		},
		
		set_value:function(node, value, task){
			taskEdited = task;
			node.getElementsByTagName("input")[0].value = value || "";
			if (task.manpowerw != undefined ) node.getElementsByTagName("input")[1].value = task.manpowerw || "";			
			if (task.materialw != undefined ) node.getElementsByTagName("input")[2].value = task.materialw || "";
			if (task.machineryw != undefined ) node.getElementsByTagName("input")[3].value = task.machineryw || "";
			
			if (task.manpowerr != undefined ) manpowerAddition = task.manpowerr || "";
			if (task.machineryr != undefined ) machineryAddition = task.machineryr || "";
			if (task.materialr != undefined ) materialAddition = task.materialr || "";
			if (task.worktyper != undefined ) workAddition = task.worktyper || "";
		},
		
		get_value:function(node, task){
			task.manpowerw = node.getElementsByTagName("input")[1].value			
			task.materialw = node.getElementsByTagName("input")[2].value
			task.machineryw = node.getElementsByTagName("input")[3].value
			
			task.manpowerr = manpowerAddition
			task.machineryr = machineryAddition
			task.materialr = materialAddition
			task.worktyper = workAddition
			return node.getElementsByTagName("input")[0].value;
		},
			
		focus:function(node) {
			var a = node.getElementsByTagName("input")[0];
			a.select();
			a.focus();
		}
	};
	
	gantt.config.lightbox.sections = [
		{name: "description", height: 30, map_to: "text", type : "my_editor"},
		{name: "type", type: "typeselect", map_to: "type"}, 
		{name: "time", height: 72, type: "duration", map_to: "auto"}
	];
	
	if (docProj.txProjectGanttSet.checked == true){
		gantt.config.drag_progress = true;
	}
	else{
		gantt.config.drag_progress = false;
	}
	
	gantt.templates.tooltip_text = function(start,end,task){
	    return "<b>Task:</b> "+task.text+"<br/><b>Start Date: </b>" + start.getFullYear() + "-" + ( start.getMonth() + 1 ) + "-" + start.getDate() + "<br/><b>End Date: </b>" + end.getFullYear() + "-" + ( end.getMonth() + 1 ) + "-" + end.getDate() + "<br/><b>Duration: </b> " + task.duration + "<br/><b>Progress: </b>" + parseFloat(Number(task.progress) * 100 ).toFixed(2) + "%";
	};
	
	gantt.config.tooltip_hide_timeout = 2000;
	gantt.config.tooltip_timeout = 500;
	gantt.init("gantt_here");
	gantt.parse(users_data);

	//Code for AMD...STARTS HERE
	var GiftModel = function(gifts) {
	    var self = this;
	    self.gifts = ko.observableArray(gifts);
	    sampleResourceCategories = ko.observableArray(['Work', 'Manpower', 'Machinery', 'Material'])
	 
	    self.addGift = function() {
	        self.gifts.push({
	            type: "",
	            name: "" ,
	            quantity: "" , 
	            unit: ""
	        });
	        $( ".liveExample" ).animate({ scrollTop: 3000 } , 100);
	    };
	 
	    self.removeGift = function(gift) {
	        self.gifts.remove(gift);
	    };
	 
	    self.save = function(form) {
	        mtDataUpdate( ko.utils.stringifyJson(self.gifts) )
	    };
	};

	if (docProj.txResourceType.value == "")
	{
		var viewModel = new GiftModel([]);
		ko.applyBindings(viewModel);
	}
	else
	{
		var strJSONRes = "";
		var resType = docProj.txResourceType.value.split("@^@")
		var resName = docProj.txResourceName.value.split("@^@")
		var resQnty = docProj.txResourceQuantity.value.split("@^@")
		var resUnit = docProj.txResourceUnit.value.split("@^@")
		for( i=0 ; i<resType.length ; i++)
		{
			if (strJSONRes == "")
			{
				strJSONRes = "{\"type\" : \"" + resType[i] + "\" , ";
				strJSONRes = strJSONRes + "\"name\" : \"" + resName[i] + "\" , ";
				strJSONRes = strJSONRes + "\"quantity\" : \"" + resQnty[i] + "\" , ";
				strJSONRes = strJSONRes + "\"unit\" : \"" + resUnit[i] + "\"}"
			}
			else
			{
				strJSONRes = strJSONRes + " , "
				strJSONRes = strJSONRes + "{\"type\" : \"" + resType[i] + "\" , ";
				strJSONRes = strJSONRes + "\"name\" : \"" + resName[i] + "\" , ";
				strJSONRes = strJSONRes + "\"quantity\" : \"" + resQnty[i] + "\" , ";
				strJSONRes = strJSONRes + "\"unit\" : \"" + resUnit[i] + "\" }"
			}
		}
		strJSONRes = JSON.parse( "[" + strJSONRes + "]" );
		var viewModel = new GiftModel(strJSONRes);
		ko.applyBindings(viewModel);
	}
	
    $(document).ready(function(){
        $('.newWindow').click(function (event){
            var url = $(this).attr("href");
            var windowName = "popUp";
            var windowWidth = 400;
            var windowHeight = 550;
            var windowLeft = parseInt((screen.availWidth/2) - (windowWidth/2));
            var windowTop = parseInt((screen.availHeight/2) - (windowHeight/2));
            var windowSize = "width=" + windowWidth + ", height=" + windowHeight + ", left=" + windowLeft + ", top=" + windowTop + ", screenX=" + windowLeft + ", screenY=" + windowTop;
            window.open(url, windowName, windowSize);
            event.preventDefault();
 
        });
    });

	// Activate jQuery Validation
	$("form").validate({ submitHandler: viewModel.save });
	//Code for AMD...ENDS HERE

}