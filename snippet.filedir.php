<?php
/**
 * FileDir
 * Copyright 2012 by Artdevue.com  <info@artdevue.com>
 * Arguings at a forum http://modx.im/blog/addons/78.html
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
 *     Available placeholders:
 * [[+file]] - path to the file
 * [[+fname]] - the name of the file
 * [[+fdclass]] — class name depends on the file extension
 * [[+size]] - the file size
 * [[+fidx]] - the number of order
 * [[+date]] - creation date file
 * &tplOut - outer Tpl chunk, by default - 'tplFileDirOut'
 *    Available placeholders:
 * [[+res_filedir]] - Required, do not delete!
 * [[+ftotal]] - The total number of files
 * &limit - maximum number of output files
 * &fcache - to enable the cache (false/true), the default - 'false'
 * &cachetime - the time of caching, default '0 ', to clear the cache
 * &filetip - types of output files (comma), by default - 'jpg,png,gif'
 * &sort - Enable or disable sorting (true or false, default - false)
 * &sortDir - sort direction (DESC or ASC, default - ASC)
 * &sortBy - name to sort (possibly: fname,file,fsize,date)
 * &class — prefix for the style in the template tpl, by default - 'fd', ie if the file
 *          format is jpg, then placeholder [[+ fdclass]] in the template tpl bulet
 *          output - fd_jpg. If we are in the carts snippets Specify & class = ``,
 *          then displays the placeholder in the class extension - jpg.
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
 *    Saze: [[+size]]
 *    <a href="[[+file]]">Download</ a>
 * </div>
 */

//default settings
$dir = $modx->getOption('dir', $scriptProperties, 'assets/');
$tpl = $modx->getOption('tpl', $scriptProperties, 'tplFileDir');
$tplOut = $modx->getOption('tplOut', $scriptProperties, 'tplFileDirOut');
$fcache = $modx->getOption('fcache', $scriptProperties, null);
$cachetime = $modx->getOption('cachetime', $scriptProperties, 0);
$start = $modx->getOption('start', $scriptProperties, 0);
$limit = $modx->getOption('limit', $scriptProperties, 0);
$offset = $modx->getOption('offset', $scriptProperties, 0);
$class = $modx->getOption('class', $scriptProperties, 'fd');
$filetip = isset($filetip) ? explode(',', $filetip) : array('jpg', 'png', 'gif');
$sort = $modx->getOption('sort', $scriptProperties, false);
$sortDir = $modx->getOption('sortDir', $scriptProperties, 'ASC'); // possibly: DESC or ASC
$sortBy = $modx->getOption('sortBy', $scriptProperties, 'fname'); // possibly: fname,file,fsize,date
$formatDate = $modx->getOption('formatDate', $scriptProperties, 'd/m/Y H:i');
$inCache = true;

if (!array_search($sortBy, array('fname', 'file', 'fsize', 'date')))
    $sortBy = 'fname';

$base_path = $modx->getOption('base_path');
if ($modx->getCacheManager() && isset($fcache)) {
    $inCache = true;
    $keyc = md5('filedir::dir:' . implode('', $scriptProperties));
    $out = $modx->cacheManager->get($keyc);
    if (isset($out)) {
        $modx->setPlaceholder('total', $out['total']);
        return $out['out'];
    } else {
        $inCache = false;
    }
}
// check for the presence of slash
if ($dir{0} == '/')
    $dir = substr($dir, 1);
if (substr($dir, -1) != '/')
    $dir .= '/';
// verify the existence of a directory
if (!is_dir($dir))
    return 'Error! directory does not exist.';

$sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
$diri = new DirectoryIterator($base_path . $dir);

$x = 1;
$output = array();
$sortArray = array();

foreach ($diri as $file) {
    if ($file->isFile() and in_array($file->getExtension(), $filetip) and ($file->getSize() > 0)) {
        switch ($sortBy) {
            case 'fname':
                $sortArray[$x] = $file->getBasename('.' . $file->getExtension());
                break;

            case 'file':
                $sortArray[$x] = $dir . $file->getFilename();
                break;

            case 'fsize':
                $sortArray[$x] = $file->getSize();
                break;

            case 'date':
                $sortArray[$x] = $file->getCTime();
                break;
        }

        if (($x - 1 < $limit + $start + $offset && $x > $offset + $start) || ($offset == $start && $offset == $limit)) {
            $itemArray = array(
                'fname' => $file->getBasename('.' . $file->getExtension()),
                'file' => $dir . $file->getFilename(),
                'fsize' => $file->getSize(),
                'size' => round(($file->getSize()) / pow(1024, ($i = floor(log(($file->getSize()), 1024)))), $i > 1 ? 2 : 0) . $sizes[$i],
                'fdclass' => trim(strlen($class)) > 0 ? $class . '_' . $file->getExtension() : $file->getExtension(),
                'date' => date($formatDate, $file->getCTime()),
                'fidx' => $x
            );
            $imgArray[$x] = $itemArray;
        }
        $x++;
    }
}
if ($sort == true)
    array_multisort($sortArray, ($sortDir === 'ASC' ? SORT_ASC : SORT_DESC), SORT_STRING, $imgArray);

$x = 1;
foreach ($imgArray as $img) {
    $img['fidx'] = $x;
    $output[$x] = $modx->getChunk($tpl, $img);
    $x++;
}

$output['out'] = $modx->getChunk($tplOut, array(
    'res_filedir' => implode('', $output),
    'ftotal' => $x - 1
));
$output['total'] = $x - 1;

if (!$inCache && isset($fcache)) {
    $modx->cacheManager->set($keyc, $output, $cachetime);
}
$modx->setPlaceholder('total', $x - 1);
return $output['out'];