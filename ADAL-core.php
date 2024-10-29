<?php
/*
Plugin Name: Plugin Afiliados de Amazon lite.
Plugin URI: https://adapluginwp.com/
Description: (Show and update Amazon items) Mostrar articulos de afiliados de Amazon.
Author: Linkkos México linkkosmexico@gmail.com
Version: 1.0.0
*/
if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'ADAL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

define( 'ADAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

add_action('admin_menu', 'ADAL_plugin_create_menu');


function ADAL_plugin_create_menu() {	
	add_menu_page('Administración ADA Plugin Lite', 'Afiliados de Amazon LIte', 'administrator', __FILE__, 'ADAL_settings_page' , plugins_url ('/img/adaico.png', __FILE__) );
	add_action( 'admin_init', 'register_ADAL_plugin_settings' );
}

function register_ADAL_plugin_settings() {
	register_setting( 'valores_ADAL', 'ADAL_AssociateTag' );
	register_setting( 'valores_ADAL', 'ADAL_AWSAccessKeyId' );
	register_setting( 'valores_ADAL', 'ADAL_AWSASecretKey' );
	register_setting( 'valores_ADAL', 'ADAL_moneda' );
}

//load JQuery
function load_jquery_ADAL() {
    if ( ! wp_script_is( 'jquery', 'enqueued' )) {        
        wp_enqueue_script( 'jquery' );
    }
}
add_action( 'wp_enqueue_scripts', 'load_jquery_ADAL' );

function ADAL_scripts_load_cdn()
{
    wp_register_style( 'ADAL_style', plugins_url( '/css/ADAL-css-lite.css', __FILE__ ) );
	wp_enqueue_style( 'ADAL_style' );
}
add_action( 'wp_enqueue_scripts', 'ADAL_scripts_load_cdn' );

function ADAL_css_admin() {
  	wp_register_style( 'adal_wp_admin_css', plugins_url( '/css/ADAL-css-lite-menu.css', __FILE__ ) );
	wp_enqueue_style( 'adal_wp_admin_css' );
}
add_action('admin_enqueue_scripts', 'ADAL_css_admin');

function ADAL_create_db() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'Amazon_articulos_lite';
	$sql = "CREATE TABLE $table_name (
	idart mediumint(9) NOT NULL AUTO_INCREMENT,
	sku text NOT NULL,
	titulo text NOT NULL,
	caracteristicas text NOT NULL,
	precio_nor text NOT NULL,
	precio_desc text NOT NULL,
	descuento text NOT NULL,
	porcentaje text NOT NULL,
	url text NOT NULL,
	imagen text NOT NULL,
	prime text NOT NULL,
	UNIQUE KEY id (idart)
) $charset_collate;";
$wpdb->query($sql);		
	
}
register_activation_hook( __FILE__, 'ADAL_create_db' );

//borra base de datos
function ADAL_erase_db(){
	global $wpdb; 
	$table_name1 = $wpdb->prefix . 'Amazon_articulos_lite';
	$sqld = "DROP TABLE IF EXISTS $table_name1";
	$wpdb->query($sqld);
	

}
register_deactivation_hook( __FILE__, 'ADAL_erase_db' );

include(plugin_dir_path( __FILE__ ) . 'includes/general.php');
include(plugin_dir_path( __FILE__ ) . 'includes/func_art.php');
include(plugin_dir_path( __FILE__ ) . 'includes/ADAL_boton.php');


