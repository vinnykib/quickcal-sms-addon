<div class="apt-container">
            <div class="apt-panel-head">
               <h3>SMS Notification</h3>
            </div>
            <div class="apt-panel-body">
             <div id="disable-sms">

             <form action="options.php" method="post">
             <p>Enable or disable sms notifications</p>
             <?php settings_fields( 'sms_options_group' ); ?>
             <?php          

             // Get the value of this option.
             $options_value = get_option( 'sms_control' );            
            ?>
            <br>
            <b><label for="">Enabled</label></b>
            <input type="radio" name="sms_control" value="enable" class="show_sms_fields" id="<?php $options_value ?>"
            <?php checked( $options_value, 'enable' ); ?> />
            <b><label for="">Disabled</label></b>
             <input type="radio" name="sms_control" value="disable" class="hide_sms_fields" id="<?php $options_value ?>"
             <?php checked( $options_value, 'disable' ); ?>/>
             <br><br>
            <div class="sms-fieds">
        
          <?php if($options_value=='enable'): ?>
          
          <div class="sms_fields">
            <p>Make sure you have the details below filled for the SMS fuctionality to work. You can get them from <a target="_blank" href="https://www.twilio.com/">Twilio</a> </p>

            <b><label for="">Application ID</label></b><br>
            <input type="text" name="application_id" value="<?php echo get_option('application_id'); ?>"><br><br>

            <b><label for="">Application Token </label></b><br>
            <input type="text" name="application_token" value="<?php echo get_option('application_token'); ?>"><br><br>
            
            
            <b><label for="">Sender Phone Number </label></b><br>
            <input type="text" name="sender_phone" value="<?php echo get_option('sender_phone'); ?>"><br><br>
            


          </div>      

         <?php endif;?>
          
            </div>
                 
               <?php          
               submit_button();
               ?>

             </form>
             
            
             </div> 
            </div>
</div>