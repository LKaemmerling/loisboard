<?php
session_start(); 
$html = ""; 
$User = array(
	"logged" => 0,
	"dbid" => 0
);

if(isset($_SESSION["b3_logged"]) && $_SESSION["b3_logged"] == 1) 
{
	$User["logged"] = 1; 
	$User["dbid"] = $_SESSION["b3_dbid"]; 
}

if($User["logged"] == 0) 
	die("Du bist nicht angemeldet!"); 

$updir = "../../../media/upload/"; 
$updir2 = "media/upload/"; 

$showstart = 1; 
if(isset($_FILES["file"]))
{
	$showstart = 0; 
	$target_file = $updir . basename($_FILES["file"]["name"]);
	$target_file2 = $updir2 . basename($_FILES["file"]["name"]); 
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

	$i = 1; 
	while(file_exists($target_file))
	{
		$target_file = $updir . $i . basename($_FILES["file"]["name"]); 
		$target_file2 = $updir2 . $i . basename($_FILES["file"]["name"]); 
		$i++; 
	}
	
	
	
	if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
	{
        $html .= "<img src='$target_file' style='max-width:500px;' /><br />
		<button onclick=\"useImage('".$target_file2."');\">Bild Verwenden</button><br />"; 
    } 
	else 
	{
        $html .= "Der Upload ist Fehlgeschlagen! Versuch es später erneut.<br />";
		$showstart = 1; 
    }
	
	
}

if($showstart == 1) 
{
$html .= "<div class='uploader'><form method='post' enctype='multipart/form-data'>
	<input type='file' name='file' /> <br />
	&nbsp; &nbsp; <button>Hochladen</button>
</form></div>"; 
}


?>

<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<!--script src='function.js'></script-->
	<script>
		function useImage(imgSrc) {
			function getUrlParam( paramName ) {
				var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' ) ;
				var match = window.location.search.match(reParam) ;

				return ( match && match.length > 1 ) ? match[ 1 ] : null ;
			}
			var funcNum = getUrlParam( 'CKEditorFuncNum' );
			var imgSrc = imgSrc;
			var fileUrl = imgSrc;
			window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl );
			window.close();
		}
		
		function use(src) {
			alert("use"); 
		}
	</script> 
	<style>
		body {
		text-align:center; 
		padding-top:50px; 
		}
		
		.uploader {
		width:40%; 
		margin:auto; 
		padding:15px; 
		border:1px solid rgba(0,0,0,0.1); 
		border-radius:5px; 
		text-align:left; 
		}
		
		button {
		paddign:15px 25px; 
		background:rgb(102, 204, 255); 
		color:white; 
		border:1px solid rgba(0,0,0,0.04); 
		border-radius:5px; 
		margin-top:10px; 
		font-size:18px;
		}
	</style>
</head>
<body>
	<?php echo $html; ?>
</body>
</html>