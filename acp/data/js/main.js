// shows/hides select box when creating a new board/kategory 
function checkNewBRadios() 
{
	var radios = document.getElementsByName("type");
	var obj = document.getElementById("b_sel"); 
	
	for (var i = 0, length = radios.length; i < length; i++) {
		  if (radios[i].checked) {
		      
		      if(radios[i].value == "1") 
		      {
				obj.style.display = "block"; 
		      }
		      if(radios[i].value == "0") 
		      {
		      	obj.style.display = "none"; 
		      }
		      break;
		  }
	}
}

function showDelKategory(id) 
{
	var bg = document.getElementById("middlewindow_bg"); 

	$.ajax({
		url:"data/ajax/showDelKategory.php",
		data:"id="+id,
		type:"POST",
		success:function(msg) {
			$("#middlewindow").html(msg);
			bg.style.display = "block"; 
		}
	});
}

function DelKategory(id) 
{
	var bg = document.getElementById("middlewindow_bg"); 
	
	$.ajax({
		url:"data/ajax/DelKategory.php",
		data:"id="+id,
		type:"POST",
		success:function(msg) {
			bg.style.display = "none"; 
			window.location.href="index.php?page=content&up=list-b";
		}
	});
}

function showDelBoard(id) 
{
	var bg = document.getElementById("middlewindow_bg"); 

	$.ajax({
		url:"data/ajax/showDelBoard.php",
		data:"id="+id,
		type:"POST",
		success:function(msg) {
			$("#middlewindow").html(msg);
			bg.style.display = "block"; 
		}
	});
}

function DelBoard(id) 
{
	var bg = document.getElementById("middlewindow_bg"); 
	
	$.ajax({
		url:"data/ajax/DelBoard.php",
		data:"id="+id,
		type:"POST",
		success:function(msg) {
			bg.style.display = "none"; 
			window.location.href="index.php?page=content&up=list-b";
		}
	});
}

function showDelUser(id, no) 
{
	var bg = document.getElementById("middlewindow_bg"); 

	$.ajax({
		url:"data/ajax/showDelUser.php",
		data:"id="+id+"&num="+no,
		type:"POST",
		success:function(msg) {
			$("#middlewindow").html(msg);
			bg.style.display = "block"; 
		}
	});
}

function DelUser(id, no) 
{
	var bg = document.getElementById("middlewindow_bg"); 
	
	$.ajax({
		url:"data/ajax/DelUser.php",
		data:"id="+id,
		type:"POST",
		success:function(msg) {
			bg.style.display = "none"; 
			window.location.href="index.php?page=users&up=list&pageNo="+no;
		}
	});
}

function showDelUserGroup(id) 
{
	var  bg = document.getElementById("middlewindow_bg"); 
	
	$.ajax({
		url:"data/ajax/showDelUserGroup.php",
		data:"id="+id,
		type:"POST",
		success:function(msg) {
			$("#middlewindow").html(msg); 
			bg.style.display = "block"; 
		}
	});
}

function DelUserGroup(id) 
{
	var bg = document.getElementById("middlewindow_bg"); 
	
	$.ajax({
		url:"data/ajax/DelUserGroup.php",
		data:"id="+id,
		type:"POST",
		success:function(msg) {
			bg.style.display = "none"; 
			window.location.href="index.php?page=users&up=usergroups";
		}
	});
}

function hideMiddleWindow()
{
	$("#middlewindow").html(""); 
	document.getElementById("middlewindow_bg").style.display = "none"; 
}