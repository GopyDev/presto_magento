<?php
	if(@$_REQUEST['submit'] == 'Submit'){
		$curl = curl_init('http://api.yummly.com/v1/api/recipes?_app_id=0556b095&_app_key=f3846e76fa3a5b55daf5f2979b617c35&q='.$_REQUEST['search']);
		//$curl = curl_init('http://api.yummly.com/v1/api/recipes?_app_id=0556b095&_app_key=f3846e76fa3a5b55daf5f2979b617c35&q=onion+soup&allowedAllergy[]=396^Dairy-Free&allowedAllergy[]=393^Gluten-Free');
	
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
	
	$response = curl_exec($curl);                                          
	$resultStatus = curl_getinfo($curl); 	
		//echo "<pre>";
		//print_r($response);
		$temp = json_decode($response,true);
		//echo "<pre>";
		//print_r($temp);
		$temp = $temp['matches'];
		if($temp != '' ){
			$count = 0;
		?>
		<table width="220px" cellspacing="2" cellpadding="2" style="border:1px solid;">
		<?php
			foreach($temp as $data){
				@$name .= $data['sourceDisplayName'];
				@$images .= "<img src=".$data['imageUrlsBySize']['90']." />";
				@$results .= $data['recipeName'] . "<br><br>";
				@$ingredients .= $data['ingredients'];
				@$totalTimeInSeconds .= $data['totalTimeInSeconds'];
			?>
			<tr>
				<td style="text-align:center;"><b><a href="details.php?recipeName=<?php echo $data['id'];?>"><?php echo $data['recipeName'];?></b></a></td>
			</tr>
			<tr>
				<td style="text-align:center;"><img src="<?php echo $data['imageUrlsBySize']['90']; ?>" /></td>
			</tr>
			<?php
				//echo "Name is " . $data['recipeName'] . "<br>";
			//	@$images  .= '<img src="'.$data['imageUrlsBySize']['90'].'" />';
				
				$count ++;
			}
		?>
		</table>
		<?php
		}
		else{
			@$results =  "Your search item not found..";
		}
	}
		
		
	
?>
	<form name="yummly" method="post" action="">
		<input type="text" name="search" value="">
		<br>
		<input type="submit" value="Submit" name="submit" />
	</form>
	