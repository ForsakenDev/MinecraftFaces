<?php

$cacheFolder = 'cache';
$cacheTime = 60 * 60 * 24; # in seconds

# Request a users skin from the session servers
function fetchUrlByUUID($uuid) {
	$res = file_get_contents("https://sessionserver.mojang.com/session/minecraft/profile/{$uuid}");
	if($res === false || strpos($res, 'TooManyRequest') !== FALSE) { return false; }
	$json =  json_decode($res, true);
	foreach ($json['properties'] as $p) {
		if (strpos($p['name'], 'textures') !== FALSE) { $tex = $p['value']; break; }
	}
	if (!isset($tex)) { return false; }
	$imgData = base64_decode($tex);
	$json = json_decode($imgData, true);
	return $json['textures']['SKIN']['url'];
}

# Check base Minecraft Skin Urls, otherwise we need to do some digging for their skin
function getImageUrl($name) {
	$url = "https://s3.amazonaws.com/MinecraftSkins/{$name}.png";
	$headers = get_headers($url, 1);
	if ($headers[0] == 'HTTP/1.1 200 OK') { return $url; }
	$post = "{\"agent\":\"Minecraft\",\"name\":\"{$name}\"}";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.mojang.com/profiles/page/1");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json', 'Connection: Keep-Alive'));
	$res = str_replace(" ", "", str_replace("\r", "", str_replace("\n", "", curl_exec($ch))));
	if($res === false || strpos($res, 'TooManyRequest') !== FALSE) { return false; }
	$json = json_decode($res, true);
	return fetchUrlByUUID($json['profiles'][0]['id']);
}

# returns default steve skin
function getDefaultImage() {
	$def = 'iVBORw0KGgoAAAANSUhEUgAAAEAAAABACAYAAACqaXHeAAAFDUlEQVR42u2a20sUURzH97G0LKMotPuWbVpslj1olJ';
	$def .= 'XdjCgyisowsSjzgrB0gSKyC5UF1ZNQWEEQSBQ9dHsIe+zJ/+nXfM/sb/rN4ZwZ96LOrnPgyxzP/M7Z+X7OZc96JpE';
	$def .= 'ISfWrFhK0YcU8knlozeJKunE4HahEqSc2nF6zSEkCgGCyb+82enyqybtCZQWAzdfVVFgBJJNJn1BWFgC49/VpwGVl';
	$def .= 'D0CaxQiA5HSYEwBM5sMAdKTqygcAG9+8coHKY/XXAZhUNgDYuBSPjJL/GkzVVhAEU5tqK5XZ7cnFtHWtq/TahdSw2';
	$def .= 'l0HUisr1UKIWJQBAMehDuqiDdzndsP2EZECAG1ZXaWMwOCODdXqysLf++uXUGv9MhUHIByDOijjdiSAoH3ErANQD7';
	$def .= '3C7TXXuGOsFj1d4YH4OTJAEy8y9Hd0mCaeZ5z8dfp88zw1bVyiYhCLOg1ZeAqC0ybaDttHRGME1DhDeVWV26u17lR';
	$def .= 'APr2+mj7dvULfHw2q65fhQRrLXKDfIxkau3ZMCTGIRR3URR5toU38HbaPiMwUcKfBAkoun09PzrbQ2KWD1JJaqswj';
	$def .= 'deweoR93rirzyCMBCmIQizqoizZkm2H7iOgAcHrMHbbV9KijkUYv7qOn55sdc4fo250e+vUg4329/Xk6QB/6DtOws';
	$def .= '+dHDGJRB3XRBve+XARt+4hIrAF4UAzbnrY0ve07QW8uHfB+0LzqanMM7qVb+3f69LJrD90/1axiEIs6qIs21BTITo';
	$def .= 'ewfcSsA+Bfb2x67OoR1aPPzu2i60fSNHRwCw221Suz0O3jO+jh6V1KyCMGse9721XdN5ePutdsewxS30cwuMjtC86';
	$def .= '0T5JUKpXyKbSByUn7psi5l+juDlZYGh9324GcPKbkycaN3jUSAGxb46IAYPNZzW0AzgiQ5tVnzLUpUDCAbakMQXXr';
	$def .= 'OtX1UMtHn+Q9/X5L4wgl7t37r85OSrx+TYl379SCia9KXjxRpiTjIZTBFOvrV1f8ty2eY/T7XJ81FQAwmA8ASH1ob';
	$def .= '68r5PnBsxA88/xAMh6SpqW4HRnLBrkOA9Xv5wPAZjAUgOkB+SHxgBgR0qSMh0zmZRsmwDJm1gFg2PMDIC8/nAHIMl';
	$def .= 's8x8GgzOsG5WiaqREgYzDvpTwjLDy8NM15LpexDEA3LepjU8Z64my+8PtDCmUyRr+fFwA2J0eAFYA0AxgSgMmYBMZ';
	$def .= 'TwFQnO9RNAEaHOj2DXF5UADmvAToA2ftyxZYA5BqgmZZApDkdAK4mAKo8GzPlr8G8AehzMAyA/i1girUA0HtYB2Ca';
	$def .= 'IkUBEHQ/cBHSvwF0AKZFS5M0ZwMQtEaEAmhtbSUoDADH9ff3++QZ4o0I957e+zYAMt6wHkhzpjkuAcgpwNcpA7AZD';
	$def .= 'LsvpwiuOkBvxygA6Bsvb0HlaeKIF2EbADZpGiGzBsA0gnwQHGOhW2snRpbpPexbAB2Z1oicAMQpTnGKU5ziFKc4xS';
	$def .= 'lOcYpTnOIUpzgVmgo+XC324WfJAdDO/+ceADkCpuMFiFKbApEHkOv7BfzfXt+5gpT8V7rpfYJcDz+jAsB233r6yyB';
	$def .= 'sJ0mlBCDofuBJkel4vOwBFPv8fyYAFPJ+wbSf/88UANNRVy4Awo6+Ig2gkCmgA5DHWjoA+X7AlM//owLANkX0w035';
	$def .= '9od++pvX8fdMAcj3/QJ9iJsAFPQCxHSnQt8vMJ3v2wCYpkhkAOR7vG7q4aCXoMoSgG8hFAuc/grMdAD4B/kHl9da7';
	$def .= 'Ne9AAAAAElFTkSuQmCC';
	$def = base64_decode($def);
	return $def;
}

