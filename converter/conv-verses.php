<?php

// Converts list of verses from multiple TXT file to multiple MD files

$source_dir = "./verses-text";
$target_dir = "./verses-md";

//

// $file_content = file_get_contents($json_path);
// $json = json_decode($file_content);
// print_r($json);

function read_txt_file($input_filepath) {
	global $source_dir;
  if ($handle = fopen($input_filepath, "r")) {
    $contents = fread($handle, filesize($input_filepath));
    if (!$contents) {
      var_dump("Failed fread " . $input_filepath);
      return false;
    }
  } else {
    var_dump("Failed fopen " . $input_filepath);
  }
return $contents;
}

function get_verse_list_from_dir() {
	global $source_dir;
  $dh = opendir($source_dir);
  $list = [];
  $i = 1;
  while (($file = readdir($dh)) !== false) {
    if ($file != "." && $file != "..") {
      if (substr($file, -4, 4) == ".txt") {
        $number = substr($file, 0, -4);
        $ok = true;
        /* for ($c = 0; $c < strlen($number); $c++) {
          $isd = ctype_digit($number[$c]);
          if (!$isd) $ok = false;
        } */
        if ($ok) {
          $item = array();
          $item["index"] = $number;
          $item["filename"] = $file;
          $item["fullfilename"] = $source_dir . "/" . $file;
          $list[] = $item;
        }
      }
      $i++;
    }
  }
  closedir($dh);
  return $list;
}



function write_md($item, $md_content) {
	global $target_dir;
	$file_name = $target_dir . "/" . $item["index"] . ".md";
	$handle = fopen($file_name, "w");
	if (!$handle) {
		echo "Error creating file: " . $file_name;
		return;
	}
	fwrite($handle, $md_content);
/* 
	fwrite($handle, "title = '" . $item->title . "'\n");
	fwrite($handle, "[params]\n");
	fwrite($handle, "  filename = '" . $item->file . ".mp3'\n");
	fwrite($handle, "+++\n");
	fclose($handle); */

	echo "Created file: " . $file_name . "\n";

	return true;
}


function get_tags()
{
  return [
    '' => ['tag' => 'p', 'class' => ''],
    'TITLE' => ['tag' => 'h1', 'class' => ''],
    'DATE' => ['tag' => 'p', 'class' => 'date'],
    'COMMENT' => ['tag' => 'p', 'class' => 'comment'],
    'AUTHORCOMMENT' => ['tag' => 'p', 'class' => 'authorcomment'],
    'FOOTER' => ['tag' => 'p', 'class' => 'footer'],
    'HIDE' => ['tag' => 'p', 'class' => 'hidden'],
    'BOOK' => ['tag' => 'p', 'class' => 'hidden'],
  ];
}

function parse_line($line)
{
  $line = trim($line);
  $tokens = explode(" ", $line, 2);
  $token = $tokens[0];
  if (mb_substr($token, 0, 1) === "#") {
    $tag = mb_substr($token, 1, NULL);
    return ['tag' => $tag, 'content' => isset($tokens[1]) ? $tokens[1] : ""];
  } else {
    return ['tag' => '', 'content' => $line];
  }
}

function parse_lines($lines) {
	$tags = get_tags();
	$result = [];
	foreach ($lines as &$line) {
    $line = trim($line);
    if ($line !== '' || sizeof($result) > 0) {
      $parsed = parse_line($line);
      if (isset($tags[$parsed['tag']])) {
        $item = $tags[$parsed['tag']];
				$result[] = ['tag' => $parsed['tag'], 'element' => $item['tag'], 'class' => $item['class'], 
					'content' => $parsed['content']];
        // $out .= '<' . $item['tag'] . ($item['class'] != '' ? ' class="' . $item['class'] . '"' : '') . '>' . $parsed['content'] . '</' . $item['tag'] . '>' . PHP_EOL;
      }
    }
  }
	return $result;
}

function get_title_from_lines($structured) {
	foreach ($structured as $item) {
		if ($item['tag'] == 'TITLE') {
			return $item['content'];
		}
	}
	foreach ($structured as $item) {
		if ($item['tag'] == '' && $item['content'] != '') {
			return $item['content'];
		}
	}
	return '';
}


function get_date_from_lines($structured) {
	foreach ($structured as $item) {
		if ($item['tag'] == 'DATE') {
			return $item['content'];
		}
	}
	return false;
}


function get_book_from_lines($structured) {
	foreach ($structured as $item) {
		if ($item['tag'] == 'BOOK') {
			return $item['content'];
		}
	}
	return false;
}

function process_lines($structured) {
	$result = "";
	foreach ($structured as $item) {
		if ($item['tag'] != '') {
			$result .= "```\n". $item['content'] . "\n". "```\n";
		} else {
			$result .= $item['content'] . "\n";
		}
	}
	return $result;
}


function detect_hidetitle($structured) {
	foreach ($structured as $item) {
		if ($item['tag'] == 'TITLE') {
			return "false";
		}
	}
	return "true";
}

function make_md_content_from_item($item) {
	$source = read_txt_file($item["fullfilename"]);
	if (!$source) {
		echo "Error reading file: " . $item->fullfilename;
		return;
	}
	$lines = explode("\n", $source);
	// print_r($lines);
	$structured = parse_lines($lines);
	// print_r($structured);


	$meta = [];
	$meta['title'] = get_title_from_lines($structured);
	$meta['datesigned'] = get_date_from_lines($structured);
	$meta['book'] = get_book_from_lines($structured);

	$body = process_lines($structured);

	$result = "+++\n";
	$result .= "title = '" . $meta['title'] . "'\n";
	$result .= "[params]\n";
	$result .= "  hidetitle = " . detect_hidetitle($structured) . "\n";
	$result .= "  datesigned = '" . $meta['datesigned'] . "'\n";
	$result .= "  book = '" . $meta['book'] . "'\n";
	$result .= "+++\n";
	$result .= $body;

	print_r($meta['title']."\n");

	return $result;
}

//

mkdir($target_dir);
$list = get_verse_list_from_dir();
// print_r($list);
foreach ($list as $item) {
	$md_content = make_md_content_from_item($item);
	write_md($item, $md_content);
}