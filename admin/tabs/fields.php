<?php 
global $userscontrol, $userscontrol_form,  $userscontrolcomplement;
$forms = $userscontrol_form->get_all();
$fields = get_option('userscontrol_profile_fields');
ksort($fields);


$last_ele = end($fields);
$new_position = $last_ele['position']+1;

$meta_custom_value = "";
$qtip_classes = 'qtip-light ';
?>


<h1>
	<?php _e('Fields Customizer','users-control'); ?>
</h1>
<p>
	<?php _e('Create and customize fields that displays on registration forms.','users-control'); ?>
</p>


<p >
<div class='userscontrol-ultra-success userscontrol-notification' id="fields-mg-reset-conf"><?php _e('Fields have been restored','users-control'); ?></div>

</p>


<div class="userscontrol-ultra-sect" >




<select name="userscontrol__custom_registration_form " class="userscontrol-btn-add" id="userscontrol__custom_registration_form">

				<option value="" selected="selected">

					<?php _e('Default Form','users-control'); ?>

				</option>

                

                <?php foreach ( $forms as $key => $form )

				{?>

				<option value="<?php echo esc_attr($key)?>">

					<?php echo esc_attr($form['name']); ?>

				</option>

                

                <?php }?>

		</select>

        

        <input type="text" id="userscontrol_custom_registration_form_name" class="userscontrol-btn-add" name="userscontrol_custom_registration_form_name" />

        <a href="#userscontrol-duplicate-form-btn" class="button button-secondary userscontrol-btn-add"  id="userscontrol-duplicate-form-btn"><i

	class="userscontrol-icon-plus"></i>&nbsp;&nbsp;<?php _e('Duplicate Current Form','users-control'); ?>

</a>



<a href="#userscontrol-add-field-btn" class="button button-secondary userscontrol-btn-add"  id="userscontrol-add-field-btn"></i><i class="fa fa-plus fa-lg"></i> <?php _e('Add New Field','users-control'); ?>
</a>


</div>




<div class="userscontrol-ultra-sect userscontrol-ultra-rounded" id="userscontrol-add-new-custom-field-frm" >

