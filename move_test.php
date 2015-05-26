<?php
// This script is a test of doing the file move with scandir instead of the iterator approach which fails on some android devices
// Currently fails at the rename step on mac?
// once working would form the basis of the "alterantive" move option in getinfected.php

$debug = 1;

//----------
// Make Directory

function makeDIR($directory,$debugtxt=0) {
    // Create infect directory if it doesn't exist:
    if (file_exists($directory)) {
        if ($debugtxt) { echo "<p>Directory <b>$directory</b> already exists </p>"; }
        $result = true; // Return true as success is when the directory has either been created or already exists
    } else {
        // Make the new temp sub_folder for unzipped files
        if (!mkdir($directory, 0755, true)) {
            if ($debugtxt) { echo "<p>Error: Could not create folder <b>$directory</b> - check file permissions";}
            $result= false;
        } else { 
            if ($debugtxt) { echo "Folder <b>$directory</b> Created <br>";}  
            $result = true;
        } // END mkdir
    } // END if file exists
    return $result;
} // END makeDIR 

//---------
// Move Directory

function moveDIR($dir,$dest="") {
    $debug = 1;
    $result=true;
    
    if($debug) { echo "<p>Moving directory $dir to $dest</p>";}

    if($dest!="") {
        if($debug) { echo "<p>Dest not empty</p>";}
        if(!file_exists($dest)) {
            if($debug) { echo "<p>$dest folder doesn't exist - mkdir</p>"; }
            makeDIR($dest,1);
            $dest = realpath($dest);
        } else {
            if($debug) { echo "<p>Dir already exists</p>";} 
        }// END file doesn't exist
    } else {
        if($debug) { echo "<p>Dest empty</p>";}
    } // END dest

    $path = dirname(__FILE__);
    $files = scandir($dir);
    
    foreach($files as $file) {
        if (substr( $file ,0,1) != ".") {
            if (is_dir(realpath($file))) {
                echo "<h2>$file is a directory</h2>";
                echo "<p>Scandir it..</p>";
                
                // Destination:
                if ($dest==""){
                    $newDir = realpath($file);
                } else {
                    $newDir = $dest."/".$file;
                }

                $fullpath=$dir."/".$file;
                if (!moveDIR($fullpath,$newDir)) {
                    $result = false;
                }
                
            } else {
                echo "<p>$file is a file</p>";
                 
                
                // $currentFile = realpath($file); // current location
                $currentFile = $dir.'/'.$file;
                
                echo "<p>Current File is $currentFile</p>";
                                // Destination:
                if ($dest==""){
                    $newFile = realpath($file);
                } else {
                    $newFile = $dest."/".$file;
                }
        
                // if file already exists remove it
                if (file_exists($newFile)) {
                    if($debug) { echo "<p>File $newFile already exists - Deleting</p>"; }
                    unlink($newFile);
                } else {
                    if($debug) { echo "<p>File $newFile doesn't exist yet"; }
                }
        
                // Move via rename
                // rename(oldname, newname)
                if (rename($currentFile, $newFile)) {
                    if($debug) { echo "<p>Moved $currentFile to $newFile</p>"; }
                } else {
                    if($debug) { echo "<p>Failed to move $currentFile to $newFile</p>"; }
                    $result = false;
                } // END rename 
                
            } // END if dir or file
        } // end if no dot
    } // END foreach
    return $result;
} // END moveDIR

// ----------
// START MAIN:

echo "<h1>Scandir Test</h1>";

$subfolder = "temp/subfolder2";
$destination = "moveto";
$path = dirname(__FILE__);

echo $path."<br>";

// is_dir requires full path so:
$subfolder = $path.'/'.$subfolder;

if ($destination=="") {
    $destination = $path;
} else {
    $destination = $path.'/'.$destination;
}

if($debug) { echo "<p>Moving files from<br>  $subfolder <br> to: $destination</p>"; }

if (moveDIR($subfolder,$destination)) {
    echo "<h2>Move Succeeded</h2>";
} else {
    echo "<h2>ERROR! Move Failed!</h2>";
} // End moveDIR check

?>
