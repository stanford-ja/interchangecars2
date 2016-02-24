<?php

?>
<div style="padding: 10px; margin; 0px; margin-left: 10%; margin-right: 10%;">
<h3>Using the Map</h3>
The previous version of the MRICF had a Google Map based map system. 
It was found that the Google Map based system was not reliable enough to show all waybills, and often required map locations to be exact due to it needing to convert a real location to a longitude and latitude coordinate. 
As a reult of the un-reliability of Google Maps for the MRICF, it was decided to try a customised map system, which is what this version of the MRICF has. 
Most functions on the map page require you to be logged in!
<br /><br />

<strong>State / Province rather than exact Location based</strong><br />
The map system on this version of the MRICF is State / Province based rather than exact location based, which makes it more reliable as long as a valid state / province code is included in the last portion of the Map Location for the latest progress report for a waybill. 
For states within the United States, only the State code is required. But for other regions, a country code is also required. 
Current Country Codes for locations outside the United States are:
<ul>
	<li>AUS - Australia</li>
	<li>CAN - Canada</li>
</ul>
To make a waybill appear in a state or province, the Map Location needs to be have the following at the end of the Map Location...<br /><br />
<span style="font-size: 14pt; font-weight: bold; color: red;">&nbsp;&nbsp;&nbsp;&nbsp;,[ST]-[CO]</span><br /><br />
... where <strong>[ST]</strong> is the State or Province, and <strong>[CO]</strong> is the Country code (where applicable). 
For valid state / province codes, go to the relevant map (using the region links on the Map page). 
The state / province codes are included on each region map for reference and as a way of . 
Note that the <strong>-[CO]</strong> is only required for locations outside the United States.<br /><br />
Here are some examples of locations that the MRICF map system will recognise:
<ul>
	<li>IHB BLUE ISLAND,IL - Will place the waybill in the list for Illinois (state code: IL) on the USA map.</li>
	<li>VICTORIA DOCKSIDE YARD,BC-CAN - This will place the waybill in the list for British Columbia (province code: BC) on the Canada map.</li>
	<li>HAMMERSLY IRON,WA-AUS - This will place the waybill in the list for Western Australia (state code: WA) on the Australia / NZ map.</li>
	<li>WHEREVER,GA - This will place the waybill in the list for Georgia (state code: GA) on the USA map even though the town of Wherever doesn't exist!</li>
</ul>
Following are some examples of locations that WILL NOT be recognised by the MRICF map system:
<ul>
	<li>RIVERDALE,IL,USA - The ,USA should be omitted as it is not needed.</li>
	<li>BLACKHORSE,YT,CAN - Should have used YT-CAN instead of YT,CAN.</li>
	<li>CICELY,ALASKA - ALASKA should be replaced with the state code AK.</li>
	<li>HOBART,TAS-AUST - The country code should be AUS not AUST.</li>
</ul>
Note that as long as there is a comma and the valid state / province and country code (where applicable) in the format indicated on this page the waybill will appear on the map in the state / province for region it belongs on. 
An exact recognisable Google Map location no longer need to be specified or implied!<br /><br />
<strong>Adding a Pin to a map</strong><br />
In the Map system it is possible to add a location pin to any map. 
To do this, use the New Pin form that appear on the right hand side of the map view. Select the region, and the coordinates where the pin will go. 
Use the grid system which appears on each map to calculate where a pin should be.
</div>
