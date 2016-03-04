<?php

function ci(){ return get_instance(); }


function checklogin(){
	if(islogged()) {
		redirect(base_url());
	}
}

function requirelogin(){
	if(!islogged()){
		die('<center>You are not login or your session has been expired<br /><a href="'.base_url().'">Back to Home</a></center>');
	}
}

function header_info($additional_data = array()){
	$basic_info = array(
		'site_title' => ci()->config->item('site_title'),
		'footer_text' => ci()->config->item('footer_text'),
		'site_email' => ci()->config->item('site_email'),
		'site_address' => ci()->config->item('site_address'),
		'site_contact' => ci()->config->item('site_contact'),
		'sidebarlink' => 0,
		'sidebarlink2' => 0
	);
	
	$user_info = array(
		'user_id' => 0,
		'display_name' => 'Guest',
		'email_address' => '',
		'user_level' => 0,
		'profile_pic' => ''
	);
	
	
	return array_merge(array_merge($basic_info,$user_info),$additional_data);
}

function getprivilege($pid,$uid){
	$sql = ci()->model->project_checkprivilege($pid,$uid);
	if(isset($sql->privilege)){ return $sql->privilege; }
	else return 0;
}

function recaptcha($method = ''){
	ci()->load->helper('recaptchalib');
	
	$publickey = ci()->config->item('captcha_publickey');
	$privatekey = ci()->config->item('captcha_privatekey');
	$error = null;
	
	switch($method){
		case 'check':
			if (ci()->input->post("recaptcha_response_field")){
				$resp = null;
				$resp = recaptcha_check_answer(
					$privatekey,
					$_SERVER["REMOTE_ADDR"],
					ci()->input->post("recaptcha_challenge_field"),
					ci()->input->post("recaptcha_response_field")
				);
				
				if($resp->is_valid){ return 'ok'; }
				else { return $resp->error; }
			}
			break;
		default: echo recaptcha_get_html($publickey, $error);
	}
}

function displaytags($text,$display = '',$class = ''){
	//PATTERNS // DIFFERENT TAGS
	$pattern = array(
		'project',
		'task',
		'user'
	);
	
	//TEXT TO BE REPLACED BASED FROM INDEX VALUE OF THE PATTERN
	$replace_text = array(
		'<a href="%s" class="'.$class.'">%s</a>',
		'<a href="%s" class="'.$class.'">%s</a>',
		'<a href="%s" class="'.$class.'">%s</a>'
	);
	
	//START
	for($x = 0; $x < count($pattern); $x++){
		$regex = '/\['.$pattern[$x].'=(.*?)\]/';
		
		preg_match_all($regex,$text,$match);
		
		switch($x){
			case 0:
				$match_val = $match[1];
				for($y = 0; $y < count($match_val); $y++){
					if(is_numeric($match_val[$y])){
						$id = $match_val[$y];
						$query = ci()->model->bbcode_project($id);
						
						if($query->num_rows() > 0){
							$row = $query->row();
							
							switch($display){
								case 'span': $do_replace = sprintf('<span class="'.$class.'">%s</span>',$row->project_title);break;
								case 'none': $do_replace = $row->project_title;break;
								default: $do_replace = sprintf($replace_text[$x],site_url().'projects?id='.$id,$row->project_title);
							}
							$text = str_replace($match[0][$y],$do_replace,$text);
						} else {
							switch($display){
								case 'span': $do_replace = sprintf('<span class="'.$class.'">%s</span>',"[?]");break;
								case 'none': $do_replace = "[?]";break;
								default: $do_replace = sprintf($replace_text[$x],'javascript:;',"[?]");
							}
							$text = str_replace($match[0][$y],$do_replace,$text);
						}
					}
				}
				break;
			case 1:
				$match_val = $match[1];
				for($y = 0; $y < count($match_val); $y++){
					if(is_numeric($match_val[$y])){
						$id = $match_val[$y];
						$query = ci()->model->bbcode_task($id);
						
						if($query->num_rows() > 0){
							$row = $query->row();
							
							switch($display){
								case 'span': $do_replace = sprintf('<span class="'.$class.'">%s</span>',htmlspecialchars($row->task_title));break;
								case 'none': $do_replace = htmlspecialchars($row->task_title);break;
								default: $do_replace = sprintf($replace_text[$x],site_url().'tasks?id='.$id,$row->task_title);
							}
							$text = str_replace($match[0][$y],$do_replace,$text);
						} else {
							switch($display){
								case 'span': $do_replace = sprintf('<span class="'.$class.'">%s</span>',"[?]");break;
								case 'none': $do_replace = "[?]";break;
								default: $do_replace = sprintf($replace_text[$x],'javascript:;',"[?]");
							}
							$text = str_replace($match[0][$y],$do_replace,$text);
						}
					}
				}
				break;
			case 2:
				$match_val = $match[1];
				for($y = 0; $y < count($match_val); $y++){
					if(is_numeric($match_val[$y])){
						$id = $match_val[$y];
						$query = ci()->model->bbcode_user($id);
						
						if($query->num_rows() > 0){
							$row = $query->row();
							
							switch($display){
								case 'span': $do_replace = sprintf('<span class="'.$class.'">%s</span>',$row->fullname);break;
								case 'none': $do_replace = $row->fullname;break;
								default: $do_replace = sprintf($replace_text[$x],site_url().'profile?id='.$id,$row->fullname);
							}
							$text = str_replace($match[0][$y],$do_replace,$text);
						} else {
							switch($display){
								case 'span': $do_replace = sprintf('<span class="'.$class.'">%s</span>',"[?]");break;
								case 'none': $do_replace = "[?]";break;
								default: $do_replace = sprintf($replace_text[$x],'javascript:;',"[?]");
							}
							$text = str_replace($match[0][$y],$do_replace,$text);
						}
					}
				}
				break;
		}
	}
	//END
	return $text;
}

