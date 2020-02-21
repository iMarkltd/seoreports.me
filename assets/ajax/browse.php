<?php
/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2004 Frederico Caldeira Knabben
 *
 * Licensed under the terms of the GNU Lesser General Public License
 * (http://www.opensource.org/licenses/lgpl-license.php)
 *
 * For further information go to <a href="http://www.fredck.com/FCKeditor/" target="_blank">http://www.fredck.com/FCKeditor/</a> 
 * or contact <a href="mailto:fckeditor@fredck.com" target="_new">fckeditor@fredck.com</a>.
 *
 * browse.php: Browse function.
 *
 * Authors:
 *   Frederic TYNDIUK (http://www.ftls.org/ - tyndiuk[at]ftls.org)
 */

// Init var :
$IMAGES_BASE_URL = '/assets/uploads/easyimage/'; 
$IMAGES_BASE_DIR = $_SERVER['DOCUMENT_ROOT'].$IMAGES_BASE_URL;
$IMAGE_MAX_DISPLAY_WIDTH = 300;
// End int var

// Thanks : php dot net at phor dot net
function walk_dir($path) {
    if ($dir = opendir($path)) {
        while (false !== ($file = readdir($dir))) {
            if ($file[0]==".") continue;
            if (is_dir($path."/".$file))
                $retval = array_merge($retval,walk_dir($path."/".$file));
            else if (is_file($path."/".$file))
                $retval[]=$path."/".$file;
            }
        closedir($dir);
        }
    return $retval;
}

function CheckImgExt($filename) {
    $img_exts = array("gif","jpg", "jpeg","png");
    foreach($img_exts as $this_ext) {
        if (preg_match("/\.$this_ext$/", strtolower($filename))) {
            return TRUE;
        }
    }
    return FALSE;
}

###################################################################################
# process uploaded files
###################################################################################
if(isset($_FILES['upload'])) {
    if (file_exists($IMAGES_BASE_DIR.$_FILES['upload']['name']) &&
        !empty($_FILES['upload']['name'])) {
        $fck_errMsg = "Error : File ".$_FILES['upload']['name']." exists, can't overwrite it...";
    } else {
        if (is_uploaded_file($_FILES['upload']['tmp_name'])) { //if file is uploaded
            $savefile = $IMAGES_BASE_DIR.$_FILES['upload']['name'];
            if (move_uploaded_file($_FILES['upload']['tmp_name'], $savefile)) { //move file from tmp to upload dir
                chmod($savefile, 0666); //change perms
                list($iWidth) = getimagesize($savefile);
                $fck_loadScript = "getImage('" . $_FILES['upload']['name'] . "', " . $iWidth . ");";
                $fck_sucMsg = "Your image ('" . $_FILES['upload']['name'] . "') was successfully uploaded.";
            }
        } else { //if there were errors uploading the file
            $fck_errMsg = "Error : ";
            switch($_FILES['upload']['error']) {
                case 0: //no error; possible file attack!
                    $fck_errMsg .= "There was a problem with your upload.";
                    break;
                case 1: //uploaded file exceeds the upload_max_filesize directive in php.ini
                    $fck_errMsg .= "The file you are trying to upload is too big.";
                    break;
                case 2: //uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                    $fck_errMsg .= "The file you are trying to upload is too big.";
                    break;
                case 3: //uploaded file was only partially uploaded
                    $fck_errMsg .= "The file you are trying upload was only partially uploaded.";
                    break;
                case 4: //no file was uploaded
                    $fck_errMsg .= "You must select an image for upload.";
                    break;
                default: //a default error, just in case!  :)
                    $fck_errMsg .= "There was a problem with your upload.";
                    break;
            }
        }
    }
}

###################################################################################
# load files from directory
###################################################################################
foreach (walk_dir($IMAGES_BASE_DIR) as $file) {
    $file = preg_replace("#//+#", '/', $file);
    $IMAGES_BASE_DIR = preg_replace("#//+#", '/', $IMAGES_BASE_DIR);
    $file = preg_replace("#$IMAGES_BASE_DIR#", '', $file);
    if (CheckImgExt($file)) {
        $files[] = $file;    //adding filenames to array
    }
}

sort($files);    //sorting array

// if file wasn't upload, meaning this is our first time, set image preview to first image
if(empty($fck_loadScript)) {
    list($iWidth) = getimagesize($IMAGES_BASE_DIR . $files[0]);
    $fck_loadScript = "getImage('" . $files[0] . "', " . $iWidth . ");";
}

// generating $html_img_lst
foreach ($files as $file) {
    $html_img_lst .= "<a href=\"javascript&#058;getImage('$file');\">$file</a><br>\n";
}

?>
