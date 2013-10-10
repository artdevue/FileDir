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
    [[+fdclass]] — class name depends on the file extension
    [[+size]] - the file size
    [[+fidx]] - the number of order
    [[+date]] - creation date file
    &tplOut - outer Tpl chunk, by default - 'tplFileDirOut'
*   Available placeholders:
    [[+res_filedir]] - Required, do not delete!
    [[+ftotal]] - The total number of files
    &limit - maximum number of output files
    &fcache - to enable the cache (false/true), the default - 'false'
    &cachetime - the time of caching, default '0 ', to clear the cache
    &filetip - types of output files (comma), by default - 'jpg,png,gif'
    &sort - Enable or disable sorting (true or false, default - false)
    &sortDir - sort direction (DESC or ASC, default - ASC)
    &sortBy - name to sort (possibly: fname,file,fsize,date)
    &class — prefix for the style in the template tpl, by default - 'fd', ie if the file
           format is jpg, then placeholder [[+ fdclass]] in the template tpl bulet
           output - fd_jpg. If we are in the carts snippets Specify & class = ``,
           then displays the placeholder in the class extension - jpg.

### Example snippet call:

    ```php[[!filedir? &dir = `assets/images/[[*id]]/`]]
    [[!filedir? &dir = `assets/images/[[*id]]/`]]
    [[!filedir? &dir = `assets/images/61/` &fcache =`true`]]
    ```
    
*   An example of a template for the gallery
    ```html<div>
      <a href="[[+file]]" title="[[+fname]]">
        <img src="[[+file:rezimgcrop=`r-150x,c-150x75`]]" alt="[[+fname]]">
      </a>
    </div>```

*   An example of a template for the file archive
    ```html<div>
      File: [[+fname]]
      Saze: [[+size]]
      <a href="[[+file]]">Download</a>
    </div>
    <div>
      <a href="[[+file]]" title="[[+fname]]">
        <img src="[[+file:rezimgcrop=`r-150x,c-150x75`]]" alt="[[+fname]]">
      </a>
    </div>
    ```

*   An example of a template for the file archive
    ```html
    <div>
      File: [[+fname]]
      Saze: [[+size]]
      <a href="[[+file]]">Download</a>
    </div>
    ```

*   You can use c expansion FileUpload
    Example:
    ```php
    [[!FileUpload?
    & uploadfields=`20`
    & uploadgroups=`Administrator, Editors, Subadmins`
    & path=`assets/images/[[*id]]`
    !]]```
	```html
    < div >
      File: [[+fname]]
      Size: [[+size]]
      <a href="[[+file]]">Download</a>
    < /div >```

    ```html
    <div>
      File: [[+fname]]
      Size: [[+size]]
      <a href="[[+file]]">Download</a>
    </div>```
