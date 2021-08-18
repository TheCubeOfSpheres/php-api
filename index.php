<?php
	require_once __DIR__ . '/vendor/autoload.php';
	require(__DIR__.'/api/includes/init.php');
	require(__DIR__.'/api/includes/config.php');
	

	//https://stackoverflow.com/questions/5097085/list-of-facebook-fields-returned-by-social-graph-api
	header('Access-Control-Allow-Origin: *');
	if(isset($_POST['authFlow']) && $_POST['authFlow'] === 'Facebook') {
		error_log('-------------------Unique User Auth---------------------');
	    error_log('Token:'.$_POST['token']);
	    error_log('UserId:'.$_POST['userId']);
	    error_log('Expiration:'.$_POST['expires']);
	    error_log('ValidatedPermissions:'.$_POST['permissions']);
	    error_log('DeniedPermissions:'.$_POST['declinedPermissions']);
	    error_log('-------------------Unique User Auth---------------------');
	    header('Content-Type: application/json');
	    $response = [
	    	'token'=> $_POST['token'],
			'userId'=> $_POST['userId'],
			'expires'=> $_POST['expires'],
			'permissions'=> $_POST['permissions'],
			'declinedPermissions' => $_POST['declinedPermissions'],
	    ];
	    $fb = new Facebook\Facebook([
			'app_id' => '981943269246999',
			'app_secret' => '0a55c65b63832544e15aa18ba9ca00ad',
			'default_graph_version' => 'v3.3',
			'default_access_token' => $_POST['token']
			// . . .
		]);
	    
	    $response = $fb->get('/me?fields=id,email,hometown,first_name,last_name,birthday,gender,picture');
	    $user = $response->getGraphUser();
	    $graphNode = $response->getGraphNode();
	    error_log($graphNode->getField('email') . $graphNode->getField('hometown') . $graphNode->getField('birthday')->format('m/d/Y') . $graphNode->getField('first_name') . $graphNode->getField('last_name')  . $graphNode->getField('gender') . $graphNode->getField('picture') );
	    
	    error_log('-----------------------Saved User Data-----------------------');
	    $userPresenter = new UserPresenter();
	    echo json_encode($userPresenter->register_Facebook($graphNode->getField('id'), $graphNode->getField('first_name'), $graphNode->getField('last_name'), $graphNode->getField('email'), $graphNode->getField('birthday')->format('m/d/Y'), $graphNode->getField('gender'), $graphNode->getField('hometown'), $graphNode->getField('picture')));
	    return;
	}
	//Fetch Api Data
	if(isset($_POST['fetchApiData'])) {
		$userPresenter = new UserPresenter();
		error_log($_POST['userid']);
		if($_POST['fetchApiData'] === 'onBoardingCards') {
			echo json_encode($userPresenter->fetchApiData($_POST['fetchApiData']) );	
		}
		if($_POST['fetchApiData'] === 'onboarding_cards_userAnswers') {
			echo json_encode($userPresenter->fetchApiData($_POST['fetchApiData'], $_POST['userid'], $_POST['userAnswer']) );	
		}
		return;
	}
        
	echo 'Test String' . '<br>';
	$l_sDBHost = 'localhost';
	$l_sDBUsername = 'root';
	$l_sDBPassword = 'L1Zab8BrltFoaBm5';
	$l_sDBName = 'travl_db';

	$con = new mysqli($l_sDBHost, $l_sDBUsername, $l_sDBPassword, $l_sDBName);
	print_r($con);
	error_log('Endpoint Refreshed');
	
?>
<!DOCTYPE html>
<html>
<head>
	<title> Travlon API</title>
</head>
<body>
		<h2> Travlon Says Hello </h2>
</body>
</html>
