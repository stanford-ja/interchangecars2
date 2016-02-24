<?php

?>
<div style="padding: 10px; margin; 0px; margin-left: 10%; margin-right: 10%;">
<h3>MRICF V2.0 Information</h3>
MRICF is an acronym for <strong>Model Rail Interchange Car Forwarding</strong>. 
It is a model railroad virtual freight and cars forwarding application with Waybills, Industries, Train Sheets, Rollingstock management and more, and has been developed to help model railroaders virtually interchange / forward cars and freight between their model railroad and other model railroads. 
The main module of the application is the Waybill management module which allows waybills to be tracked and actioned with minimal effort allowing more time to enjoy the operational aspects of model railroading. Here is an example of how Virtual Car Forwarding works:
<br /><br />
Lets imagine there are two layouts. The originating layout is the Buffalo Terminal RR (BIX) based on the
Buffalo, NY area. The destination layout is the Dolton Industrial Park Railroad (DIRT),
based on the area around Dolton, IL. The type of car is a standard boxcar, carrying
crates of electrical goods. The route to get the car from the BIX to the DIRT could be BIX-
CP-BIX. In this example the first and last railroads are real layouts, the rest of the railroads
in the route (although actual prototype railroads) are only 'virtual' railroads in the sense of moving
the car to it's destination. A waybill would be created, and communicated via the internet (eg, by this application) 
to both railroads and any other modeller's railroads that might be 
enroute that would also handle the car with details of the shipment – in this example, only the BIX
and DIRT would need the waybill. The operator of the BIX layout would move the car to the
originating industry on his layout in a train so it can be loaded. The next operating session the
operator of the BIX layout may pick the car up, now loaded, and move it to the place of interchange
between his layout and the next railroad on the route – the Canadian Pacific (CP). Up until this
point the operations of moving the car have been real – the operator where the shipment is originating has operated a train over his
layout, picked up the car, and moved the car in that train and any connecting trains to the
interchange point with the CP. Now the 'virtual' interchanging process really starts. The operator of
the originating layout 'moves' the car through various interchange points by informing the
destination layout of where the car and it's cargo are at important waypoints in it's journey. 
In this example, that could be Buffalo, NY; Detroit, MI; Blue Island,IL. 
This information could be posted as a progress report in the application which could include the location and status of the car. 
When the car arrives at the interchange location for the DIRT layout (Blue Island,IL), the operator of the DIRT is notified and the waybill is allocated to DIRT. 
At this point in the process the DIRT would pick up the car from the specified interchange location by 
a train on his layout and move it to the destination industry where the car would be tagged as being
unloaded via a status report in the application. 
Once the car is unloaded a train on the DIRT then picks up the car and sends it back to
the BIX empty or backloaded with other freight. This is a fairly simple process if there are only 2
modeller's involved and none of the intermediate railroads are operated by modellers in a virtual
interchange group, but when there are more than 2 modeller's railroads involved it can get quite
confusing, and this is where the MRICF helps minimise the confusion that may result when multiple layouts are involved in the car forwarding process. 

<br /><br />
To use this application you need to become a member of the following two Yahoo! groups:
<ul>
	<li><a href="http://groups.yahoo.com/neo/groups/virtual_ops" target="_blank">Virtual Car Forwarding & Interchange Ops 1</a></li>
	<li><a href="http://groups.yahoo.com/neo/groups/MRICC" target="_blank"></a>Virtual Car Forwarding & Interchange Ops 2</li>
</ul>
... and then once your membership in both groups is approved, send the MRICC2 group owner an email with your railroad details so your railroad can be set up in the MRICF application.
<br /><br />
This application is built in PHP and uses SQL databases, AJAX / JQuery, and Javascript languages. 
It is operating system independent, and is compatible with most of the current browsers. 
<br /><br /> 
It is built using an MVC (Model View Controller) framework called Code Igniter, which offered the flexibility needed to build the various functions needed by users, while still providing certain 'standards' for views.
Most of the views were relatively easy to build in CodeIgniter, but the Waybill edit view was a bit of a mongrel to convert due to it's complexity!  
<br /><br /> 
Coding the first version of the MRICF commenced in Jun 2009, with vast improvements and some very complicated functions being added over the years since. 
This version (V2.x) was designed to be a replacement for previous version with coding commencing around Nov 2012.</div>