function add_notification($type,$data,$actor_id = 0){
	$actor_id = $actor_id > 0 ? $actor_id:session('user_id');
	
	switch($type){
		case 'post_agree':
			$post_id = $data['post_id'];
			
			$notify_to_sql = ci()->db->query("select poster_id,project_id,task_id from posts where id = $post_id limit 1");
			$notify_to = $notify_to_sql->row();
			
			$proj_id = $notify_to->project_id;
			$task_id = $notify_to->task_id;
			$redir = "?pid=".$post_id;
			$tagdata = "type=post_agree,post_id=".$post_id;
			
			$query = ci()->db->query("select id from agree_disagree where post_id = $post_id and agree = 1");
			$query_count = $query->num_rows() - 1;
			
			if($query_count > 1){ $msg = "[user=$actor_id] and ".($query_count)." others agree on your post"; }
			elseif($query_count == 1){ $msg = "[user=$actor_id] and 1 other agree on your post"; }
			else { $msg = "[user=$actor_id] agree on your post"; }
			
			if($task_id > 0){ $msg .= " in task [task=$task_id]"; }
			elseif($proj_id > 0){ $msg .= " in [project=$proj_id]"; }
			
			ci()->model->add_notification($notify_to->poster_id,$msg,$redir,$tagdata,$actor_id);
			break;
			
		case 'post_disagree':
			$post_id = $data['post_id'];
			$notify_to_sql = ci()->db->query("select poster_id,project_id,task_id from posts where id = $post_id limit 1");
			$notify_to = $notify_to_sql->row();
			
			$proj_id = $notify_to->project_id;
			$task_id = $notify_to->task_id;
			$redir = "?pid=".$post_id;
			$tagdata = "type=post_disagree,post_id=".$post_id;
			
			$query = ci()->db->query("select id from agree_disagree where post_id = $post_id and disagree = 1");
			$query_count = $query->num_rows() - 1;
			
			if($query_count > 1){ $msg = "[user=$actor_id] and ".($query_count)." others disagree on your post"; }
			elseif($query_count == 1){ $msg = "[user=$actor_id] and 1 other disagree on your post"; }
			else { $msg = "[user=$actor_id] disagree on your post"; }
			
			if($task_id > 0){ $msg .= " in task [task=$task_id]"; }
			elseif($proj_id > 0){ $msg .= " in [project=$proj_id]"; }
			
			ci()->model->add_notification($notify_to->poster_id,$msg,$redir,$tagdata,$actor_id);
			break;
			
		case 'post_feed':
			$post_id = $data['post_id'];
			$proj_id = $data['proj_id'];
			$task_id = $data['task_id'];
			$to_uid = $data['to_uid'];
			
			if(isset($data['notify'])){ $is_notify = $data['notify']; }
			else { $is_notify = ''; }
			
			$tagdata = "type=post_feed,post_id=$post_id";
			
			//IF POSTED ON PROFILE WALL
			if($to_uid > 0 && $to_uid <> $actor_id){ 
				$msg = "[user=$actor_id] posted on your wall";
				$redir = 'profile?id='.$to_uid.'&pid='.$post_id;
				
				ci()->model->add_notification($to_uid,$msg,$redir,$tagdata,$actor_id);
			} else {
				if($proj_id > 0 && $task_id > 0){
					$msg = "[user=$actor_id] posted on task [task=$task_id]";
				} else {
					$msg = "[user=$actor_id] posted on [project=$proj_id]";
				}
				
				$redir = '?pid='.$post_id;
				
				//NOTIFY TO ALL MEMBERS OF PROJECT
				$email_member = array();
				$sql = ci()->model->get_projectmembers_accepted($proj_id,'x');
				foreach($sql->result() as $row){
					if($row->user_id <> $actor_id){
						ci()->model->add_notification($row->user_id,$msg,$redir,$tagdata,$actor_id);
						$email_member[] = $row->email_address;
					}
				}
				
				if($is_notify == 'true'){
					$subject = "TeamStorm Project Update";
					$message = displaytags($msg);
					do_sendmail($subject,$message,$email_member);
				}
			}
			break;
			
		case 'follow':
			$target_user = $data['target_user'];
			$redir = "profile?id=$actor_id";
			$msg = "[user=$actor_id] is now following you";
			$tagdata = "type=follow,actor_id=$actor_id,target_id=$target_user";
			
			if($target_user <> $actor_id){
				ci()->model->add_notification($target_user,$msg,$redir,$tagdata,$actor_id);
			}
			break;
			
		case 'join_project':
			$project_id = $data['project_id'];
			$redir = "projects/members/$project_id?sort=3";
			$tagdata = "type=join_project,project_id=$project_id";
			
			$sql = ci()->model->get_projectmembers_accepted($project_id,3);
			$count = $sql->num_rows() - 1;
			
			if($count == 1){ 
				$msg = "[user=$actor_id] and 1 other wants to join [project=$project_id]";
			} elseif($count > 1){
				$msg = "[user=$actor_id] and $count others wants to join [project=$project_id]";
			} else {
				$msg = "[user=$actor_id] wants to join [project=$project_id]";
			}
			
			//NOTIFY TO ALL MEMBERS OF PROJECT
			$sql = ci()->model->get_projectmembers_accepted($project_id,'x');
			foreach($sql->result() as $row){
				if($row->user_id <> $actor_id){
					ci()->model->add_notification($row->user_id,$msg,$redir,$tagdata,$actor_id);
				}
			}
			break;
			
		case 'comment_agree':
			$comment_id = $data['comment_id'];
			$tagdata = "type=comment_agree,comment_id=$comment_id";
			
			$query = ci()->db->query("select post_id,commentor_id from post_comments where id = $comment_id");
			$row = $query->row();
			
			$redir = "?pid=".$row->post_id;
			
			$query = ci()->db->query("select id from comment_agree where post_id = $comment_id");
			$query_count = $query->num_rows() - 1;
			
			if($query_count > 1){ $msg = "[user=$actor_id] and ".($query_count)." others agree on your comment"; }
			elseif($query_count == 1){ $msg = "[user=$actor_id] and 1 other agree on your comment"; }
			else { $msg = "[user=$actor_id] agree on your comment"; }
			
			ci()->model->add_notification($row->commentor_id,$msg,$redir,$tagdata,$actor_id);
			break;
			
		case 'post_comment':
			$post_id = $data['post_id'];
			$redir = "?pid=$post_id";
			$tagdata = "type=post_comment,post_id=$post_id";
			
			$query = ci()->db->query("select distinct(commentor_id) from post_comments where post_id = $post_id");
			$query_count = $query->num_rows() - 1;
			
			if($query_count > 1){ $msg = "[user=$actor_id] and ".($query_count)." others commented on "; }
			elseif($query_count == 1){ $msg = "[user=$actor_id] and 1 other commented on "; }
			else { $msg = "[user=$actor_id] commented on "; }
			
			$notify_to_sql = ci()->db->query("select poster_id from posts where id = $post_id limit 1");
			$notify_to = $notify_to_sql->row();
			
			foreach($query->result() as $row){
				if($row->commentor_id <> $actor_id && $row->commentor_id <> $notify_to->poster_id){
					$msg1 = $msg . " a post";
					ci()->model->add_notification($row->commentor_id,$msg1,$redir,$tagdata,$actor_id);
				}
			}
			
			$notify_to_sql = ci()->db->query("select poster_id from posts where id = $post_id limit 1");
			$notify_to = $notify_to_sql->row();
			
			if($notify_to->poster_id <> $actor_id){
				$msg2 = $msg . " your post";
				ci()->model->add_notification($notify_to->poster_id,$msg2,$redir,$tagdata,$actor_id);
			}
			break;
			
		case 'task_approve':
			$task_id = $data['task_id'];
			$notify_to =$data['notify_to'];
			$tagdata = "type=task_approve,task_id=$task_id";
			$redir = "tasks?id=$task_id";
			
			$msg = "[user=$actor_id] Approves your task";
			
			if($notify_to <> $actor_id){
				$msg2 = $msg . " your post";
				ci()->model->add_notification($notify_to,$msg,$redir,$tagdata,$actor_id);
			}
			break;
			
		case 'task_remove_member':
			$task_id = $data['task_id'];
			$target_id = $data['target_id'];
			
			$redir = "tasks?id=$task_id";
			$tagdata = "type=task_remove_member,target_id=$target_id,task_id=$task_id";
			$msg = "You have been removed from task [task=$task_id]";
			
			if($target_id <> $actor_id){
				ci()->model->add_notification($target_id,$msg,$redir,$tagdata,$actor_id);
			}
			break;
		
		case 'task_creation':
			$task_id = $data['task_id'];
			$project_id = $data['project_id'];
			
			$redir = "tasks?id=$task_id";
			$tagdata = "type=task_creation,project_id=$project_id";
			
			$sql = "select distinct(t.creator_id) from tasks t where t.is_accepted = 0 and t.project_id = ?";
			$query = ci()->db->query($sql,$project_id);
			
			if($query->num_rows() == 2){ 
				$msg = "[user=$actor_id] and 1 other created a task in [project=$project_id]";
				$redir = "tasks/project_list?id=".$project_id;
			}
			elseif($query->num_rows() > 2){ 
				$msg = "[user=$actor_id] and ".$query->num_rows()." others created a task in [project=$project_id]"; 
				$redir = "tasks/project_list?id=".$project_id;
			}
			else { $msg = "[user=$actor_id] created a task in [project=$project_id]"; }
			
			$sql = "select user_id from project_members where privilege > 0 and is_accepted = 1 and project_id = ?";
			$query = ci()->db->query($sql,$project_id);
			
			foreach($query->result() as $row){
				if($row->user_id <> $actor_id  && getprivilege($project_id,$actor_id) == 0){
					ci()->model->add_notification($row->user_id,$msg,$redir,$tagdata,$actor_id);
				}
			}
			break;
			
		case 'task_grab':
			$task_id = $data['task_id'];
			
			$tagdata = "type=task_grab,task_id=$task_id";
			$redir = "tasks?id=$task_id";
			
			$sql = "select distinct(member_id) from task_members where task_id = ? and is_accepted = 0 and assigned_by = 0";
			$query = ci()->db->query($sql,$task_id);
			
			if($query->num_rows() == 2){ $msg = "[user=$actor_id] and 1 other sends a request to your task [task=$task_id]"; }
			elseif($query->num_rows() > 2){ $msg = "[user=$actor_id] and ".$query->num_rows()."others sends a request to your task [task=$task_id]"; }
			else { $msg = "[user=$actor_id] requesting to have your task [task=$task_id]"; }
			
			$query = ci()->model->get_taskinfo($task_id,$actor_id);
			if($query->num_rows()){
				$row = $query->row();
				
				$target_id = $row->creator_id;
				
				if($target_id <> $actor_id && getprivilege($row->project_id,$actor_id) == 0){
					ci()->model->add_notification($target_id,$msg,$redir,$tagdata,$actor_id);
				}
			}
			break;
			
		case 'task_complete':
			$task_id = $data['task_id'];
			
			$redir = "tasks?id=$task_id";
			$tagdata = "type=task_complete,task_id=$task_id";
			$msg = "[user=$actor_id] completed the task [task=$task_id]";
			
			$query = ci()->model->get_taskmembers($task_id);
			
			foreach($query->result() as $row){
				if($row->member_id <> $actor_id && $row->is_accepted == 1){
					ci()->model->add_notification($row->member_id,$msg,$redir,$tagdata,$actor_id);
				}
			}
			break;
		
		case 'task_remove':
			$task_id = $data['task_id'];
			$project_id = $data['project_id'];
			$task_title = $data['task_title'];
			
			$redir = "projects?id=$project_id";
			$tagdata = "type=task_remove,task_id=$task_id";
			$msg = "[user=$actor_id] removed the task <span class='name'>$task_title</span>";
			
			$query = ci()->model->get_taskmembers($task_id);
			
			foreach($query->result() as $row){
				if($row->member_id <> $actor_id && $row->is_accepted == 1){
					ci()->model->add_notification($row->member_id,$msg,$redir,$tagdata,$actor_id);
				}
			}
			break;
			
		case 'task_accept':
			$task_id = $data['task_id'];
			$redir = "tasks?id=$task_id";
			$tagdata = "type=task_accept,task_id=$task_id";
			
			$sql = "select distinct(member_id) from task_members where task_id = ? and is_accepted = 1";
			$query = ci()->db->query($sql,$task_id);
			
			if($query->num_rows() == 2){ $msg = "[user=$actor_id] and 1 other accepted your task [task=$task_id]"; }
			elseif($query->num_rows() > 2){ $msg = "[user=$actor_id] and ".$query->num_rows()."others accepted your task [task=$task_id]"; }
			else { $msg = "[user=$actor_id] accepted your task [task=$task_id]"; }
			
			$sql = "select creator_id,is_accepted from tasks where id = ?";
			$query = ci()->db->query($sql,$task_id);
			$row = $query->row();
			
			if($row->creator_id <> $actor_id && $row->is_accepted == 1){
				ci()->model->add_notification($row->creator_id,$msg,$redir,$tagdata,$actor_id);
			}
			break;
		
		case 'task_decline':
			$task_id = $data['task_id'];
			$redir = "tasks?id=$task_id";
			$tagdata = "type=task_decline,task_id=$task_id,actor_id=$actor_id";
			
			$msg = "[user=$actor_id] declined your task [task=$task_id]"; 
			
			$sql = "select creator_id,is_accepted from tasks where id = ?";
			$query = ci()->db->query($sql,$task_id);
			$row = $query->row();
			
			if($row->creator_id <> $actor_id && $row->is_accepted == 1){
				ci()->model->add_notification($row->creator_id,$msg,$redir,$tagdata,$actor_id);
			}
			break;
			
		case 'post_mention':
			$user_id = $data["user_id"];
			$post_id = $data["post_id"];
			$redir = "?pid=$post_id";
			$tagdata = "type=post_mention,user_id=$user_id,actor_id=$actor_id,post_id=$post_id";
			$msg = "[user=$actor_id] tagged you in a post";
			
			if($user_id <> $actor_id){
				ci()->model->add_notification($user_id,$msg,$redir,$tagdata,$actor_id);
			}
			break;
	}
}

