<?php
function netCapper($string){
	$words = explode(" ", $string);
	$letters = "";
	foreach ($words as $value) {
		$letters .= substr($value, 0, 1);
	}
	return $letters;
}

function securityPup($con, $rick)
{
//if double delete
	$arch = substr_count($rick, '<');
	$brch = substr_count($rick, '>');
	if($arch > 0 && $brch >0){
		$rick = str_replace("<", "", $rick);
		$rick = str_replace(">", "", $rick);
	}
	$crch = substr_count($rick, '("');
	$drch = substr_count($rick, '")');
	if($crch > 0 && $drch >0){
		$rick = str_replace('("', '', $rick);
		$rick = str_replace('")', '', $rick);
	}
	$erch = substr_count($rick, "')");
	$frch = substr_count($rick, "('");
	if($erch > 0 && $frch >0){
		$rick = str_replace("')", '', $rick);
		$rick = str_replace("('", '', $rick);
	}
	$grch = substr_count($rick, "<S");
	$hrch = substr_count($rick, "T>'");
	if($grch > 0 && $hrch >0){
		$rick = str_replace("<S", '', $rick);
		$rick = str_replace("T>", '', $rick);
	}
	$grch = substr_count($rick, "<s");
	$hrch = substr_count($rick, "t>'");
	if($grch > 0 && $hrch >0){
		$rick = str_replace("<s", '', $rick);
		$rick = str_replace("t>", '', $rick);
	}
	$grch = substr_count($rick, "}");
	$hrch = substr_count($rick, "{'");
	if($grch > 0 && $hrch >0){
		$rick = str_replace("}", '', $rick);
		$rick = str_replace("{", '', $rick);
	}
	//delete straight up
	$vowels = array("&#", "&quot", '\"', "</SCRIPT>", ");", '";', "';", "=&", "<XSS>", "<xss>", '">', '<"', "'>", "<'", "`>", '"<');
	$rick = str_replace($vowels,"",$rick);
	return mysqli_real_escape_string($con, $rick);
}

function securityPupAllowsHTML($rick)
{
//if double delete
	$crch = substr_count($rick, '("');
	$drch = substr_count($rick, '")');
	if($crch > 0 && $drch >0){
		$rick = str_replace('("', '', $rick);
		$rick = str_replace('")', '', $rick);
	}
	$erch = substr_count($rick, "')");
	$frch = substr_count($rick, "('");
	if($erch > 0 && $frch >0){
		$rick = str_replace("')", '', $rick);
		$rick = str_replace("('", '', $rick);
	}
	$grch = substr_count($rick, "<S");
	$hrch = substr_count($rick, "T>'");
	if($grch > 0 && $hrch >0){
		$rick = str_replace("<S", '', $rick);
		$rick = str_replace("T>", '', $rick);
	}
	$grch = substr_count($rick, "<s");
	$hrch = substr_count($rick, "t>'");
	if($grch > 0 && $hrch >0){
		$rick = str_replace("<s", '', $rick);
		$rick = str_replace("t>", '', $rick);
	}
	$grch = substr_count($rick, "}");
	$hrch = substr_count($rick, "{'");
	if($grch > 0 && $hrch >0){
		$rick = str_replace("}", '', $rick);
		$rick = str_replace("{", '', $rick);
	}
	//delete straight up
	$vowels = array("&#", "&quot", "&nbsp", '\"', "</SCRIPT>", ");", "=&", "<XSS>", "<xss>", "`>");
	$rick = str_replace($vowels,"",$rick);
	return mysqli_real_escape_string($con, $rick);
}

function securityPupNoMysqlEscape($rick)
{
//if double delete
	$arch = substr_count($rick, '<');
	$brch = substr_count($rick, '>');
	if($arch > 0 && $brch >0){
		$rick = str_replace("<", "", $rick);
		$rick = str_replace(">", "", $rick);
	}
	$crch = substr_count($rick, '("');
	$drch = substr_count($rick, '")');
	if($crch > 0 && $drch >0){
		$rick = str_replace('("', '', $rick);
		$rick = str_replace('")', '', $rick);
	}
	$erch = substr_count($rick, "')");
	$frch = substr_count($rick, "('");
	if($erch > 0 && $frch >0){
		$rick = str_replace("')", '', $rick);
		$rick = str_replace("('", '', $rick);
	}
	$grch = substr_count($rick, "<S");
	$hrch = substr_count($rick, "T>'");
	if($grch > 0 && $hrch >0){
		$rick = str_replace("<S", '', $rick);
		$rick = str_replace("T>", '', $rick);
	}
	$grch = substr_count($rick, "<s");
	$hrch = substr_count($rick, "t>'");
	if($grch > 0 && $hrch >0){
		$rick = str_replace("<s", '', $rick);
		$rick = str_replace("t>", '', $rick);
	}
	$grch = substr_count($rick, "}");
	$hrch = substr_count($rick, "{'");
	if($grch > 0 && $hrch >0){
		$rick = str_replace("}", '', $rick);
		$rick = str_replace("{", '', $rick);
	}
	//delete straight up
	$vowels = array("&#", "&quot", '\"', "</SCRIPT>", ");", '";', "';", "=&", "<XSS>", "<xss>", '">', '<"', "'>", "<'", "`>", '"<');
	$rick = str_replace($vowels,"",$rick);
	return $rick;
}
?>
