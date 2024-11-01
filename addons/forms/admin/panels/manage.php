<?php
global $userscontrol_form, $userscontrol;

$forms = $userscontrol_form->get_all();

?>
<div class="userscontrol-ultra-sect ">
        
      
<form action="" method="post" id="userscontrol-userslist">
          <input type="hidden" name="add-form" value="add-form" />
        
        <div class="userscontrol-ultra-success userscontrol-notification"><?php _e('Success ','users-control'); ?></div>
        
         <p><?php _e('This module gives you the capability to setup multiple or separate registration forms. For instance, If you want to have two separate forms or more e.g. Clients, Partners, Sellers, etc. This tool helps you create multiple forms with different fields.','users-control'); ?></p>
         
        
    <h3><?php _e('Add New Form ','users-control'); ?></h3>
    
   
                  <table width="100%" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td width="15%"><?php _e('Name: ','users-control'); ?></td>
             <td width="85%"><input type="text" id="form_name"  name="form_name"  /></td>
           </tr>
           
                   
          
          </table>
          
           <p>
           <input name="submit" type="submit"  class="button-primary" value="<?php _e('Confirm','users-control'); ?>"/>
          
    </p>
          
   
        </form>
        
                 <?php
			
			
				
				if (!empty($forms)){
				
				
				?>
       
           <table width="100%" class="wp-list-table widefat fixed posts table-generic">
            <thead>
                <tr>
                    <th width="12%" style="color:# 333"><?php _e('Unique Identifier', 'users-control'); ?></th>
                    <th width="21%"><?php _e('Name', 'users-control'); ?></th>
                    
                    <th width="13%"><?php _e('Shortcode', 'users-control'); ?></th>
                    <th width="20%"><?php _e('Actions', 'users-control'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			
				foreach ( $forms as $key => $form )
				{
					
			?>
              

                <tr  id="uu-edit-form-row-<?php echo $key; ?>">
                    <td><?php echo $key; ?></td>
                    <td  id="uu-edit-form-row-name-<?php echo $key; ?>"><?php echo $form['name']; ?></td>
                    
                    <td><?php echo $userscontrol_form->get_copy_paste_shortocde($key);?></td>
                   <td> <a href="#" class="button userscontrol-form-del"  id="" data-form="<?php echo $key; ?>"><i class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('Delete','users-control'); ?>
                   </a>  <a href="#" class="button-primary button-secondary userscontrol-form-edit"  id="" data-form="<?php echo $key ?>"><i class="uultra-icon-plus"></i>&nbsp;&nbsp;<?php _e('Edit','users-control'); ?>
</a> </td>
                </tr>
                
                
                <tr>
                
                 <td colspan="5" ><div id='uu-edit-form-box-<?php echo $key; ?>'></div> </td>
                
                </tr>
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no custom forms yet.','users-control'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
           <script type="text/javascript">  
		
		      var custom__del_confirmation ="<?php _e('Are you totally sure that you want to delete this form?','users-control'); ?>";
			  
			  
		
		 </script>
        
             

</div>