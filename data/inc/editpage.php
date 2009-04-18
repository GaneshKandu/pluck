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

//Make sure the file isn't accessed directly
if (!strpos($_SERVER['SCRIPT_FILENAME'], 'index.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'admin.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'install.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'login.php')) {
	//Give out an "Access denied!" error.
	echo 'Access denied!';
	//Block all other code.
	exit;
}

//redirect for a cancel
if (isset($_POST['cancel']))
	redirect('?action=page', 0);

//Get the filename.
$filename = get_page_filename($var1);

//If form is posted...
if (isset($_POST['save']) || isset($_POST['save_exit'])) {
	//Create the new filename.
	$filename_array = explode('.', $filename);
	$newfilename = $filename_array[0].'.'.seo_url($cont1);
	
	if (empty($description))
		$description = '';
	if (empty($keywords))
		$keywords = '';
		
	//Save the page.
	save_page($newfilename, $cont1, $cont2, $cont4, $description, $keywords, $cont3);
	
	//Check if the title is different from what we started with
	if ($newfilename.'.php' != $filename) {
		//Remove the old file.
		unlink('data/settings/pages/'.$filename);

		//Redirect to the new title only if it is a plain save
		if (isset($_POST['save'])) {
			redirect('?action=editpage&var1='.get_page_seoname($newfilename.'.php'), 0);
			exit;
		}
	}
	
	//Redirect the user. only if they are doing a save_exit
	if (isset($_POST['save_exit']))
		redirect('?action=page', 0);

}

//Include page information.
require_once ('data/settings/pages/'.$filename);

//Load module functions.
foreach ($module_list as $module) {
	if (file_exists('data/modules/'.$module.'/'.$module.'.site.php'))
		require_once ('data/modules/'.$module.'/'.$module.'.site.php');
}
unset($module);

//Generate the menu on the right.
?>
<div class="rightmenu">
<p><?php echo $lang_page8; ?></p>
<?php
	read_imagesinpages('images');
	read_pagesinpages('data/settings/pages', $filename);
?>
</div>
<form name="page_form" method="post" action="">
	<p>
		<label class="kop2" for="cont1"><?php echo $lang['general']['title']; ?></label>
		<br />
		<input name="cont1" id="cont1" type="text" value="<?php echo $title; ?>" />
	</p>
	<span class="kop2"><?php echo $lang['general']['contents']; ?></span>
	<br />
	<textarea class="tinymce" name="cont2" cols="70" rows="20"><?php echo htmlspecialchars($content) ?></textarea>
	<br />
	<div class="menudiv" style="width: 585px; margin-left: 0px;">
		<table>
			<tr>
				<td>
					<img src="data/image/modules.png" alt="" />
				</td>
				<td>
					<span class="kop3"><?php echo $lang['modules']['title']; ?></span>
					<br />
					<strong><?php echo $lang_modules16; ?></strong>
					<br />
					<table>
					<?php
						//First count how many modules we have, and exclude disabled modules.
						$number_modules = count($module_list);
						foreach ($module_list as $module) {
							if (!module_is_compatible($module) || !function_exists($module.'_theme_main'))
								$number_modules--;
						}
						unset($module);

						//Loop through modules, and display them.
						foreach ($module_list as $module) {
							//Only show if module is compatible.
							if (module_is_compatible($module) && function_exists($module.'_theme_main')) {
								$module_info = call_user_func($module.'_info');
								?>
									<tr>
										<td><?php echo $module_info['name']; ?></td>
										<td>
											<select name="cont3[<?php echo $module; ?>]">
												<option value="0"><?php echo $lang['general']['dont_display']; ?></option>
												<?php
													$counting_modules = 1;
													while ($counting_modules <= $number_modules) {
														//Check if this is the current setting.
														//...and select the html-option if needed
														if (isset($module_pageinc[$module]) && $module_pageinc[$module] == $counting_modules) {
														?>
															<option value="<?php echo $counting_modules; ?>" selected="selected"><?php echo $counting_modules; ?></option>
														<?php
														}

														//...if this is no the current setting, don't select the html-option.
														else {
														?>
															<option value="<?php echo $counting_modules; ?>"><?php echo $counting_modules; ?></option>
														<?php
														}

														//Higher counting_modules.
														$counting_modules++;
													}
												?>
											</select>
										</td>
									</tr>
								<?php
								}
							}
							unset($module);
						?>
					</table>
				</td>
			</tr>
		</table>
	</div>
	<div class="menudiv" style="width: 585px; margin-left: 0px;">
		<table>
			<tr>
				<td>
					<img src="data/image/options.png" alt="" />
				</td>
				<td>
					<p>
						<span class="kop3"><?php echo $lang['general']['other_options']; ?></span>
						<br />
						<input type="checkbox" name="cont4" id="cont4" <?php if ($hidden == 'no') echo'checked="checked"'; ?> value="no" /><label for="cont4"><?php echo $lang_pagehide1; ?></label>
					</p>
					<?php //TODO: Translate. ?>
					<span class="kop3">sub-page of</span>
					<br />
					<?php show_subpage_select('cont5', $var1); ?>
				</td>
			</tr>
		</table>
	</div>
	<input class="save" type="submit" name="save" value="<?php echo $lang['general']['save']; ?>"/>
	<input type="submit" name="save_exit" value="<?php echo $lang['general']['save_exit']; ?>" title="<?php echo $lang['general']['save_exit']; ?>" />
	<input class="cancel" type="submit" name="cancel" title="<?php echo $lang['general']['cancel']; ?>" value="<?php echo $lang['general']['cancel']; ?>" />
</form>

