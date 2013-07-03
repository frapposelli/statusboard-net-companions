<?php
/*
** mrtg2sb.php - MRTG to StatusBoard
** Fabio Rapposelli - fabio@rapposelli.org - http://about.me/frapposelli
**
** Easily convert MRTG RRDs to a StatusBoard friendly JSON feed
** Currently Shows only the latest MRTG result.
** Needs php5-rrd and php5-json to work.
**
** Assumes by default that MRTG is configured to output its counters in bytes
**
*/

/* Title of the StatusBoard Panel */
$title="Router Output";

/* Unit to be used in the graph, valid expressions: bytes, kilobytes, megabytes, bits, kilobits, megabits */
$unit="megabits";

/* Time Range to graph, in minutes */
$timerange=60;

/* How often StatusBoard will refresh the graph, in seconds (should be aligned with your MRTG collection timing) */
$time_refresh=60;

/* Customize with your MRTG RRD locations / interface name / upstream color / downstream color */
$RRDs=array(
	array("ipv4gw.unixborg.com_vi3.rrd", "ATM0/0/0", "green", "red"),
	array("ipv4gw.unixborg.com_vi4.rrd", "ATM0/1/0", "blue", "yellow")	
	);

/* Hic Sunt Leones
                          ,%%%%%%%,
                        ,%%/\%%%%/\%,
                       ,%%%\c "" J/%%,
  %.                   %%%%/ d  b \%%%
  `%%.         __      %%%%    _  |%%%
   `%%      .-'  `"~--"`%%%%(=_Y_=)%%'
    //    .'     `.     `%%%%`\7/%%%'____
   ((    /         ;      `%%%%%%%'____)))
   `.`--'         ,'   _,`-._____`-,   
jgs  `"""`._____  `--,`          `)))
                `~"-)))
*/

function unitConversion($bytes, $targetUnit, $precision = 2) {
	switch (strtolower($targetUnit)) {
	    case 'bytes';
	    	return round($bytes, $precision);
	    break;
	    case 'kilobytes';
	    	return round(($bytes/1024), $precision);
	    break;
	    case 'megabytes';
	    	return round((($bytes/1024)/1024), $precision);
	    break;
	   case 'bits';
	   		return round($bytes*8, $precision);
	    break;
	    case 'kilobits';
			return round((($bytes*8)/1024), $precision);
	    break;
	    case 'megabits';
			return round(((($bytes*8)/1024)/1024), $precision);
	    break;
		default:
	    	/* defaults to bytes */
	    	return round($bytes, $precision);
	}
}

/* Avoid outputting weird stuff */
error_reporting(0);

$i=0;
/* Parse RRD List */
foreach ($RRDs as $rrd) {
	$rrd_info=rrd_info($rrd[0]);
	$rra_step = $rrd_info['step']*1; 
	$endtime = $rrd_info['last_update']-($rrd_info['last_update']%$rra_step);

	/* Fetch RRD data */
	$rrddata[$i]['data']=rrd_fetch($rrd[0], array("AVERAGE","--end",($endtime-1),"--start",($endtime-($rrd_info['step']*$timerange))));

	if($rrddata[$i] === false) { 
		echo rrd_error(); 
	} else {
    	$rrddata[$i]['interface_name']=$rrd[1];
	    $rrddata[$i]['us_color']=$rrd[2];
	    $rrddata[$i]['ds_color']=$rrd[3];	    
		$i++;
	}	
}

$jsonArray = array('graph' => array('title' => $title,'type' => 'line','yAxis' => array('units' => array('suffix' => ' '.$unit)),'datasequences' => array()));

$i=0;
/* Parse RRD Data and create Array Structure */
foreach ($rrddata as $rrd_value) {

	$jsonArray['graph']['datasequences'][$i]['title']=$rrd_value['interface_name']." DS";
	$jsonArray['graph']['datasequences'][$i]['color']=$rrd_value['ds_color'];
	$jsonArray['graph']['datasequences'][$i]['refreshEveryNSeconds']=$time_refresh;
	$o=0;
	foreach ($rrd_value['data']['data']['ds0'] as $data_key => $data_value) {
		$jsonArray['graph']['datasequences'][$i]['datapoints'][$o]['title']=date('H:i', $data_key);
		$jsonArray['graph']['datasequences'][$i]['datapoints'][$o]['value']=unitConversion($data_value,$unit);
		$o++;
	}
	$i++;
	
	$jsonArray['graph']['datasequences'][$i]['title']=$rrd_value['interface_name']." US";
	$jsonArray['graph']['datasequences'][$i]['color']=$rrd_value['us_color'];
	$jsonArray['graph']['datasequences'][$i]['refreshEveryNSeconds']=$time_refresh;
	$o=0;
	foreach ($rrd_value['data']['data']['ds1'] as $data_key => $data_value) {
		$jsonArray['graph']['datasequences'][$i]['datapoints'][$o]['title']=date('H:i', $data_key);
		$jsonArray['graph']['datasequences'][$i]['datapoints'][$o]['value']=unitConversion($data_value,$unit);
		$o++;
	}
	$i++;

}

/* Encode the array to JSON and output it out */
echo json_encode($jsonArray);
?>