/*
function do_sendmail($subject,$message,$to,$to_name = ''){
	$owner_email = "support@teamstorm.net";
	
	if(strlen(trim($to_name)) > 0){ $to_name = " ". $to_name; }
	
	$site_title = ci()->config->item('site_title');
	$site_desc = ci()->config->item('site_description');
	$site_footer = ci()->config->item('footer_text');
	$site_address = ci()->config->item('site_address');
	
	$tmpl_email = file_get_contents("assets/email_template/index.html");
	$tmpl_email = str_replace("[site_url]",base_url(),$tmpl_email);
	$tmpl_email = str_replace("[target_name]",$to_name,$tmpl_email);
	$tmpl_email = str_replace("[site_title]",$site_title,$tmpl_email);
	$tmpl_email = str_replace("[message]",$message,$tmpl_email);
	$tmpl_email = str_replace("[site_description]",$site_desc,$tmpl_email);
	$tmpl_email = str_replace("[site_footer]",$site_footer,$tmpl_email);
	$tmpl_email = str_replace("[site_address]",$site_address,$tmpl_email);
	
	$headers = array();
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=iso-8859-1';
	$headers[] = 'To: ' . $to;
	$headers[] = 'From: ' . $owner_email;
	$headers[] = "Subject: {".$subject."}";
	$headers[] = "X-Mailer: PHP/".phpversion();
	
	$do_email = mail($to,$subject, $tmpl_email, implode("\r\n", $headers));
	return $do_email;
}
*/

