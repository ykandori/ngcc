<?php
require_once 'vendor\autoload.php';

use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;


// Create blob REST proxy.
//$connectionString = "DefaultEndpointsProtocol=https;AccountName=kandori;AccountKey=HEwEH9cG3Ta23zG6RuE4JrM5+ytzTqs/XjEO3qzJLqi9E+cCGkbSHYu60JB4TbcanO6JJ3oU/c0eEVDVJjWrBQ==;EndpointSuffix=core.windows.net";
$connectionString = "DefaultEndpointsProtocol=https;AccountName=kandori;AccountKey=HEwEH9cG3Ta23zG6RuE4JrM5+ytzTqs/XjEO3qzJLqi9E+cCGkbSHYu60JB4TbcanO6JJ3oU/c0eEVDVJjWrBQ==";
$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);

$containerName = "kandori-container";


try {
    $file = "cat.mp4";

    // Upload blob.
    $content = fopen($file, "r");
    $blobRestProxy->createBlockBlob($containerName, $file, $content);
    echo "upload done. <br />\n";

/*
    // List blobs.
    $blob_list = $blobRestProxy->listBlobs($containerName);
    $blobs = $blob_list->getBlobs();

    foreach($blobs as $blob)
    {
        echo $blob->getName().": ".$blob->getUrl()."<br />";
        echo "<img src='".$blob->getUrl()."'><br />";
    }
*/

    //Download blobs.
    $blob = $blobRestProxy->getBlob($containerName, $file);
    fpassthru($blob->getContentStream());
    echo "download done. <br />\n";

/*  
    // Delete blob.
    $blobRestProxy->deleteBlob ($containerName, $file);
    echo "delete done. <br />\n";
*/

}
catch(ServiceException $e){
    // Handle exception based on error codes and messages.
    // Error codes and messages are here: 
    // http://msdn.microsoft.com/en-us/library/windowsazure/dd179439.aspx
    $code = $e->getCode();
    $error_message = $e->getMessage();
    echo $code.": ".$error_message."<br />";
}
?>