<?php

// Converts list of audio recording from single JSON file to multiple MD files

$json_path = "./audio_list.json";
$target_dir = "./audio-md";

//

$file_content = file_get_contents($json_path);
$json = json_decode($file_content);
// print_r($json);

mkdir($target_dir);

function create_md($item) {
	
	global $target_dir;
	$file_name = $target_dir . "/" . $item->file . ".md";
	$handle = fopen($file_name, "w");
	if (!$handle) {
		echo "Error creating file: " . $file_name;
		return;
	}
	fwrite($handle, "+++\n");
	fwrite($handle, "title = '" . $item->title . "'\n");
	fwrite($handle, "[params]\n");
	fwrite($handle, "  filename = '" . $item->file . ".mp3'\n");
	fwrite($handle, "+++\n");
	fclose($handle);

	echo "Created file: " . $file_name . "\n";

	return true;
}

foreach ($json as $item) {
	create_md($item);
}
