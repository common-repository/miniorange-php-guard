<?php
/**
 * Plugin Name: miniOrange PHP Guard
 * Plugin URI: miniorange-php-guard
 * Version: 2.0.0
 * Description: A simple WordPress plugin for for protecting your PHP code
 * Author: miniOrange
 * Author URI: https://www.miniorange.com
 * License: MIT/Expat
 * License URI: https://docs.miniorange.com/mit-license
 *
 * @package MoPhpGuard
 */
define('MO_PHP_VERSION', '2.0.0');
define('MO_PHP_GUARD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('MOV_URL', plugin_dir_url(__FILE__));
define('MOV_LOGO_URL', MOV_URL . 'images/logo.png');
require( 'support.php' );
require( 'constants.php' );
require('curl.php');
/**
 * Main Class
 */
class Mo_Php_Guard {

	/**
	 * Constructor
	 *
	 * Initiates plugin and sets up hooks.
	 *
	 * @return void
	 **/
	public function __construct() {
	$this->setup_hooks();
	}

	/**
	* Setup WordPress Hooks
	*
	* Enqueues all the scripts (css/js) along with notices
	*
	* @return void
	**/
	private function setup_hooks() {
	add_action( 'admin_init', array( $this, 'mo_php_guard_save_settings' ) );
	add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_style' ) );
	add_action( 'admin_menu', array( $this, 'mo_php_guard_menu' ) );
	remove_action( 'admin_notices', array( $this, 'mo_php_guard_show_success_message' ) );
	remove_action( 'admin_notices', array( $this, 'mo_php_guard_show_error_message' ) );
	}

	/**
	* Enqueue CSS
	*
	* @return void
	**/
	public function plugin_settings_style() {

	wp_enqueue_style( 'mo_php_guard_phone_style', plugins_url( 'css/phone.css?version=5.1.14', __FILE__ ) );
	wp_enqueue_script( 'mo_php_guard_phone_script', plugins_url( 'js/phone.js', __FILE__ ) );
	wp_enqueue_style( 'mo_php_guard_settings_style', plugins_url( 'css/style_settings.css', __FILE__ ), null, $ver = false, 'all' );
	}

	/**
	* Add WordPress Menu Entry
	*
	* @return void
	**/
	public function mo_php_guard_menu() {

	// Add miniOrange plugin to the menu.
	$page = add_menu_page( 'MO PHP Guard Settings ' . __( 'Configure PHP Guard', 'mo_php_guard_settings' ), 'miniOrange PHP Guard', 'administrator', 'mo_php_guard_settings', array( $this, 'mo_php_guard_page' ), plugin_dir_url( __FILE__ ) . 'images/miniorange.png' );
	}

	/**
	* Setup function for admin_notices.
	*
	* @return void
	**/
	private function mo_php_guard_show_success_message() {
	remove_action( 'admin_notices', array( $this, 'mo_php_guard_success_message' ) );
	add_action( 'admin_notices', array( $this, 'mo_php_guard_error_message' ) );
	}

	/**
	* Setup function for admin_notices.
	*
	* @return void
	**/
	private function mo_php_guard_show_error_message() {
	remove_action( 'admin_notices', array( $this, 'mo_php_guard_error_message' ) );
	add_action( 'admin_notices', array( $this, 'mo_php_guard_success_message' ) );
	}

	/**
	* Setup function for admin_notices.
	*
	* @return void
	**/
	public function mo_php_guard_success_message() {
	$class   = 'error';
	$message = ( is_multisite() ) ? get_site_option( 'mo_php_guard_message' ) : get_option( 'mo_php_guard_message' );
	echo "<div class='" . esc_attr( $class ) . "'> <p>" . esc_html( $message ) . '</p></div>';
	}

	/**
	* Setup function for admin_notices.
	*
	* @return void
	**/
	public function mo_php_guard_error_message() {
	$class   = 'updated';
	$message = ( is_multisite() ) ? get_site_option( 'mo_php_guard_message' ) : get_option( 'mo_php_guard_message' );
	echo "<div class='" . esc_attr( $class ) . "'> <p>" . esc_html( $message ) . '</p></div>';
	}

	/**
	* Main Function for UI
	*
	* This function renders UI on plugin
	* configuration page.
	*
	* - Sets up Uploader.
	* - Sets up required Nonce Field.
	* - Passes control to php guard function.
	*
	* @return void
	**/
	public function mo_php_guard_page() {
	//$img_url =
		echo'	<div class="wrap" style="margin-bottom: 1%;">
		<div><img style="float:left;" src="'.MOV_LOGO_URL.'"></div>
		<div><b><h style="font-size: 23px;display: block;padding: 9px 0 10px;
		line-height: 29px;">
			PHP Guard	
			</h></b>	
		</div>				
		</div>';
		?>
		<div id="tab">
		<h2 class="nav-tab-wrapper" style="padding-top:1%;">
			<a class="nav-tab nav-tab-active" href="admin.php?page=mo_php_guard_settings">PHP Guard</a>
		</h2>
		</div>
		<div id="mo_container">
		<table style="width:100%;padding-top:2%;">
			<tr>
				<td style="vertical-align: top; width:65%;">
				<div class="mo_table_layout">
		<h3>Upload ZIP</h3>
		<p style="border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px;"> PHP Guard is designed to help PHP developers protect their code!
		<br>When you lend your code to someone it can be used and altered without your permission. </p>
		<div id="panel1">
		<form name="f1" enctype="multipart/form-data" method="post" action="" id="mo_php_guard_file_upload_form">
			<table class="mo_settings_table">
				<tr>
				<td><b>Select zip to Upload:</b></td>
				<td><input type="file" id="upload" name="upload" value="Browse"
					class="" />
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<?php
				$link = $this->mo_php_guard_validate_download_link();
				if ( false !== $link ) {
					wp_nonce_field( 'mo_php_guard_downloader', 'mo_php_guard_downloader_nonce' );
					?>
						<td><input type="submit" name="submit" value="Download Guarded File"
						class="button button-primary button-large" /></td>
						<td>&nbsp;<input type="hidden" value="downloader" name="option"/>
					<?php
				} else {
						wp_nonce_field( 'mo_php_guard_uploader', 'mo_php_guard_uploader_nonce' );
					?>
						<td><input type="submit" name="submit" value="Guard"
						class="button button-primary button-large" /></td>
						<td>&nbsp;<input type="hidden" value="uploader" name="option"/>
				<?php } ?>
					
						</tr>
					</table>
				</form>
			</div>
			</div>
			</td>
			</td>
			<td>			
			</td>
			<td style="vertical-align:top;padding-left:1%;padding-right:3%;">
					<div><?php 
						echo mo_support();
					 ?>
					</div>
			    </td>
			</tr>
			</table>
			</div>
	<?php
	}

	/**
	* Function to check if Download link is valid.
	*
	* This function checks the download link for:
	* - Existance
	* - 'is_valid' parameter of the link.
	*
	* @return bool
	**/
	public function mo_php_guard_validate_download_link() {
		$link = get_option( 'mo_php_guard_output_zip_link' );
		return ( ( ! $link ) || ( 1 !== $link['is_valid'] ) ) ? false : true;
	}

	/**
	* Function to check if Download link is valid.
	*
	* This function checks the download link for:
	* - Existance
	* - 'is_valid' parameter of the link.
	*
	* If the link is valid,
	* we invalidate the link and return the actual link.
	*
	* If the link is invalid, we return false.
	*
	* @return mixed
	**/
	public function mo_php_guard_invalidate_download_link() {
		$link = get_option( 'mo_php_guard_output_zip_link' );
		if ( ! $link || ( isset( $link['is_valid'] ) && 0 === $link['is_valid'] ) ) {
		return false;
		}
		$link['is_valid'] = 0;
		update_option( 'mo_php_guard_output_zip_link', $link );
		return $link['link'];
	}

	/**
	* Main Function to adapt library.
	*
	* This function prepares to invoke library.
	*
	* Creates the zip after library does its job.
	* Creates the link parameter and stores in wp_options
	*
	* @param string $source Source of the Zip to Guard.
	* @param string $destination Destination of the Guard Zip to place.
	* @param string $filename Filename of the Zip.
	*
	* @return mixed
	**/
	public function mo_php_guard_prepare( $source, $destination, $filename ) {
		define( 'MO_PHP_GUARD_SOURCE_PATH', $source );
		define( 'MO_PHP_GUARD_DESTINATION_PATH', $destination );
		require 'yakpro-po/yakpro-po.php';
		$zip_source = $destination . 'miniorange' . DIRECTORY_SEPARATOR . 'obfuscated' . DIRECTORY_SEPARATOR;
		$zip_dest   = $destination . $filename . '.zip';
		$zip        = new ZipArchive();
		$zip->open( $zip_dest, ZipArchive::CREATE | ZipArchive::OVERWRITE );

		$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $zip_source ),
		RecursiveIteratorIterator::LEAVES_ONLY
		);
		foreach ( $files as $name => $file ) {
		if ( ! $file->isDir() ) {
			$file_path     = $file->getRealPath();
			$relative_path = substr( $file_path, strlen( $zip_source ) );
			$zip->addFile( $file_path, $relative_path );
		}
		}
		$zip->close();
		$link = array(
		'link'     => wp_upload_dir()['baseurl'] . str_replace( '\\', '/', explode( wp_upload_dir()['basedir'], $zip_dest )[1] ),
		'is_valid' => 1,
		);
		update_option( 'mo_php_guard_output_zip_link', $link );
	}

	function _upload_zip($post)
	{
		if ( ! isset( $_POST['mo_php_guard_uploader_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_php_guard_uploader_nonce'] ) ), 'mo_php_guard_uploader' ) ) {
				wp_die( 'Sorry, Your Nonce didnt verify' );
			}				
			if ( isset( $_FILES['upload'] ) ) {
			$zip          = $_FILES['upload'];	// phpcs:ignore
			$uploaded_zip = media_handle_upload( 'upload', 0 );
			if ( is_wp_error( $uploaded_zip ) ) {
				update_option( 'mo_php_guard_message', 'Error uploading file: ' . $uploaded_zip->get_error_message() );
				$this->mo_php_guard_show_error_message();
			} else {
				$this->handle_zip_file( $uploaded_zip );
			}
			}

	}

	function _downloader_zip($post)
	{
		if ( ! isset( $_POST['mo_php_guard_downloader_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mo_php_guard_downloader_nonce'] ) ), 'mo_php_guard_downloader' ) ) {
			wp_die( 'Sorry, Your Nonce didnt verify' );
		}
		$link = $this->mo_php_guard_invalidate_download_link();
		if ( false !== $link ) {
			wp_redirect( $link ); //phpcs:ignore
			exit();
		}
	}

	public static function check_empty_or_null( $value ) {
		if ( ! isset( $value ) || $value == '' ) {
		return true;
		}
		return false;
	}
	function _mo_validation_support_query($email,$query,$phone)
	{
		$nonce = $_POST['mo_send_query_nonce'];
		update_option( 'mo_host_name', 'https://login.xecurify.com' );
		if ( ! wp_verify_nonce( $nonce, 'mo-send-query-nonce' ) ) {

		$error = new WP_Error();
		$error->add( 'empty_username', '<strong>' . mo_lt( 'ERROR' ) . '</strong>: ' . mo_lt( 'Invalid Request.' ) );

		return $error;
		} 
		else{
		$query = '';
		if ($this->check_empty_or_null( $_POST['query_email'] ) || $this->check_empty_or_null( $_POST['query'] ) ) 
		{
		update_option( 'mo_php_guard_message', MoConstants::langTranslate( "EMAIL_MANDATORY" ) );
			$this->mo_php_guard_show_error_message();				
		return;
		} 
		else {				
		$query      = sanitize_text_field( $_POST['query'] );
		$email      = sanitize_text_field( $_POST['query_email'] );
		$phone      = sanitize_text_field( $_POST['query_phone'] );

		$submited   = json_decode(MoCURLPHPG::submit_contact_us( $email, $phone, $query ), true );
		if ( json_last_error() == JSON_ERROR_NONE ) {

			
			if ( is_array( $submited ) && array_key_exists( 'status', $submited ) && $submited['status'] == 'ERROR' ) {
				
			update_option( 'mo_php_guard_message', MoConstants::langTranslate( $submited['message'] ) );
			   $this->mo_php_guard_show_error_message();

			} else 
			{
			if ( $submited == false ) {
				update_option( 'mo_php_guard_message', MoConstants::langTranslate('ERROR_WHILE_SUBMITTING_QUERY') );
			   $this->mo_php_guard_show_error_message();

			} else {

			update_option( 'mo_php_guard_message', MoConstants::langTranslate( "QUERY_SUBMITTED_SUCCESSFULLY" ) );
			$this->mo_php_guard_show_success_message();
			
				}
			}
		}
		}
		}
	}
	/**
	* Main Sort of Handler Function for Submit on UI.
	*
	* This function saves the settings or handles the POST forms on UI.
	*
	* @return mixed
	**/
	public function mo_php_guard_save_settings() {
		if ( !current_user_can( 'manage_options' )) return;
		if(!isset($_POST['option'])) return;

		$option = trim($_POST['option']);

		switch($option)
		{
		case "uploader":
			$this->_upload_zip($_POST);											   		
			        break;
		case "downloader":
			$this->_downloader_zip($_POST);													   	break;
		case "mo_contact_us_query":
			$this->_mo_validation_support_query($_POST['query_email'],$_POST['query'],$_POST['query_phone']);
					break;
		}

	}

	/**
	* Function for Un-zipping files.
	*
	* This function takes the Post ID (File ID) and handles the Zip file.
	* Outputs unzipped file onto the destination folder.
	*
	* @param int $id Post ID of uploaded zip file.
	* @return void
	**/
	public function handle_zip_file( $id ) {
		$zip          = get_post( $id );
		$zip_contents = wp_remote_get( wp_get_attachment_url( $id ) )['body'];
		$uploaddir    = wp_upload_dir()['path'] . DIRECTORY_SEPARATOR . $zip->post_title . '.zip';
		$source       = get_zip_path( false, 'upload' ) . $zip->post_title . DIRECTORY_SEPARATOR;
		$destination  = get_zip_path( false, 'output' ) . $zip->post_title . DIRECTORY_SEPARATOR;
		if ( ! file_exists( $source ) && ! file_exists( $destination ) ) {
			WP_Filesystem();
			mkdir( $source, 0777, true );
			mkdir( $destination, 0777, true );
			$unzipfile = unzip_file( $uploaddir, $source );
			if ( $unzipfile ) {
				$this->mo_php_guard_prepare( $source, $destination, $zip->post_title );
			} else {
				update_option( 'mo_php_guard_message', 'There was an error unzipping the file.' );
				$this->mo_php_guard_show_error_message();
			}
		} 
		else {
			update_option( 'mo_php_guard_message', 'File already Exists!' );
			$this->mo_php_guard_show_error_message();
		}
		}
	}

	/**
	* Function for Getting dir links.
	*
	* This function takes returns the link if $is_link is true
	* $which is which folder link is needed.
	*
	* @param bool $is_link Type of path.
	* @param bool $which Type of Dir.
	* @return string
	**/
	function get_zip_path( $is_link = false, $which = 'upload' ) {
		if ( ! $is_link ){
		return preg_replace( '[\\/]', DIRECTORY_SEPARATOR, wp_upload_dir()['basedir'] ) . DIRECTORY_SEPARATOR . 'phpguard' . DIRECTORY_SEPARATOR . $which . DIRECTORY_SEPARATOR;
		}
		return preg_replace( '[\\/]', DIRECTORY_SEPARATOR, wp_upload_dir()['url'] ) . '/phpguard/' . $which;
}

$obs = new Mo_Php_Guard();