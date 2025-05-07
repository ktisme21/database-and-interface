<?php
function check_login($con)
{
	//Check if user is logged in
	if(isset($_SESSION['user_id']))
	{

		$id = $_SESSION['user_id'];
		$query = "SELECT * FROM users WHERE UserID = '$id' LIMIT 1";

		$result = mysqli_query($con,$query);
		//Check if query was successful and if user exists
		if($result && mysqli_num_rows($result) > 0)
		{
			//Fetch data
			$user_data = mysqli_fetch_assoc($result);
			return $user_data;
		}
	}

	//Redirect to login
	header("Location: login.php");
	die;
}

function random_num($length)
{
	$text = "";
	//Ensure minimum length of 5 characters
	if($length < 5){
		$length = 5;
	}

	//Generate random number of specified length
	$len = rand(4,$length);

	for ($i=0; $i < $len; $i++) { 
		$text .= rand(0,9);
	}

	return $text;
}