# parse a full minecraft skin into just the face
function translateImageToFace($im) {
	$face = imagecreatetruecolor(512, 512);
	$hat  = imagecreatetruecolor(8, 8);
	imagecopy($hat, $im, 0, 0, 40, 8, 8, 8);
	$col = imagecolorat($hat, 1, 1);
	$hadHat = 0;
	for ($x=0; $x<8; $x++) {
	  for ($y=0; $y<8; $y++) {
	    $ncol = imagecolorat($hat,$x,$y);
	    if ($ncol != $col) {
	      $hadHat = 1;
	      break 2;
	    }
	  }
	}
	$trans = imagecolorallocate($face, 255, 0, 255);
	imagecolortransparent($face, $trans);
	imagefill($face, 0, 0, $trans);
	imagecopyresized($face, $im, ((512/8) / 2), ((512 / 8) / 2), 8, 8, 512 - ((512 / 8)), 512 - ((512 / 8)), 8, 8);
	if ($hadHat == 1) {
	  imagecopyresized($face, $im, 0, 0, 40, 8, 512, 512, 8, 8);
	}
	imagedestroy($im);
	imagedestroy($hat);
	return $face;
}

# Scale an image to a new size
function getScaledImage($srcImage, $size) {
	if (!is_resource($srcImage)) { return false; }
	$defImage = imagecreatetruecolor($size, $size);
	$saveSize = imagesy($srcImage);
	$trans = imagecolorallocate($srcImage, 255, 0, 255);
	imagecolortransparent($srcImage, $trans);
	imagecolortransparent($defImage, $trans);
	imagefill($defImage, 0, 0, $trans);
	imagecopyresized($defImage, $srcImage, 0, 0, 0, 0, $size, $size, $saveSize, $saveSize);
	imagedestroy($srcImage);
	return $defImage;
}


ini_set('display_errors', 0);
header("Content-type: image/png");
$name = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : 'char';
if (!preg_match("#[a-z0-9_]{2,16}#i", $name)) { $name = 'char'; }
$size = isset($_GET['size']) && !empty($_GET['size']) ? $_GET['size'] : 256;

if (!is_dir($cacheFolder)) { mkdir($cacheFolder); }
$cachePath = $cacheFolder . DIRECTORY_SEPARATOR . $name . '.png';
if (is_file($cachePath) && (time() - filemtime($cachePath)) <= $cacheTime) {
	$image = imagecreatefrompng($cachePath);
} else {
	$body = imagecreatefrompng(getImageUrl($name));
	if (!$body) {
		$isDefault = true;
		$body = imagecreatefromstring(getDefaultImage());
	}
	$image = translateImageToFace($body);
	if (!$isDefault) {
		unlink($cachePath);
		imagepng($image, $cachePath);
	}
}

$scaledImage = getScaledImage($image, $size);
if (is_resource($scaledImage)) { imagepng($scaledImage); }
if (is_resource($image)) { imagedestroy($image); }
if (is_resource($scaledImage)) { imagedestroy($scaledImage); }
?>