# FileDir (MODX Revolution)
=========
** It is easy to display any type of files from a directory, filtering on the output type.
You can create a photo gallery or video, file archives.

----------------------------------------

Donwload [MODX extras](http://modx.com/extras/package/filedir)

### Options:

    &dir - path to the file folder by default - 'assets /'
    &tpl - inner Tpl chunk, by default - 'tplFileDir'
*   Available placeholders:
    [[+file]] - path to the file
    [[+fname]] - the name of the file
    [[+size]] - the file size
    [[+fidx]] - the number of order
    &tplOut - outer Tpl chunk, by default - 'tplFileDirOut'
*   Available placeholders:
    [[+res_filedir]] - Required, do not delete!
    [[+ftotal]] - The total number of files
    &limit - maximum number of output files
    &fcache - to enable the cache (false/true), the default - 'false'
    &cachetime - the time of caching, default '0 ', to clear the cache
    &filetip - types of output files (comma), by default - 'jpg,png,gif'

### Example snippet call:

    ``[[!filedir? &dir = `assets/images/[[*id]]/`]]
    [[!filedir? &dir = `assets/images/[[*id]]/`]]
    [[!filedir? &dir = `assets/images/61/` &fcache =`true`]]``
    
*   An example of a template for the gallery
    ```<div>
      <a href="[[+file]]" title="[[+fname]]">
        <img src="[[+file:rezimgcrop=`r-150x,c-150x75`]]" alt="[[+fname]]">
      </a>
    </div>```

*   An example of a template for the file archive
    ``<div>
      File: [[+fname]]
      Saze: [[+size]]
      <a href="[[+file]]">Download</a>
    </div>
    <div>
      <a href="[[+file]]" title="[[+fname]]">
        <img src="[[+file:rezimgcrop=`r-150x,c-150x75`]]" alt="[[+fname]]">
      </a>
    </div>``

*   An example of a template for the file archive

    ``<div>
      File: [[+fname]]
      Saze: [[+size]]
      <a href="[[+file]]">Download</a>
    </div>
    ``

*   You can use c expansion FileUpload
    Example:

    ``[[!FileUpload?
    & uploadfields=`20`
    & uploadgroups=`Administrator, Editors, Subadmins`
    & path=`assets/images/[[*id]]`
    ]]``

    ``< div >
      File: [[+fname]]
      Size: [[+size]]
      <a href="[[+file]]">Download</a>
    < /div >``

   `` <div>
      File: [[+fname]]
      Size: [[+size]]
      <a href="[[+file]]">Download</a>
    </div>``

### Authors
<table>
  <tr>
    <td><img src="http://www.gravatar.com/avatar/39ef1c740deff70b054c1d9ae8f86d02?s=60"></td><td valign="middle">Valentin Rasulov<br>artdevue.com<br><a href="http://artdevue.com">http://artdevue.com</a></td>
  </tr>
</table>
