<?php
require_once 'vendor\autoload.php';

use WindowsAzure\Common\ServicesBuilder;
use WindowsAzure\Common\ServiceException;

//hogege
// Create blob REST proxy.
//$connectionString = "DefaultEndpointsProtocol=https;AccountName=kandori;AccountKey=HEwEH9cG3Ta23zG6RuE4JrM5+ytzTqs/XjEO3qzJLqi9E+cCGkbSHYu60JB4TbcanO6JJ3oU/c0eEVDVJjWrBQ==;EndpointSuffix=core.windows.net";
$connectionString = "DefaultEndpointsProtocol=https;AccountName=kandori;AccountKey=HEwEH9cG3Ta23zG6RuE4JrM5+ytzTqs/XjEO3qzJLqi9E+cCGkbSHYu60JB4TbcanO6JJ3oU/c0eEVDVJjWrBQ==";
$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);


//var_dump($blobRestProxy);
//exit();


try {
    // List blobs.
    $blob_list = $blobRestProxy->listBlobs("kandori-container");
    $blobs = $blob_list->getBlobs();


    echo "test-test";


    foreach($blobs as $blob)
    {
        echo $blob->getName().": ".$blob->getUrl()."<br />";
        echo "<img src='".$blob->getUrl()."'><br />";
    }
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