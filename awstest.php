<?php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);

   require 'godplus/aws-autoloader.php';
   $awsAccessKey = 'AKIAIEJMJZ57P2YQWNBA';
   $awsSecretKey = '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';
  // $s3 = new S3($awsAccessKey, $awsSecretKey);
    use Guzzle\Service\Resource\Model;
    use Aws\Common\Enum\Region;
    use Aws\S3\S3Client;

	$s3 = S3Client::factory(array(
		'credentials' => array(
			'key'    => 'AKIAIEJMJZ57P2YQWNBA',
			'secret' => 'GpQkE9op9sF8lDhCvbVV8CLUmLSwXunvqdVlMN02',
		)
	));
	 $bucket = 'acm-audio-samples';
	 $objects = $s3->getIterator('ListObjects', array('Bucket' => $bucket));
        $i=1;
        echo "Keys retrieved!\n";
        foreach ($objects as $object) {
	    $key=$object['Key'];
		$key2=str_replace(" ","+",$key);
		$pos = strpos($key, ".mp3");
		  if($pos==true)
		  {
				  $key = html_entity_decode($key, ENT_QUOTES, "UTF-8");
				 $headers = $s3->headObject(array(
				  "Bucket" => $bucket,
				  "Key" => $key
				 ));
				 $tmparray = $headers->toArray();
				 if($tmparray["ContentDisposition"]!="attachment")
				 {
						 $pos2 = strpos($key, "'");
						 $pos3 = strpos($key, "God's");
						 $pos4 = strpos($key, "%E2%80%99");
						 
						 $key=str_replace("’t","",$key);
						 $key=str_replace("'","",$key);
						 
						 if($pos2==false && $pos3==false && $pos4==false )
						 {
							  // echo $i."-".$key."<br/>";
							  $result = $s3->copyObject(array(
							  'Bucket' => $bucket,
							  'CopySource' => "$bucket/$key",
							  'Key' => $key,
							  'Acl' => 'FULL_CONTROL',
							  'ContentDisposition' => 'attachment',
							  'ContentType' => 'audio/mpeg',
							  'MetadataDirective'  => 'REPLACE'
							  ));
						 }
						 else
						 {
							echo $i."-".$key."<br/><br/><br/>";
						 }
				 } 
				 
		      $i++;
		   }
        }
?>