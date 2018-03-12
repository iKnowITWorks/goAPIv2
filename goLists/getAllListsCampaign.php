<?php
    #######################################################
    #### Name: goGetAllLists.php	               ####
    #### Description: API to get all Lists             ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################

	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);

	$query = "SELECT
			vicidial_lists.list_id,vicidial_lists.list_name,vicidial_lists.list_description,
			(SELECT count(*) as tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) as tally,
			(SELECT count(*) as counter FROM vicidial_lists_fields WHERE list_id = vicidial_lists.list_id) as cf_count,
			vicidial_lists.active,vicidial_lists.list_lastcalldate,vicidial_lists.campaign_id,vicidial_lists.reset_time,vicidial_lists.web_form_address,
			vicidial_lists.agent_script_override,vicidial_lists.campaign_cid_override,vicidial_lists.drop_inbound_group_override,
			vicidial_list.called_since_last_reset as reset_called_lead_status,
			vicidial_lists.xferconf_a_number,vicidial_lists.xferconf_b_number,vicidial_lists.xferconf_c_number,vicidial_lists.xferconf_d_number,
			vicidial_lists.xferconf_e_number
		FROM vicidial_lists
		LEFT JOIN vicidial_list
		ON vicidial_lists.list_id=vicidial_list.list_id
		WHERE vicidial_lists.campaign_id = '$campaign_id'
		GROUP BY vicidial_lists.list_id
		ORDER BY vicidial_lists.list_id;";
	$rsltv = $astDB->rawQuery($query);
	
	if ($astDB->getRowCount() > 0) {
		foreach ($rsltv as $fresults){
			$dataListId[] =  $fresults['list_id'];
			$dataListName[] =  $fresults['list_name'];
			$dataActive[] =  $fresults['active'];
			$dataListLastcallDate[] =  $fresults['list_lastcalldate'];
			$dataTally[] =  $fresults['tally'];
			$dataCFCount[] =  $fresults['cf_count'];
			$dataCampaignId[] =  $fresults['campaign_id'];
			$datareset_called_lead_status[] =  $fresults['reset_called_lead_status'];
			$dataweb_form_address[] =  $fresults['web_form_address'];
			$dataagent_script_override[] =  $fresults['agent_script_override'];
			$datacampaign_cid_override[] =  $fresults['campaign_cid_override'];
			$datadrop_inbound_group_override[] =  $fresults['drop_inbound_group_override'];
			$datareset_time[] = $fresults['reset_time'];
			$datalist_desc[] = $fresults['list_description'];
			$dataxferconf_a_number[] = $fresults['xferconf_a_number'];
			$dataxferconf_b_number[] = $fresults['xferconf_b_number'];
			$dataxferconf_c_number[] = $fresults['xferconf_c_number'];
			$dataxferconf_d_number[] = $fresults['xferconf_d_number'];
			$dataxferconf_e_number[] = $fresults['xferconf_e_number'];
			
			$apiresults = array(
				"result" => "success",
				"list_id" => $dataListId,
				"list_name" => $dataListName,
				"active" => $dataActive,
				"list_lastcalldate" => $dataListLastcallDate,
				"tally" => $dataTally,
				"cf_count" => $dataCFCount,
				"campaign_id" => $dataCampaignId,
				"reset_called_lead_status" => $datareset_called_lead_status,
				"web_form_address" => $dataweb_form_address,
				"agent_script_override" => $dataagent_script_override,
				"campaign_cid_override" => $datacampaign_cid_override,
				"drop_inbound_group_override" => $datadrop_inbound_group_override,
				"reset_time" => $datareset_time,
				"list_description" => $datalist_desc,
				"xferconf_a_number" => $dataxferconf_a_number,
				"xferconf_b_number" => $dataxferconf_b_number,
				"xferconf_c_number" => $dataxferconf_c_number,
				"xferconf_d_number" => $dataxferconf_d_number,
				"xferconf_e_number" => $dataxferconf_e_number
			);
		}
	} else {
		$apiresults = array("result" => "Error: No record found.");
	}
?>
