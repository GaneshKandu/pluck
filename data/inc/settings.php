<?php
/*
 * This file is part of pluck, the easy content management system
 * Copyright (c) somp (www.somp.nl)
 * http://www.pluck-cms.org

 * Pluck is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * See docs/COPYING for the complete license.
*/

//Make sure the file isn't accessed directly.
if (!strpos($_SERVER['SCRIPT_FILENAME'], 'index.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'admin.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'install.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'login.php')) {
	//Give out an "Access denied!" error.
	echo 'Access denied!';
	//Block all other code.
	exit;
}

//Include old settings, to display them.
require_once ('data/settings/options.php');

//If form has been submitted.
if (isset($_POST['save'])) {
	//Check if a sitetitle has been given in.
	if (empty($cont1))
		$error = show_error($lang['settings']['fill_name'], 1, true);

	//Check if emailaddress is valid.
	elseif (!filter_input(INPUT_POST, 'cont2', FILTER_VALIDATE_EMAIL))
		$error = show_error($lang['settings']['email_invalid'], 1, true);

	else {
		//If XHTML-ruleset is not on, turn it off.
		if (!isset($cont3))
			$cont3 = 'false';

		//Then, save the settings.
		save_options($cont1, $cont2, $cont3);

		show_error($lang['settings']['changing_settings'], 3);
		redirect('?action=options', 2);
		include_once ('data/inc/footer.php');
		exit;
	}
}
?>
<p>
	<strong><?php echo $lang['settings']['message']; ?></strong>
</p>
<?php
if (isset($error))
	echo $error;
?>
<?php run_hook('admin_settings_before'); ?>
<form method="post" action="">
	<p>
		<label class="kop2" for="cont1"><?php echo $lang['general']['change_title']; ?></label>
		<br />
		<span class="kop4"><?php echo $lang['settings']['choose_title']; ?></span>
		<br />
		<input name="cont1" id="cont1" type="text" value="<?php echo $sitetitle; ?>" style="width:30em;" />
	</p>
	<p>
		<label class="kop2" for="cont2"><?php echo $lang['settings']['email']; ?></label>
		<br />
		<span class="kop4"><?php echo $lang['settings']['email_descr']; ?></span>
		<br />
		<input name="cont2" id="cont2" type="text" value="<?php echo $email; ?>" />
	</p>
	<p>
		<span class="kop2"><?php echo $lang['general']['other_options']; ?></span>
		<br />
		<input type="checkbox" name="cont3" id="cont3" value="true" <?php if ($xhtmlruleset == 'true') echo 'checked="checked"'; ?> />
		<label for="cont3">&nbsp;<?php echo $lang['settings']['xhtml_mode']; ?></label>
	</p>
	<?php show_common_submits('?action=options'); ?>
</form>