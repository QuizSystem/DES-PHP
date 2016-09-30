<?php
include 'destable.php';
include 'convert.php';

//-------Start Sinh khoa
$key = "123457799BBCDFF1";
$key_bin = convert::hexToBin($key);
$key_pc1 = convert::hoanVi($key_bin, destable::$pc1);
$catkey = convert::cat2($key_pc1);
$C[0] = $catkey[0];
$D[0] = $catkey[1];
for ($i=1; $i<=16; $i++){
	$bit = 2;
	if (($i==1) || ($i==2) || ($i==9) || ($i==16) ){
		$bit = 1;
	}
	$C[$i] = convert::dichTrai($C[$i-1], $bit);
	$D[$i] = convert::dichTrai($D[$i-1], $bit);
	$CD[$i] = $C[$i] . $D[$i];
	$K[$i] = convert::hoanVi($CD[$i], destable::$pc2);
	$K_hex[$i] = convert::binToHex($K[$i]);
}
//-------End Sinh khoa

echo "QUA TRINH SINH KHOA";
echo "<br>";
echo "Khoa: " . $key;
for ($i=1; $i<=16; $i++){
	echo "<br>";
	echo "K" . $i . ": " . $K_hex[$i];
}


//-------Start MA HOA
function Sbox($bit,$stt){
	if($stt==1){
		$arr2Chieu = convert::toArray2Chieu(destable::$s1);
	}
	if($stt==2){
		$arr2Chieu = convert::toArray2Chieu(destable::$s2);
	}
	if($stt==3){
		$arr2Chieu = convert::toArray2Chieu(destable::$s3);
	}
	if($stt==4){
		$arr2Chieu = convert::toArray2Chieu(destable::$s4);
	}
	if($stt==5){
		$arr2Chieu = convert::toArray2Chieu(destable::$s5);
	}
	if($stt==6){
		$arr2Chieu = convert::toArray2Chieu(destable::$s6);
	}
	if($stt==7){
		$arr2Chieu = convert::toArray2Chieu(destable::$s7);
	}
	if($stt==8){
		$arr2Chieu = convert::toArray2Chieu(destable::$s8);
	}
	$arrDec = convert::binTo2Dec($bit);
	$hang=$arrDec[0];
	$cot=$arrDec[1];
	$output = convert::dec16ToBin($arr2Chieu[$hang][$cot]);
	return $output;
}

function hamF($R, $K){
	$Rmorong = convert::hoanVi($R, destable::$e);
	$xorRK = convert::phepXOR($Rmorong, $K);
	$catxorRK = convert::cat8($xorRK);
	$strSbox = "";
	for ($i=1;$i<=8;$i++){
		$B[$i] = $catxorRK[$i-1];
		$BS[$i] = Sbox($B[$i], $i);
		$strSbox .= $BS[$i];
	}
	$f = convert::hoanVi($strSbox, destable::$p);
	return $f;
}

$x = "0123456789ABCDEF";
$x_bin = convert::hexToBin($x);
$x_ip = convert::hoanVi($x_bin, destable::$ip);
$catx = convert::cat2($x_ip);
$L[0] = $catx[0];
$R[0] = $catx[1];
for ($i=1; $i<=16; $i++){
	$L[$i] = $R[$i-1];
	$F = hamF($R[$i-1], $K[$i]);
	$R[$i] = convert::phepXOR($L[$i-1], $F);
}
$R16L16 = $R[16].$L[16];
$y = convert::hoanVi($R16L16, destable::$ip_1);
$y_hex = convert::binToHex($y);
//-------End MA HOA

echo "<br><br><br>";
echo "QUA TRINH MA HOA";
echo "<br>";
echo "Ban ro: " . $x;
echo "<br>";
echo $x_bin;
echo "<br>";
echo $x_ip;
echo "<br>";
echo $y;
echo "<br>";
echo "Ban ma: " . $y_hex;

//-------Start GIAI MA (tuong tu ma hoa chi dao nguoc lai khoa tu K16->K1)
$x2 = "85E813540F0AB405";
$x_bin = convert::hexToBin($x2);
$x_ip = convert::hoanVi($x_bin, destable::$ip);
$catx = convert::cat2($x_ip);
$L[0] = $catx[0];
$R[0] = $catx[1];
for ($i=1; $i<=16; $i++){
	$L[$i] = $R[$i-1];
	$F = hamF($R[$i-1], $K[17-$i]);
	$R[$i] = convert::phepXOR($L[$i-1], $F);
}
$R16L16 = $R[16].$L[16];
$y = convert::hoanVi($R16L16, destable::$ip_1);
$y_hex = convert::binToHex($y);
//-------End GiA MA
echo "<br><br><br>";
echo "QUA TRINH GIAI MA";
echo "<br>Giai ma: ".$y_hex;

?>