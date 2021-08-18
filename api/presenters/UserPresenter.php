<?php

class UserPresenter {
	public function register_Facebook() {
		$fb = new Facebook\Facebook([
			'app_id' => FACEBOOK_APP_ID,
			'app_secret' => FACEBOOK_APP_SECRET,
			'default_graph_version' => 'v3.3',
			'default_access_token' => $_POST['token']
			// . . .
		]);
		//$fb.login(function(response) {
  		// Original FB.login code
		//}, { auth_type: 'reauthenticate' })
	    
	    $response = $fb->get('/me?fields=id,email,hometown,first_name,last_name,birthday,gender,picture');
	    $user = $response->getGraphUser();
	    $graphNode = $response->getGraphNode();
		//initial values from api	   
	    $facebook_id = $graphNode->getField('id');
	    $first_name = $graphNode->getField('first_name');
	    $last_name = $graphNode->getField('last_name');
	    $email = $graphNode->getField('email'); 
	    $birthday = $graphNode->getField('birthday')->format('m/d/Y');
	    $gender = $graphNode->getField('gender');
	    $hometown = $graphNode->getField('hometown');
	    $picture = $graphNode->getField('picture');

	    //Facebook Profile Details------------------Handle initial api values that are null
		$userName = $first_name . ' ' . $last_name;
		$photoUrl = $picture["url"];
		if($gender == null) {
			$gender = 'unspecified';
		}
		if($birthday ==  null) {
			$birthday == '01/01/2000';
		}
		//----------------------------------------- 
	    error_log('------------------------------------Fb Token------------------------------------------------');
	   	error_log(print_r($_POST['token'], true) );
	   	//Check to see if user exists
		$xUser = $this->getUserByEmail($email);
		$xFacebook_id = $this->getUserByFacebook_id($facebook_id);

		if($xUser || $xFacebook_id) {
			//Hash random string to serve as access token
			$access_token = password_hash(randomString(7).generateSalt().time().generateSalt(), PASSWORD_DEFAULT);

			$l_sSql = 'UPDATE `users` SET access_token="'.$access_token.'" WHERE facebook_id="'.$facebook_id.'"';
			$user = App::getInstance()->runQuery($l_sSql, true, false);

			
			error_log(' ---------------------------------------------------------- Return Current Trip ---------------------------------------------------------- ');
			$currentTrips = $this->getCurrentUserTrips($access_token);
			$currentTrips = json_encode($currentTrips);

			$xUser_id = $this->getUserIdByToken($access_token);
			$l_sSql = 'SELECT gender FROM `users` WHERE id = "'.$xUser_id.'"';
			$gender = App::getInstance()->runQuery($l_sSql, true, false);
			
			$gender = $gender[0]["gender"];
			error_log(print_r($gender, true));
			
			$l_sSql = 'SELECT birthday FROM `users` WHERE id = "'.$xUser_id.'"';
			$birthday = App::getInstance()->runQuery($l_sSql, true, false);
			$birthday = $birthday[0]["birthday"];
			error_log(print_r($birthday, true)); 

			if($gender == null) {
				$gender = 'unspecified';
			}
			if($birthday ==  null) {
				$birthday = '01/21/2000';
			}
			
			return ['user' => 'exists', 'access_token' => $access_token, 'userName' => $userName, 'email' => $email, 'photoUrl' => $photoUrl,'gender' => $gender, 'birthday' => $birthday];
		} else { 
			//Hash random string to serve as initial access token
			$access_token = password_hash(randomString(7).generateSalt().time().generateSalt(), PASSWORD_DEFAULT); 
			//Add user to DB
			$l_sSql = 'INSERT INTO users (facebook_id, first_name, last_name, email, birthday, gender, access_token) VALUES ("'.$facebook_id.'","'.$first_name.'","'.$last_name.'","'.$email.'","'.$birthday.'","'.$gender.'","'.$access_token.'")';
			App::getInstance()->runQuery($l_sSql);
			
			//Get users id
			$l_sSql = 'SELECT * FROM `users` WHERE facebook_id = "'.$facebook_id.'"';
			$newUser = App::getInstance()->runQuery($l_sSql, true, false);
			$newUserId = $newUser[0]['id'];
			
			
			//Add user id to answer columns	
			$l_sSql = 'INSERT INTO 	onboarding_cards_userAnswers (userid) VALUES  ("'.$newUserId.'")';
			App::getInstance()->runQuery($l_sSql, true, false);
			
			return ['user' => 'created', 'access_token' => $access_token, 'userName' => $userName, 'email' => $email, 'photoUrl' => $photoUrl,'gender' => $gender, 'birthday' => $birthday];
		}
	}
	public function register_Google() {
		//initial values from api	  
		error_log($_POST['authFlow']);
		$google_id = $_POST['userId'];
		$email = $_POST['email'];
      	$name = $_POST['name'];
      	$photoUrl = $_POST['photoUrl'];
      	$authentication - $_POST['authentication'];
      	$gender = null;
      	$birthday = null;

      	//Google Profile Details------------------ if null
		if($photoUrl == 'null') {
			$photoUrl = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ31IcSledV0MZL_qpOpdEJGgyZycojlw5QLUAQJ_oiRj9a7rVDwhHEDJOn3_vvtIJEtcE&usqp=CAU';
		}
      	if($gender == null) {
			$gender = 'unspecified';
		}
		if($birthday ==  null) {
			$birthday = '01/01/2000';
		}
		//-----------------------------------------

      	error_log('------------------------------------Google Token------------------------------------------------');
	   	error_log(print_r($_POST['authentication'], true) );

      	$xUser = $this->getUserByEmail($email);
		$xGoogle_id = $this->getUserByGoogle_id($userId);

		if($xUser || $xGoogle_id) {
			//Hash random string to serve as access token
			$access_token = password_hash(randomString(7).generateSalt().time().generateSalt(), PASSWORD_DEFAULT);

			$l_sSql = 'UPDATE `users` SET access_token="'.$access_token.'" WHERE google_id="'.$google_id.'"';
			$user = App::getInstance()->runQuery($l_sSql);
			
			$xUser_id = $this->getUserIdByToken($access_token);
			$l_sSql = 'SELECT gender FROM `users` WHERE id = "'.$xUser_id.'"';
			$gender = App::getInstance()->runQuery($l_sSql, true, false);
			
			$gender = $gender[0]["gender"];
			error_log(print_r($gender, true));
			
			$l_sSql = 'SELECT birthday FROM `users` WHERE id = "'.$xUser_id.'"';
			$birthday = App::getInstance()->runQuery($l_sSql, true, false);
			$birthday = $birthday[0]["birthday"];
			error_log(print_r($birthday, true)); 

			if($gender == null) {
				$gender = 'unspecified';
			}
			if($birthday ==  null) {
				$birthday = '01/21/2000';
			}

			return ['user' => 'exists', 'access_token' => $access_token, 'userName' => $name, 'email' => $email, 'photoUrl' => $photoUrl, 'gender' => $gender, 'birthday' => $birthday];
		} else { 
			//Hash random string to serve as initial access token
			$access_token = password_hash(randomString(7).generateSalt().time().generateSalt(), PASSWORD_DEFAULT); 
			//Add user to DB
			$l_sSql = 'INSERT INTO users (google_id, email, picture, access_token) VALUES ("'.$google_id.'","'.$email.'","'.$photoUrl.'","'.$access_token.'")';
			App::getInstance()->runQuery($l_sSql);
			
			//Get users id
			$l_sSql = 'SELECT * FROM `users` WHERE google_id = "'.$google_id.'"';
			$newUser = App::getInstance()->runQuery($l_sSql, true, false);
			$newUserId = $newUser[0]['id']; 
			
			
			//Add user id to answer columns	
			$l_sSql = 'INSERT INTO 	onboarding_cards_userAnswers (userid) VALUES  ("'.$newUserId.'")';
			App::getInstance()->runQuery($l_sSql, true, false);

			return ['user' => 'created', 'access_token' => $access_token, 'userName' => $name, 'email' => $email, 'photoUrl' => $photoUrl, 'gender' => $gender, 'birthday' => $birthday];
		} 
	}

