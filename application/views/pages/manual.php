<?php

?>
<div style="padding: 10px; margin; 0px; margin-left: 10%; margin-right: 10%;">
<h1>MRICF Manual</h1>
<ul>
	<li><a href="javascript:{}" onclick="document.getElementById('active_info').innerHTML = document.getElementById('whatis').innerHTML;">What is the MRICF?</a></li>
	<li><a href="javascript:{}" onclick="document.getElementById('active_info').innerHTML = document.getElementById('login').innerHTML;">Logging In</a></li>
	<li><a href="javascript:{}" onclick="document.getElementById('active_info').innerHTML = document.getElementById('setup').innerHTML;">Setting up your data</a></li>
	<li><a href="javascript:{}" onclick="document.getElementById('active_info').innerHTML = document.getElementById('rr').innerHTML;">Railroad Information and Settings</a></li>
	<li><a href="javascript:{}" onclick="document.getElementById('active_info').innerHTML = document.getElementById('indust').innerHTML;">Industries</a></li>
	<li><a href="javascript:{}" onclick="document.getElementById('active_info').innerHTML = document.getElementById('train').innerHTML;">Trains</a></li>
	<li><a href="javascript:{}" onclick="document.getElementById('active_info').innerHTML = document.getElementById('waybill').innerHTML;">Waybills</a></li>

	<!--
	<li><a href="javascript:{}" onclick="showEle('login');">Logging In</a></li>
	<li><a href="javascript:{}" onclick="showEle('setup');">Setting up your data</a></li>
	<li><a href="javascript:{}" onclick="showEle('rr');">Railroad Information and Settings</a></li>
	<li><a href="javascript:{}" onclick="showEle('indust');">Industries</a></li>
	<li><a href="javascript:{}" onclick="showEle('train');">Trains</a></li>
	// -->
	<!--
	<li><a href="javascript:{}" onclick="showEle('whatis');">[name]</a></li>
	<li><a href="javascript:{}" onclick="document.getElementById('active_info').innerHTML = document.getElementById('whatis').innerHTML;">[name]</a></li>
	// -->
</ul>
<hr />

<!-- START MANUAL INFORMATION DIVS // -->

<div id="active_info"></div>