function do_sendmail($subject,$message,$to,$to_name = ''){
	$from_name = "TeamStorm";
	$from = "support@teamstormapps.net";
		
	$config = Array(
		'protocol' => 'smtp',
		'smtp_host' => 'mail.teamstormapps.net',
		'smtp_port' => 26,
		'smtp_user' => 'support@teamstormapps.net',
		'smtp_pass' => 'teamstorm123',
		'mailtype' => 'html',
		'wordwrap' => TRUE
	);
		
	ci()->load->library('email',$config);
	
	//HTML TEMPLATE
	$site_title = ci()->config->item('site_title');
	$site_desc = ci()->config->item('site_description');
	$site_footer = ci()->config->item('footer_text');
	$site_address = ci()->config->item('site_address');
	
	$tmpl_email = file_get_contents("assets/email_template/index.html");
	$tmpl_email = str_replace("[site_url]",base_url(),$tmpl_email);
	$tmpl_email = str_replace("[target_name]",$to_name,$tmpl_email);
	$tmpl_email = str_replace("[site_title]",$site_title,$tmpl_email);
	$tmpl_email = str_replace("[message]",$message,$tmpl_email);
	$tmpl_email = str_replace("[site_description]",$site_desc,$tmpl_email);
	$tmpl_email = str_replace("[site_footer]",$site_footer,$tmpl_email);
	$tmpl_email = str_replace("[site_address]",$site_address,$tmpl_email);
	
	//SENDING MAIL
	ci()->email->from($from,$from_name);
	ci()->email->to($to); 
	ci()->email->subject($subject);
	ci()->email->message($tmpl_email); 
	ci()->email->send();
	
	//echo ci()->email->print_debugger();
	return true;
}

