# Network-minded Companions for [Panic][PanicLink]'s [StatusBoard][StatusBoardLink].

## What's in the box

- sm2sb.php - Smokeping to [StatusBoard][StatusBoardLink], show Smokeping latest latency status on a Bar Graph
- mrtg2sb.php - MRTG to [StatusBoard][StatusBoardLink], show Upstream and Downstream traffic on a Line Graph
- More to come...

## What it looks like

![My StatusBoard Dash][StatusBoardPic]

## What you need to run it

- A fairly recent PHP (tested with 5.4).
- php-rrd and php-json modules (test with php -m).
- A web server to host the script or the resulting .json file.

## What is missing

- The TOP4 widget at the top, coming in the next weeks

[PanicLink]: http://www.panic.com
[StatusBoardLink]: http://www.panic.com/statusboard
[StatusBoardPic]: https://dl.dropboxusercontent.com/u/57053024/screens/statusboard.PNG  "My StatusBoard Dash"