<div id="whatis" style="display: none;">
<span style="float: right; display: none;">&nbsp;<a href="javascript:{}" onclick="hideEle('whatis');">Hide</a>&nbsp;</span>
<h2>What is the MRICF?</h2>
This manual relates to MRICF v2. MRICF is an accronym for Model Rail Interchange Car Forwarding.<br /><br />
<h2>Why was it built?</h2>
When James Stanford (the application developer), became the group owner in 2009 he decided to build an application to manage his own waybills. 
Originally he built a FreeBASIC application which used various data files to manage the different aspects of waybill management. 
After a computer crash which resulted in most of the code and data for the FreeBASIC program being lost, he decided to build a web based application instead.<br /><br />
After becoming group owner, the application developer opened up access to the MRICF to all group members. 
While he did all the coding for the application himself, other members of the group acted in an advisory capacity, providing a lot of interesting ideas many of which were implemented into the application. 
So the fact that the application exists and is widely used by members is testament to the team work of all the members who offer suggestions and ideas, and the implementation of those ideas into the application by the application developer.
<h2>How does it work?</h2>
It is a web based application which uses HTML, Javascript, AJAX, PHP and MySQL web
technologies. The MySQL database system handles all the data exchange, while the HTML, Javascript,
PHP and AJAX are used to display the relevant interfaces for the user to interact with the data. The
application is built in a modular design, which makes troubleshooting the code easier and also often
makes the implementation of new functions quicker .The application is located at:
<a href="http://jstan2.pairserver.com/apps/interchangecars2">http://jstan2.pairserver.com/apps/interchangecars2</a> . 
The password to access the application is dependent on the railroad being logged in as.
<h2>What if something doesn't work?</h2>
As the application is internet based and is available through any standards compliant web browser,
most problems relating to being able to access the application are because of settings in the user's
browser. The most common problem experienced is not being able to log in, which is normally because
of a saved password in the browser which the application is is thinking applies to it. The reason why
this happens is because the password field identifier is a common identifier, which may be used by
other web sites. If you experience problems logging in, try re-typing the password for the application.
If that still doesn't allow you to log in you may need to clear out your temporary internet files /
browsing history, and saved passwords.<br /><br />
Another common problem is that certain functions wont work. This is often due to the browsers
settings blocking that type of web request, or that the browser being used does not have any support for
the function.<br /><br />
If you have come across a problem with the application that seems like a program bug, please contact
the application developer through the Virtual Ops group at <a href="http://groups.yahoo.com/group/virtual_ops" target="_blank">http://groups.yahoo.com/group/virtual_ops</a>
or by direct email to <a href="mailto:james@stanfordhosting.net">james@stanfordhosting.net</a> .
<h2>What if I don't understand how to use the MRICF even after reading this manual?</h2>
Don't worry! The Virtual Ops group at <a href="http://groups.yahoo.com/group/virtual_ops" target="_blank">http://groups.yahoo.com/group/virtual_ops</a> is available to post
questions regarding the MRICF, the general concepts of virtual interchanging, interchanging protocol,
etc.
<h2>What do I need to know before I use the MRICF?</h2>
A basic understanding of how real railroads and model model railroads use waybills for the
management of the shipment of freight is a definite advantage. Basic web surfing skills are essential.
Apart from those two things, there is not really much else you need to know that you won't be able to
learn through posting interchange related questions to the Virtual Ops groups at <a href="<a href="http://groups.yahoo.com/group/virtual_ops" target="_blank">">http://groups.yahoo.com/group/virtual_ops</a> .
<h2>Terms of Use</h2>
Use of the application is governed by the MRICC Yahoo group. Before a user can log in to the
application, they must join the MRICC Yahoo Group and send details of their railroad to Virtual Ops group. 
The MRICF administrator will then set up the railroad and issue a password (which the user can change later).
<h2>System Requirements</h2>
The MRICF tested successfully in most versions of IE, Firefox, Google Chrome and Safari or equivalent browsers. 
If you find some functions do not work properly on your browser, try upgrading your browser to the latest
version. Some functions will not work with IE6 or earlier browsers!
</div>

<div id="login" style="display: none;">
<span style="float: right; display: none;">&nbsp;<a href="javascript:{}" onclick="hideEle('login');">Hide</a>&nbsp;</span>
<h2>Logging In</h2>
Access to most of the MRICF features are only available to members of the MRICC2 group. You will need to provide the name of your railroad, the reporting mark, your first and last name, your email address that is used in Yahoo groups, and a preferred password. If the password you provide is considered to be insecure then the Owner may change the password to a similar password and will notify you if that was required. When the railroad has been set up in the MRICF you can then log in to the MRICF and use the many members only features.
<br /><br />
To log in, click the <strong>Login</strong> link in the top right hand corner of the navigation menu on the Home page. If the Login link is not visible then it means you are already logged in.
<br /><br />
Once on the Login page, select your railroad's reporting mark from the Railroad selector, enter your railroad's password in the Password field, then click the Login button. If login is successful then you will see a display of waybills according to how your railroad is set to display waybills. Clicking on any other link in the menu will display information related to your railroad, or all information if the link you click is set to display all information. You will also have access to edit data related to your railroad.
</div>

