<?php 
require($_SERVER['DOCUMENT_ROOT'].'/scripts/smarty.php');

class Admin_user extends Access_user {
	
	var $user_found = false;
	var $user_id;
	var $user_name;
	var $old_user_email;
	var $user_access_level;
	var $activation;

	function get_userdata($for_user, $type = "login") {
		if ($type == "login") {
			$sql = sprintf("SELECT id, login, email, access_level, active FROM %s WHERE login = '%s'", $this->table_name, trim($for_user));
		} else {
			$sql = sprintf("SELECT id, login, email, access_level, active FROM %s WHERE id = %d", $this->table_name, intval($for_user));
		}
		$result = mysql_query($sql);
		if (mysql_num_rows($result) == 1) {
			$obj = mysql_fetch_object($result);
			$this->user_id = $obj->id;
			$this->user_name = $obj->login;
			$this->old_user_email = $obj->email;
			$this->user_access_level = $obj->access_level;
			$this->activation = $obj->active;
			if ($this->user_name != $_SESSION['user']) {
				$this->user_found = true;
			} else {
				$this->user_found = false;
				$this->the_msg = "It's not allowed to change your own data!";
			}
			mysql_free_result($result);
		} else {
			$this->the_msg = "Sorry, no data for this loginname!";
		}	
	}
	function update_user_by_admin($new_level, $user_id, $def_pass, $new_email, $active, $confirmation = "no") {
		$this->user_found = true;
		$this->user_access_level = $new_level;
		if ($def_pass != "" && strlen($def_pass) < 4) {
			$this->the_msg = "Password is to short use the min. of 4 chars.";
		} else {
			if ($this->check_email($new_email)) {
				$sql = "UPDATE %s SET access_level = %d, email = '%s', active = '%s'";
				$sql .= ($def_pass != "") ? sprintf(", pw = '%s'", md5($def_pass)) : "";
				$sql .= " WHERE id = %d";
				$sql_compl = sprintf($sql, $this->table_name, $new_level, $new_email, $active, $user_id);
				if (mysql_query($sql_compl)) {
					$this->the_msg = "Data is modified for user with id#<b>".$user_id."</b>";
					if ($confirmation == "yes") {
						if ($this->send_confirmation($user_id)) {
							$this->the_msg .= "<br>...a confirmation mail is send to the user.";
						} else {
							$this->the_msg .= "<br>...ERROR no confirmation mail is send to the user.";
						}
					}
				} else {
					$this->the_msg = "Database error, please try again!";
				}
			} else {
				$this->the_msg = "The e-mail address is invalid!";
			}
		}
	}
	function access_level_menu($curr_level, $element_name = "level") {
		$menu = "<select name=\"".$element_name."\">\n";
		for ($i = MIN_ACCESS_LEVEL; $i <= MAX_ACCESS_LEVEL; $i++) {
			$menu .= "  <option value=\"".$i."\"";
			$menu .= ($curr_level == $i) ? " selected>" : ">";
			$menu .= $i."</option>\n";
		}
		$menu .= "</select>\n";
		return $menu;
	}
	// modified in version 1.97
	function activation_switch($formelement = "activation") {
		$radio_group = "<label for=\"".$formelement."\">Active?</label>\n";
		$labels = array("y"=>"yes", "n"=>"no", "b"=>"blocked");
		foreach ($labels as $key => $val) {
			$radio_group .= " <input name=\"".$formelement."\" type=\"radio\" value=\"".$key."\" ";
			$radio_group .= ($this->activation == $key) ? "checked=\"checked\" />\n" : "/>\n";
			$radio_group .= $val;
		}
		return $radio_group;        
	}
}

// Allowed users
$arrAuth = array(AUTH_ADMIN, AUTH_MOD);

$admin_update = new Admin_user($smarty);
$admin_update->access_page_levels($_SERVER['PHP_SELF'], $_SERVER['QUERY_STRING'], $arrAuth); // check the level inside the config file

if (isset($_POST['Submit'])) {
	if ($_POST['action'] == "update") {
		$conf_str = (isset($_POST['send_confirmation'])) ? $_POST['send_confirmation'] : ""; // the checkbox value to send a confirmation mail 
		$admin_update->update_user_by_admin($_POST['level'], $_POST['user_id'], $_POST['password_'.$_POST['user_id']], $_POST['email'], $_POST['activation'], $conf_str);
		$admin_update->get_userdata($_POST['login_name']); // this is needed to get the modified data after update
	} elseif ($_POST['action'] == "search") {
		$admin_update->get_userdata($_POST['login_name']);
	}
} elseif (isset($_GET['login_id']) && intval($_GET['login_id']) > 0) {
		$admin_update->get_userdata($_GET['login_id'], "is_id");
} 
$error = $admin_update->the_msg; // error message

if (isset($error))
{
	$smarty->interface->assignVar("error", $error);
}

$smarty->interface->assignText("user_admin");
$smarty->assign("php_self", $_SERVER['PHP_SELF']);
if ($admin_update->user_found)
{
	$smarty->assign("user_found", true);
	$smarty->interface->assignText("user_admin_desc");
	$smarty->assign("user_username", $admin_update->user_name);
	$smarty->assign("user_level", $admin_update->access_level_menu($admin_update->user_access_level));
	$smarty->interface->assignText("user_access_level");
	$smarty->interface->assignText("password");
	$smarty->assign("value_password", (isset($_POST['password_'.$_POST['user_id']])) ? $_POST['password_'.$_POST['user_id']] : "");
	$smarty->interface->assignTextVar('user_reg_pass_min_length', PW_LENGTH);
	$smarty->interface->assignText("email");
	$smarty->assign("value_email", (isset($_POST['email'])) ? $_POST['email'] : $admin_update->old_user_email);
	$smarty->assign("activation_switch", $admin_update->activation_switch());
	$smarty->interface->assignText("user_conf_mail");
	$smarty->interface->assignText("user_conf_mail_desc");
	$smarty->assign("value_user_id", (isset($_POST['user_id'])) ? $_POST['user_id'] : $admin_update->user_id);
	$smarty->assign("value_admin_name", $admin_update->user_name);
	$smarty->interface->assignText("user_admin_update");
	$smarty->interface->assignText("user_search_next_user");
}
else {
	$smarty->interface->assignText("user_find_user");
	$smarty->interface->assignText("user_admin_enter_username");
	$smarty->assign("value_username", (isset($_POST['login_name'])) ? $_POST['login_name'] : "");
}
$smarty->interface->assignText("back_home");
$smarty->assign("url_main_page", $admin_update->main_page);

$smarty->display('user_admin.tpl');
?>