function notification_icons($tagdata){
	$arr = array(
		'agree' => array('post_agree','comment_agree'),
		'disagree' => array('post_disagree'),
		'post' => array('post_feed'),
		'comment' => array('post_comment'),
		'task' => array('task_approve','task_creation','task_grab','task_accept'),
		'task_x' => array('task_remove_member','task_remove','task_decline'),
		'task_check' => array('task_complete'),
		'user' => array('follow'),
		'project' => array('join_project')
	);
	
	$tagdata = str_replace('type=','',$tagdata);
	
	if(in_array($tagdata,$arr['agree'])){ return '<span class="fa fa-thumbs-o-up"></span>'; }
	elseif(in_array($tagdata,$arr['disagree'])){ return '<span class="fa fa-thumbs-o-down"></span>'; }
	elseif(in_array($tagdata,$arr['post'])){ return '<span class="ico ico-bubble-13"></span>'; }
	elseif(in_array($tagdata,$arr['comment'])){ return '<span class="ico ico-bubbles-10"></span>'; }
	elseif(in_array($tagdata,$arr['task'])){ return '<span class="ico ico-stack-list"></span>'; }
	elseif(in_array($tagdata,$arr['task_x'])){ return '<span class="ico ico-stack-cancel"></span>'; }
	elseif(in_array($tagdata,$arr['task_check'])){ return '<span class="ico ico-stack-checkmark"></span>'; }
	elseif(in_array($tagdata,$arr['user'])){ return '<span class="ico ico-user"></span>'; }
	elseif(in_array($tagdata,$arr['project'])){ return '<span class="ico ico-folder"></span>'; }
	else { return '<span class="ico ico-quill-3"></span>'; }
}

