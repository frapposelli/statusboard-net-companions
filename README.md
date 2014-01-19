# Network-minded Companions for [Panic][PanicLink]'s [StatusBoard][StatusBoardLink].

## What's in the box

- sm2sb.php - Smokeping to [StatusBoard][StatusBoardLink], show Smokeping latest latency status on a Bar Graph.
- mrtg2sb.php - MRTG to [StatusBoard][StatusBoardLink], show Upstream and Downstream traffic on a Line Graph.
- packetshaper2sb.php - [PacketShaper][PacketShaperLink] to [StatusBoard][StatusBoardLink], show topN bandwidth consumer in a Table.
- More to come...

## What it looks like

![My StatusBoard Dash][StatusBoardPic]

More info on my WAN setup can be found here: <http://p2v.it/misc/show-your-network-stats-on-statusboard/>

## What you need to run it

- A fairly recent PHP (tested with 5.4).
- php-rrd, php-json and php-snmp modules (test with php -m).
- BlueCoat SNMP MIBs (only for packetshaper2sb.php).
- A web server to host the script or the resulting .json or html file.

## What is missing

- Working on more widgets: Cisco IOS IPS status, Cisco Call Manager Express call status and more...

[PanicLink]: http://www.panic.com
[StatusBoardLink]: http://www.panic.com/statusboard
[PacketShaperLink]: http://www.bluecoat.com/products/packetshaper
[StatusBoardPic]: https://dl.dropboxusercontent.com/u/57053024/screens/statusboard.PNG  "My StatusBoard Dash"

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/frapposelli/statusboard-net-companions/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
