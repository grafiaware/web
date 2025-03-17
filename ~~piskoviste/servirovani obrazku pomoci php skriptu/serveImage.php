<?php

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

// https://stackoverflow.com/questions/1353850/serve-image-with-php-script-vs-direct-loading-an-image

Instead of changing the direct image url in the HTML, you can put a line in the Apache configuration or .htaccess to rewrite all the requests of images in a directory to a php script. Then in that script you can make use of the request headers and the $_server array to process the request and serve the file.

First in your .htaccess:

RewriteRule ^(.*)\.jpg$ serve.php [NC]
RewriteRule ^(.*)\.jpeg$ serve.php [NC]
RewriteRule ^(.*)\.png$ serve.php [NC]
RewriteRule ^(.*)\.gif$ serve.php [NC]
RewriteRule ^(.*)\.bmp$ serve.php [NC]

The script serve.php must be in the same directory as .htaccess. You will probably write something like this:

<?php
$filepath=$_SERVER['REQUEST_URI'];
$filepath='.'.$filepath;
if (file_exists($filepath))
{
touch($filepath,filemtime($filepath),time()); // this will just record the time of access in file inode. you can write your own code to do whatever
$path_parts=pathinfo($filepath);
switch(strtolower($path_parts['extension']))
{
case "gif":
header("Content-type: image/gif");
break;
case "jpg":
case "jpeg":
header("Content-type: image/jpeg");
break;
case "png":
header("Content-type: image/png");
break;
case "bmp":
header("Content-type: image/bmp");
break;
}
header("Accept-Ranges: bytes");
header('Content-Length: ' . filesize($filepath));
header("Last-Modified: Fri, 03 Mar 2004 06:32:31 GMT");
readfile($filepath);

}
else
{
 header( "HTTP/1.0 404 Not Found");
 header("Content-type: image/jpeg");
 header('Content-Length: ' . filesize("404_files.jpg"));
 header("Accept-Ranges: bytes");
 header("Last-Modified: Fri, 03 Mar 2004 06:32:31 GMT");
 readfile("404_files.jpg");
}
/*
By Samer Mhana
www.dorar-aliraq.net
*/
?>

(This script can be improved!)
+



There are a lot of good answers above, but none of them provide working code that you can use in your PHP app. I've set mine up so that I lookup the name of the image in a database table based off a different identifier. The client never sets the name of the file to download as this is a security risk.

Once the image name is found, I explode it to obtain the extension. This is important to know what type of header to serve based off the image type (i.e. png, jpg, jpeg, gif, etc.). I use a switch to do this for security reasons and to convert jpg -> jpeg for the proper header name. I've included a few additional headers in my code that ensure the file is not cached, that revalidation is required, to change the name (otherwise it will be the name of the script that is called), and finally to read the file from the server and transmit it.

I like this method since it never exposes the directory or actual file name. Be sure you authenticate the user before running the script if you are trying to do this securely.

$temp = explode('.', $image_filename);
$extension = end($temp);    // jpg, jpeg, gif, png - add other flavors based off your use case

switch ($extension) {
    case "jpg":
        header('Content-type: image/jpeg');
        break;
    case "jpeg":
    case "gif":
    case "png":
        header('Content-type: image/'.$extension);
        break;
    default:
        die;    // avoid security issues with prohibited extensions
}

header('Content-Disposition: filename=photo.'.$extension);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
readfile('../SECURE_DIRECTORY/'.$image_filename);


+

    Last-Modified
        The last modified date for the requested object, in RFC 2822 format
        Example: header('Last-Modified: Tue, 15 Nov 1994 12:45:26 GMT');
        See the function filemtime and date to format it into the required RFC 2822 format
            Example: header('Last-Modified: '.date(DATE_RFC2822, filemtime($filename)));
        You can exit the script after sending a 304 if the file modified time is the same.
    status code
        Example: header("HTTP/1.1 304 Not Modified");
        you can exit now and not send the image one more time

For last modified time, look for this in $_SERVER

    If-Modified-Since
        Allows a 304 Not Modified to be returned if content is unchanged
        Example: If-Modified-Since: Sat, 29 Oct 1994 19:43:31 GMT
        Is in $_SERVER with the key http_if_modified_since