function gethashtags($text)
{
	//Match the hashtags
	preg_match_all('/(^|[^a-z0-9_])#([a-z0-9_]+)/i', $text, $matchedHashtags);
	$hashtag = array();
	// For each hashtag, strip all characters but alpha numeric
	if(!empty($matchedHashtags[0])) {
		foreach($matchedHashtags[0] as $match) {
			$hashtag[] = preg_replace("/[^a-z0-9]+/i", "", $match);
		}
	}
	//to remove last comma in a string
	return $hashtag;
}

function display_mention($text = ''){
	$regex = '/@\[(.*?)\]/';
	preg_match_all($regex,$text,$match);
				
	$match_val = $match[1];
	for($y = 0; $y < count($match_val); $y++){
		if(isset($match_val[$y])){
			$id_arr = explode(":",$match_val[$y]);
			$id = $id_arr[0];
			$name = $id_arr[2];
			
			$do_replace = sprintf('<a href="'.site_url().'profile?id=%s">%s</a>',$id,$name);
			$text = str_replace($match[0][$y],$do_replace,$text);
		}
	}
	return $text;
}

function mention_notify($text,$post_id){
	$regex = '/@\[(.*?)\]/';
	preg_match_all($regex,$text,$match);
				
	$match_val = $match[1];
	for($y = 0; $y < count($match_val); $y++){
		if(isset($match_val[$y])){
			$id_arr = explode(":",$match_val[$y]);
			$id = $id_arr[0];
			$name = $id_arr[2];
			
			$da = array(
				'post_id' => $post_id,
				'user_id' => $id
			);
			
			add_notification('post_mention',$da);
		}
	}
	return true;
}