<div id="setup" style="display: none;">
<span style="float: right; display: none;">&nbsp;<a href="javascript:{}" onclick="hideEle('setup');">Hide</a>&nbsp;</span>
<h2>Setting up your data</h2>
To access the Railroad information and settings as an editor you will need to logged in as your railroad. Access to most of the MRICF features are only available to members of the MRICC2 group. You will need to provide the name of your railroad, the reporting mark, your first and last name, and a preferred password. If the password you provide is considered to be insecure then the Owner may change the password to a similar password and will notify you if that was required. When the railroad has been set up in the MRICF you can then log in to the 
<br /><br />
Before you can use the MRICF effectively the following information needs to be set up:
<ul>
<li>Railroad information and settings.</li>
<li>Industries on your railroad (see the Industries section).</li>
<li>Trains that run on your railroad (see the Trains section).</li>
</ul>
See the relevant sections of this manual for more information on how to set up your data in the MRICF. 
</div>

<div id="rr" style="display: none;">
<span style="float: right; display: none;">&nbsp;<a href="javascript:{}" onclick="hideEle('rr');">Hide</a>&nbsp;</span>
<h2>Railroad Information and Settings</h2>
To access the railroad information, you need to be logged into the application (see Logging In in this manual).
<br /><br />
Click the <strong>Railroads</strong> box at the top of the browser window on the Home view, and the railroad list will be expanded. Find your railroad, and click the Edit link.
<br /><br />
The Railroad Edit view has the following fields:
<ul>
<li><strong>Report Mark</strong> - This is the acronym for your railroad. Ideally it should be no more than 5 characters and be comprised of letters an the ampersand (&amp;) symbol only. It should be unique. Note that the reporting mark is changeable but it is recommended that you not change it unless absolutely necessary as this may affect which waybills display on your railroad's home page view.</li>
<li><strong>RR Name</strong> - This is the full title of your railroad.</li>
<li><strong>Owner Name</strong> - This should be your name.</li>
<li><strong>Description</strong> - This is a detailed description of your railroad and may use images, and various other HTML elements.</li>
<li><strong>Interchanges</strong> - This is a semi-colon (;) delimited list of interchanges that your railroad serves. Eg, <em>BARR YARD, IL (B&OCT);CICERO, IL (BN)</em>. The data in this list is used to populate various selectors elsewhere in the application, especially in relation to waybill management.</li>
<li><strong>Affiliates</strong> - This is a semi-colon (;) delimited list of reporting marks of other railroads that are also owned or controlled by you in the MRICF. If you have more the one railroad in the MRICF you can switch between them more easily if you enter the reporting marks of those railroads in this field. Eg, DIRT;BIX;ILLX;PN.</li>
<li><strong>Website</strong> - The URL of the website for your railroad. Be sure to include the 'http://' portion of the URL at the start. It is recommended that this be a website controlled by you, but if you model a prototype railroad rather than freelanced railroad and don't have a website for your railroad you could use the address of a website about that railroad here. Note that entering a website URL that is designed primarily to sell items (whether railroad related or not), or is considered to be spam related or otherwise illegal or suspect by the group Owner may result in suspension of MRICC2 group membership and use of the MRICF! If you are not sure whether the website URL you want to use in this field is allowed please ask the group owner for clarification.</li>
<li><strong>Social Website Links</strong> - A semi-colon (;) delimited list of URLs for Social media profile / page links that below to you or your railroad. Using profile URLs that do not belong to you or that you do not have control of may result in suspesion of MRICC2 group membership use of the MRICF.</li>
<li><strong>Password</strong> - Password that is used to log in to the application. If you change this be sure to remember what you changed it to. If you do forget your password you can request a new one from the MRICC2 group owner.</li>
<li><strong>Show Allocated to Only</strong> - If this is set to 'Yesy' then only waybills currently allocated to the railroad you are logged in to will display in the Home view.</li>
<li><strong>Show Generated Loads</strong> - [desc]</li>
<li><strong>Show Affiliates WBs</strong> - [desc]</li>
<li><strong>Hide Waybills in Auto Trains</strong> - [desc]</li>
<li><strong>Common Flag</strong> - The MRICF has a check built in to it to disable railroads that have not had any activity for a long time. Setting this flag to Yes means it will be ignored by this unused railroad check.</li>
<li><strong>Quick Select</strong> - Currently not used.</li>
<li><strong>Timezone</strong> - The timezone used by your railroad.</li>
<li><strong>Use TZ Time</strong> - If set to Yes will use the timezone setting above when entering progress reports in the Waybill editor, otherwise it uses Chicago, IL, USA time.</li>
<li><strong>Admin Flag</strong> - Normally set to No.</li>
<li><strong>Custom Styles</strong> - A field that allows custom styles to be applied to your Home view. These styles can be edited by clicking the "Edit Styles &amp; Views</li>
</ul>
Click the 'Update' button at the bottom of the form to update informtion you have entered / changed. 
</div>

