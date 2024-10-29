<?php
if ( ! defined( 'ABSPATH' ) ) exit;
function ADAL_correr_cron (){
	
$ADAL_pais_base=get_option('ADAL_paises');

switch ($ADAL_pais_base) 
{
case "au":
	$urlg_ama="webservices.amazon.com.".$ADAL_pais_base;
	$url_busq="www.amazon.com.".$ADAL_pais_base;
	break;
case "br":
	$urlg_ama="webservices.amazon.com.".$ADAL_pais_base;
	$url_busq="www.amazon.com.".$ADAL_pais_base;
	break;
case "mx":
	$urlg_ama="webservices.amazon.com.".$ADAL_pais_base;
	$url_busq="www.amazon.com.".$ADAL_pais_base;
	break;
case "ca":
	$urlg_ama="webservices.amazon.".$ADAL_pais_base;
	$url_busq="www.amazon.".$ADAL_pais_base;
	break;
case "cn":
	$urlg_ama="webservices.amazon.".$ADAL_pais_base;	
	$url_busq="www.amazon.".$ADAL_pais_base;
	break;
case "de":
	$urlg_ama="webservices.amazon.".$ADAL_pais_base;
	$url_busq="www.amazon.".$ADAL_pais_base;
	break;
case "es":
	$urlg_ama="webservices.amazon.".$ADAL_pais_base;	
	$url_busq="www.amazon.".$ADAL_pais_base;
	break;
case "fr":
	$urlg_ama="webservices.amazon.".$ADAL_pais_base;
	$url_busq="www.amazon.".$ADAL_pais_base;
	break;
case "in":
	$urlg_ama="webservices.amazon.".$ADAL_pais_base;	
	$url_busq="www.amazon.".$ADAL_pais_base;
	break;
case "it":
	$urlg_ama="webservices.amazon.".$ADAL_pais_base;	
	$url_busq="www.amazon.".$ADAL_pais_base;
	break;
case "jp":
	$urlg_ama="webservices.amazon.co.".$ADAL_pais_base;
	$url_busq="www.amazon.co.".$ADAL_pais_base;
	break;
case "uk":
	$urlg_ama="webservices.amazon.co.".$ADAL_pais_base;	
	$url_busq="www.amazon.co.".$ADAL_pais_base;
	break;
case "us":
	$urlg_ama="webservices.amazon.com";
	$url_busq="www.amazon.com";
	break;		
}
	
global $wpdb;	
$table_name = $wpdb->prefix . 'Amazon_articulos_lite';
$num_rows = $wpdb->get_var( "SELECT COUNT(*) FROM ".$table_name."");
if ($num_rows>0 )
{
	$rows_per_page= 10;
	$lastpage= ceil($num_rows / $rows_per_page);
	$pagina=1;
	while($pagina<=$lastpage)
	{
	global $wpdb;
	$table_name = $wpdb->prefix . 'Amazon_articulos_lite';
	//$sqlart="SELECT * FROM ".$table_name.";";
	$query = ( "SELECT * FROM ".$table_name." LIMIT " . ($pagina -1) * $rows_per_page . "," .$rows_per_page.";");
	$rawproducts = $wpdb->get_results( $query );
	//print_r($rawproducts);
	$ItemId="";
	foreach( $rawproducts as $row) {
		$ItemId.= $row->sku.",";
	}
	$ItemId=rtrim($ItemId,",");
	$Timestamp = gmdate("Y-m-d\TH:i:s\Z");


$AWSAccessKeyId=get_option('ADAL_AWSAccessKeyId');
$AWSASecretKey=get_option('ADAL_AWSASecretKey');
		
$AssociateTag=get_option('ADAL_AssociateTag');
$Version = "2013-08-01";

$str = "Service=AWSECommerceService&AssociateTag=".$AssociateTag."&AWSAccessKeyId=".$AWSAccessKeyId."&ResponseGroup=Images%2COffers%2CItemAttributes%2CEditorialReview%2CReviews&Operation=ItemLookup&ItemId=".urlencode($ItemId)."&Timestamp=".urlencode($Timestamp);	
$ar = explode("&", $str);									
natsort($ar);
$str = "GET
".$urlg_ama."
/onca/xml
";
$str .= implode("&", $ar); 
//echo ($str)."<br><br>";
$str = urlencode(base64_encode(hash_hmac("sha256",$str,$AWSASecretKey,true)));
$url = "https://".$urlg_ama."/onca/xml?Service=AWSECommerceService&AssociateTag=".$AssociateTag."&AWSAccessKeyId=".$AWSAccessKeyId."&ResponseGroup=Images%2COffers%2CItemAttributes%2CEditorialReview%2CReviews&Operation=ItemLookup&ItemId=".urlencode($ItemId)."&Timestamp=".urlencode($Timestamp)."&Signature=".$str;
//echo $url."<br>";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
		
$xml=simplexml_load_string($result) or die("Error: Cannot create object");	
		
$preciodec1=$descuento=$porcentaje=$precio_nor="";	
		
foreach($xml->Items->Item as $art)
{	
$iframe=($art->CustomerReviews->IFrameURL);

$preciodec1=$art->Offers->Offer->OfferListing->SalePrice->Amount;
if ($preciodec1=="")
{
	$preciodec1=$art->Offers->Offer->OfferListing->Price->Amount;
}
if ($preciodec1<>"" ) {
	$descuento=$art->Offers->Offer->OfferListing->AmountSaved->Amount;
	$porcentaje=$art->Offers->Offer->OfferListing->PercentageSaved;
	$EligibleForPrime=$art->Offers->Offer->OfferListing->IsEligibleForPrime;	
	$SuperSaverShipping=$art->Offers->Offer->OfferListing->IsEligibleForSuperSaverShipping;
	if ($EligibleForPrime=="")
			{
				$EligibleForPrime='0';
			}
			if ($SuperSaverShipping=="")
			{
				$SuperSaverShipping='0';
			}
	if ($art->ItemAttributes->ListPrice->Amount<>"")
		{
			$precio_nor= substr($art->ItemAttributes->ListPrice->Amount, 0, -2);
		}
		else
		{
			$precio_nor= substr($art->OfferSummary->LowestNewPrice->Amount, 0, -2);
		}
	$precio_Desc=substr($preciodec1, 0, -2);
	$descuento=substr($descuento, 0, -2);
	
	
}
else
{
	$precio_nor= substr($art->ItemAttributes->ListPrice->Amount, 0, -2);
	$precio_Desc=$descuento=$porcentaje=0;
}
		if ($precio_nor<$precio_Desc)
		{
			$precio_nor=$precio_Desc;
			$descuento=$porcentaje=$precio_Desc=0;
		}
		
		if ($descuento=="")
		{
			$descuento=$porcentaje=0;
		}
		
		$nprecn=number_format((float)$precio_norDB,0);
	
if (trim($descuento)=="")
{
	$descuento='0';
}
	if (trim($env)=="")	
	{
		$env="0";
	}
		
		
global $wpdb;
$table_name = $wpdb->prefix . 'Amazon_articulos_lite';
$wpdb->update($table_name, array('precio_nor'=>$precio_nor,'precio_desc'=>$precio_Desc,'descuento'=>$descuento,'porcentaje'=>$porcentaje,'prime'=>$EligibleForPrime), array('sku'=>$ItemId));	
	
	
}
$time="1.2";
usleep($time * 1000000);
$pagina++;
		
	}
	
}
	
}
echo ADAL_correr_cron();
?>