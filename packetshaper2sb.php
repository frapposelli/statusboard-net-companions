<?php
/*
** packetshaper2sb.php - BlueCoat PacketShaper to StatusBoard
** Fabio Rapposelli - fabio@rapposelli.org - http://about.me/frapposelli
**
** Converts PacketShaper SNMP output to a StatusBoard table
** Needs php5-snmp, php5-json and BlueCoat PacketShaper SNMP MIBs to work.
**
*/

/* How many results to fetch in the table */
$topN=4;

/* Customize with your PacketShaper IP/hostname and SNMP Community */
$PacketShaperIP="10.250.254.251";
$PacketShaperCommunity="public";

/* Name of the directory containing the images */
$imgDirectory="imgs";

/* Decide whether to exclude first-level folders from the results, if setting to true, also specify which folders are to be excluded */
$excludeFolders=true;
$folders=array('Social', 'AV', 'P2P', 'SystemCritical', 'Work', 'IPV6');

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

function formatBytes($bytes, $precision = 2) { 
    $units = array('B/s', 'KB/s', 'MB/s', 'GB/s', 'TB/s'); 
    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 

/* Just get the real output */
snmp_set_valueretrieval(SNMP_VALUE_PLAIN);

/* Do the locomotion! */
$snmpWalk=snmp2_real_walk($PacketShaperIP, $PacketShaperCommunity, "BLUECOAT-PACKETSHAPER-MIB::classCurrentRate");

/* Kill the globals */
unset($snmpWalk["BLUECOAT-PACKETSHAPER-MIB::classCurrentRate.1"]);
unset($snmpWalk["BLUECOAT-PACKETSHAPER-MIB::classCurrentRate.2"]);

/* Sort and reverse the array */
arsort($snmpWalk, SORT_NUMERIC);

$i=0;
foreach($snmpWalk as $key => $value) {

    if($i == $topN) { break; }

	$servicekey=explode(".", $key);
 	$service_array=explode("/", snmp2_get($PacketShaperIP, $PacketShaperCommunity, "BLUECOAT-PACKETSHAPER-MIB::classFullName.".$servicekey[1]));
	array_shift($service_array);
	
    if(!array_key_exists(2, $service_array) && in_array($service_array[1], $folders) && $excludeFolders) { continue; }
	
    $resultsArray[$i]['direction']=$service_array[0];
	if(array_key_exists(2, $service_array)) { $resultsArray[$i]['service']= $service_array[1]."/".$service_array[2]; } else { $resultsArray[$i]['service']=$service_array[1]; }
	/* convert to bytes per second and then format the output */
	$resultsArray[$i]['data']=FormatBytes(($value/8),0);     

    unset($service_array);
    unset($servicekey);

    $i++;
}


?>
<table id="projects">
<?php foreach($resultsArray as $key => $value) { ?>
	<tr>

<?php
$serviceName=explode("/", strtolower($value['service']));
$iconName=array_pop($serviceName);
if(!is_readable($imgDirectory."/".$iconName.".png")) { $icon=$imgDirectory."/servicedefault.png"; } else { $icon=$imgDirectory."/".$iconName.".png"; }
?>
		<td class="serviceIcon" style="width: 32px"><img src="<?php echo $icon; ?>"></td>
		<td class="serviceName"><?php if ($value['service'] == "Default") { echo "Unclassified Traffic"; } elseif ($value['service'] == "Localhost") { echo "Internal Traffic"; } else { echo $value['service']; } ?></td>
		<td class="serviceDirection" style="width: 26px"><img src="<?php echo $imgDirectory; ?>/<?php if ($value['direction'] == "Inbound") { echo "down.png"; } else { echo "up.png"; } ?>"></td>
		<td class="serviceRate" style="width: 130px"><?php echo $value['data']; ?></td>
	</tr>
<?php } ?>
</table>