<div id="indust" style="display: none;">
<span style="float: right; display: none;">&nbsp;<a href="javascript:{}" onclick="hideEle('indust');">Hide</a>&nbsp;</span>
<h2>Industries</h2>
Industries are customers for a railroad. You can set up industries for your railroad which can then receive commodities from other industries and / or send commodities to other industries. To view a list of your current industries, click the Industries link in the Navigation Menu. For full access to this section you need to be logged in.
<br /><br />
To edit an existing industry click the <strong>Edit</strong> link in the Options column of the list for the industry you want to edit. To create a new industry, click the <strong>New</strong> link located at the top of the list.
<br /><br />
The Industry Edit view has the following fields:
<ul>
<li><strong>Industry Name</strong> - The industry's name.</li>
<li><strong>Town</strong> - The town / suburb where the industry is located.</li>
<li><strong>Description</strong> - A detailed decription of the industry.</li>
<li><strong>Serving RR</strong> - The railroad that is served by the industry. This would normally be a railroad you control.</li>
<li><strong>Freight In</strong> - The commodities that the industry receives.</li>
<li><strong>Freight Out</strong> - The commodities that the industry sends.</li>
<li><strong>Railroad Operation Info</strong> - Information relating to railroad movements to the industry. This field can include the spur that is used, or which facility is used to deliver or pick up commodities to the industry. Eg, VIA TEAM TRACK or DELIVERY LIQUIDS TO TRK 1 AND CRATED GOODS TO TRK 2, etc.</li>
</ul>
To save the data entered, click the Update button at the bottom of the form.
<br /><br />
To <strong>delete</strong> the record, tick the Delete checkbox underneath the Update button, then click the Delete button that appears.
<br /><br />
The MRICF has the ability to automatically generate outbound commodities that would be manufactured by inbound commodities. 
To allow Generated Loads for a Commodity, the values in the Freight Out MUST be comma (,) separated and <strong>match exactly</strong> a semi-colon (;) separated value in the Commodities: Generates these Commods field.
</div>

