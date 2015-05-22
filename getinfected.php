<?php


function rrmdir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object); 
       } 
     } 
     reset($objects); 
     rmdir($dir); 
   } 
} 

// Download ZIP file from gitHub

$download_filename = uniqid('download', true).".zip";

    echo "Download Zip file from gitHub <br>";

// $username="harrylongworth";
$username="OATSEA";
// $repo="harrylongworth.com";
// $repo="sandpit";
$repo="teachervirus.com";

$giturl='https://github.com/'.$username.'/'.$repo.'/zipball/master/';

// Download master
$copyflag = copy($giturl,$download_filename);

if ($copyflag === TRUE) {
  echo "File downloaded from Github <br>";
} else {
  echo "File download FAILED! <br>";
}

// Download latest release:

// Code from Stakeover flow:
// http://stackoverflow.com/questions/8889025/unzip-a-file-with-php

// UNZIP and move:
// http://codereview.stackexchange.com/questions/24251/unzip-and-move-function

// assuming zipped file is in the same directory as the executing script.
$zipfile = $download_filename;

echo "Unzip file:".$zipfile."<br>";

// get the absolute path to $file
$path = pathinfo(realpath($zipfile), PATHINFO_DIRNAME);

$temp_unzip_folder = uniqid('unzip_temp_', true);

// Create full temp sub_folder path
$temp_unzip_path = $path."/".$temp_unzip_folder."/";

echo "Temp Unzip Path is: ".$temp_unzip_path."<br>";

 
// Make the new temp sub_folder for unzipped files
if (!mkdir($temp_unzip_path, 0755, true)) {
            die('Error: Could not create path: '.$temp_unzip_path);
        } else { echo "Folder Created! <br>"; }

// destination
// ** This section needs update to incorporate need to check play & username folders exist too 
$destination='play/'.$username.'/'.$repo.'/';
if (file_exists($destination)) {
    echo $destination." folder already exists! <br>";
	rrmdir($destination);
	}	
		
if (!mkdir($destination, 0755, true)) {
	     die('Error: Could not create folder: '.$destination);
	 } else { echo "Destination folder created! <br>"; }

$zip = new ZipArchive;
$res = $zip->open($zipfile);
if ($res === TRUE) {
  // extract it to the path we determined above
  $zip->extractTo($temp_unzip_path);
  // $zip->extractTo($path);
  $zip->close();
  echo "WOOT! $zipfile extracted to $temp_unzip_path <br>";
} else {
  echo "Doh! I couldn't open $zipfile <br>";
}

// GitHub puts all files in an enclosing folder that changes name to signify commits so we don't can't assume the name of the folder.


$subfolder='';

// $rootFolder = preg_replace( '~(\w)$~' , '$1' . DIRECTORY_SEPARATOR , realpath( getcwd() ) );
// $temp_unzip_path= $rootFolder."unzip_temp_555bcb105c4967.95890756";
    
echo "scan directory starting from: $temp_unzip_path <br>";
$subfolder='';

$depth=1;
$dir = new RecursiveDirectoryIterator( $temp_unzip_path);
$files = new RecursiveIteratorIterator($dir);
$files->setMaxDepth($depth);

$tally=0;
 foreach($files as $file) {
 	 $tally++;
 	if ($file->isDir()) {
         $dirname= $file->getPath(); 
		  // echo $dirname." is a directory!<br>";
        if ($dirname==$temp_unzip_path) {
         //  echo "ignore this one as current folder<br>";   
        } else {
            $subfolder=$dirname.'/';
        }
            
    } // END if
        
  } // END foreach

// echo "Tally: $tally <br>";
echo "Subfolder is : $subfolder <br>";


// Move files
 
// $startingloc = $temp_unzip_path.'/'.$subfolder;
$startingloc = $subfolder;

echo "starting location for move is: ".$startingloc." <br>";

// Get array of all source files
 $files = scandir($startingloc);
  // Identify directories
  $source = $startingloc;
		
  // Cycle through all source files
  foreach ($files as $file) {
    if (in_array($file, array(".",".."))) continue;
        // if move files is successful delete the original temp folder
        if (rename($source.$file, $destination.$file)) {
            // rmdir($startingloc);
            // rmdir($temp_unzip_path);
            // rrmdir($temp_unzip_path);
            
           
        }
    }        

rrmdir($temp_unzip_path);
 // Delete original Zip file
unlink($path."/".$zipfile);

?>
