<?php
/*
  Plugin Name: AIStore Attendance System
  Plugin URI: https://wordpress.org/plugins/aistore-attendance-system/#description
  Author: susheelhbti
  Version : 3.0
  Author URI: http://www.aistore2030.com/
  Description: AIStore Attendance System wordpress plugin for Attendance of company employee
*/
 
 				
function aistore2030_options_install() {
   	global $wpdb;
  	 

$table_name = $wpdb->prefix . 'attendance';
$table_name_index = $wpdb->prefix . 'attendance_index';


	if($wpdb->get_var("show tables like '$table_name '") != $table_name ) 
	{
		$sql = "CREATE TABLE " . $table_name . " (
		  `id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `adate` date NOT NULL,
  `user_id` int(10) NOT NULL,
  `entrytime` datetime NOT NULL,
  `display_name` varchar(250) NOT NULL,
  `exittime` datetime NOT NULL,
  `entry_ip_address` varchar(100) NOT NULL,
  `exit_ip_address` varchar(100) NOT NULL,
    UNIQUE KEY " . $table_name_index . " (`user_id`,`adate`) 
	 
		);";
 
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
 
}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__,'aistore2030_options_install');
include_once dirname(__FILE__) . '/Attendance_system.class.php';


add_shortcode('aistore2030_punch_in_punch_out', array(
    'Attendance_system',
    'aistore2030_punch_in_punch_out'
));





 add_action( 'admin_menu', 'aistore2030_register_my_custom_menu_page' );
function aistore2030_register_my_custom_menu_page() {

 
    
    add_menu_page('Attendance', 'Attendance', 'manage_options', 'aistore2030_full_attendance','aistore2030_full_attendance','',71);
    
    
add_submenu_page( 'aistore2030_full_attendance', 'Attendance Page', 'Attendance Page',
    'manage_options', 'aistore2030_daily_attendance', 'aistore2030_daily_attendance');
    
    
    
    
add_submenu_page( 'aistore2030_full_attendance', 'Punch in/out', 'Punch in/out',
    'manage_options', 'aistore2030_punch_in_punch_out', 'aistore2030_punch_in_punch_out');
    
     
      
}
 
 

  function aistore2030_daily_attendance()
  {   
$user = wp_get_current_user();
$id=$user->ID ;
global $wpdb;
$month=date('m');


if (isset($_REQUEST['month'])) {
    $month=$_REQUEST['month'];
} 

$table_name = $wpdb->prefix . 'attendance';

$result = $wpdb->get_results("SELECT user_id,display_name,count(user_id ) as working_days FROM  $table_name WHERE MONTH(adate)=".$month." GROUP by user_id,display_name");

echo "<h2>Employee working days report monthly for salary preparation </h2>";
 
$url=admin_url("admin.php?page=aistore2030_daily_attendance&month=".date('m', strtotime('-4 month')));
echo "<a href='$url'>".date('F', strtotime('-4 month')) . "</a>  ";
 
$url=admin_url("admin.php?page=aistore2030_daily_attendance&month=".date('m', strtotime('-3 month')));
echo "<a href='$url'>".date('F', strtotime('-3 month')) . "</a>  ";
 
$url=admin_url("admin.php?page=aistore2030_daily_attendance&month=".date('m', strtotime('-2 month')));
echo "<a href='$url'>".date('F', strtotime('-2 month')) . "</a>  ";

$url=admin_url("admin.php?page=aistore2030_daily_attendance&month=".date('m', strtotime('-1 month')));
echo "<a href='$url'>".date('F', strtotime('-1 month')) . "</a>  ";

$url=admin_url("admin.php?page=aistore2030_daily_attendance&month=".date('m'));
echo "<a href='$url'>".date('F') . "</a>  ";

 
?>
 <table class="widefat"> 

 <thead>
    <tr>
         <th> <?php  _e( 'User Id', 'aistore' ) ?></th>  
        <th> <?php  _e( 'Full Name', 'aistore' ) ?></th>
              
        <th> <?php  _e( 'Working Days', 'aistore' ) ?></th>
    </tr>
</thead>
<?php 
foreach($result as $wp_formmaker_submits){

 
  echo "<tr>";
    echo "<td>".esc_attr($wp_formmaker_submits->user_id)."</td>";
    echo "<td>".esc_attr($wp_formmaker_submits->display_name)."</td>";
    echo "<td>".esc_attr($wp_formmaker_submits->working_days)."</td>";
      
	 
   echo "</tr>";
}
  

    echo "</table>";
}








 
  function aistore2030_full_attendance()
  {   
      echo  "<div class='wrap'>" ;
      $user = wp_get_current_user();
      
      $id=$user->ID ;
      
      
    global $wpdb;

 


$user_id=$id;








 if (isset($_REQUEST['user_id'])) {
    
	$user_id = sanitize_text_field($_REQUEST['user_id']);
}



echo  "<h2>Full Attendance  sheet of the company (Recent 60 Records )</h2>" ;

$table_name = $wpdb->prefix . 'attendance';

$result = $wpdb->get_results("SELECT distinct display_name,user_id FROM $table_name order by id desc");

$i=0;

echo  '<table class="widefat"> <tr>' ;

foreach($result as $display_name){

 $i=$i+1;

  $url=admin_url( 'admin.php?page=aistore2030_full_attendance&user_id='.$display_name->user_id   );
  
  
  
    echo  "<td><a href='".$url."' >".esc_attr($display_name->display_name)."</a></td>" ;
    
    if($i==3)
    {
    echo  "</tr><tr>" ;
    $i=0;
    }
    
    
    
}
 echo  "</tr></table>" ;


$table_name = $wpdb->prefix . 'attendance';

$result = $wpdb->get_results( $wpdb->prepare( "SELECT * , TIMESTAMPDIFF(HOUR, entrytime, entrytime) AS hours_different FROM $table_name  where user_id=%d   limit 60",$user_id));
?>

 <table class="widefat"> 
 

 <thead>
    <tr>
        <th><?php  _e( 'Id', 'aistore' ) ?></th>
        <th><?php  _e( 'Date', 'aistore' ) ?></th>       
        <th><?php  _e( 'Name', 'aistore' ) ?> </th>
        <th><?php  _e( 'Entry Time', 'aistore' ) ?> </th>
        <th><?php  _e( 'Entry IP Address', 'aistore' ) ?> </th>
        <th><?php  _e( 'Exit Time', 'aistore' ) ?> </th>
        <th><?php  _e( 'Exit IP Address', 'aistore' ) ?> </th> 
        
         <th><?php  _e( 'Hours Different', 'aistore' ) ?></th>       
         
    </tr>
</thead> 

<?php 

foreach($result as $wp_formmaker_submits){

 
  echo "<tr>";
    echo "<td>".esc_attr($wp_formmaker_submits->id)."</td>";
    echo "<td>".esc_attr($wp_formmaker_submits->adate)."</td>";
    echo "<td>".esc_attr($wp_formmaker_submits->display_name)."</td>";
     
     echo "<td>".esc_attr($wp_formmaker_submits->entrytime)."</td>";
	 
     echo "<td>".esc_attr($wp_formmaker_submits->entry_ip_address)."</td>";
     echo "<td>".esc_attr($wp_formmaker_submits->exittime)."</td>";
	 
     echo "<td>".esc_attr($wp_formmaker_submits->exit_ip_address)."</td>";
	  
	 
	 
     echo "<td>".esc_attr($wp_formmaker_submits->hours_different)."</td>";
	 
   echo "</tr>";
}
  

    echo "</table>";
    
     
     

}



 

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
	
	
 $wpdb->query( $wpdb->prepare( "INSERT INTO $table_name (user_id,adate,
display_name,entrytime,entry_ip_address ) VALUES (%d,date(now()),%s,now() ,%s)",array($id, $display_name,$ip_address )));
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


 
 <h2><?php  _e( 'Attendance  sheet', 'aistore' ) ?>   </h2> 

 
 
 
   <table width="50%" border=1>
        
        <tr><th><?php  _e( 'Punch IN', 'aistore' ) ?></th><th><?php  _e( 'Punch Out', 'aistore' ) ?></th></tr>
        
        <tr>
        
        <td>
            
            <form method="post" action="">
   <!-- some inputs here ... -->
   <input type="hidden" name="type" value="in" />
   
   <?php wp_nonce_field( 'punch_nonce', 'punch_nonce' ); ?>
   
   <input type="submit" value="<?php  _e( 'Punch In', 'aistore' ) ?>" />
</form>
        </td>
        
        <td>
             <form method="post" action="">
   <!-- some inputs here ... -->
   <input type="hidden" name="type" value="out" /> 
   
   <?php wp_nonce_field( 'punch_nonce', 'punch_nonce' ); ?>
 
   <input type="submit"   value="<?php  _e( 'Punch Out', 'aistore' ) ?>"/>
   
  
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
   
      
  
  

 $result = $wpdb->get_results("SELECT   *  FROM {$wpdb->prefix}attendance WHERE DATE(entrytime) = CURDATE()  order by id desc");

 
 

foreach($result as $wp_formmaker_submits){

 
 echo "<tr>";
 
  
    echo "<td>".esc_attr($wp_formmaker_submits->display_name)."</td>";
     
     echo "<td>".esc_attr($wp_formmaker_submits->entrytime)." (";
	 
     echo "".esc_attr($wp_formmaker_submits->entry_ip_address)." )</td>";
     echo "<td>".esc_attr($wp_formmaker_submits->exittime)." ( ";
	 
     echo "".esc_attr($wp_formmaker_submits->exit_ip_address)." )</td>";
	  
	  
    echo "</tr>";
 
}
   

    echo "</table>";
}



function aistore_getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}


?>
