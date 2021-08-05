<?php
  session_start();
  if (!isset($_SESSION['loggedin'])) {
  	header('Location: ../index.html');
  	exit;
  }
?>

<!doctype html>
<html>
<head>
   <meta charset="UTF-8">
   <link rel="shortcut icon" href="./.favicon.png">
   <title>LedCameras - Archive</title>

   <link rel="stylesheet" href="./.style.css">
   <script src="./.sorttable.js"></script>
</head>

<body>
<nav>
  <div>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFFFFF"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78C15.57 19.36 13.86 20 12 20s-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33C4.62 15.49 4 13.82 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6zm0 5c-.83 0-1.5-.67-1.5-1.5S11.17 8 12 8s1.5.67 1.5 1.5S12.83 11 12 11z"/></svg>
    <p><?=$_SESSION['name']?></p>
  </div>
  <div>
  <img id="sn_logo" src="../sn_logo.png" alt="sn_logo">
  </div>
  <div>
  <a id="home" href="../index_logged.php">
    <p>Home</p>
	<svg id="home" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#FFFFFF"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 5.69l5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3z"/></svg>
  </a>
  <a href="../logout.php">
    <p>Wyloguj</p>
    <svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" viewBox="0 0 24 24" fill="#FFFFFF"><g><path d="M0,0h24v24H0V0z" fill="none"/></g><g><path d="M17,8l-1.41,1.41L17.17,11H9v2h8.17l-1.58,1.58L17,16l4-4L17,8z M5,5h7V3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h7v-2H5V5z"/></g></svg>
  </a>
  </div>
</nav>
<p id="p_nav"></p>
<div id="container">
	<h1 id="title">LedCameras Archive</h1>
	
	<table class="sortable">
	    <thead>
		<tr>
			<th>Filename</th>
			<th>Type</th>
			<th>Size (MB)</th>
			<th>Date Modified</th>
		</tr>
	    </thead>
	    <tbody><?php

	// Adds filesizes
	function folderSize($dir){
		$count_size = 0;
		$count = 0;
		$dir_array = scandir($dir);
		  foreach($dir_array as $key=>$filename){
			if($filename!=".." && $filename!="."){
			   if(is_dir($dir."/".$filename)){
				  $new_foldersize = foldersize($dir."/".$filename);
				  $count_size = $count_size+ $new_foldersize;
				}else if(is_file($dir."/".$filename)){
				  $count_size = $count_size + filesize($dir."/".$filename);
				  $count++;
				}
		   }
		 }

		return $count_size;
	}

 	// Checks to see if veiwing hidden files is enabled
	if($_SERVER['QUERY_STRING']=="hidden") { 
		$hide=".";
		$ahref="./?hidden";
		$atext="Hide";
	}
	else { 
		$hide=".";
		$ahref="./?hidden";
		$atext="Show";
	}

	 // Opens directory
	 $myDirectory=opendir("../archive");

	// Gets each entry
	while($entryName=readdir($myDirectory)) {
	   $dirArray[]=$entryName;
	}

	// Closes directory
	closedir($myDirectory);

	// Counts elements in array
	$indexCount=count($dirArray);

	// Sorts files
	sort($dirArray);

	// Loops through the array of files
	for($index=0; $index < $indexCount; $index++) {

	// Decides if hidden files should be displayed, based on query above.
	    if(substr("$dirArray[$index]", 0, 1)!=$hide) {

	// Resets Variables
		$favicon="";
		$class="file";

	// Gets File Names
		$name=$dirArray[$index];
		$namehref=$dirArray[$index];

	// Gets Date Modified
		$modtime=date("M j Y g:i A", filemtime("../archive/$namehref"));
		$timekey=date("YmdHis", filemtime("../archive/$namehref"));


	// Separates directories, and performs operations on those directories
		if(is_dir($dirArray[$index]))
		{
				$extn="&lt;Directory&gt;";
				$size="&lt;Directory&gt;";
				$sizekey="0";
				$class="dir";

			// Gets favicon.ico, and displays it, only if it exists.
				if(file_exists("$namehref/favicon.ico"))
					{
						$favicon=" style='background-image:url($namehref/favicon.ico);'";
						$extn="&lt;Website&gt;";
					}

			// Cleans up . and .. directories
				if($name=="."){$name=". (Current Directory)"; $extn="&lt;System Dir&gt;"; $favicon=" style='background-image:url($namehref/.favicon.ico);'";}
				if($name==".."){$name=".. (Parent Directory)"; $extn="&lt;System Dir&gt;";}
		}

	// File-only operations
		else{
			// Gets file extension
			$extn=pathinfo($dirArray[$index], PATHINFO_EXTENSION);

			// Prettifies file type
			switch ($extn){
				case "png": $extn="PNG Image"; break;
				case "jpg": $extn="JPEG Image"; break;
				case "jpeg": $extn="JPEG Image"; break;
				case "svg": $extn="SVG Image"; break;
				case "gif": $extn="GIF Image"; break;
				case "ico": $extn="Windows Icon"; break;

				case "txt": $extn="Text File"; break;
				case "log": $extn="Log File"; break;
				case "htm": $extn="HTML File"; break;
				case "html": $extn="HTML File"; break;
				case "xhtml": $extn="HTML File"; break;
				case "shtml": $extn="HTML File"; break;
				case "php": $extn="PHP Script"; break;
				case "js": $extn="Javascript File"; break;
				case "css": $extn="Stylesheet"; break;

				case "pdf": $extn="PDF Document"; break;
				case "xls": $extn="Spreadsheet"; break;
				case "xlsx": $extn="Spreadsheet"; break;
				case "doc": $extn="Microsoft Word Document"; break;
				case "docx": $extn="Microsoft Word Document"; break;

				case "zip": $extn="ZIP Archive"; break;
				case "htaccess": $extn="Apache Config File"; break;
				case "exe": $extn="Windows Executable"; break;

				default: if($extn!=""){$extn=strtoupper($extn)."Folder";} else{$extn="Directory";} break;
			}

			// Gets and cleans up file size
				$size=number_format(round((folderSize("../archive/$namehref") / (1024*1024)),2), 2, '.', ' ');
				$sizekey=number_format(round((folderSize("../archive/$namehref") / (1024*1024)),2), 2, '.', ' ');
		}

	// Output
	 echo("
		<tr class='$class'>
			<td><a href='../archive/$namehref'$favicon class='name'>$name</a></td>
			<td><a href='../archive/$namehref'>$extn</a></td>
			<td sorttable_customkey='$sizekey'><a href='../archive/$namehref'>$size</a></td>
			<td sorttable_customkey='$timekey'><a href='../archive/$namehref'>$modtime</a></td>
		</tr>");
	   }
	}
	?>

	    </tbody>
	</table>
</div>
<div class="footer">
    <p>Copyright &copy; 2021 Screen Network S.A.</p>
    <p>Made by <a href="http://exiges.pl">Exiges</a></p>
</div>
</body>
</html>