<div id="train" style="display: none;">
<span style="float: right; display: none;">&nbsp;<a href="javascript:{}" onclick="hideEle('train');">Hide</a>&nbsp;</span>
<h2>Trains</h2>
Trains can be set up for any railroad. To view a list of current trains for the railroad you are logged into, click the Trains link in the Navigation Menu. For full access to this section you need to be logged in.
<br /><br />
To edit an existing train click the <strong>Edit</strong> link in the Options column of the list for the industry you want to edit. To create a new train, click the <strong>New</strong> link located at the top of the list.
<br /><br />
The Train Edit view has the following fields:
<ul>
<li><strong>Train ID</strong> - The train's symbol.</li>
<li><strong>Train Description</strong> - The longer description of the train.</li>
<li><strong>Max Cars</strong> - Maximum number of cars for this train.</li>
<li><strong>Operatiion Notes</strong> - An notes pertinent to the operating of the train. Can include switching instructions, interchange locations, intermediate stops, etc.</li>
<li><strong>Railroad</strong> - The railroad that 'owns' / operates the train.</li>
<li><strong>Locomotive</strong> - The locomotive allocated to the train. if more than one locomotive is the motive power specify only the one 'on the point'.</li>
<li><strong>Origin</strong> - Place where train starts it's journey. This can be either a real location, a fictional location, a railroad yard / siding / spur as long as it is meaningful to the railroad that operates the train.</li>
<li><strong>Destination</strong> - Place where train ends it's journey. This can be either a real location, a fictional location, a railroad yard / siding / spur as long as it is meaningful to the railroad that operates the train.</li>
<li><strong>Train Sheet Order / Depart Time</strong> - This can be a time or a number. It is suggested that all trains for a railroad have the same format so that the sorting of trains on the train sheets works properly.</li>
<li><strong>Days Operated on</strong> - Days the train is operated on. This is used to include / exclude a train from the Train Sheet for any day/s of the week.</li>
<li><strong>Days / Waypoints for Auto Train</strong> - If this field has anything in it then the train is treated as an Auto Train (Automatic Train). 
If this field has a number in it then the number is how many days the train will take to travel between two points. 
If the field has JSON array data (ie, not a normal number) then the train uses the JSON data to work out how long the train will take to get from one waypoint to another. 
It is best not to manually edit JSON data as if a mistake is made dueint manual editing then the Auto Train data for that train will not work at all. 
Use the <strong>Manage Auto Train Waybpoints</strong> link above the field to manage the various waypoints for the train if the data in the field is not a standard number.</li>
</ul>
To save the data entered, click the Update button at the bottom of the form.
<br /><br />
To <strong>delete</strong> the record, tick the Delete checkbox underneath the Update button, then click the Delete button that appears.
<br /><br />
The MRICF has the ability to display train sheets for any day of the week based on the Operation Day settings for trains. To view a train sheet, use the Train Sheets selector next to the New link at the top of the list. In the Train Sheet selector, 'no auto' means don't display Auto Trains, 'incl auto' means include Auto Trains in the train sheet.
</div>

<!-- END MANUAL INFORMATION DIVS // -->

</div>

<div id="waybill" style="display: none;">
<span style="float: right; display: none;">&nbsp;<a href="javascript:{}" onclick="hideEle('waybill');">Hide</a>&nbsp;</span>
<h2>Waybills</h2>
Waybills are the main reason the MRICF exists, and so the Waybills Edit view is where many of the other types of data found in the application are used to streamline the managing of waybills. 
The waybill view has been enhanced many times over the life of the application. 
The Waybill Edit view is used to <strong>create</strong> and <strong>edit</strong> waybills, and is accessible by clicking <strong>Edit WB</strong> link in the Home view, the <strong>Edit</strong> link for a waybill in the Switchlist view, and the <strong>New WB</strong> link in the application menu.<br /><br />
The Waybill Edit view has the following sections:
<ul>
	<li><strong>Heading</strong> - Has basic information about the waybill, and links to specific waybill actions and features.</li>
	<li><strong>Car Details</strong> - Car management takes place in this section.</li>
	<li><strong>Industries / Locations Details</strong> - Details relating to Industries is managed in this section.</li>
	<li><strong>Railroad Operation Details</strong> - How railroads action the waybill is found in this section.</li>
	<li><strong>Train Details</strong> - Allocation of the waybill to a specific train is done in this section.</li>
	<li><strong>Progress</strong> - Progress report entry, and progress history is in this section.</li>
