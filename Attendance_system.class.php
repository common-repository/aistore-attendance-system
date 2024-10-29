<?php

class Attendance_system{

 function aistore2030_punch_in_punch_out()
  {   
  
  
    global $wpdb;
  $table_name = $wpdb->prefix . 'attendance';
      
      $user = wp_get_current_user();
      
      $id=$user->ID ;
      
        $display_name=$user->display_name  ;
        $ip_address=aistore_getRealIpAddr();
        
  
    
    
    if ( 
    ! isset( $_POST['punch_nonce'] ) 
    || ! wp_verify_nonce( $_POST['punch_nonce'], 'punch_nonce' ) 
) {
 
    

} else {


$type = sanitize_text_field($_REQUEST['type']);


if($type=="in")
{
    	

$p=( $wpdb->prepare( "INSERT INTO $table_name (user_id,adate,
display_name,entrytime,entry_ip_address ) VALUES (%d,date(now()),%s,now() ,%s)",array($id, $display_name,$ip_address )));


	
 $wpdb->query($p);
}

elseif($type=="out")
{
	
	
 $wpdb->query( $wpdb->prepare( "update $table_name 
 set 
  exittime= now()  ,
  exit_ip_address = %s
 where
  user_id = %d    and 
  adate=  date(now())
  " ,array( $ip_address, $id) )
 );
 

}

}

 ?>


 
 <h2> <?php  _e( 'Attendance  sheet', 'aistore' ) ?>  </h2> 

 
 
 
   <table width="50%" border=1>
        
        <tr><th><?php  _e( 'Punch IN', 'aistore' ) ?></th><th><?php  _e( 'Punch Out', 'aistore' ) ?></th></tr>
        
        <tr>
        
        <td>
            
            <form method="post" action="">
   <!-- some inputs here ... -->
   <input type="hidden" name="type" value="in" />
   
   <?php wp_nonce_field( 'punch_nonce', 'punch_nonce' ); ?>
   
   <input type="submit" value="<?php  _e( 'Punch IN', 'aistore' ) ?>" />
</form>
        </td>
        
        <td>
             <form method="post" action="">
   <!-- some inputs here ... -->
   <input type="hidden" name="type" value="out" /> 
   
   <?php wp_nonce_field( 'punch_nonce', 'punch_nonce' ); ?>
 
   <input type="submit"   value="<?php  _e( 'Punch Out', 'aistore' ) ?> "/>
   
  
  </form>
      
            
        </td></tr>
    </table>
    
       

       
   



 <table class="widefat"> 
 

 <thead>
    <tr>
        <th><?php  _e( 'Name', 'aistore' ) ?></th>
               
   
       <th><?php  _e( 'Entry time', 'aistore' ) ?></th>       
        <th><?php  _e( 'Exit time', 'aistore' ) ?></th>  
    </tr>
</thead> 


<?php  
   
      
  
  

 $result = $wpdb->get_results("SELECT *  FROM {$wpdb->prefix}attendance WHERE DATE(entrytime) = CURDATE()  order by id desc");

 
 

foreach($result as $wp_formmaker_submits){

 
 echo "<tr>";
 
  
    echo "<td>".$wp_formmaker_submits->display_name."</td>";
     
     echo "<td>".$wp_formmaker_submits->entrytime." (";
	 
     echo "".$wp_formmaker_submits->entry_ip_address." )</td>";
     echo "<td>".$wp_formmaker_submits->exittime." ( ";
	 
     echo "".$wp_formmaker_submits->exit_ip_address." )</td>";
	  
	  
    echo "</tr>";
 
}
   

    echo "</table>";
}

}