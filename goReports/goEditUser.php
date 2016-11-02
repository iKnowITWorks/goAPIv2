<?php
   ####################################################
   #### Name: goEditUser.php	                   ####
   #### Description: API to edit specific user     ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");

    ### Check file is existed
	if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf")) {
        	$conf_path = "{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf";
	} elseif (file_exists("/etc/goautodial.conf")) {
        	$conf_path = "/etc/goautodial.conf";
	} else {
		$apiresults = array("result" => "Error: File goautodial.conf not found.");
	}
 
    ### POST or GET Variables
   //     $values = $_REQUEST['items'];
        $user = $_REQUEST['user'];
        $pass = $_REQUEST['pass'];
        $full_name = $_REQUEST['full_name'];
        $phone_login = $_REQUEST['phone_login'];
        $phone_pass = $_REQUEST['phone_pass'];
        $user_group = $_REQUEST['user_group'];
        $active = strtoupper($_REQUEST['active']);
        $hotkeys_active = strtoupper($_REQUEST['hotkeys_active']);
        $user_level = $_REQUEST['user_level'];
        $modify_same_user_level = strtoupper($_REQUEST['modify_same_user_level']);
        $ip_address = $_REQUEST['hostname'];
        $goUser = $_REQUEST['goUser'];

    ### Default Values
	$defActive = array("Y","N");
	$defhotkeys_active = array("Y","N");
	$defmodify_same_user_level = array("Y","N");	

    ### Error Checking
        if($user == null) {
                $apiresults = array("result" => "Error: Set a value for User ID.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user)){
                $apiresults = array("result" => "Error: Special characters found in user");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pass)){
                $apiresults = array("result" => "Error: Special characters found in password");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $full_name)){
                $apiresults = array("result" => "Error: Special characters found in full_name");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_login)){
                $apiresults = array("result" => "Error: Special characters found in phone_login");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone_pass)){
                $apiresults = array("result" => "Error: Special characters found in phone_pass");
        } else {
                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {

                if(!in_array($hotkeys_active,$defhotkeys_active) && $hotkeys_active != null) {
                        $apiresults = array("result" => "Error: Default value for hotkeys_active is Y or N only.");
                } else {

                if(!in_array($modify_same_user_level,$defmodify_same_user_level) && $modify_same_user_level != null) {
                        $apiresults = array("result" => "Error: Default value for modify_same_user_level is Y or N only.");
                } else {
		
		if($user_level < 1 && $user_level!=null || $user_level > 9 && $user_level!= null) {
                        $apiresults = array("result" => "Error: User Level Value should be in between 1 and 9");
                } else {

                if($VARSERVTYPE == "gofree" && $hotkeys_active != null) {
                        $apiresults = array("result" => "Error: hotkeys is disabled");
                } else {

                $group_ug = go_get_groupid($goUser);
                if($group_ug !== "ADMIN" && $modify_same_user_level != null) {
                        $apiresults = array("result" => "Error: modify_same_user_level is disabled");
                } else {

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE user_group='$user_group'";
			$ulUser = "AND user='$user'";
                } else {
                        $ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
			$ulUser = "AND user='$user' AND user_group='$groupId'";
                }

        #### Check User Group if valid
		if($user_group != null){
                $query = "SELECT user_group FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
                $countResult = mysqli_num_rows($rsltv);
		}
                $queryUserCheck = "SELECT user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ulUser ORDER BY user ASC LIMIT 1;";
                $rsltvCheck = mysqli_query($link, $queryUserCheck);
                $countCheckResult = mysqli_num_rows($rsltvCheck);

                if($countCheckResult > 0) {

                        while($fresults = mysqli_fetch_array($rsltvCheck, MYSQLI_ASSOC)){
                                $dataUserLevel = $fresults['user_level'];
                                $dataUserGroup = $fresults['user_group'];
                        }
			if($hotkeys_active == "Y" || $modify_same_user_level == "Y") {
				$hotkeys_active = 0;
				$modify_same_user_level = 0;
			} else {
				$hotkeys_active = 1;
                                $modify_same_user_level = 1;
				}
			

                           /*     $items = $values;
                                foreach (explode("&",$items) as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        if (strlen($val) > 0)
                                        {

                                                if ($var!="user")
                                                        $itemSQL .= "$var='".str_replace('+',' ',mysqli_real_escape_string($val))."', ";

                                                if ($var=="user")
                                                        $user="$val";

                                        }
                                }
                                $itemSQL = rtrim($itemSQL,', ');
			    */
                	if($countResult <= 0 && $user_group!=null) {
                        	$apiresults = array("result" => "Error: User Group doesn't exist");
               	 	} else {
                              //  $query = "UPDATE vicidial_users SET $itemSQL WHERE user='$user';";
                              //  $resultQuery = mysqli_query($link, $query);



				$queryUpdateUser = "UPDATE `vicidial_users` SET `pass` = '$pass',  `full_name` = '$full_name',  `phone_login` = '$phone_login',  `phone_pass` = '$phone_pass',  `user_group` = '$user_group',  `active` = '$active',  `hotkeys_active` = '$hotkeys_active',  `user_level` = '$user_level',  `modify_same_user_level` = '$modify_same_user_level' WHERE `user` = '$user';";
				$resultQueryUser = mysqli_query($link, $queryUpdateUser);
				
				
        $queryPhoneUpdate = "UPDATE `phones` SET `pass` = '$pass',  `conf_secret` = '$pass' WHERE `login` = '".mysqli_real_escape_string($link, $phone_login)."'";

				$resultQueryPhoneUpdate = mysqli_query($link, $queryPhoneUpdate);


        $queryJSIUpdate = "UPDATE justgovoip_sippy_info SET web_password='$phone_pass' where carrier_id='".mysqli_real_escape_string($link, $user_group)."'";

				$resultQueryJSIUpdate = mysqli_query($link, $queryJSIUpdate);


	### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','MODIFY User $user','UPDATE vicidial_users SET user=$user,pass=$pass,full_name=$full_name,phone_login=$phone_login,phone_pass=$phone_pass,user_group=$user_group,active=$active,hotkeys_active=$hotkeys_active,user_level=$user_level,modify_same_user_level=$modify_same_user_level');";
                                        $rsltvLog = mysqli_query($link, $queryLog);


				if($resultQueryUser == false){
				$apiresults = array("result" => "Error: Update failed. Check your details");
				} else {	
				$apiresults = array("result" => "success");
				} 
			}
				} else {
					$apiresults = array("result" => "Error: User doesn't exist.");
				}

			}
			}
			}
			}
			}
			}

		}
		}
		}
		}
		}
	}


?>