function convert_hashtag($text,$class = ''){
	$text = htmlspecialchars($text);
	
	$regex = array(
		'/(?i)\b((?:https?:\/\/|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', 
		//'/(?i)\b((www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))/', 
		//'/(^|[^a-z0-9_])@([a-z0-9_]+)/i', 
		'/(^|[^a-z0-9_])#([a-z0-9_]+)/i'
	);
	$regexval = array(
		'<a href="$1" target="_blank">$1</a>', 
		//'<a href="http://$1" target="_blank">$1</a>', 
		//'$1<a href="">@$2</a>', 
		'$1<a href="'.site_url().'hashtag?key=$2" class="'.$class.'">#$2</a>'
	);
	
	$parsedMessage = preg_replace($regex, $regexval, $text);
	return display_mention($parsedMessage);
}

function can_delete_comment($id){
	$query = ci()->model->get_commentnposter($id);
	$ret = 0;
	
	if($query->num_rows() == 1){
		$row = $query->row();
		
		if($row->commentor_id == session('user_id') || $row->poster_id == session('user_id')){ $ret = 1; }
	}
	
	return $ret;
}

function check_project_member_stat($pid = 0,$uid = 0){
	$uid = $uid  > 0 ? $uid:session('user_id');
	/* FUNCTION CHECK PROJECT MEMBER
	OUTPUT
		0 = not member
		1 = member
		2 = invited at project
		3 = sends requests to the project
	*/
	$sql = ci()->model->check_ifprojectmember($pid,$uid);
	if($sql->num_rows() > 0){
		$row = $sql->row();
		if($row->is_accepted > 0){ return 1; } 
		else {
			if($row->joined_by > 0){ return 2; }
			else { return 3; }
		}
	} else {
		return 0;
	}
}

function can_view_project($pid,$uid = 0){
	if($uid == 0){ $uid = session('user_id'); }
	
	//GET PROJECT SETTINGS
	ci()->load->model('projsett_model');
	$query = ci()->projsett_model->get_defaults($pid);
	
	if($query->num_rows()){
		$row = $query->row();
		$privacy = $row->privacy;
		
		$chk_member = check_project_member_stat($pid);
		
		if($privacy == 2 && $chk_member <> 1){ 
			redirect("errors/404");
			exit;
		}
	}
}

