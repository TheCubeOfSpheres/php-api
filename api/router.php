<?php
define('API_HEADER_KEY', 'eLfzWQLQMrevBHrYC6naF');

if (! isset(getallheaders()['gotravlon-api-key'])) exit;
if (getallheaders()['gotravlon-api-key'] != API_HEADER_KEY) exit;

error_log('X CONFIRM 22222');

require(__DIR__.'/includes/config.php');
require(__DIR__.'/includes/init.php');

$authorization = NULL;
error_log(getallheaders()['authorization'] . '---------------------------------------');
if (isset(getallheaders()['Authorization'])) {
  $authorization = str_replace("Bearer", "", getallheaders()['Authorization']);
  error_log('Hit A! ' . $authorization);
} else if (isset(getallheaders()['authorization'])) {
  $authorization = str_replace("Bearer", "", getallheaders()['authorization']);
  error_log('Hit a! ' . $authorization);
}

$method = $_SERVER['REQUEST_METHOD'];
$request = $_GET['request'];

switch ($method) {
  	case 'PUT':
    	route_put($con, $request);  
    	break;
  	case 'POST':
    	route_post($con, $request, $authorization);  
    	break;
  	case 'GET':
    	route_get($con, $request);  
    	break;
   	case 'DELETE':
    	route_delete($con, $request);
    	break;
  	default:
    	handle_error($request);  
    	break;
}

function route_get($request) {


}

function route_post($con, $request, $authorization) {
  // post clean
  foreach ($_POST as $key => $value) {
    $_POST[$key] = securityPup($con, $value);
  }

  // user
  if (isset($authorization)) {
    $l_sSql = 'SELECT * FROM `users` WHERE access_token="'.$authorization.'"';
    $user = App::getInstance()->runQuery($l_sSql,true,true);
    $xUser = new User($user);
    if (! $xUser) {
      $data['data'] = 'User session expired. Try logging out and logging back in.';
      $data['error'] = true;
      echo json_encode($data);
      return;
    }
    $l_sSql = 'UPDATE `users` set last_seen = now() WHERE access_token="'.$authorization.'"';
    App::getInstance()->runQuery($l_sSql);
  }

  // presenters
  if (strpos($request, 'user') !== false) $userPresenter = new UserPresenter();
  // if (strpos($request, 'room') !== false) $roomPresenter = new RoomPresenter();

  // routes
  error_log($request);
  if($request == "user/X") {
   error_log($_POST['ApiRouterX']); 
   echo json_encode($userPresenter->x()); 
  }


  if ($request == "user/facebook_registration") echo json_encode($userPresenter->register_Facebook());
  if ($request == "user/google_registration") echo json_encode($userPresenter->register_Google());
  if ($request == "user/twitter_registration") echo json_encode($userPresenter->register_Twitter());
  if ($request == "user/fectchOnboardingCards") echo json_encode($userPresenter->fetchApiData($_POST['fetchApiData'], $authorization));
  if ($request == "user/answerOnboardingCards") {
      error_log('HIT!');
      error_log($_POST['userAnswer']);
    echo json_encode($userPresenter->fetchApiData($_POST['fetchApiData'], $authorization)); 
  } 
  
  if ($request == "user/login") echo json_encode($userPresenter->logIn());
  if ($request == "user/create") echo json_encode($userPresenter->signUp());
  if ($request == "user/completeSignup") echo json_encode($userPresenter->completeSignup($authorization));

  //User Api Requests
  if ($request == "user/checkScheduleConflicts") echo json_encode($userPresenter->checkScheduleConflicts($authorization));
  if ($request == "user/createNewTrip") echo json_encode($userPresenter->createNewTrip($authorization));
  if ($request == "user/getTrips") echo json_encode($userPresenter->getUserTrips($authorization));
  if ($request == "user/getTripDetails") echo json_encode($userPresenter->getTripDetails($authorization));
  if ($request == "user/deleteTrip") echo json_encode($userPresenter->deleteTripByTripId($authorization));
  
  //Connect Social Media Accounts user/socialConnectStatus
  if ($request == "user/socialConnectStatus") echo json_encode($userPresenter->getSocConStatus($authorization));
  if ($request == "user/connectSocialMediaAccount") echo json_encode($userPresenter->connectSocialMediaAccount($authorization));

  //User Account updates
  if ($request == "user/updateProfilePicture") echo json_encode($userPresenter->updateProfilePicture($authorization));
  if ($request == "user/saveProfilePicture") echo json_encode($userPresenter->saveProfilePicture($authorization));
  if ($request == "user/updateProfileStrings") echo json_encode($userPresenter->updateProfileStrings($authorization));
  if ($request == "user/getCurrentTrip") echo json_encode($userPresenter->getCurrentUserTrips($authorization));
}

App::getInstance()->closeConnection();

?>