function ADAL_settings_page() {
	settings_fields( 'valores_ADAL' ); 
   	do_settings_sections( 'valores_ADAL' );	
	if (isset( $_POST['ADAL_core_nounce'] ) || wp_verify_nonce( $_POST['ADAL_core_nounce'], 'actualizar_ADAL_core' )) {
		
		if (isset($_POST['ADAL_AssociateTag'])) {
		update_option('ADAL_AssociateTag', $_POST['ADAL_AssociateTag']); 
	}
	if (isset($_POST['ADAL_AWSAccessKeyId'])) {
		update_option('ADAL_AWSAccessKeyId', $_POST['ADAL_AWSAccessKeyId']); 
	}
	if (isset($_POST['ADAL_AWSASecretKey'])) {
		update_option('ADAL_AWSASecretKey', $_POST['ADAL_AWSASecretKey']); 
	}
	if (isset($_POST['ADAL_monedaTag'])) {
		update_option('ADAL_monedaTag', $_POST['ADAL_monedaTag']); 
	}
	
	}
	$abr = array("au","us", "uk", "es", "mx", "br", "ca", "cn", "de", "fr", "in", "it", "jp");
	$sitios = array("com.au","com", "co.uk", "es", "com.mx", "com.br", "ca", "cn", "de", "fr", "in", "it", "co.jp");
	$pais_sitio = array("Australia","USA", "Gran Bretaña", "España", "México", "Brasil", "Canada", "China", "Dinamarca", "Francia", "India", "Italia", "Japon");
?>
	<div class="wrap">
		<div class="bloque1">
		<div class="cuadro">
		<h2>Afiliados de Amazon Lite</h2>
		<div></div>
		</div>		
     	<div style="clear:both; height:25px"></div>
		<div class="cuadro">	
		<form method="post">
		<div>
    	<label for="Pkey">Key del producto</label>
   		<input disabled name="ADAL_Pkey" type="text" class="form-control" id="ADAL_Pkey" value="" placeholder="Key del producto no necesario en version lite">    
    	</div>
			<div style="clear:both; height:25px"></div>
		<div style="display: block;display: inline-block;">
    	
		<?php
		if (get_option('post_auto')==1)
		{
			
			$mostrar=' style="display: inline-block;"';
			
		}
		else
		{
			
			$mostrar=' style="display: none;"';
		}
		?>
   		   
			
		<label for="ADAL_post_auto">Crear post automáticos </label>
			<select disabled id="ADAL_post_auto" name="ADAL_post_auto">
				
				<option value="0" selected>Crear post</option>				
				
			</select>		
			
    	</div>
			
		<div id="cpost"<?=$mostrar?>  style="display: inline-block;">
			<div>
			<label for="ADAL_ppubli">¿Post publicados o en borrador?</label>
			<select id="ADAL_ppubli" name="ADAL_ppubli" disabled>
			<option value="2" selected>Post en borrador</option>
				
			</select>	
			</div>
		</div>
			<div style="clear:both; height:25px"></div>	
		<div>
    	<label for="ADAL_monedaTag">Moneda a utilizar</label>
   		<input name="ADAL_monedaTag" type="text" class="form-control" id="ADAL_monedaTag" value="<?php echo get_option('monedaTag'); ?>" placeholder="MXN">    
    	</div>	
			<div style="clear:both; height:25px"></div>	
		<div>
    	<label for="ADAL_AssociateTag">Tag asociado de Amazon</label>
   		<input name="ADAL_AssociateTag" type="text" class="form-control" id="ADAL_AssociateTag" value="<?php echo get_option('AssociateTag'); ?>">    
    	</div>			
			<div style="clear:both; height:25px"></div>
		<div>
    	<label for="ADAL_paises">Escoge tu tienda local</label>
		<select	class="form-control" id="ADAL_paises" name="ADAL_paises">
			<?php
			if (get_option('ADAL_paises')=="")
			{
			?>
			<option value="0" selected>Escoge un sitio</option>
			<?php
				$n=0;
				foreach($abr as $npais)
				{
					if(get_option('ADAL_paises')==$npais)
					{
						?>
						<option value="<?=$npais?>" selected>www.amazon.<?=$sitios[$n]?> (<?=$pais_sitio[$n]?>)</option>
						<?php
					}
					else
					{
						?>
						<option value="<?=$npais?>">www.amazon.<?=$sitios[$n]?> (<?=$pais_sitio[$n]?>)</option>
						<?php
					}
					$n++;
				}
			}
			else
			{
				$n=0;
				foreach($abr as $npais)
				{
					if(get_option('ADAL_paises')==$npais)
					{
						?>
						<option value="<?=$npais?>" selected>www.amazon.<?=$sitios[$n]?> (<?=$pais_sitio[$n]?>)</option>
						<?php
					}
					else
					{
						?>
						<option value="<?=$npais?>">www.amazon.<?=$sitios[$n]?> (<?=$pais_sitio[$n]?>)</option>
						<?php
					}
					$n++;
				}
			}
			?>
			
			</select>
   		   
    	</div>
			<div style="clear:both; height:20px"></div>
		<div>
			
		<div>
    	<label for="ADAL_AWSAccessKeyId">AWSAccessKeyId de Amazon</label>
   		<input name="ADAL_AWSAccessKeyId" type="text" class="form-control" id="ADAL_AWSAccessKeyId" value="<?php echo get_option('ADAL_AWSAccessKeyId'); ?>">    
    	</div>
			<div style="clear:both; height:10px"></div>	
		<div>
    	<label for="ADAL_AWSASecretKey">AWSASecretKey de Amazon</label>
   		<input name="ADAL_AWSASecretKey" type="text" class="form-control" id="ADAL_AWSASecretKey" value="<?php echo get_option('ADAL_AWSASecretKey'); ?>">    
    	</div>
			<div style="clear:both; height:25px"></div>	
		</div>	
		<h2>Tag de Asociado de Amazon en otros países</h2>
			<?php
				$n=0;
				foreach($abr as $npais)
				{
					if ($npais<>get_option('ADAL_paises'))
					{
					?>
			<div>
    	<div style="float: left; width: 250px"><label  for="ADAL_AssociateTag<?=$npais?>">Tag asociado de Amazon (<?=$pais_sitio[$n]?>)</label></div>
   		<div style="float: left;"><input disabled name="ADAL_AssociateTag<?=$npais?>" type="text" id="ADAL_AssociateTag<?=$npais?>" value="<?php echo get_option('ADAL_AssociateTag'.$npais); ?>"></div>    
    	</div>
			<div style="clear:both; height:5px"></div>
			<?php
					}
				$n++;	
				}
			?>
			
			<?php wp_nonce_field( 'actualizar_ADAL_core', 'ADAL_core_nounce' ); ?>
		 <?php submit_button(); ?> 
		 <div style="clear:both; height:15px"></div>
		</form>
			</div>
		</div>
		<div class="bloque2">
		<div class="cuadro">
		<h2>Soporte</h2>
		<div>¿Necesitas soporte?</div>	
		<div>Puedes obtener soporte <strong>limitado</strong> desde es <a target="_blank" href="https://adapluginwp.com/soporte/">link</a></div>	
		<h2>Trabajos personalizados</h2>	
		<div>Por favor contactanos desde esta pagina <a target="_blank" href="https://adapluginwp.com/contacto/">Contacto</a></div>	
		<h2>Características versión premium</h2>	
			<div>
			<ul>
			<li>Diferentes tipos de plantillas</li>	
				<li>Cargas asincronicas</li>	
				<li>Soporte diferentes tiendas por geo-targeting </li>
				<li>Link de Url</li>
				<li>Lo más vendido</li>
				<li>Crear post automáticamente</li>
				<li>Recopilación de ofertas automáticamente</li>
				<li>Soporte en Español</li>
				</ul>
			</div>
		<div>Mas información <a target="_blank" href="https://adapluginwp.com/">Plugin de Afiliados de Amazon</a></div>		
		</div>		
		</div>
		
	</div>
<?php	

	}
?>