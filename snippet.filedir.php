<?php
/**
 * FileDir
 * Copyright 2012 by Artdevue.com  <info@artdevue.com>
 * Arguings at a forum http://community.modx-cms.ru/
 * @package filedir
 */
/**
 * FileDir
 * You can display any type of files to specified directory. Snippet for MODx Revolution 2.0 and above.
 *
 * @package filedir
 * It is easy to display any type of files from a directory, filtering on the output type.
 * You can create a photo gallery or video, file archives.
 * 
 * Options:
 * &dir - path to the file folder by default - 'assets /'
 * &tpl - inner Tpl chunk, by default - 'tplFileDir'
 * 	Available placeholders:
 * 	[[+file]] - path to the file
 * 	[[+fname]] - the name of the file
 * 	[[+size]] - the file size
 * 	[[+fidx]] - the number of order
 * &tplOut - outer Tpl chunk, by default - 'tplFileDirOut'
 * 	Available placeholders:
 * 	[[+res_filedir]] - Required, do not delete!
 * 	[[+ftotal]] - The total number of files
 * &limit - maximum number of output files
 * &fcache - to enable the cache (false/true), the default - 'false'
 * &cachetime - the time of caching, default '0 ', to clear the cache
 * &filetip - types of output files (comma), by default - 'jpg,png,gif'
 * 
 * Example snippet call:
 * [[!filedir? &dir = `assets/images/[[*id]]/`]]
 * [[!filedir? &dir = `assets/images/61/` &fcache =`true`]]
 * 
 * An example of a template for the gallery
 * <div>
 *    <a href="[[+file]]" title="[[+fname]]">
 *      <img src="[[+file:rezimgcrop=`r-150x,c-150x75`]]" alt="[[+fname]]">
 *    </a>
 * </div>
 * 
 * An example of a template for the file archive
 * <div>
 *      File: [[+fname]]
 * 	Saze: [[+size]]
 * 	<a href="[[+file]]">Download</ a>
 * </div>
 */

//default settings
$dir = $modx->getOption('dir',$scriptProperties,'assets/');
$tpl = $modx->getOption('tpl',$scriptProperties,'tplFileDir');
$tplOut = $modx->getOption('tplOut',$scriptProperties,'tplFileDirOut');
$fcache = $modx->getOption('fcache',$scriptProperties,null);
$cachetime = $modx->getOption('cachetime',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,null);
$filetip = isset($filetip) ? explode(',',$filetip) : array('jpg','png','gif');

$base_path = $modx->getOption('base_path');
if($modx->getCacheManager() && isset($fcache)){
  $keyc = md5('filedir::dir:'.$dir.':tpl:'.$tpl.':tplOut:'.$tplOut.':limit:'.$limit.':$filetip:'.implode('', $filetip));
  $out = $modx->cacheManager->get($keyc);
  if (isset($out)){
    return $out;
  }else{
    $inCache = false;
  }
}
// check for the presence of slash
if ($dir{0} == '/') $dir = substr($dir,1);
if (substr($dir, -1) != '/') $dir .= '/';
// verify the existence of a directory
if(!is_dir($dir)) return 'Error! directory does not exist.'; 

$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
$x=1;
$out='';
if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) { 
        if ($file != "." && $file != ".." && $file != "Thumb.db") {
	  $rash = array_pop(explode('.',$file));
	  $fname = str_replace('.'.$rash, '', $file);
	  if(in_array($rash, $filetip)){
	    $size = filesize($dir.$file);	    
	    $sizef = round($size/pow(1024, ($i = floor(log($size, 1024)))), $i > 1 ? 2 : 0) . $sizes[$i];
	    if($size != 0){
	      $out .= $modx->getChunk($tpl,array('file'=>$dir.$file, 'fname'=>$fname, 'size'=>$sizef, 'fidx'=>$x));
	      if($x++ == $limit) break;
	    }
	  }
        } 
    }
    closedir($handle);
  $out = $modx->getChunk($tplOut,array('res_filedir'=>$out, 'ftotal'=>$x-1));
}
if (!$inCache && isset($fcache)){
  $modx->cacheManager->set($keyc, $out, $cachetime);
}
return $out;