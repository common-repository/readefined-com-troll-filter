<?php
	/*
	Plugin Name: Readefined.com Troll Filter
	Plugin URI:  https://readefined.com/t/trollfilter
	Description: Install this plugin to hide your comments section and reveal it only when readers have engaged enough with your page. Fight the trolls! The plugin is free to use.
				 Due to GDRP, you must first create an account at readefined.com/trollfilter
				 We DO NOT share your e-mail with any third parties or use it for ANY advertising purposes. The ONLY communication you will receive from us will be reminding you that you can login to
				 Readefined.com to access additional features. 
	Version:     1.02
	Author:      Readefined.com
	Author URI:  https://readefined.com
	License:     GPL2
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
	*/
		
	function rewordly_readefined_code() {
		
		$args = array(
		    'body' => array('host' => $_SERVER['HTTP_HOST']),
		    'timeout' => '5',
		    'redirection' => '5',
		    'httpversion' => '1.0',
		    'blocking' => true,
		    'headers' => array(),
		    'cookies' => array()
		);
		 
		$response = wp_remote_post('https://content.readefined.com/t/trollcode',$args);
		$handle = wp_remote_retrieve_body($response);
		
		// Handle is not empty, and handle is only letters, numbers, dashes
		if(!empty($handle) && !preg_match('/[^A-Za-z0-9-]/', $handle)) {
			
			// If account not found, fire admin e-mail so we can create an account automatically that user can later retrieve
			if($handle == "404") {
				
				// Modify the POST args body
				$args['body'] = array('host' => $_SERVER['HTTP_HOST'],'trollcode' => 1,'owner' => get_bloginfo('admin_email'));

				$response = wp_remote_post('https://content.readefined.com/t/trollcode',$args);
				$handle = wp_remote_retrieve_body($response);
				
			}
			
			// Invalid return
			if(empty($handle) || preg_match('/[^A-Za-z0-9-]/', $handle)) {
				?>
					<!-- Invalid response. Please contact Readefined.com for help. -->
				<?php
			}
			// If not found, still
			elseif($handle == "404") {
				?>
					<!-- Account not found. Please contact Readefined.com for help. -->
				<?php
			} 
			// Otherwise echo code
			else {
				
				wp_register_style('rw_css',"https://hooks.readefined.com/css/rwhooks.css");
				wp_enqueue_style('rw_css')
			    
			    ?>
			        <script type="text/javascript" id="rwrd-reading-<?php echo $handle; ?>">(function(){var a=window;function b(){var e=document.createElement("script"),c="https://content.readefined.com/t/<?php echo $handle; ?>",d=document.getElementById("rwrd-reading-<?php echo $handle; ?>");e.type="text/javascript";e.async=true;e.src=c+(c.indexOf("?")>=0?"&":"?")+"rwref="+encodeURIComponent(a.location.href)+"&v=1";d.parentNode.insertBefore(e,d)}if(a.attachEvent){a.attachEvent("onload",b)}else{a.addEventListener("load",b,false)}})();
					</script>
			    <?php
			}
		}
	}
	add_action('wp_head', 'rewordly_readefined_code');
	
?>