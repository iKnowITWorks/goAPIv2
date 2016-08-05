<?php
    #######################################################
    #### Name: goUserLogin.php	                       ####
    #### Description: API to get specific user	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
	$user_name = $_REQUEST['user_name'];
	//$user_email = $_REQUEST['user_email'];
	$user_pass = $_REQUEST['user_pass'];
	$pass_hash = '';
	$cwd = $_SERVER['DOCUMENT_ROOT'];
	$auth = 0;

	##### START SYSTEM_SETTINGS LOOKUP #####
	$rsltp = mysqli_query($link, "SELECT use_non_latin,webroot_writable,pass_hash_enabled,pass_key,pass_cost,hosted_settings FROM system_settings;");
	$qm_conf_ct = mysqli_num_rows($rsltp);
	if ($qm_conf_ct > 0) {
		$rowp = mysqli_fetch_array($rsltp, MYSQLI_ASSOC);
		$non_latin =            $rowp['use_non_latin'];
		$SSwebroot_writable =   $rowp['webroot_writable'];
		$SSpass_hash_enabled =  $rowp['pass_hash_enabled'];
		$SSpass_key =           $rowp['pass_key'];
		$SSpass_cost =          $rowp['pass_cost'];
		$SShosted_settings =    $rowp['hosted_settings'];
	}
	##### END SETTINGS LOOKUP #####
	###########################################
	
    ### Check if user_name or user_email
	if(!empty($user_name)){
		//username
		$user = "user='".$user_name."'";
	}else{
		//email
		$user = "email='".$user_name."'";
	}
	
    $passSQL = "pass='$user_pass'";
	if ($SSpass_hash_enabled > 0) {
		if ($bcrypt < 1) {
			$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$user_pass");
			$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
		} else {$pass_hash = $user_pass;}
		$passSQL = "pass_hash='$pass_hash'";
		//$aDB->where('pass_hash', $pass_hash);
	}
	
	$query = "SELECT user_id, user, email, pass, full_name, user_level, user_group, active, pass_hash
			  FROM vicidial_users
			  WHERE ".$user."
			  AND ".$passSQL."
			  ORDER BY user ASC
			  LIMIT 1;";
	$rsltv = mysqli_query($link, $query);
	$countResult = mysqli_num_rows($rsltv);

	if($countResult > 0) {
		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataUser = $fresults['user'];
				$dataFullName = $fresults['full_name'];
				$dataUserLevel = $fresults['user_level'];
				$dataUserGroup = $fresults['user_group'];
				$dataActive   = $fresults['active'];
				$dataUserId = $fresults['user_id'];
				$dataEmail = $fresults['email'];
				$dataPass = ($SSpass_hash_enabled > 0) ? $fresults['pass_hash'] : $fresults['pass'];
				
				$apiresults = array(
									"result" => "success",
									"user_group" => $dataUserGroup,
									"userno" => $dataUser,
									"full_name" => $dataFullName,
									"user_level" => $dataUserLevel,
									"active" => $dataActive,
									"user_id" => $dataUserId,
									"email" => $dataEmail,
									"pass" => $dataPass,
									"bcrypt" => $SSpass_hash_enabled,
									"salt" => $SSpass_key,
									"cost" => $SSpass_cost
							);
		}
	} else {
		$apiresults = array("result" => "Error: Invalid login credentials please try again.");
	}
	
?>