	public function register_Twitter() {
		error_log('Twitter Registrar');
		$settings = array(
    		'oauth_access_token' => TWITTER_ACCESS_TOKEN,
    		'oauth_access_token_secret' => TWITTER_ACCESS_TOKEN_SECRET,
    		'consumer_key' => CONSUMER_KEY,
    		'consumer_secret' => CONSUMER_SECRET
		);
		// Authorization Values From Flutter Login
		$authFlow = $_POST['authFlow'];
		$token = $_POST['token'];
		$secret = $_POST['secret'];
		$userId = $_POST['userId'];
		$username = $_POST['username'];
		
		error_log('------------------------------------Twitter Token------------------------------------------------');
	   	error_log(print_r($_POST['token'], true) );

		//Scrape Profile Data
		$twitter = new TwitterAPIExchange($settings);
		$url = 'https://api.twitter.com/1.1/users/lookup.json';
		$getfield = '?user_id=' . $userId;
		$requestMethod = 'GET';

		$response = $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();

		$twitter_user = json_decode($response);
		
		error_log(print_r($twitter_user, true));
		//initial values from api	  
		$twitter_id = $twitter_user[0]->id_str;
	    $display_name = $twitter_user[0]->name;
	    $photoUrl = $twitter_user[0]->profile_image_url; 
	    $location = $twitter_user[0]->location;
	    
	    error_log($twitter_id);
		error_log($name);
		error_log($location);
		error_log($photoUrl);

		$xUser = $this->getUserByEmail($email);
		$xTwitter_id = $this->getUserByTwitter_id($twitter_id);


		//Twitter Profile Details------------------//App needs to be whitelisted by twitter to get user emails with api --- if null
		$email = 'update@youremail.dne' . password_hash(randomString(2).generateSalt().time().generateSalt(), PASSWORD_DEFAULT);
		
		if($photoUrl == 'null') {
			$photoUrl = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ31IcSledV0MZL_qpOpdEJGgyZycojlw5QLUAQJ_oiRj9a7rVDwhHEDJOn3_vvtIJEtcE&usqp=CAU';
		}
      	if($gender == null) {
			$gender = 'unspecified';
		}
		if($birthday ==  null) {
			$birthday = '01/21/2000';
		}
		//-----------------------------------------

		if($xUser || $xTwitter_id) { 
			//Hash random string to serve as access token
			$access_token = password_hash(randomString(7).generateSalt().time().generateSalt(), PASSWORD_DEFAULT);

			$l_sSql = 'UPDATE `users` SET access_token="'.$access_token.'" WHERE twitter_id="'.$twitter_id.'"';
			$user = App::getInstance()->runQuery($l_sSql);
						
			$xUser_id = $this->getUserIdByToken($access_token);
			$l_sSql = 'SELECT gender FROM `users` WHERE id = "'.$xUser_id.'"';
			$gender = App::getInstance()->runQuery($l_sSql, true, false);
			
			$gender = $gender[0]["gender"];
			error_log(print_r($gender, true));
			
			$l_sSql = 'SELECT birthday FROM `users` WHERE id = "'.$xUser_id.'"';
			$birthday = App::getInstance()->runQuery($l_sSql, true, false);
			$birthday = $birthday[0]["birthday"];
			error_log(print_r($birthday, true)); 

			if($gender == null) {
				$gender = 'unspecified';
			}
			if($birthday ==  null) {
				$birthday = '01/21/2000';
			}

			return ['user' => 'exists', 'access_token' => $access_token, 'userName' => $display_name, 'email' => $email, 'photoUrl' => $photoUrl, 'gender' => $gender, 'birthday' => $birthday];
		} else {
			//Hash random string to serve as initial access token
			$access_token = password_hash(randomString(7).generateSalt().time().generateSalt(), PASSWORD_DEFAULT); 
			//Add user to DB
			$l_sSql = 'INSERT INTO users (twitter_id, display_name, picture, location, access_token) VALUES ("'.$twitter_id.'","'.$display_name.'","'.$photoUrl.'","'.$location.'","'.$access_token.'")';
			App::getInstance()->runQuery($l_sSql);
			
			//Get users id
			$l_sSql = 'SELECT * FROM `users` WHERE twitter_id = "'.$twitter_id.'"';
			$newUser = App::getInstance()->runQuery($l_sSql, true, false);
			$newUserId = $newUser[0]['id']; 
			
			
			//Add user id to answer columns	
			$l_sSql = 'INSERT INTO 	onboarding_cards_userAnswers (userid) VALUES  ("'.$newUserId.'")';
			App::getInstance()->runQuery($l_sSql, true, false);
			
			return ['user' => 'created', 'access_token' => $access_token, 'userName' => $display_name, 'email' => $email, 'photoUrl' => $photoUrl,'gender' => $gender, 'birthday' => $birthday];
		}
	}