<table class="form-table userscontrol-add-form">

	

	<tr valign="top">
		<th scope="row"><label for="userscontrol_type"><?php _e('Type','users-control'); ?> </label>
		</th>
		<td><select name="userscontrol_type" id="userscontrol_type">
				<option value="usermeta">
					<?php _e('Registration Form Field','users-control'); ?>
				</option>
				<option value="separator">
					<?php _e('Separator','users-control'); ?>
				</option>
		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
			title="<?php _e('You can create a separator or a usermeta (profile field)','users-control'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="userscontrol_field"><?php _e('Editor / Input Type','users-control'); ?>
		</label></th>
		<td><select name="userscontrol_field" id="userscontrol_field">
				<?php  foreach($userscontrol->allowed_inputs as $input=>$label) { ?>
				<option value="<?php echo esc_attr($input); ?>">
					<?php echo esc_attr($label); ?>
				</option>
				<?php } ?>
		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
			title="<?php _e('When user edit profile, this field can be an input (text, textarea, image upload, etc.)','users-control'); ?>"></i>
		</td>
	</tr>

	<tr valign="top" >
		<th scope="row"><label for="userscontrol_meta_custom"><?php _e('New Custom Meta Key','users-control'); ?>
		</label></th>
		<td><input name="userscontrol_meta_custom" type="text" id="userscontrol_meta_custom"
			value="<?php echo esc_attr($meta_custom_value); ?>" class="regular-text" /> <i
			class="userscontrol-icon-question-sign userscontrol-tooltip2"
			title="<?php _e('Enter a custom meta key for this profile field if do not want to use a predefined meta field above. It is recommended to only use alphanumeric characters and underscores, for example my_custom_meta is a proper meta key.','users-control'); ?>"></i>
		</td>
	</tr>
    
   
	<tr valign="top">
		<th scope="row"><label for="userscontrol_name"><?php _e('Label','users-control'); ?> </label>
		</th>
		<td><input name="userscontrol_name" type="text" id="userscontrol_name"
			value="<?php if (isset($_POST['userscontrol_name']) && isset($this->errors) && count($this->errors)>0) echo esc_attr($_POST['userscontrol_name']); ?>"
			class="regular-text" /> <i
			class="userscontrol-icon-question-sign userscontrol-tooltip2"
			title="<?php _e('Enter the label / name of this field as you want it to appear in front-end (Profile edit/view)','users-control'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="userscontrol_tooltip"><?php _e('Tooltip Text','users-control'); ?>
		</label></th>
		<td><input name="userscontrol_tooltip" type="text" id="userscontrol_tooltip"
			value="<?php if (isset($_POST['userscontrol_tooltip']) && isset($this->errors) && count($this->errors)>0) echo esc_attr($_POST['userscontrol_tooltip']); ?>"
			class="regular-text" /> <i
			class="userscontrol-icon-question-sign userscontrol-tooltip2"
			title="<?php _e('A tooltip text can be useful for social buttons on profile header.','users-control'); ?>"></i>
		</td>
	</tr>
    
    
    <tr valign="top">
                <th scope="row"><label for="userscontrol_help_text"><?php _e('Help Text','users-control'); ?>
                </label></th>
                <td>
                    <textarea class="userscontrol-help-text" id="userscontrol_help_text" name="userscontrol_help_text" title="<?php _e('A help text can be useful for provide information about the field.','users-control'); ?>" ><?php if (isset($_POST['userscontrol_help_text']) && isset($this->errors) && count($this->errors)>0) echo esc_attr($_POST['userscontrol_help_text']); ?></textarea>
                    <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
                                title="<?php _e('Show this help text under the profile field.','users-control'); ?>"></i>
                </td>
    </tr>

	
  

	<tr valign="top">
		<th scope="row"><label for="userscontrol_can_edit"><?php _e('User can edit','users-control'); ?>
		</label></th>
		<td><select name="userscontrol_can_edit" id="userscontrol_can_edit">
				<option value="1">
					<?php _e('Yes','users-control'); ?>
				</option>
				<option value="0">
					<?php _e('No','users-control'); ?>
				</option>
		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
			title="<?php _e('Users can edit this profile field or not.','users-control'); ?>"></i>
		</td>
	</tr>

	
	<tr valign="top">
		<th scope="row"><label for="userscontrol_social"><?php _e('This field is social','users-control'); ?>
		</label></th>
		<td><select name="userscontrol_social" id="userscontrol_social">

				<option value="0">

					<?php _e('No','users-control'); ?>

				</option>

				<option value="1">

					<?php _e('Yes','users-control'); ?>

				</option>

		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"

			title="<?php _e('A social field can show a button with your social profile in the head of your profile. Such as Facebook page, Twitter, etc.','users-control'); ?>"></i>

		</td>

	</tr>

	
	<tr valign="top">

		<th scope="row"><label for="userscontrol_can_hide"><?php _e('User can hide','users-control'); ?>

		</label></th>

		<td><select name="userscontrol_can_hide" id="userscontrol_can_hide">

				<option value="1">

					<?php _e('Yes','users-control'); ?>

				</option>

				<option value="0">

					<?php _e('No','users-control'); ?>

				</option>

		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"

			title="<?php _e('Allow users to hide this profile field from public viewing or not. Selecting No will cause the field to always be publicly visible if you have public viewing of profiles enabled. Selecting Yes will give the user a choice if the field should be publicly visible or not. Private fields are not affected by this option.','users-control'); ?>"></i>

		</td>

	</tr>

	<tr valign="top">

		<th scope="row"><label for="userscontrol_can_hide"><?php _e('Allows HTML?','users-control'); ?>

		</label></th>

		<td>
		<select name="userscontrol_allow_html" id="userscontrol_allow_html">
			<option value="0"><?php _e('No','users-control'); ?></option>
			<option value="1"><?php _e('Yes','users-control'); ?></option>
		</select> 
		
		<i class="userscontrol-icon-question-sign userscontrol-tooltip2"

			title="<?php _e('Allow users to hide this profile field from public viewing or not. Selecting No will cause the field to always be publicly visible if you have public viewing of profiles enabled. Selecting Yes will give the user a choice if the field should be publicly visible or not. Private fields are not affected by this option.','users-control'); ?>"></i>

		</td>

	</tr>


	

	
	<tr valign="top">

		<th scope="row"><label for="userscontrol_private"><?php _e('This field is private','users-control'); ?>

		</label></th>

		<td><select name="userscontrol_private" id="userscontrol_private">

				<option value="0">

					<?php _e('No','users-control'); ?>

				</option>

				<option value="1">

					<?php _e('Yes','users-control'); ?>

				</option>

		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"

			title="<?php _e('Make this field Private. Only admins can see private fields.','users-control'); ?>"></i>

		</td>

	</tr>


	
	


	<tr valign="top">
		<th scope="row"><label for="userscontrol_private"><?php _e('This field is required','users-control'); ?>
		</label></th>
		<td><select name="userscontrol_required" id="userscontrol_required">
				<option value="0">
					<?php _e('No','users-control'); ?>
				</option>
				<option value="1">
					<?php _e('Yes','users-control'); ?>
				</option>
		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
			title="<?php _e('Selecting yes will force user to provide a value for this field at registration and edit profile. Registration or profile edits will not be accepted if this field is left empty.','users-control'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="userscontrol_show_in_register"><?php _e('Show on Registration form','users-control'); ?>
		</label></th>
		<td><select name="userscontrol_show_in_register" id="userscontrol_show_in_register">
				<option value="0">
					<?php _e('No','users-control'); ?>
				</option>
				<option value="1">
					<?php _e('Yes','users-control'); ?>
				</option>
		</select> <i class="userscontrol-icon-question-sign userscontrol-tooltip2"
			title="<?php _e('Show this field on the registration form? If you choose no, this field will be shown on edit profile only and not on the registration form. Most users prefer fewer fields when registering, so use this option with care.','users-control'); ?>"></i>
		</td>
        
        
	</tr>   


	<tr valign="top" class="userscontrol-icons-holder">

		<th scope="row"><label><?php _e('Icon for this field','users-control'); ?> </label>

		</th>

		<td><label class="userscontrol-icons"><input type="radio" name="userscontrol_icon"

				value="0" /> <?php _e('None','users-control'); ?> </label> 

				<?php foreach($this->fontawesome as $icon) { ?>

			<label class="userscontrol-icons"><input type="radio" name="userscontrol_icon"

				value="<?php echo $icon; ?>" />

                <i class="fa fa-<?php echo $icon; ?> userscontrol-tooltip3" title="<?php echo $icon; ?>"></i> </label>            <?php } ?>

		</td>

	</tr>
    
   

	<tr valign="top">
		<th scope="row"></th>
		<td>
          <div class="userscontrol-ultra-success userscontrol-notification" id="userscontrol-sucess-add-field"><?php _e('Success ','users-control'); ?></div>
        <input type="submit" name="userscontrol-add" 	value="<?php _e('Submit New Field','users-control'); ?>"
			class="button button-primary" id="userscontrol-btn-add-field-submit" /> 
            <input type="button"class="button button-secondary " id="userscontrol-close-add-field-btn"	value="<?php _e('Cancel','users-control'); ?>" />
		</td>
	</tr>

</table>


</div>


<!-- show customizer -->
<ul class="userscontrol-ultra-sect userscontrol-ultra-rounded" id="uu-fields-sortable" >
		
  </ul>
  
           <script type="text/javascript">  
		
		      var custom_fields_del_confirmation ="<?php _e('Are you totally sure that you want to delete this field?','users-control'); ?>";
			  var custom_fields_reset_confirmation ="<?php _e('Are you totally sure that you want to restore the default fields?','users-control'); ?>";
			  var custom_fields_duplicate_form_confirmation ="<?php _e('Please input a name','users-control'); ?>";
		 
		 userscontrol_reload_custom_fields_set();
		 </script>
         
         
         
        