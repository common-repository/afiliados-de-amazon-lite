<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function ADAL_substrwords_lite($text, $maxchar, $end='...') {
    if (strlen($text) > $maxchar || $text == '') {
        $words = preg_split('/\s/', $text);      
        $output = '';
        $i      = 0;
        while (1) {
            $length = strlen($output)+strlen($words[$i]);
            if ($length > $maxchar) {
                break;
            } 
            else {
                $output .= " " . $words[$i];
                ++$i;
            }
        }
        $output .= $end;
    } 
    else {
        $output = $text;
    }
	$output=mb_convert_case($output, MB_CASE_TITLE, 'UTF-8');
    return $output;
}



function ADAL_schedules_6horas($schedules){
    if(!isset($schedules["6hor"])){
        $schedules["6hor"] = array(
            'interval' => 60*60*6,
            'display' => __('Once every 3 Hours'));
    }
    return $schedules;
}
add_filter('cron_schedules','ADAL_schedules_6horas');

// create a scheduled event (if it does not exist already)
function ADA_cron_starter_activation_lite() {
	if( !wp_next_scheduled( 'update_data' ) ) {  
	   wp_schedule_event( time(), '6hor', 'update_data' );  
	}
}
// and make sure it's called whenever WordPress loads
add_action('wp', 'ADA_cron_starter_activation_lite');

// unschedule event upon plugin deactivation
function ADA_cron_starter_deactivate_lite() {	
	// find out when the last event was scheduled
	$timestamp = wp_next_scheduled ('update_data');
	// unschedule previous event if any
	wp_unschedule_event ($timestamp, 'update_data');
} 
register_deactivation_hook (__FILE__, 'ADA_cron_starter_deactivate_lite');

// here's the function we'd like to call with our cron job
function ADA_cron_function_lite() {
	
	include( ADAL_PLUGIN_URL. 'includes/ADAL_cron.php');
}

// hook that function onto our scheduled event:
add_action ('update_data', 'ADA_cron_function_lite'); 


function ADAL_url_lo_lie($pais_base,$pais_usuario)
{	
	
		
	$abr = array("au","us", "uk", "es", "mx", "br", "ca", "cn", "de", "fr", "in", "it", "jp");
	//$abr = array_diff($abr, array($pais_base));
	if (in_array($pais_usuario, $abr)) {	
		switch ($pais_usuario) 
					{
					case "au":						
						$url_loc_gen="www.amazon.com.".$pais_usuario;
						break;
					case "br":						
						$url_loc_gen="www.amazon.com.".$pais_usuario;
						break;
					case "mx":
						$url_loc_gen="www.amazon.com.".$pais_usuario;
						break;
					case "ca":						
						$url_loc_gen="www.amazon.".$pais_usuario;
						break;
					case "cn":						
						$url_loc_gen="www.amazon.".$pais_usuario;
						break;
					case "de":						
						$url_loc_gen="www.amazon.".$pais_usuario;
						break;
					case "es":							
						$url_loc_gen="www.amazon.".$pais_usuario;
						break;
					case "fr":						
						$url_loc_gen="www.amazon.".$pais_usuario;
						break;
					case "in":						
						$url_loc_gen="www.amazon.".$pais_usuario;
						break;
					case "it":							
						$url_loc_gen="www.amazon.".$pais_usuario;
						break;
					case "jp":						
						$url_loc_gen="www.amazon.co.".$pais_usuario;
						break;
					case "uk":						
						$url_loc_gen="www.amazon.co.".$pais_usuario;
						break;
					case "us":						
						$url_loc_gen="www.amazon.com";
						break;		
					}
	}
	else
	{
		switch ($pais_base) 
					{
					case "au":						
						$url_loc_gen="www.amazon.com.".$pais_base;
						break;
					case "br":						
						$url_loc_gen="www.amazon.com.".$pais_base;
						break;
					case "mx":
						$url_loc_gen="www.amazon.com.".$pais_base;
						break;
					case "ca":						
						$url_loc_gen="www.amazon.".$pais_base;
						break;
					case "cn":						
						$url_loc_gen="www.amazon.".$pais_base;
						break;
					case "de":						
						$url_loc_gen="www.amazon.".$pais_base;
						break;
					case "es":							
						$url_loc_gen="www.amazon.".$pais_base;
						break;
					case "fr":						
						$url_loc_gen="www.amazon.".$pais_base;
						break;
					case "in":						
						$url_loc_gen="www.amazon.".$pais_base;
						break;
					case "it":							
						$url_loc_gen="www.amazon.".$pais_base;
						break;
					case "jp":						
						$url_loc_gen="www.amazon.co.".$pais_base;
						break;
					case "uk":						
						$url_loc_gen="www.amazon.co.".$pais_base;
						break;
					case "us":						
						$url_loc_gen="www.amazon.com";
						break;		
					}
	}
	return $url_loc_gen;
				
}
function ADAL_UR_exists_lite($url){
   $headers=get_headers($url);
   return stripos($headers[0],"200 OK")?true:false;
}
function ADAL_getFile_lite($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $tmp = curl_exec($ch);
    curl_close($ch);
    if ($tmp != false){
        return $tmp;
    }
}

function ADAL_acentos_lite ($cadena){
    $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ
ßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuy
bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $cadena = utf8_decode($cadena);
    $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
    $cadena = strtolower($cadena);
    return utf8_encode($cadena);
}
?>