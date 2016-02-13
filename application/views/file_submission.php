<!DOCTYPE html>
<html>
	<head>
	
	<link rel="stylesheet" href="../../css/metro-bootstrap.css"/>
	
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/jquery.widget.min.js"></script>
    <script src="../../js/metro.min.js"></script>
	
	<title> File Submission Page </title>
	
	<script>
		function Aclick(form,Course_ID,Course_Name,Section_Number){
			var miao= document.getElementById(form);
			miao.Course_ID.value= Course_ID;
			miao.Course_Name.value= Course_Name;
			miao.Section_Number.value= Section_Number;
			miao.submit();
		}
		
		  $(function(){
			$("#createFlatWindow").on('click', function(){
				$.Dialog({
					overlay: true,
					shadow: true,
					flat: true,
					icon: '<img src="images/excel2013icon.png">',
					title: 'Flat window',
					content: '',
					padding: 10,
					onShow: function(_dialog){
								var content = '<form>' +
								'<label>File Submission</label>' +
								'<input type="file" name="submissionFile">' +
								'<label>Description</label>' +
								'<input type="text" name="description">' +
								'<div class="form-actions">' +
								'<button class="button">Submit...</button> '+
								'<button class="button" type="button" onclick="$.Dialog.close()">Cancel</button> '+
								'</div>'+
								'</form>';
 
								$.Dialog.title("Submission form");
								$.Dialog.content(content);
								$.Metro.initInputs();
							}
				});
			});
		})
	</script>
	
	<style>
		#submission{
			float: left;
			width:170px;
			height:250px;
			margin:18px 20px 20px 20px;
		}
	
		.content{
			float: left;
			width: 1040px;
		}
		
		li[onclick] {
		cursor: pointer;
		}
	</style>
	
	</head>
	
	<body class="metro" >
	
	<form method= "POST" action= "submitAssist" id= "hi">
		<input type= "hidden" name= "Course_ID" value= "meitounao">
		<input type= "hidden" name= "Course_Name" value= "hehehe">
		<input type= "hidden" name= "Section_Number" value= "bugaoxing">
	</form>
	
	<div id="submission">
	
	<?php
		DEFINE("HOST","host= dbhost-pgsql.cs.missouri.edu");/*secure connection to DataBase in php*/
		DEFINE("DBNAME","dbname=zh4x9");
		DEFINE("USERNAME","user=zh4x9");
		DEFINE("PASSWORD","password=Lm1NINHu");
		
		$conn = pg_connect(HOST." ".DBNAME." ".USERNAME." ".PASSWORD) 
				or die("Could not connect: ". pg_last_error());
		
		$query_1= "SELECT * FROM submit_collection.course AS s1 LEFT JOIN submit_collection.section AS s2 USING (Course_ID) ORDER BY Course_ID ASC";
		//The query should be reconsidered.Here just join the course and the section, which isn't correct, just for test.
		//since the student should log in first, the section for the student in each course has already decided.
		//in the actual case, just need to select the course name+section for the specific student and list them in the side bar!
		$result_1= pg_query($query_1) or die("Error:database error!");
		while($line_1= pg_fetch_array($result_1)){
			$courses[]= $line_1;
		}

	?>
		<div>
			<nav class= "sidebar">
				<ul>
					<li class= "title">Courses</li>
	<?php
		$m= pg_num_rows($result_1);
		for($i=0;$i<$m;$i++){	
			echo "<li onclick= 'Aclick(\"hi\",\"".$courses[$i][0]."\",\"".$courses[$i][1]."\",\"".$courses[$i][2]."\");'>";
			echo "<a>".$courses[$i][1]."</a>";//print the name of the courses
			echo "</li>";
		}

	?>
				</ul>
			</nav>
		</div>
	<br>	
	</div>
	
	<div class= "content">
	<?php
		if(isset($_POST["Course_Name"])){
            
			echo "<h3><strong >".$_POST['Course_Name']."</strong></h3><hr><hr>";//show the course name after click
		
	?>
		<div>
	<?php
		//click course name to show the assignments in them
			$query_2= "SELECT * FROM submit_collection.assignment AS a1 NATURAL JOIN submit_collection.course AS a2 WHERE Course_ID='".trim($_POST['Course_ID'])."';";
			$result_2= pg_query($query_2) or die("course name get error!<br>");
			while($line_2= pg_fetch_array($result_2)){
				$course_allassignments[]= $line_2;
			}
			
			$n= pg_num_rows($result_2);
			
	?>
				<table border= "1" >
					<tr>
						<th>Course_ID</th>
						<th>Assignment_ID</th>
						<th>Due_date</th>
						<th>Post_date</th>
						<th>Submit</th>
					</tr>
					<?php for($k=0;$k<$n;$k++){ ?>
					<tr>
						<td><?php echo $course_allassignments[$k][0];?></td>
						<td><?php echo $course_allassignments[$k][1];?></td>
						<td><?php echo $course_allassignments[$k][2];?></td>
						<td><?php echo $course_allassignments[$k][3];?></td>
						<td><button class= "button" id="createFlatWindow"><?php echo "Submit your assignment!"?></button></td>
					</tr>
					<?php }
		}
					?>
				</table>
		</div>
	</div>
	</body>
</html>