<?php
/*
** sm2sb.php - Smokeping to StatusBoard
** Fabio Rapposelli - fabio@rapposelli.org - http://about.me/frapposelli
**
** Easily convert Smokeping RRDs to a StatusBoard friendly JSON feed
** Currently Shows only the latest Smokeping result.
** Needs php5-rrd and php5-json to work.
*/

/* Title of the StatusBoard Panel */
$title="Ping Status";

/* Customize with your Smokeping RRD locations / host name / color */
$RRDs=array(
	array("/var/lib/smokeping/network/maya.rrd", "maya.ngi.it", "green"),
	array("/var/lib/smokeping/network/google.rrd", "google.it", "red"),
	array("/var/lib/smokeping/network/switch.rrd", "switch.ch", "yellow")
	);

/* Avoid outputting weird stuff */
error_reporting(0);

/* Hic Sunt Leones */
$i=0;
/* Parse RRD List */
foreach ($RRDs as $rrd) {

	$rrd_info=rrd_info($rrd[0]);
	$rra_step = $rrd_info['step']*1; 
	$endtime = $rrd_info['last_update']-($rrd_info['last_update']%$rra_step);

	/* Fetch RRD data */
	$rrddata[$i]['data']=rrd_fetch($rrd[0], array("AVERAGE","--end",($endtime-1),"--start",($endtime-$rrd_info['step'])));

	if($rrddata[$i] === false) { 
		echo rrd_error(); 
	} else {
    	$rrddata[$i]['hostname']=$rrd[1];
	    $rrddata[$i]['color']=$rrd[2];
		$i++;
	}	
}

/* Create Array Skel */
$jsonArray = array('graph' => array('title' => $title,'type' => 'bar','yAxis' => array('units' => array('suffix' => ' ms')),'datasequences' => array()));

$i=0;

/* Parse RRD Data and create Array Structure */
foreach ($rrddata as $rrd_key => $rrd_value) {
	$jsonArray['graph']['datasequences'][$rrd_key]['title']=$rrd_value['hostname'];
	$jsonArray['graph']['datasequences'][$rrd_key]['color']=$rrd_value['color'];
	foreach ($rrd_value['data']['data']['median'] as $data_key => $data_value) {
		$jsonArray['graph']['datasequences'][$rrd_key]['datapoints'][0]['title']=date('H:i', $data_key);
		$jsonArray['graph']['datasequences'][$rrd_key]['datapoints'][0]['value']=$data_value*1024;
		$i++;
	}
}

/* Encode the array to JSON and output it out */
echo json_encode($jsonArray);
?>