	public function getSocConStatus($access_token) {
		$xUserId = $this->getUserIdByToken($access_token);
		//Checks to see what social media accoutns the user has registered
		$l_sSql = 'SELECT facebook_id, google_id, twitter_id, pinterest_id FROM `users` WHERE `id`= "'.$xUserId.'"';
		$socConStatus = App::getInstance()->runQuery($l_sSql, true, false); 
		error_log(print_r($socConStatus, true));

		return $socConStatus[0]; // [0] returns just the AssociativeArray/Dart Map, instead of returning it in an array.
	}
	public function connectSocialMediaAccount($access_token) {
		$xUserId = $this->getUserIdByToken($access_token);
		// error_log('Connect Here');
		// $socAccount = $_POST['socAccount'];
		// $token = $_POST['token'];
		// $socialId = $_POST['socialId'];
		// $expires = $_POST['expires'];
		// $permissions = $_POST['permissions'];
		// $declinedPermissions = $_POST['declinedPermissions'];

		// error_log('Facebook');
		// error_log($socAccount);
		// error_log($token);
		// error_log($socialId);
		// error_log($expires);
		// error_log($permissions);
		// error_log($declinedPermissions);

		// error_log('Connect Here');
		// $socAccount = $_POST['socAccount'];
		// $email = $_POST['email'];
		// $socialId = $_POST['socialId'];
		// $name = $_POST['name'];
		// $photoUrl = $_POST['photoUrl'];
		
		// error_log('Google');
		// error_log($socAccount);
		// error_log($email);
		// error_log($socialId);
		// error_log($name);
		// error_log($photoUrl);

		switch ($socAccount) {
    		case 'facebook':
    			error_log('Add Facebook Id To User Account');
    			$l_sSql = 'UPDATE `users` SET `facebook_id` = "'.$socialId.'" WHERE id= "'.$xUserId.'"';
				App::getInstance()->runQuery($l_sSql);
    			return ['updatedSocial' => 'facebook'];		
				break;
			case 'google':
    			error_log('Add google Id To User Account');
    			$l_sSql = 'UPDATE `users` SET `google_id` = "'.$socialId.'" WHERE id= "'.$xUserId.'"';
				App::getInstance()->runQuery($l_sSql);
    			return ['updatedSocial' => 'google'];		
				break;
			case 'pinterest':
    			error_log('Add pinterest Id To User Account');
    			return ['update protocol' => 'pinterest'];		
				break;
			case 'twitter':
    			error_log('Add twitter Id To User Account');
    			return ['update protocol' => 'twitter'];		
				break;
		}
	}
	public function fetchApiData($apiResource, $access_token) {
		$xUserId = $this->getUserIdByToken(trim($access_token));
		switch ($apiResource) {
    		case 'onBoardingCards':
    		    error_log('Get onboarding cards from SQL Database');
	   		    
				$l_sSql = 'SELECT * FROM `onboarding_cards_userAnswers` WHERE `userid`= "'.$xUserId.'"';
				$userAnswers = App::getInstance()->runQuery($l_sSql, true, false); 
				
				$inlsSql = '';
				$nullArray = [];
				//Get Unaswered Questions
				foreach($userAnswers[0] as $key => $val) {
					if($val === null) {
						array_push($nullArray, $key);	 
					}
				}
				//Map them to a parenthesis for lsSql IN clause
				foreach($nullArray as $key => $cardId) {
					if(count($nullArray) > 1) {
						if($key === 0) {
						$inlsSql .= '('.$cardId.',';
						} else if($key === count($nullArray) - 1) {
							$inlsSql .= ''.$cardId.')';
						} else {
							$inlsSql .= ''.$cardId.',';
						}	
					} else if(count($nullArray) === 1) {
						$inlsSql .= '('.$cardId.')';
					}
					
				}
				error_log($inlsSql);
				if(count($nullArray) === 0) { //If no cards to answer for user
					$onboarding_cards = 'noCards';
					$return['data'] = $onboarding_cards;
					$return['error'] = false;
					return $return;
				} else { // return unanswered cards
					$l_sSql = 'SELECT * FROM `onboarding_cards` WHERE `active`= 1 AND `id` IN '.$inlsSql.'';
					$onboarding_cards_array = array();
					$xOnboardingCards = $onboarding_cards = App::getInstance()->runQuery($l_sSql, true, false);	
					foreach ($xOnboardingCards as $xOnboardingCard) {
						$xOnboardingCard['image_location'] = SITE_URL.'/onboardingcards/'.$xOnboardingCard['image_location'];
						array_push($onboarding_cards_array, $xOnboardingCard);
					}
					$return['data'] = $onboarding_cards_array;
					// $return['data'] = $xOnboardingCards;
					$return['error'] = false;
					return $return;
				}				
				
    		    break;
    		case 'onboarding_cards_userAnswers':
    			error_log('Update onboarding cards Answer in SQL databse');
    			// error_log($xUserId);
    			// error_log($_POST['userAnswer']);
    			// error_log($_POST['card_title']);
    			$qCardId = $_POST['card_id'];
    			// $l_sSql = 'SELECT * FROM `onboarding_cards` WHERE title = "'.$_POST['card_title'].'"';
				// $qCard = App::getInstance()->runQuery($l_sSql, true, false);
				// $qCardId = $qCard[0]['id'];
				// error_log($qCardId);
				//UPDATE `onboarding_cards_userAnswers` SET `userid`=75,`1`=0; in this comment `1` === `'.$qCardId.'`
				$l_sSql = 'UPDATE `onboarding_cards_userAnswers` SET `'.$qCardId.'` = "'.$_POST['userAnswer'].'" WHERE userid= "'.$xUserId.'"';
				App::getInstance()->runQuery($l_sSql);
				return;
    			break;
    		case 'onBoardingSocial':
    		    error_log('Get onboarding social questions from SQL Database'); 
    		    return ['TestItem0' => 'TestValue in my TestArray', 'TestItem1' => 'AnotherTestValue'];
    		    break;
    		case 'Trips':
    		    error_log('Get trip data from SQL Database'); 
    		    return ['TestItem0' => 'TestValue in my TestArray', 'TestItem1' => 'AnotherTestValue'];
    		    break;
		}
	}
	public function getUserTrips($accessToken) {
		error_log('Get Trips By User Id');
		$xUser_id = $this->getUserIdByToken($accessToken);
		error_log($xUser_id);
			//, SELECT FROM
		$l_sSql = 'SELECT DISTINCT trips.trip_id FROM `trips` 
		LEFT JOIN image_assets
		ON trips.location_id = image_assets.api_id 
		WHERE user_id = "'.$xUser_id.'"';
		$uniqueTripIDs = App::getInstance()->runQuery($l_sSql, true, false);			 
		
		$l_sSql = 'SELECT trips.trip_id, image_assets.image_url, trips.location_id, trips.startDate, trips.endDate FROM `trips` 
		LEFT JOIN image_assets
		ON trips.location_id = image_assets.api_id 
		WHERE user_id = "'.$xUser_id.'"' ;
		$xUserTrips = App::getInstance()->runQuery($l_sSql, true, false);

		error_log(print_r($uniqueTripIDs, true));
		error_log(count($uniqueTripIDs) );
		$uniqueUserTrips = [];
		$exclude = [];
		foreach($uniqueTripIDs as $trip) {
			error_log('unique id ' . $trip["trip_id"]);
			foreach($xUserTrips as $userTrip) {
				if(!in_array($userTrip["trip_id"], $exclude) ) {
					array_push($exclude, $userTrip["trip_id"]);
					array_push($uniqueUserTrips, $userTrip);			
				}
			}	
		}
		error_log( count($uniqueUserTrips) . ' send to App' );
		// error_log(print_r($xUserTrips, true));
		// error_log(count($xUserTrips) );
		return $uniqueUserTrips;
	}
	public function getCurrentUserTrips($accessToken) {
		error_log('Get Trips By User Id');
		$xUser_id = $this->getUserIdByToken($accessToken);
		error_log($xUser_id);
			//, SELECT FROM
		$l_sSql = 'SELECT * FROM `trips` WHERE user_id = "'.$xUser_id.'"';
		$allUserTrips = App::getInstance()->runQuery($l_sSql, true, false);
		
		$now = new DateTime();
		$now = $now->format('Y-m-d') ;
		error_log($now);
		
		$currentTrips = [];

		foreach($allUserTrips as $trip) {
			error_log( 'Start: ' . $trip['startDate']);
			error_log( 'End: ' . $trip['endDate']);
			if($trip['startDate'] <= $now && $trip['endDate'] >= $now) {
				error_log('Trip is Current');
				$currentTrip = $this->buildUserItineraryByTripId($trip['trip_id']);
				array_push($currentTrips, $currentTrip);
			} 
		}
		error_log(print_r($currentTrips, true));
		return $currentTrips;
	}
	public function getTripDetails($access_token) {
		error_log('Get A Trip By Trip_Id');
		$xUser_id = $this->getUserIdByToken($access_token);
		$xTrip_id = $_POST['trip_id'];
		error_log('User id: ' . $xUser_id . ' Trip id: ' .  $xTrip_id);
		$xTrip = $this->buildUserItineraryByTripId($xTrip_id);
		error_log(print_r($xTrip, true) );
		return $xTrip; 
	}
	public function getTripByTrip_id($xUser_id, $xTrip_id) {
		$l_sSql = 'SELECT * FROM `trips` WHERE `trip_id`= "'.$xTrip_id.'" AND `user_id` = "'.$xUser_id.'"';
		$xTrip = App::getInstance()->runQuery($l_sSql, true, false);	
		error_log($xTrip);
		return $xTrip[0];
	}

