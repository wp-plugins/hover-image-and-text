<?php
/*
Plugin Name: Hover Image Plugin
Plugin URI: http://www.linkedin.com/in/tapharan
Description: Hover Image makes you able to add hover images/text and read more button to your WordPress Posts and Pages.
Author URI: http://www.linkedin.com/in/tapharan
Version: 2
License: GPLv2
*/



/*  
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/



// Make sure that no info is exposed if file is called directly -- Idea taken from Akismet plugin

if ( !function_exists( 'add_action' ) ) {
	echo "This page cannot be called directly.";
	exit;
}

// Define some useful constants that can be used by functions
if ( ! defined( 'WP_CONTENT_URL' ) ) {	
	if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
	define( 'WP_CONTENT_URL', WP_SITEURL . '/wp-content' );
}

if ( ! defined( 'WP_SITEURL' ) ) define( 'WP_SITEURL', get_option("siteurl") );
if ( ! defined( 'WP_CONTENT_DIR' ) ) define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) ) define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) ) define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );
if ( basename(dirname(__FILE__)) == 'plugins' )
	define("HOVER_DIR",'');
else define("HOVER_DIR" , basename(dirname(__FILE__)) . '/');
define("HOVER_PATH", WP_PLUGIN_URL . "/" . HOVER_DIR);


//---------------------------------------------------------------------------------------------

//create table block

global $table_name;
$table_name = $wpdb->prefix . "hover_image";


global $jal_db_version;
$jal_db_version = "1.0";

function jal_install() {
   global $wpdb;
   global $jal_db_version;

   $table_name = $wpdb->prefix . "hover_image";
      
   $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  heading text NOT NULL,
  file tinytext NOT NULL,
  text text NOT NULL,
  url VARCHAR(55) DEFAULT '' NOT NULL,
  UNIQUE KEY id (id)
    );";

   require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
   dbDelta($sql);
 
   add_option("jal_db_version", $jal_db_version);
}

function jal_install_data() {
   global $wpdb;
   $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql') ) );
}


register_activation_hook(__FILE__,'jal_install');
register_activation_hook(__FILE__,'jal_install_data');


//create table block
//-------------------------------------------------------------------------------------------------


/* Add new menu */
add_action('admin_menu', 'hover_add_pages');
// http://codex.wordpress.org/Function_Reference/add_action
/*
******** BEGIN PLUGIN FUNCTIONS ********
*/

// function for: 
function hover_add_pages() {
  // anyone can see the menu for the Hover Plugin
  add_menu_page('Hover Overview','Hover Plugin', 'read', 'hover_overview', 'hover_overview', HOVER_PATH.'images/b_status.png');
  // http://codex.wordpress.org/Function_Reference/add_menu_page
  // this is just a brief introduction
  add_submenu_page('hover_overview', 'Overview for the Hover Plugin', 'Overview', 'read', 'hover_overview', 'hover_intro');
  // http://codex.wordpress.org/Function_Reference/add_submenu_page
}


//--------------------------------------------------------------------------------------------------
// front end section start
function hover_func( $atts ){
 
$path=WP_PLUGIN_URL."/hover";
 
 
wp_register_style( 'demo', plugins_url('css/demo.css', __FILE__) );
wp_enqueue_style( 'demo' );

wp_register_style( 'style_common', plugins_url('css/style_common.css', __FILE__) );
wp_enqueue_style( 'style_common' );

wp_register_style( 'style1', plugins_url('css/style1.css', __FILE__) );
wp_enqueue_style( 'style1' );
 
 ?>
  
        <div class="container">
            <div class="main_hover">
            
            <?php 
			global $wpdb;
   			$table_name = $wpdb->prefix . "hover_image";
			
			$query = "SELECT * FROM $table_name";  
			$fivesdrafts=$wpdb->get_results($query);  
			
			$year1 = date("Y"); 
			$month1 = date("m");
			$file_path=site_url()."/wp-content/uploads/".$year1.'/'.$month1.'/';
			
			
					foreach($fivesdrafts as $fivesdraft) 
					{
			
			?>
            
                <div class="view view-first">
                    <img src="<?php echo $file_path.$fivesdraft->file; ?>" />
                    <div class="mask">
                        <h2><?php echo $fivesdraft->heading; ?></h2>
                        <p><?php echo $fivesdraft->text; ?></p>
                        <a href="http://<?php echo $fivesdraft->url; ?>" class="info">Read More</a>
                    </div>
                </div>  
                
                <?php } ?>              
            </div>
        </div>
<?php
 }
 // front end close--------------------------------------------------------------------------------------
 
//-------------------------------------------------------------------------------------------------------
// sort code
 
add_shortcode( 'hover', 'hover_func' );

