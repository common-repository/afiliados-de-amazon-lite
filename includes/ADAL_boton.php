<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function tiny_ADAL_add_buttons_lite( $plugins ) {
  $plugins['ADAL_mytinymceplugin_lite'] = ADAL_PLUGIN_URL."js/ADAL_javaload_lite.js";
  return $plugins;
}

function tiny_ADAL_register_buttons_lite( $buttons ) {
  $newBtns = array(
	'ADAL_myblockquotebtn_lite'
  );
  $buttons = array_merge( $buttons, $newBtns );
  return $buttons;
}
add_action( 'init', 'tiny_ADAL_new_buttons_lite' );

function tiny_ADAL_new_buttons_lite() {
  add_filter( 'mce_external_plugins', 'tiny_ADAL_add_buttons_lite' );
  add_filter( 'mce_buttons', 'tiny_ADAL_register_buttons_lite' );
}
?>