	public function checkScheduleConflicts($access_token) {
		$xUserId = $this->getUserIdByToken($access_token);
		// error_log($xUserId);
		// error_log($_POST['startDate']);
		// error_log($_POST['endDate']);
		// //Test value starts on the 24th and ends on the 30th

		// $convertedTime = strtotime($_POST['startDate']) ;
		// error_log($convertedTime);
		// $datetime = new DateTime("@$convertedTime");
		// $startDate = $datetime->format('Y-m-d') ;
		// error_log($startDate);
		
		// //Generate end date
		// $convertedTimeEnd = strtotime($_POST['endDate']) ;
		// error_log($convertedTimeEnd);
		// $datetime = new DateTime("@$convertedTimeEnd");
		// $endDate = $datetime->format('Y-m-d') ;
		// error_log($endDate);

		$l_sSql = 'SELECT startDate, endDate FROM `trips` WHERE user_id= '. $xUserId.'';
		$xTripDates = App::getInstance()->runQuery($l_sSql, true, false);
		$unselectableDates = [];
		foreach($xTripDates as $range) {
			$startStamp = strtotime($range['startDate']);
			$endStamp = strtotime($range['endDate']);
			for($i = $startStamp; $i <= $endStamp; $i = $i +  86400) {
				//error_log($i);
				$datetime = new DateTime("@$i");
				$day = $datetime->format('Y-m-d') ;
				//error_log($day);
				array_push($unselectableDates, $day);			
			}
		}
		return $unselectableDates;
	}

