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
 */

//default settings
$dir = $modx->getOption('dir',$scriptProperties,'assets/');
$tpl = $modx->getOption('tpl',$scriptProperties,'tplFileDir');
$tplOut = $modx->getOption('tplOut',$scriptProperties,'tplFileDirOut');
$fcache = $modx->getOption('fcache',$scriptProperties,null);
$cachetime = $modx->getOption('cachetime',$scriptProperties,0);
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,0);
$offset = $modx->getOption('offset',$scriptProperties,0);
$class = $modx->getOption('class',$scriptProperties,'fd');
$filetip = isset($filetip) ? explode(',',$filetip) : array('jpg','png','gif');

$base_path = $modx->getOption('base_path');
if($modx->getCacheManager() && isset($fcache)){
  $keyc = md5('filedir::dir:'.implode('', $scriptProperties));
  $out = $modx->cacheManager->get($keyc);
  if (isset($out)){
    $modx->setPlaceholder('total',$out['total']);
    return $out['out'];
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
$output = array();
if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) { 
        if ($file != "." && $file != ".." && $file != "Thumb.db") {
          $rash = array_pop(explode('.',$file));
          $fname = str_replace('.'.$rash, '', $file);
          if(in_array($rash, $filetip)){ 
            $size = filesize($dir.$file);
            $fdclass = trim(strlen($class)) > 0 ? $class.'_'.$rash : $rash;
            $sizef = round($size/pow(1024, ($i = floor(log($size, 1024)))), $i > 1 ? 2 : 0) . $sizes[$i];
            if($size != 0){
              if(($x-1 < $limit+$start+$offset && $x > $offset+$start) || ($offset == $start && $offset == $limit)){
                $out .= $modx->getChunk($tpl,array('file'=>$dir.$file, 'fname'=>$fname, 'size'=>$sizef, 'fidx'=>$x, 'fdclass'=>$fdclass));
              }
              $x++;
            }
          }
        } 
    }
    closedir($handle);
  $output['out'] = $modx->getChunk($tplOut,array('res_filedir'=>$out, 'ftotal'=>$x-1));
  $output['total'] = $x-1;
}
if (!$inCache && isset($fcache)){
  $modx->cacheManager->set($keyc, $output, $cachetime);
}
$modx->setPlaceholder('total',$x-1);
return $output['out'];