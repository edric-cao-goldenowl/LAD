# Sharing objects using presigned URLs
By default, all S3 objects are private. Only the object owner has permission to access them. However, the object owner can optionally share objects with others by creating a presigned URL, using their own security credentials, to grant time-limited permission to download the objects.

When you create a presigned URL for your object, you must provide your security credentials and then specify a bucket name, an object key, an HTTP method (GET to download the object), and an expiration date and time. The presigned URLs are valid only for the specified duration. If you created a presigned URL using a temporary token, then the URL expires when the token expires, even if the URL was created with a later expiration time.   

Anyone who receives the presigned URL can then access the object. For example, if you have a video in your bucket and both the bucket and the object are private, you can share the video with others by generating a presigned URL. Because presigned URLs grant access to your Amazon S3 buckets to whoever has the URL, we recommend that you protect them appropriately
*****
# Generating a presigned URL to share an object
You can generate a presigned URL for an object without writing any code by using the S3 console or AWS Explorer for Visual Studio. You can also generate a presigned URL programmatically using the AWS SDKs for Java, .NET, Ruby, PHP, Node.js, Python, and Go.

In the document, i will introduce you generate presigned URL without writing any code by using the S3 console and use SDK for PHP version 3 to generate a presigned URL to share an Object in S3.
# Using the S3 console
You can use the AWS Management Console to generate a presigned URL for an object by following these steps.

> **Note**
In the Amazon S3 console, the maximum expiration time for a presigned URL is 12 hours from the time of creation.

**To generate a presigned URL using the AWS Management Console**

1. Sign in to the AWS Management Console and open the Amazon S3 console at https://console.aws.amazon.com/s3/.

2. In the Buckets list, choose the name of the bucket that contains the object that you want a presigned URL for.

3. In the Objects list, select the object that you want to create a presigned URL for.

4. On the Actions menu, choose Share with a presigned URL.

5. Specify how long you want the presigned URL to be valid.

6. Choose Create presigned URL.

7. When a confirmation appears, the URL is automatically copied to your clipboard. You will see a button to copy the presigned URL if you need to copy it again.

# Using the PHP AWS SDKs
## Credentials
Before running the example code, configure your AWS credentials.
To make requests to Amazon Web Services, supply AWS access keys, also known as credentials, to the AWS SDK for PHP.

You need create new User IMA have permission share S3 (AmazonS3FullAccess)
### Create Access Security credentials
Select a user who have permission access the object S3.

In summary of Users. Choose Security credentials tag. 

![](assets/user-overview.png)
In the access keys part. Use can see list of access key you created before. You can have a maximum of two access keys (active or inactive) at a time. 


You can use access key exits, if you miss or forget the secret key, you can remove access key and create new one. I recommend create new access key. 
- Click create access key
- Aws will generate auto 1 access key include: 
    - **Access key ID**
    - **Secret access key**: This is the only time that the secret access keys can be viewed or downloaded. You cannot recover them later. However, you can create new access keys at any time.
![](assets/create-access-key.png)

### Install SDK for PHP project (via Composer)
If Composer is already installed globally on your system, run the following in the base directory of your project to install AWS SDK for PHP as a dependency:
```angular2html
composer require aws/aws-sdk-php
```
### Add autoloader to your php scripts
To utilize the AWS SDK for PHP in your scripts, include the autoloader in your scripts, as follows.
```angular2html
<?php
   require '/path/to/vendor/autoload.php';
?>
```

### Including the SDK in your code
#### Creating a client & pass credentials 
You can create a client by passing an associative array of options to a client’s constructor.

```require 'vendor/autoload.php';

use Aws\S3\S3Client;

//Create an S3Client
$s3Client = new S3Client([
 'version'     => 'latest',
    'region'      => 'ap-southeast-1',
    'credentials' => [
        'key'    => 'access-key-id',
        'secret' => 'Secret-access-key',
    ],
]);
```


### Creating a pre-signed URL
You can create pre-signed URLs for any Amazon S3 operation using the getCommand method for creating a command object, and then calling the _createPresignedRequest()_ method with the command. When ultimately sending the request, be sure to use the same method and the same headers as the returned request.


```angular2html
//Creating a presigned URL
$cmd = $s3Client->getCommand('GetObject', [
    'Bucket' => 'my-bucket',
    'Key' => 'testKey'
]);

$request = $s3Client->createPresignedRequest($cmd, '+ minute');

// Get the actual presigned-url
$presignedUrl = (string)$request->getUri();
```

- **my-bucket**: this is name bucket you created in S3
- **testKey**: this is ```Key``` in Object Overview. Actually it is the path of object.
- **minute**: object lifetime  (in minutes)

![](assets/s3-overview.png)
## Full code DEMO

```angular2html
<?php
include './../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;


// Hard-coded credentials
$s3Client = new S3Client([
    'version'     => 'latest',
    'region'      => 'ap-southeast-1',
    'credentials' => [
        'key'    => 'AKIAWK6JJURBCV4O46V7',
        'secret' => 'BpLc6JamBDtENI1eqOdM4DjaNOFbgTf6ABX****',
    ],
]);

//$url = $s3Client->getObjectUrl('GetObject', 'private-folder/GO Lịch nghỉ Tết 2023.png');


$cmd = $s3Client->getCommand('GetObject', [
    'Bucket' => 'edric',
    'Key' => 'private-folder/GO Lịch nghỉ Tết 2023.png'
]);


$request = $s3Client->createPresignedRequest($cmd, '+20 minutes');

// Get the actual presigned-url
$presignedUrl = (string)$request->getUri();
echo $presignedUrl;
```