	public function createNewTrip($access_token) { 
		error_log('Create New Trip Here');
		$xUser_id = $this->getUserIdByToken($access_token);
		// $myTestQuery = $this->buildUserItineraryByTripId(49);
		// return $myTestQuery;
		$location = $_POST['location'];
		//Convert Dates To Api Format				
		$convertedTime = strtotime($_POST['startDate']) ;
		error_log($convertedTime);
		$datetime = new DateTime("@$convertedTime");
		$startDate = $datetime->format('Y-m-d') ;
		error_log($startDate);
		
		//Generate end date
		$convertedTimeEnd = strtotime($_POST['endDate']) ;
		error_log($convertedTimeEnd);
		$datetime = new DateTime("@$convertedTimeEnd");
		$endDate = $datetime->format('Y-m-d') ;
		error_log($endDate);

		$budget = $_POST['budget'];
		$whoAreYouGoingWith = $_POST['whoAreYouGoingWith'];
		$endOfTripFeel = $_POST['endOfTripFeel'];
		

		//Create Trip
		$l_sSql = 'INSERT INTO  trips (user_id, location_id, startDate, endDate, budget, whoAreYouGoingWith, endOfTripFeel)  VALUES ("'.$xUser_id.'","'.$location.'","'.$startDate.'","'.$endDate.'","'.$budget.'","'.$whoAreYouGoingWith.'","'.$endOfTripFeel.'")';
		$xTrip_id = App::getInstance()->runQuery($l_sSql);

		//The url you wish to send the POST request to
		$url = 'https://www.triposo.com/api/20200405/day_planner.json?location_id='.$location.'&start_date='.$startDate.'&end_date='.$endDate.'&account=12EQ8PXQ&token=qs9xlko5ytk8jsrk3o0ewbsjgrqwz2sp';
		//The data you want to send via POST
		$fields = [
		    'X-Triposo-Account' => TRIPOSO_ACCOUNT,
        	'X-Triposo-Token' => TRIPOSO_TOKEN
		];
		$ch = curl_init();
		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		//execute post
		$result = curl_exec($ch); //$result = json_encode($result) -----> valid for Sql JSON storage
		$resultObj = json_decode($result);
		$itinerary = $resultObj->results[0]->days;
		$country_id = $resultObj->results[0]->location->country_id;
		$location = $resultObj->results[0]->location;
		$this->harvestPOIsFromItinerary($itinerary, $country_id, $xTrip_id,  $xUser_id);
		$this->harvestLocationFromItinerary($location, $country_id);
		$itinerary = $this->buildUserItineraryByTripId($xTrip_id);
		return $itinerary;
	}

	public function deleteTripByTripId($access_token) {
		$xTrip_id = $_POST['trip_id'];
		error_log('delete trip' . $xTrip_id);

		$l_sSql = 'DELETE FROM trips WHERE `trip_id`= '.$xTrip_id.' ';
		App::getInstance()->runQuery($l_sSql);

	}

	
	public function harvestPOIsFromItinerary($itinerary, $country_id, $trip_id, $user_id) { // harvest images assets as well
		error_log(print_r($itinerary, true) );
		foreach($itinerary as $day) {
			foreach($day->itinerary_items as $poi) {
				//Check if poi exists
				$xPOI = $this->getPOIbyApi_id($poi->poi->id);
				if(!$xPOI) {
					$l_sSql = 'INSERT INTO point_of_interest (country_id, location_id, description, title, api_id, name, longitude, latitude, snippet, score) VALUES ("'.$country_id.'","'.$poi->poi->location_id.'","'.$poi->description.'","'.$poi->title.'","'.$poi->poi->id.'","'.$poi->poi->name.'" ,"'.$poi->poi->coordinates->longitude.'","'.$poi->poi->coordinates->latitude.'" ,"'.$poi->poi->snippet.'", "'.$poi->poi->score.'")';
					App::getInstance()->runQuery($l_sSql);
					$xPOI = $this->getPOIbyApi_id($poi->poi->id);
				}
				//Save itinerary_items for user
				$date_of_activity = $day->date;
				$slot = intval(array_search($poi,$day->itinerary_items));
				$activity_id = $xPOI['activity_id'] ;
				$api_id = $poi->poi->id;

				$l_sSql = 'INSERT INTO  itinerary (trip_id, user_id, date_of_activity, slot ,activity_id, api_id)  VALUES ("'.$trip_id.'","'.$user_id.'","'.$date_of_activity.'","'.$slot.'","'.$activity_id.'","'.$api_id.'")';
				App::getInstance()->runQuery($l_sSql);
				
				foreach($poi->poi->images as $image) {
					//Check if image exits
					$type = 'point_of_interest';
					$xImage = $this->getImageBySourceUrl($image->source_url);
					if(!$xImage) {
						$l_sSql = 'INSERT INTO image_assets (api_id, type, image_url) VALUES ("'.$poi->poi->id.'", "'.$type.'", "'.$image->source_url.'")';
						App::getInstance()->runQuery($l_sSql);
					}
				} 
			}			
		}
	}

