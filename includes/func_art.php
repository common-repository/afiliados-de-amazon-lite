<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function get_article_ADA_lite($attr, $content = null) {
$a = shortcode_atts( array(
		'articulos' => '',		
		'tboton' => ''
	), $attr );
$articulos=esc_attr($a['articulos']);	
if (!$articulos)
{
	return false;
}
$tboton=esc_attr($a['tboton']);
if ($tboton=="")
{
	$tboton="Ver Producto";
}

$html_ama_listag="";
$rand=rand(1000,3500);
$html_ama_listag.='
<div style="clear: both; height: 15px"></div>
<div align="center" style="text-align: center;" class="cuerpo_ada_art'.$rand.'">';
$html_ama_listag.=mostar_post_art_lite($articulos,$tboton);
$html_ama_listag.='</div>
<div style="clear: both; height: 15px"></div>
</div>';
return $html_ama_listag;	
}

add_shortcode('amazon_lite', 'get_article_ADA_lite');	


function mostar_post_art_lite($articulos,$tboton) {
$pais_base=get_option('ADAL_paises');
	if ( wp_is_mobile() ) {
		$cuerpog=' style="height: auto;justify-content: center; align-items: center;"';
	}
	else
	{
		$cuerpog=' style="height: 530px; justify-content: center; align-items: center;"';
	}

$rec=' style="font-size: 15px;"';
$imgp=' style="width: 100%;float: none; height: 265px; position: relative;"';
$boton=' style="width: 100%;float: none;"';
//$col4=' class="col-4"';
$lista2='';
$cssttitulo=' style="height:100px; overflow:hidden; font-size: 15px;"';
$cssenvio=' style="font-size: 14px; font-weight: bold; color: red"';
$cssimg="max-height: 170px;position: absolute;top: 0;bottom: 0;margin: auto;left: 0;right: 0";	
	$col4=' class="col-4-lite"';
	
switch ($pais_base) 
{
case "au":
	$urlg_ama="webservices.amazon.com.".$pais_base;
	$url_busq="www.amazon.com.".$pais_base;
	break;
case "br":
	$urlg_ama="webservices.amazon.com.".$pais_base;
	$url_busq="www.amazon.com.".$pais_base;
	break;
case "mx":
	$urlg_ama="webservices.amazon.com.".$pais_base;
	$url_busq="www.amazon.com.".$pais_base;
	break;
case "ca":
	$urlg_ama="webservices.amazon.".$pais_base;
	$url_busq="www.amazon.".$pais_base;
	break;
case "cn":
	$urlg_ama="webservices.amazon.".$pais_base;	
	$url_busq="www.amazon.".$pais_base;
	break;
case "de":
	$urlg_ama="webservices.amazon.".$pais_base;
	$url_busq="www.amazon.".$pais_base;
	break;
case "es":
	$urlg_ama="webservices.amazon.".$pais_base;	
	$url_busq="www.amazon.".$pais_base;
	break;
case "fr":
	$urlg_ama="webservices.amazon.".$pais_base;
	$url_busq="www.amazon.".$pais_base;
	break;
case "in":
	$urlg_ama="webservices.amazon.".$pais_base;	
	$url_busq="www.amazon.".$pais_base;
	break;
case "it":
	$urlg_ama="webservices.amazon.".$pais_base;	
	$url_busq="www.amazon.".$pais_base;
	break;
case "jp":
	$urlg_ama="webservices.amazon.co.".$pais_base;
	$url_busq="www.amazon.co.".$pais_base;
	break;
case "uk":
	$urlg_ama="webservices.amazon.co.".$pais_base;	
	$url_busq="www.amazon.co.".$pais_base;
	break;
case "us":
	$urlg_ama="webservices.amazon.com";
	$url_busq="www.amazon.com";
	break;		
}

$articulos=rtrim($articulos,",");
$xitem=explode(",",$articulos);
if (count($xitem)>3)
{
	exit();
}
$ncontrol=0;		
foreach($xitem as $ItemId)
{

if (!$ItemId)
{
	exit();
}

$ItemId=trim($ItemId);
global $wpdb;
$table_name = $wpdb->prefix . 'Amazon_articulos_lite';		
$existencia = $wpdb->get_var("SELECT COUNT(*) FROM ".$table_name." WHERE sku='".$ItemId."'");

if ($existencia==0)
{
$AWSAccessKeyId=get_option('ADAL_AWSAccessKeyId');
$AWSASecretKey=get_option('ADAL_AWSASecretKey');		

$AssociateTag=get_option('ADAL_AssociateTag');

if (isset($AWSAccessKeyId) && isset($AWSASecretKey))
{
$Timestamp = gmdate("Y-m-d\TH:i:s\Z");
$Version = "2013-08-01";
$str = "Service=AWSECommerceService&AssociateTag=".$AssociateTag."&AWSAccessKeyId=".$AWSAccessKeyId."&ResponseGroup=Images%2COffers%2CItemAttributes%2CEditorialReview%2CReviews&Operation=ItemLookup&ItemId=".$ItemId."&Timestamp=".urlencode($Timestamp);	
$ar = explode("&", $str);									
natsort($ar);
$str = "GET
".$urlg_ama."
/onca/xml
";
$str .= implode("&", $ar); 
//echo ($str)."<br><br>";
$str = urlencode(base64_encode(hash_hmac("sha256",$str,$AWSASecretKey,true)));
$url = "https://".$urlg_ama."/onca/xml?Service=AWSECommerceService&AssociateTag=".$AssociateTag."&AWSAccessKeyId=".$AWSAccessKeyId."&ResponseGroup=Images%2COffers%2CItemAttributes%2CEditorialReview%2CReviews&Operation=ItemLookup&ItemId=".$ItemId."&Timestamp=".urlencode($Timestamp)."&Signature=".$str;
	
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
	//print_r($result);
$xml=simplexml_load_string($result) or die("Error: Cannot create object");
	
$descuento=substr($xml->Items->Item->Offers->Offer->OfferListing->AmountSaved->Amount, 0, -2);;
$porcentaje=$xml->Items->Item->Offers->Offer->OfferListing->PercentageSaved;
$EligibleForPrime=$xml->Items->Item->Offers->Offer->OfferListing->IsEligibleForPrime;
	
if ($EligibleForPrime=="")
{
$EligibleForPrime='0';
}	

$preciodec1=$xml->Items->Item->Offers->Offer->OfferListing->SalePrice->Amount;
if ($preciodec1=="")
{
$preciodec1=$xml->Items->Item->Offers->Offer->OfferListing->Price->Amount;
}

if ($preciodec1<>"" ) {
if ($xml->Items->Item->ItemAttributes->ListPrice->Amount<>"")
{
	$precion= substr($xml->Items->Item->ItemAttributes->ListPrice->Amount, 0, -2);
}
else
{
	$precion= substr($xml->Items->Item->OfferSummary->LowestNewPrice->Amount, 0, -2);
}

$preciod=substr($preciodec1, 0, -2);


}
else
{
$precion= substr($xml->Items->Item->ItemAttributes->ListPrice->Amount, 0, -2);
$preciod="0";
}

$titulo=($xml->Items->Item->ItemAttributes->Title);
$iframe=($xml->Items->Item->CustomerReviews->IFrameURL);

if ($iframe=="")
{
$iframe="0";
}

if (count($xml->Items->Item->ItemAttributes->Feature))
{
$cant_car=count($xml->Items->Item->ItemAttributes->Feature);
}
	
if ($cant_car>0) 
{
$caract="";
$caract.='<ul>';
$carn=0;
foreach($xml->Items->Item->ItemAttributes->Feature as $carac)
{
$caract.='<li>'.$carac.'</li>';
$carn++;
if ($carn==3)
{
	break;
}
}
$caract.='</ul>';				
}
else
{
$caract=	'<strong>Caracteristicas pendientes</strong>';
}

if($preciod>0 & $descuento>0)
{
$cant_desc=$precion-$preciod;
}
else
{
$cant_desc="0";
}
	
if (trim($precion)=="")
{
$precion=0;
$preciod=0;
$porcentaje=0;
$cant_desc=0;
}
	
if (trim($porcentaje)=="")
{
$porcentaje=0;
}
	
if ($precion==$preciod)
{
$preciod=0;
}
	
if (trim($preciod)=="")
{
$preciod=0;
}	

$imagent=$xml->Items->Item->LargeImage->URL;	
$urlv=$xml->Items->Item->DetailPageURL;	
	
if (ADAL_UR_exists_lite($imagent))
{
$upload_dir = wp_upload_dir();
$fecha=time();
$image_data = ADAL_getFile_lite($imagent);
$titulo=ADAL_acentos_lite($titulo);
$ntit=str_replace("/","",(substr($titulo,0,50)));
$ntit=str_replace("&","",$ntit);
$ntit=str_replace(",","",$ntit);
$ntit=str_replace(";","",$ntit);
$ntit=str_replace('"','',$ntit);
$ntit=str_replace("'",'',$ntit);
$nomimg=trim($ntit);
$nomimg=str_replace(' ','-',$nomimg);
$nomimg=(substr($nomimg,0,25));
$filename   = $nomimg."-".$fecha.".jpg";
if( wp_mkdir_p( $upload_dir['path'] ) ) {
$file = $upload_dir['path'] . '/' . $filename;
} else {
$file = $upload_dir['basedir'] . '/' . $filename;
}

file_put_contents( $file, $image_data );

$wp_filetype = wp_check_filetype( $filename, null );

// Set attachment data
$attachment = array(
'post_mime_type' => $wp_filetype['type'],
'post_title'     => sanitize_file_name( $filename ),
'post_content'   => '',
'post_status'    => 'inherit'
);

// Create the attachment
$attach_id = wp_insert_attachment( $attachment, $file );

// Include image.php
require_once(ABSPATH . 'wp-admin/includes/image.php');

// Define attachment metadata
$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

// Assign metadata to attachment
wp_update_attachment_metadata( $attach_id, $attach_data );

$image_attributes = wp_get_attachment_image_src( $attach_id, 'medium');

$nurlimg=$image_attributes[0];		
}
global $wpdb;
$table_name = $wpdb->prefix . 'Amazon_articulos_lite';			
			$wpdb->insert( 
				$table_name, 
				array( 
					'sku' => $ItemId, 
					'titulo' => $titulo, 
					'caracteristicas' => $caract, 
					'precio_nor' => $precion,
					'precio_desc' => $preciod, 
					'descuento' => $descuento, 
					'porcentaje' => $porcentaje, 
					'url' => $urlv, 					
					'imagen' => $nurlimg,					
					'prime' =>$EligibleForPrime					
				) 
			);	
	usleep(1200000);	
	
}
	
}
else
{
global $wpdb;
$table_name = $wpdb->prefix . 'Amazon_articulos_lite';
$mylink = $wpdb->get_row( "SELECT * FROM ".$table_name." WHERE sku='".$ItemId."'");
$titulo=$mylink->titulo;
$caracteristicas=$mylink->caracteristicas;
$precio_nor=$mylink->precio_nor;
$precio_desc=$mylink->precio_desc;
$descuento=$mylink->descuento;
$porcentaje=$mylink->porcentaje;		
$imagen=$mylink->imagen;
$prime=$mylink->prime;	
	
$imagenss=ADAL_PLUGIN_URL.'img/amazon_prime.png';

$url=$mylink->url;

if ($prime==1)
{
$mostrar_prime='<a rel="nofollow" href="https://'.$url_busq.'/gp/prime/?tag='.get_option("ADAL_AssociateTag").'"><img title="¿Que es Amazon Prime?" alt="¿Que es Amazon Prime?" src="'.$imagenss.'"></a>';
}
else
{
$mostrar_prime='';
}

if ($precio_nor<>"0")
{
if ($descuento>0) {
$precio_Desc=money_format('%.2n', $precio_desc);	
$html4='<span class="descuento">Descuento: '.$porcentaje.'% </span>';
$precn='<div class="cinc"'.$boton.'><span class="tachado">'.number_format($precio_nor).' '.get_option('ADAL_monedaTag').'</span>';
$precd='<span class="pnormal">'.number_format($precio_desc).' '.get_option('ADAL_monedaTag').'</span></div>';		
}
else
{
$html4='';
$precn='<div class="cinc"'.$boton.'>';
$precd='<span class="pnormal">'.number_format($precio_nor).' '.get_option('ADAL_monedaTag').'</span></div>';	

}
}
else
{
$html4='';
$precn='<div class="cinc"'.$boton.'>';
$precd='<span class="pnormal">Precio no disponible</span></div>';		
}

if ($mylink->envio<>"0")
{
$envio=$mylink->envio;
}
else
{
$envio="";
}	

$url=$mylink->url;

	
$muestra.='<div'.$col4.'><div class="cuerpog"'.$cuerpog.'>
			<span class="rec"'.$rec.'>Te recomendamos:</span>
			'.$html4.'
			<div style="clear: both; height: 1px"></div>
		<div class="imgp"'.$imgp.' align="center">
		<div style="vertical-align: middle">
		<div style="display: block"><a'.$redi.' rel="nofollow" href="'.$url.'"><img style="'.$cssimg.'" title="'.$titulo.'" alt="'.$titulo.'" src="'.$imagen.'"></a></div>
		<div style="display: block; position: relative;">
				'.$mostrar_prime.'
				</div></div>		
		</div>
		<div class="cuerpoan"'.$boton.'>


		<h2 class="titulo"'.$cssttitulo.' align="center"><a'.$redi.' style="text-decoration: none" rel="nofollow" href="'.$url.'">'.ADAL_substrwords_lite($titulo,65).'</a>

		</h2>';		
			
			
			$muestra.='
			<div style="clear: both; height: 1px"></div>
			'.$precn.''.$precd.'
			
			<div style="clear: both; height: 5px"></div>
			<a rel="nofollow"'.$redi.' class="aawp-button aawp-button--buy aawp-button aawp-button--amazon rounded shadow aawp-button--icon aawp-button--icon-black" href="'.$url.'" title="'.$tboton.'">'.$tboton.'</a>
			<div style="clear: both; height: 5px"></div>
		</div>
		<div style="clear: both; height: 5px"></div>
		<div>

		</div>
			<div style="clear: both; height: 1px"></div>
		</div></div>';
		
		$ncontrol++;
	if ($ncontrol==3)
	{
		$muestra.='<div style="clear: both; height: 2px"></div>';
		
	}
	
		

		
}
	
}
	return $muestra;
}
?>