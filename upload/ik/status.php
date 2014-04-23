<?php

include('../system.php');
BDConnect("pay");
loadTool('log.class.php');
function ikSign($params, $ikKey){
	// ������� �������� ���������
	unset($params['ik_sign']);
	foreach($params as $key => $value) if(! preg_match("/^ik_/is", $key)) unset($params[$key]);
	
	ksort($params, SORT_STRING);
	array_push($params, $ikKey);
	$sign = implode(":", $params);
	$sign = base64_encode(md5($sign, true));
	return $sign;
}


$kassaId = trim($_REQUEST['ik_co_id']);
$paymentId = trim(strip_tags($_REQUEST['ik_pm_no']));
$summ = intval($_REQUEST['ik_am']);
$paySystem = trim($_REQUEST['ik_pw_via']);
$payStatus = trim($_REQUEST['ik_inv_st']);
$sign = trim($_REQUEST['ik_sign']);
$ik_payment_timestamp = trim($_REQUEST['ik_inv_prc']);
$secretKey = $secret_key;
// ������������
if($ikTesting and ($paySystem == "test_interkassa_test_xts")){
	$secretKey = $secret_key_test;
} elseif($paySystem == "test_interkassa_test_xts") {
	Logs::write($ik_payment_timestamp."\t$paymentId �� �������� �������� ������ �� $summ ���");
	exit("OK");
}

if($kassaId != $ikshopid) exit("�������� ID �����");
if($sign != ikSign($_REQUEST, $secretKey)) {
	Logs::write($ik_payment_timestamp."\t�������� �������: $sign $summ ");
	exit("Bad sign");
}
BD("UPDATE `{$bd_names['iconomy']}` SET `{$bd_money['bank']}`=`{$bd_money['bank']}`+$summ WHERE `{$bd_money['login']}`='$paymentId'");


	Logs::write($ik_payment_timestamp."\t$paymentId �������� ������ �� $summ ���");
echo "ok";