	public function harvestLocationFromItinerary($location) {
		$xLocation = $this->getLocationByApi_Id($location->id, $location->country_id);
		if(!$xLocation) {
			$l_sSql = 'INSERT INTO locations (api_id, country_id, type) VALUES ("'.$location->id.'", "'.$location->country_id.'", "'.$location->type.'")';
			App::getInstance()->runQuery($l_sSql);	
		}
		foreach($location->images as $image) {
			$type = 'locations';
			$xImage = $this->getImageBySourceUrl($image->source_url);
			error_log($xImage);
			//error_log(print_r($xImage, true));
			if(!$xImage) {
				$l_sSql = 'INSERT INTO image_assets (api_id, type, image_url) VALUES ("'.$location->id.'", "'.$type.'", "'.$image->source_url.'")';
				App::getInstance()->runQuery($l_sSql);
			}
		}
	}

	public function buildUserItineraryByTripId($xTrip_id) {
		error_log($xTrip_id);
		//Itinerary Query --- May want to switch forloop builder with distinct parameter in SQL query
		$l_sSql = 'SELECT itinerary.date_of_activity, itinerary.slot, image_assets.image_url, point_of_interest.name, point_of_interest.description FROM itinerary 
		LEFT JOIN  image_assets  
		ON itinerary.api_id = image_assets.api_id 
		RIGHT JOIN point_of_interest
		ON point_of_interest.api_id = itinerary.api_id
		WHERE trip_id = "'.$xTrip_id.'"';
		$xJoinCheck = App::getInstance()->runQuery($l_sSql, true, false);
		
		error_log(print_r($xJoinCheck[0], true) );	
		error_log('---------------------------------Length ' . count($xJoinCheck) );
		$userItinerary = [];
		$exclude = [];
		//Convert SQL Result to flutter app _event object 
		foreach($xJoinCheck as $itin_item) {
			$uniqueItem =  ['date'=> $itin_item["date_of_activity"], 
			'itinerary_items' => []
			];
			if(!in_array($itin_item["date_of_activity"], $exclude) ) {
				array_push($exclude, $itin_item["date_of_activity"] );
				foreach($xJoinCheck as $itin_item) {
					if(!in_array($itin_item["date_of_activity"] . $itin_item["slot"], $exclude) ) {
						if($itin_item["date_of_activity"] == $uniqueItem["date"] ) {
							array_push($exclude, $itin_item["date_of_activity"] . $itin_item["slot"]);
							array_push($uniqueItem['itinerary_items'], [
							"name" => $itin_item['name'], 
							"isDone" => false,
							"time" => $itin_item['slot'],
							"description" => $itin_item['description'] , 
							"image_url" => $itin_item['image_url'],
							"trip_id" => $itin_item['trip_id']
							]);
						}					
					}	
				}
				array_push($userItinerary, $uniqueItem);	
			}
		}
		
		// error_log(print_r($xJoinCheck, true) );	
		// error_log(print_r($exclude, true) );	
		return $userItinerary;
	}
	
	public function getImageBySourceUrl($source_url) {
		$l_sSql = 'SELECT * FROM `image_assets` WHERE image_url = "'.$source_url.'"';
		$xImage = App::getInstance()->runQuery($l_sSql,true,false);
		if ($xImage) {
			//error_log(print_r($xImage, true));
			return $xImage[0];
		}
	}
	public function getPOIbyApi_id($api_id) {
		$l_sSql = 'SELECT * FROM `point_of_interest` WHERE api_id = "'.$api_id.'"';
		$xPOI = App::getInstance()->runQuery($l_sSql,true,false);
		if ($xPOI) {
			//error_log(print_r($xPOI, true));
			return $xPOI[0];
		}
	}
	public function getLocationByApi_Id($api_id, $country_id) {
		$l_sSql = 'SELECT * FROM `locations` WHERE api_id = "'.$api_id.'" AND country_id = "'.$country_id.'" ' ;
		$xLocation = App::getInstance()->runQuery($l_sSql,true,false);
		if ($xLocation) {
			//error_log(print_r($xLocation, true));
			return $xLocation[0];
		}
	}
	public function update_password($xUser) {
		$passwprd_verify = password_verify($_POST['old_password'], $xUser['password']);
		if ($passwprd_verify) {
			$new_pass = password_hash(trim($_POST['new_password']), PASSWORD_DEFAULT);
			$l_sSql = 'UPDATE `users` SET password="'.$new_pass.'" WHERE access_token="'.$access_token.'"';
			$user = App::getInstance()->runQuery($l_sSql);
			$data['error'] = false;
			return $data;
		} else {
			$data['error'] = true;
			$data['data'] = 'Your current password does not match.';
			return $data;
		}
	}

	public function updateInfo($xUser) {
        $username = $_POST['username'];
        $bio = $_POST['bio'];
        $email = $_POST['email'];
        $birthdayDate = isset($_POST['birthdayDate']) ? $_POST['birthdayDate'] : "";
        $gender = (int) $_POST['gender'];

		if ($email != $xUser['email']) {
			if ($this->getUserByEmail($email) != false) {
				$data['error'] = true;
				$data['data'] = 'Email already exists.';
				return $data;
			}
		}
		if ($username != $xUser['username']) {
			if ($this->getUserByExactName($username) != false) {
				$data['error'] = true;
				$data['data'] = 'Username already exists.';
				return $data;
			}
		}

		if ($email != $xUser['email']) {
			$email_verified = randomString(10);
			$message = "
  				Welcome to ".SITE_NAME."
  				<BR><BR>
  				Click on the following link to verify your email:
  				<BR><a href='".URL_PATH."/verify?email=".$email."&v=".$email_verified."'>".URL_PATH."/verify?email=".$email."&v=".$email_verified."</a>".EMAIL_SIGNATURE;
			send_mail_by_mailgun($email, $username, SITE_NAME, "no-reply@".SITE_URL_CLEAN, SITE_NAME." - Email Verification", $message, "text", "tag", "no-reply@".SITE_URL_CLEAN);

			$l_sSql = 'UPDATE `users` SET email_verified="'.$email_verified.'", email="'.$email.'" WHERE access_token="'.$access_token.'"';
			$user = App::getInstance()->runQuery($l_sSql);
		}

		$l_sSql = 'UPDATE `users` SET username="'.$username.'", about="'.$bio.'", sex="'.$gender.'" WHERE access_token="'.$access_token.'"';
		$user = App::getInstance()->runQuery($l_sSql);

		if (isset($_POST['birthdayDate']) && $birthdayDate != "") {
			$l_sSql = 'UPDATE `users` SET dob="'.$birthdayDate.'" WHERE access_token="'.$access_token.'"';
			$user = App::getInstance()->runQuery($l_sSql);
		}

		$l_sSql = 'SELECT * FROM `users` WHERE access_token="'.$access_token.'"';
		$xUser = App::getInstance()->runQuery($l_sSql,true,true);

		$data['error'] = false;
		$data['username'] = $xUser['username'];
		$data['bio'] = $xUser['about'];
		$data['email'] = $xUser['email'];
		$data['birthday'] = $xUser['dob'];
		$data['gender'] = $xUser['sex'];
		return $data;
	}