function is_closed_project_nonemembers($pid,$uid = 0){
	$uid = $uid > 0 ? $uid:session('user_id'); 
	
	//GET PROJECT SETTINGS
	ci()->load->model('projsett_model');
	$query = ci()->projsett_model->get_defaults($pid);
	
	if($query->num_rows()){
		$row = $query->row();
		$privacy = $row->privacy;
		
		$chk_member = check_project_member_stat($pid,$uid);
		
		if($privacy == 1 && $chk_member <> 1){ return false; } 
		else { return true; }
		
	} else { return false; }
}

function get_myrate($stars = 5){
	$sql = ci()->model->get_mytasks(session('user_id'),0,1);
	$data = array();
	$duedate = 0;
	$completed_on_date = 0;
	
	foreach($sql->result()  as $row){
		$str_start = strtotime($row->date_start);
		$str_end = strtotime($row->date_end);
		
		if(strtotime('now') > $str_start){
			if(strtotime('now') > $str_end){ $duedate++; }
			else { $completed_on_date++; }
		}
	}
	
	if($completed_on_date > 0){ $percentage = round(($completed_on_date / ($completed_on_date + $duedate)) * 100); }
	else { $percentage = 0; }
	$html = '';
	
	for($x = 1; $x <= $stars; $x++){
		$markfull = (100 / $stars) * $x;
		$markhalf = $markfull - (100 / $stars) / 2;
		
		
		if($percentage >= $markfull){ $html .= '<span class="ico ico-star-6"></span>'; }
		elseif($percentage >= $markhalf){ $html .= '<span class="ico ico-star-5"></span>'; }
		else { $html .= '<span class="ico ico-star-4"></span>'; }
	}
	
	return $html;
}

function convert_datetime($original_datetime){
	$original_timezone = new DateTimeZone(date_default_timezone_get());

	// Instantiate the DateTime object, setting it's date, time and time zone.
	$datetime = new DateTime($original_datetime, $original_timezone);

	$target_timezone = $original_timezone;
	
	//get user timezone
	$query = ci()->model->get_userinfo(session('user_id'));

	if($query->num_rows()){
		$row = $query->row();
		if(in_array($row->timezone,timezone_identifiers_list())){ $target_timezone = new DateTimeZone($row->timezone);}
	}
	
	// Set the DateTime object's time zone to convert the time appropriately.
	$datetime->setTimeZone($target_timezone);

	// Outputs a date/time string based on the time zone you've set on the object.
	$dt_result = $datetime->format('Y-m-d H:i:s');

	return strtotime($dt_result);
}

function proj_filesize($proj_id){
	$query = ci()->model->count_proj_filesize($proj_id);
	$maxuploadfile = ci()->config->item('cnf_max_file_upload');
	$maxfilesize = ci()->config->item('cnf_proj_max_filesize');
	
	if($query->num_rows()){
		$row = $query->row();
		$usedspace = $row->filesize;
		$usedspacepercent = round(($usedspace / $maxfilesize) * 100);
		$freespace = ($maxfilesize - $usedspace);
		$freespace_percent = round(($freespace / $maxfilesize) * 100);
		
		$return = array(
			'max_file_size' => $maxfilesize,
			'used_space' => $usedspace,
			'free_space' => $freespace,
			'used_space_percent' => $usedspacepercent,
			'free_space_percent' => $freespace_percent,
			'max_upload_file' => $maxuploadfile
		);
		
		return $return;
	} else {
		return  array(
			'max_file_size' => $maxfilesize,
			'used_space' => 0,
			'free_space' => $maxfilesize,
			'used_space_percent' => 0,
			'free_space_percent' => 100,
			'max_upload_file' => $maxuploadfile
		);
	}
}

function current_theme(){
	$theme = ci()->session->userdata('current_theme');
	if($theme){ 
		$base = base_url() . 'assets/css/color/';
		$themeList = ci()->config->item('theme_list');
		
		if(array_key_exists($theme,$themeList)){
			return $base . $themeList[$theme]['path'];
		}
	}
}