//-------------------------------------------------------------------------------------------------------

function hover_overview() {
	
//insert into database start-----------------------------------------------------------------------------	
	
require_once(ABSPATH . "wp-admin" . '/includes/image.php');
require_once(ABSPATH . "wp-admin" . '/includes/file.php');
require_once(ABSPATH . "wp-admin" . '/includes/media.php');

$attachment_id = media_handle_upload('file-upload', $post->ID);

$uploadid=$_GET['upload'];

if($uploadid==1){
// if using a custom function, you need this
global $wpdb;

$year = date("Y"); 
$month = date("m");

$filename = $_FILES["file-upload"]["name"];
$file_url=site_url()."/wp-content/uploads/".$year/$month/$filename;

$text = $_POST['text'];
$url=$_POST['url'];
$heading=$_POST['heading'];

$table_name = $wpdb->prefix . "hover_image";
$wpdb->insert( $table_name, array( 'file' => $filename, 'text' => $text, 'url' => $url, 'heading' =>$heading), array( '%s', '%s', '%s', '%s') );
?>
<script type="text/javascript">
<!--
window.location = "admin.php?page=hover_overview"
//-->
</script>

<?php
	}else{	}
	
	//insert into database end----------------------------------------------------------------------------
?>
    

<div class="wrap"><h2>Plugin Overview</h2>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>&upload=1" method="post" enctype="multipart/form-data">
<table width="600" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>Heading</td>
    <td><input type="text" name="heading" value="" /></td>
  </tr>
  <tr>
    <td width="110">Text</td>
    <td width="490"><textarea name="text" cols="40" rows="5"></textarea></td>
  </tr>
  <tr>
    <td>Url</td>
    <td><input type="text" name="url" value="" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td><input type="file" name="file-upload" id="file-upload" /></td>
  </tr>
</table>

<input type="submit" name="submit" value="Submit">
</form>


</div>
<br /><br /><br />
<div>
<?php 
// delete data start--------------------------------------------------------------------------------------

if(isset($_GET['del'])){
global $wpdb;	
$delid=$_GET['del'];	

global $wpdb;
$table_name = $wpdb->prefix . "hover_image";
			
$sql ="DELETE FROM $table_name WHERE id ='$delid'";
$wpdb->query($sql);
?>

<script type="text/javascript">
<!--
window.location = "admin.php?page=hover_overview"
//-->
</script>

<?php	
	}else{}

// delete data end-------------------------------------------------------------------------------------


global $wpdb;
$table_name = $wpdb->prefix . "hover_image";

$query = "SELECT * FROM $table_name";  
$fivesdrafts=$wpdb->get_results($query);  

$year1 = date("Y"); 
$month1 = date("m");
$file_path=site_url()."/wp-content/uploads/".$year1.'/'.$month1.'/';


if(isset($_POST['formid'])){
	$table_name = $wpdb->prefix . "hover_image";
	
	$formid=$_POST['formid'];
	$text=$_POST['text'];
	$url=$_POST['url'];
	$heading=$_POST['heading'];
	
	$wpdb->query(
	"
	UPDATE ".$table_name."
	SET text = '$text',
	url='$url',
	heading='$heading'
	WHERE id= '$formid'"
	);
	?>
	<script type="text/javascript">
	<!--
	window.location = "admin.php?page=hover_overview"
	//-->
	</script>
    
	<?php 
	}else{}

		foreach($fivesdrafts as $fivesdraft) 
		{			
			?>
           <form action="admin.php?page=hover_overview" method="post">         
               <table width="1000" border="0" cellspacing="0" cellpadding="0">
                 <tr>
                     <td align="left" width="173"><img src="<?php echo $file_path.$fivesdraft->file; ?>" height="100" /></td>
                        <td align="left" width="197">
                        <textarea name="heading" cols="30" rows="2"><?php echo $fivesdraft->heading; ?></textarea>
						</td>
                        <td align="left" width="214"><textarea name="text" cols="30" rows="5"><?php echo $fivesdraft->text; ?></textarea></td>
                        <td align="left" width="205"><textarea name="url" cols="30" rows="2"><?php echo $fivesdraft->url; ?></textarea></td>
                        <td align="left" width="102">
                        <input type="hidden" value="<?php echo $fivesdraft->id; ?>" name="formid" />
                        <input type="submit" name="save" value="save" /></td>
                        <td align="left" width="109"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&del=<?php echo $fivesdraft->id; ?>">Delete</a></td>
                   </tr>
                </table>
          </form>
		
		<?php } ?>
        
 <br />
 <hr />
 <br />
 <p>Installation</p>
 <p>Place [hover] in your posts or pages</p>
</div>
<?php
exit;
}
?>