	public function updateProfilePicture($access_token) {
		error_log('updateProfilePicture -----------------------------------------------------------> ' .  SITE_URL."/api/upload");
		if (isset($_POST['base64Image'])){
			$base64Image = $_POST['base64Image'];
		} else {
			$base64Image = "";
		}
		if (isset($_POST['filename'])){
			$filename = md5(time().uniqid()) . "_" . $_POST['filename'];
		} else {
			$filename = "";
		}

		if (isset($base64Image) && $base64Image != "") {
			$target_url = SITE_URL."/api/user/saveProfilePicture";
			error_log($target_url . ' Route!--------------');
			$post = array('path' => 'avatars','base64Image'=> $base64Image,'filename'=>$filename);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$target_url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    'gotravlon-api-key: ' . API_HEADER_KEY,
			    'Authorization: ' . 'Bearer' . $access_token, 
			    'Content-Type: multipart/form-data'
			));
			// curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			$result=curl_exec ($ch);
			curl_close ($ch);
			// error_log("RESULT");
			// error_log(print_r($result, true));
		}
		
		$picture_url = 'https://api.gotravlon.com/api/upload/media/avatars/'.$filename;

		$l_sSql = 'UPDATE `users` SET picture="'.$picture_url.'" WHERE access_token="'.$access_token.'"';
		$user = App::getInstance()->runQuery($l_sSql);

		if ($filename != "") {
			$data['data'] = $picture_url;
		}
		$data['error'] = false;
		return $data;
	}

	public function saveProfilePicture($access_token) {
		error_log('Save Avatar Logic');
		$path = $_POST['path'];
		$base64Image = $_POST['base64Image'];
		$filename = $_POST['filename'];
		
		error_log($path);
		error_log(print_r($base64Image, true));
		error_log($filename);
		
		$filename_path = $filename; //md5(time().uniqid()).".jpg";
		$decoded=base64_decode($base64Image);
		if ($filename_path != "") {
			file_put_contents("upload/media/".$path.'/'.$filename_path, $decoded);
		}
	}

	public function updateProfileStrings($access_token) {
		$username = $_POST['username'];
		$email = $_POST['email'];
		$gender = $_POST['gender'];
		$birthday = $_POST['birthday'];

		error_log($username);
		error_log($email);
		error_log($gender);
		error_log($birthday);

		if($username != '') {
			$l_sSql = 'UPDATE `users` SET username = "'.$username.'" WHERE access_token="'.$access_token.'"';
			$user = App::getInstance()->runQuery($l_sSql);
		}
		if($email != '') {
			$l_sSql = 'UPDATE `users` SET email = "'.$email.'" WHERE access_token="'.$access_token.'"';
			$user = App::getInstance()->runQuery($l_sSql);
		}
		if($gender != '') {
			$l_sSql = 'UPDATE `users` SET gender = "'.$gender.'" WHERE access_token="'.$access_token.'"';
			$user = App::getInstance()->runQuery($l_sSql);
		}
		if($birthday != '') {
			$l_sSql = 'UPDATE `users` SET birthday = "'.$birthday.'" WHERE access_token="'.$access_token.'"';
			$user = App::getInstance()->runQuery($l_sSql);
		}
	}

	public function completeSignUp($access_token) {
		$bio = $_POST['bio'];
		if (isset($_POST['base64Image'])){
			$base64Image = $_POST['base64Image'];
		} else {
			$base64Image = "";
		}
		if (isset($_POST['filename'])){
			$filename = md5(time().uniqid()) . "_" . $_POST['filename'];
		} else {
			$filename = "";
		}

		if (isset($base64Image) && $base64Image != "") {
			$target_url = SITE_URL."/api/upload";
			$post = array('path' => 'avatars','base64Image'=> $base64Image,'filename'=>$filename);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$target_url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			    'gotravlon-api-key: ' . PCHAT_API_HEADER_KEY,
			    'Content-Type: multipart/form-data'
			));
			// curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			$result=curl_exec ($ch);
			curl_close ($ch);
			// error_log("RESULT");
			// error_log(print_r($result, true));
		}

		$l_sSql = 'UPDATE `users` SET about = "'.$bio.'", avatar="'.$filename.'" WHERE access_token="'.$access_token.'"';
		$user = App::getInstance()->runQuery($l_sSql);

		if ($filename != "") {
			$data['data'] = $filename;
		}
		$data['error'] = false;
		return $data;
	}

	public function signUp() {
		$username = $_POST['username'];
		$email = $_POST['email'];
		$password = $_POST['password'];

		if ($this->getUserByExactName($username) != false) {
			$data['error'] = true;
			$data['data'] = 'Username already exists.';
			return $data;
		}

		if ($this->getUserByEmail($email) != false) {
			$data['error'] = true;
			$data['data'] = 'Email already exists.';
			return $data;
		}

		$p = password_hash(trim($password), PASSWORD_DEFAULT);
		$last_name = "";
		$user_status = 1;
		$available_status = 1;
		$email_verified = randomString(10);
		$ip = IP_ADDRESS;
		$hash = password_hash($email.generateSalt().time().generateSalt(), PASSWORD_DEFAULT);
        $access_token = $hash;
		$l_sSql = 'INSERT INTO users (username, first_name, last_name, email, password, user_status, available_status, email_verified, ip, access_token) VALUES ("'.$username.'","'.$username.'","'.$last_name.'","'.$email.'","'.$p.'","'.$user_status.'","'.$available_status.'","'.$email_verified.'","'.$ip.'","'.$access_token.'")';
		$l_aData = App::getInstance()->runQuery($l_sSql);
		$user_id = App::getInstance()->db_last_insert_id();

        $message = "
  				Welcome to ".SITE_NAME."
  				<BR><BR>
  				Click on the following link to verify your email:
  				<BR><a href='".URL_PATH."/verify?email=".$email."&v=".$email_verified."'>".URL_PATH."/verify?email=".$email."&v=".$email_verified."</a>".EMAIL_SIGNATURE;
		send_mail_by_mailgun($email, $username, SITE_NAME, "no-reply@".SITE_URL_CLEAN, SITE_NAME." - Email Verification", $message, "text", "tag", "no-reply@".SITE_URL_CLEAN);

		$l_sSql = 'SELECT * FROM `users` WHERE id="'.$user_id.'"';
		$user = App::getInstance()->runQuery($l_sSql,true,true);

		$data['error'] = false;
		$data['data'] = $user;
		return $data;
	}

	public function logIn() {
		$l_sSql = 'SELECT * FROM `users` WHERE email="'.$_POST['email'].'"';
		$user = App::getInstance()->runQuery($l_sSql,true,true);
		if (! $user) {
			$data['error'] = true;
			$data['data'] = 'Invalid Login';
			return $data;
		}
		$passwprd_verify = password_verify($_POST['password'], $user['password']);
		if ($passwprd_verify) {
			$hash = password_hash($user['email'].generateSalt().time().generateSalt(), PASSWORD_DEFAULT);
			$data['error'] = false;
			$data['data'] = $user;
			return $data;
		}
		$data['error'] = true;
		$data['data'] = 'Invalid Login';
		return $data;
	}

	public function getAllUsers() {
		$page = 0; //(int) $_POST['page'] - 1;
		$offset = $page * ROOM_PAGINATION;
		$l_sSql = 'SELECT * FROM `users` ORDER BY last_seen DESC limit '.$offset.',100';
		$user = App::getInstance()->runQuery($l_sSql,true,false);
		$data['data'] = $user;

		$l_sSql = 'SELECT count(*) as total FROM `users`';
		$user_count = App::getInstance()->runQuery($l_sSql,true,true);
		$data['user_count'] = $user_count['total'];

		$data['error'] = false;
		return $data;
	}

	public function getUserByEmail($email) {
		$l_sSql = 'SELECT * FROM `users` WHERE email = "'.$email.'"';
		$xUsers = App::getInstance()->runQuery($l_sSql,true,true);
		if ($xUsers) {
			return $xUsers;
		}
		return false;
	}
	public function getUserByFacebook_id($facebook_id) {
		$l_sSql = 'SELECT * FROM `users` WHERE facebook_id = "'.$facebook_id.'"';
		$xUsers = App::getInstance()->runQuery($l_sSql,true,true);
		if ($xUsers) {
			return $xUsers;
		}
		return false;
	}
	public function getUserByGoogle_id($google_id) {
		$l_sSql = 'SELECT * FROM `users` WHERE google_id = "'.$google_id.'"';
		$xUsers = App::getInstance()->runQuery($l_sSql,true,true);
		if ($xUsers) {
			return $xUsers;
		}
		return false;
	}
	public function getUserByTwitter_id($twitter_id) {
		$l_sSql = 'SELECT * FROM `users` WHERE twitter_id = "'.$twitter_id.'"';
		$xUsers = App::getInstance()->runQuery($l_sSql,true,true);
		if ($xUsers) {
			return $xUsers;
		}
		return false;
	}
	public function getUserIdByToken($access_token) {
		$l_sSql = 'SELECT * FROM `users` WHERE access_token = "'.$access_token.'"';
		$xUsers = App::getInstance()->runQuery($l_sSql,true,true);
		if (sizeof($xUsers) > 0) {
			error_log(print_r($xUsers, true));
			// return $xUsers[0]['id'];
			return $xUsers['id'];
		}
		return false;
	}
	public function getUserByExactName($username) {
		$l_sSql = 'SELECT * FROM `users` WHERE username = "'.$username.'"';
		$xUsers = App::getInstance()->runQuery($l_sSql,true,true);
		if ($xUsers) {
			return $xUsers;
		}
		return false;
	}

	public function getUserByName() {
		$l_sSql = 'SELECT * FROM `users` WHERE username LIKE "%'.$_POST['username'].'%"';
		$xUsers = App::getInstance()->runQuery($l_sSql,true,false);
		$data['error'] = false;
		$data['data'] = $xUsers;
		return $data;
	}

	public function get() {
		$l_sSql = 'SELECT * FROM `users` WHERE id="'.$_POST['user_id'].'"';
		$user = App::getInstance()->runQuery($l_sSql,true,true);
		if (! $user) {
			$data['error'] = true;
			$data['data'] = 'User not found.';
			return $data;
		}
		$user['last_seen'] = (int) strtotime($user['last_seen']);
		$data['error'] = false;
		$data['data'] = $user;
		return $data;
	}

	public function update_user_token() {
		$l_sSql = 'UPDATE `users` SET fcm_token = "'.$_POST['token'].'" WHERE id="'.$_POST['user_id'].'"';
		$user = App::getInstance()->runQuery($l_sSql);
		$data['error'] = false;
		return $data;
	}
}