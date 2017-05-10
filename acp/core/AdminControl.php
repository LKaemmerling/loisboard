<?php
namespace Main\Admin; 
class Control 
{
	/*
			id
			name
			enter_administration 
			edit_themes 	
			move_themes 		
			close_themes 	
			tag_themes 
			edit_posts 	
			del_posts 	
			autogain*/
	public static function createUsergroup($name, $enter_administration, $edit_themes, $move_themes, $close_themes, $tag_themes, $edit_posts, $del_posts, $autogain)
	{
		\Main\DB::insert("gruppen", array(
			"name" => $name,
			"enter_administration" => $enter_administration,
			"edit_themes" => $edit_themes,
			"move_themes" => $move_themes,
			"close_themes" => $close_themes,
			"tag_themes" => $tag_themes,
			"edit_posts" => $edit_posts,
			"del_posts" => $del_posts,
			"autogain" => $autogain
		));
	}
	
	public static function getUsergroupData($id) 
	{
		$array = array(); 
		$rst = \Main\DB::select("gruppen", "*", "id='".\Main\DB::escape($id)."'");
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			$array = array("id" => $id,
						   "name" => $row->name,
						   "enter_administration" => $row->enter_administration,
						   "edit_themes" => $row->edit_themes,
						   "move_themes" => $row->move_themes,
						   "close_themes" => $row->close_themes,
						   "tag_themes" => $row->tag_themes,
						   "edit_posts" => $row->edit_posts,
						   "del_posts" => $row->del_posts,
						   "autogain" => $row->autogain
						   );
					  
		}
		return $array; 
	}

	public static function getUsergroupName($gid) 
	{
		$html = ""; 
		$rst = \Main\DB::select("gruppen", "name", "id='".\Main\DB::escape($gid)."'");
		if($rst->num_rows > 0) 
		{
			$row = $rst->fetch_object(); 
			$html = $row->name; 
		}
		return $html; 
	}
	
	public static function saveUsergroup($id, $name, $enter_administration, $edit_themes, $move_themes, $close_themes, $tag_themes, $edit_posts, $del_posts, $autogain) 
	{
		\Main\DB::update("gruppen", $id, array("name" => $name,
										 "enter_administration" => $enter_administration,
										 "edit_themes" => $edit_themes,
										 "move_themes" => $move_themes,
										 "close_themes" => $close_themes,
										 "tag_themes" => $tag_themes,
										 "edit_posts" => $edit_posts,
										 "del_posts" => $del_posts,
										 "autogain" => $autogain
										 ));
	}

	public static function isUserInGroup($uid, $gid) 
	{
		$rst = \Main\DB::select("gruppen_user", "id", "user='".\Main\DB::escape($uid)."' AND gruppe='".\Main\DB::escape($gid)."'");
		if($rst->num_rows > 0) return true; 
		return false; 
	}

	public static function createPlugin($name, $foldername, $author, $active)
	{
		$html = ""; 
		
		if(strlen($name) < 1 || strlen($foldername) < 1)
		{
			$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["plugins_need_name_fname"]."</div>"; 
			return $html; 
		}		
		
		if($author == null) $author = ""; 
		
		$dir = "../data/plugins/$foldername"; 
		if(is_dir($dir)) 
		{
			$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["plugins_folder_exists"]."</div>"; 
			return $html; 
		}
		
		mkdir($dir); 
		chmod($dir, 0777); 
		if(!is_dir($dir)) 
		{
			$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["plugin_missing_chmod"]."</div>";
			return $html; 
		}
		
		$file = $dir."/plugin.php"; 
		$fop = @fopen($file, "w+"); 
		fwrite($fop, "<?php\n/**\n*LoisBoard 1.0 Plugin\n**/\n\n\n?>"); 
		fclose($fop); 
		chmod($file, 0777); 
		
		$file = $dir."/plugin.js"; 
		$fop = @fopen($file, "w+"); 
		fwrite($fop, "////  LoisBoard 1.0 Plugin  ////\n"); 
		fclose($fop); 
		chmod($file, 0777); 
		
		$file = $dir."/plugin.css"; 
		$fop = @fopen($file, "w+"); 
		fwrite($fop, ""); 
		fclose($fop); 
		chmod($file, 0777); 
		
		$file = $dir."/admin.php"; 
		$fop = @fopen($file, "w+"); 
		fwrite($fop, "<?php\n/**\n*LoisBoard 1.0 Administration Plugin\n**/\n\n\n?>"); 
		fclose($fop); 
		chmod($file, 0777); 
		
		$file = $dir."/admin.js"; 
		$fop = @fopen($file, "w+"); 
		fwrite($fop, "////  LoisBoard 1.0 Administration Plugin  ////\n"); 
		fclose($fop); 
		chmod($file, 0777); 
		
		$file = $dir."/admin.css"; 
		$fop = @fopen($file, "w+"); 
		fwrite($fop, ""); 
		fclose($fop); 
		chmod($file, 0777); 
		
		$dir = $dir . "/ajax";
		mkdir($dir); 
		chmod($dir, 0777); 
		
		\Main\DB::insert("plugins", array("name" => $name,
									"foldername" => $foldername,
									"author" => $author,
									"active" => $active));
		
		$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["plugin_created_success"]."</div>"; 
		
		return $html; 
	}
	
	public static function createDesign($name, $filename, $author, $footertxt, $active, $standard) 
	{
		$html = ""; 
		if(strlen($name) < 1 || strlen($filename) < 1) 
		{
			$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["design_missing_name"]."</div>";
			return $html; 
		}
		
		$filename = str_replace(" ", "_", $filename);
		$filename = rtrim($filename); 
		$filename = ltrim($filename); 
		
		$file = "../data/designs/".$filename.".css";
		if(file_exists($file)) 
		{
			$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["design_file_exists"]."</div>";
			return $html; 
		}
		
		$fop = @fopen($file, "w+"); 
		fwrite($fop, ""); 
		fclose($fop); 
		chmod($file, 0777); 
		
		if(!file_exists($file)) 
		{
			$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["design_missing_chmod"]."</div>"; 
			return $html; 
		}
		
		if($standard == 1) 
		{
			$active = 1; 
			$sql = "UPDATE designs SET standard='0'";
			\Main\DB::query($sql); 
		}
		
		$filename .= ".css"; 
		\Main\DB::insert("designs", array("name" => $name,
									"fname" => $filename,
									"autor" => $author,
									"footer_txt" => $footertxt,
									"active" => $active,
									"standard" => $standard)); 
									
		$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["design_created_success"]."</div>";
		
		return $html; 
	}
	
	public static function createTag($name, $useable) 
	{
		$html = ""; 
		if(strlen($name) < 1) {
			$html .= "<div class='alert alert-danger'>".\Main\Language::$txt["alerts"]["tag_create_fail"]."</div>"; 
			return $html; 
		}
		
		if($useable != 0 && $useable != 1) $useable = 0; 
		
		\Main\DB::insert("tags", array("name" => $name, "useable" => $useable));
		$html .= "<div class='alert alert-success'>".\Main\Language::$txt["alerts"]["tag_create_success"]."</div>"; 
		
		return $html; 
	}
	
	public static function deleteTag($id) 
	{
		\Main\DB::delete("tags", $id); 
	}
	
	public static function createTagGroup($tag, $group) 
	{
		$html = ""; 
		
		$rst = \Main\DB::select("tags_gruppen", "id", "tag='".\Main\DB::escape($tag)."' AND gruppe='".\Main\DB::escape($group)."'");
		if($rst->num_rows > 0) 
		{
			return $html; 
		}
		
		\Main\DB::insert("tags_gruppen", array("tag" => $tag, "gruppe" => $group));
		
		return $html; 
	}
	
	public static function deleteTagGroup($tag, $gruppe) 
	{
		$sql = "DELETE FROM tags_gruppen WHERE tag='".\Main\DB::escape($tag)."' AND gruppe='".\Main\DB::escape($gruppe)."'";
		\Main\DB::query($sql); 
	}
	
	public static function deleteTagBoard($id) 
	{
		\Main\DB::delete("tags_foren", $id);
	}
	
}

?>