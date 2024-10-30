<?php

/**
* Support form 
**/

function mo_support() {
	global $user;
	$user       = wp_get_current_user();	
	$user_email = $user->user_email;
	$user_phone = '';
	?>
    <div class="mo_support_layout">
        <h3>
        <?php echo mo_lt( 'Support' ); ?>
        </h3>
        <hr width="100%">
         <b>Need any help? Just send us a query so we can help you.</b> 
        <br>
        <form name="f" method="post" action="">           
            <br>
            <div>
              <table style="width:95%;">
                  <tr>
                      <td>
                        <input type="email" class="mo_table_textbox" style="width:100% !important;" id="query_email" name="query_email"
                               value="<?php echo $user_email ?>"
                               placeholder="Enter your email" required="true"/>
                      </td>
                  </tr>
                  <tr>
                      <td>
                        <input type="text" class="mo_table_textbox" style="width:100% !important;"
                               name="query_phone" id="query_phone"
                               value="<?php echo $user_phone; ?>"
                               placeholder="Enter your phone"/>
                      </td>
                  </tr>
                  <tr>
                    <td>
                        <textarea id="query" name="query"
                                  style="resize: vertical;width:100%;height:143px;"
                                  onkeyup="mo_valid(this)" onblur="mo_valid(this)" onkeypress="mo_valid(this)"
                                  placeholder="<?php echo mo_lt( 'Your query here...' ); ?>">
                        </textarea>
                    </td>
                  </tr>
              </table>
            </div>
            <br>
            <input type="hidden" name="option" value="mo_contact_us_query"/>
			      <input type="hidden" name="mo_send_query_nonce"
						value="<?php echo wp_create_nonce( "mo-send-query-nonce" ) ?>"/>
            <input type="submit" name="send_query" id="send_query"
                   value="<?php echo mo_lt( 'Submit Query' ); ?>"
                   style="float:right;" class="button button-primary button-large"/>
         <br><br>
        </form>
        <br>
    </div>
    <br>

    <script> 
        jQuery("#query_phone").intlTelInput();
        function mo_valid(f) {
            !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(/[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
        }
    </script>
	<?php
}

function mo_lt( $string ) {
	return __($string ,'miniorange-PHP-Guard' );

}