</ul>
The Waybill Edit view has the following fields and data:
<ul>
	<li><strong>HEADING</strong></li>
	<ul>
		<li><strong>Waybill Type</strong> - Standard or Internal waybill types can be selected here. If not sure what type to choose select STANDARD.</li>
		<li><strong>Purchase Order</strong> - If you have a purhcase order number for the waybill you can enter it in this field.</li>
	</ul>

	<li><strong>CAR DETAILS</strong></li>
	<ul>
		<li><strong>Cars attached to waybill section</strong></li>
		<ul>
			<li><strong>Car</strong> - Car Number, including the Reporting Mark</li>
			<li><strong>AAR</strong> - The AAR Code for the car type. This can be left empty, but it is best to select an AAR type so that users know what sort of car to use when they are moving the car/s on the waybill.</li>
			<li><strong>Attach to RR</strong> - This is a list of your railroad/s. Select the Railroad that will move the Car entered.</li>
			<li><strong>Add button</strong> - When the Car, AAR and Attach to RR fields are correctly set, clicking this button will add the car details to the waybill. If you enter the car details but do not click the Add button then the car will not appear on the waybill after you submit the cahanges to the waybill! Cars that have been added to the waybill are shown in the <strong>Cars on waybill</strong> section.</li>
			<li><strong>Car Select</strong> - This allows you to select one of your cars. When selected the details will appear in the other fields in the section, then click the Add button to add the selected car to the waybill.</li>
		</ul>

		<li><strong>Car Search section</strong></li>
		<ul>
			<li><strong>Find Cars At</strong> - This field will display found cars as you type the location you are searching for. For best results, type as much of the town name as possible. Results will appear in the Cars Found list under the field as you type.</li>
		</ul>
	</ul>

	<li><strong>INDUSTRIES / LOCATIONS DETAILS</strong></li>
	<ul>
		<li><strong>Lading</strong> - The contents of the car/s on the waybill.</li>
		<li><strong>Origin</strong> - The Originating industry (or location) on the waybill.</li>
		<li><strong>Destination</strong> - The destination industry (or location) for the car/s on the waybill.</li>
		<li><strong>Return To</strong> - Where to return the car to after the lading on the waybill has been delivered to the destination industry / location.</li>
		<li><strong>Waybill Photos</strong> - Contains all photos uploaded pertaining to the waybill.</li>
	</ul>

	<li><strong>RAILROAD OPERATION DETAILS</strong></li>
	<ul>
		<li><strong>From</strong> - The From Railroad - normally this would be the railroad that serves the industry indicated in the Industries / Locations -> Origin field.</li>
		<li><strong>To</strong> - The To Railroad - normally this would be the railroad that serves the industry indicated in the Industries / Locations -> Destination field.</li>
		<li><strong>Assigned to</strong> - The railroad that the waybill is currently assigned to.</li>
		<li><strong>Routing</strong> - The route the car/s on the waybill will take to get to the destination. This should normally include the return (empty) journey as well, and would normally include the reporting marks of railroads that will action the waybill in the order they will action it.</li>
		<li><strong>Notes</strong> - Other notes not set in the other fields in this section of the waybill.</li>
	</ul>

	<li><strong>TRAIN DETAILS</strong></li>
	<ul>
		<li><strong>In / Allocated To</strong> - Field that indicates the train the waybill is currently allocated to. Can be used for searching for a train to allocate to the waybill to that train - to find a train enter a location the train will pass through, or part of the train identifier then click the Find button next to the field.</li>
	</ul>

	<li><strong>PROGRESS</strong></li>
	<ul>
		<li><strong>Date / Time</strong> - The Date / Time for the progress report being entered.</li>
		<!-- <li><strong>Express Save</strong> - This currently does not work as it was originally designed!</li> // -->
		<li><strong>Progress Description</strong> - A details description for the progress report being entered.</li>
		<li><strong>Map Location</strong> - The location the train will be at the time of the progress report. Should be in <strong>TOWN,STATE</strong> format. If the location is outside the USA the format should be <strong>TOWN,STATE,COUNTRY</strong>.</li>
		<li><strong>Status</strong> - The status of the waybill. This includes various standard statuses, and all the interchange locations as set in each railroad's Interchange settings.</li>
		<li><strong>Add Extra Progress Report Form link</strong> - This adds an extra Progress Report form under the last one, and allows more than one progress report per waybill edit session speeding up some waybill editing requirements.</li>
		<li><strong>Progress History</strong> - Under the Save Changes button, this displays all the progress history for the waybill, in descending date/time order.</li>
	</ul>
</ul>
</div>

<div style="display: none">
ELEMENT TEMPLATES:
Field name: <li><strong>[fld name]</strong> - [desc]</li>

</div>