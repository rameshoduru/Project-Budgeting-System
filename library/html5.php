<?php

include('dbconnect.php');
include('appconfig.php');
include('global_functions.php');

$project_id = "5FE0F1FC";
$project_number = "201711071510076900";
	

	
?>
<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">

	<?php
		include ("uilinks.php");
	?>

	
</head>
<body>

<div class='container'>


<?php 
function test(){
	$resource_tables = $GLOBALS["resource_table_array"]; 
//	echo $resource_tables["Work"];
}
test();

	?>


</div>

	<script>
			Array.prototype.unique = function () {
			var arr = this;
			return $.grep(arr, function (v, i) {
				return $.inArray(v, arr) === i;
			});
		}
		array1 = [1,2,3,4,2,3,6].unique();

		$.each(array1, function(index, value) {
			//alert(value);

	});
	</script>
</body>
</html>
