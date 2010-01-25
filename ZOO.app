<?php
// ---------------------------------------------------------------
// $Id: ZOO.app,v 1.2 2010-01-25 13:45:25 eric Exp $
// $Source: /home/cvsroot/anakeen/addons/zoo3/ZOO.app,v $


$app_desc = array (
		   "name"	 =>"ZOO",		//Name
		   "short_name"	=>N_("Zoo"),    	//Short name
		   "description"=>N_("Zoo formation"),  //long description
		   "access_free"=>"N",			//Access free ? (Y,N)
		   "icon"	=>"zoo.png",	//Icon
		   "displayable"=>"Y",			//Should be displayed on an app list (Y,N)
		   "with_frame"	=>"Y",			//Use multiframe ? (Y,N)
		   "childof"	=>"ONEFAM"		        // instance of FREEDOM GENERIC application	
		   );

  
$app_acl = array (
  array(
   "name"               =>"ZOO_MONEY",
   "description"        =>N_("Access to ticket sales"))
  
);

$action_desc = array (
  array( 
   "name"		=>"ZOO_TICKETSALES",
   "short_name"		=>N_("sum of sales"),
   "acl"		=>"ZOO_MONEY"),
  array( 
   "name"		=>"ZOO_TEXTTICKETSALES",
   "short_name"		=>N_("text sum of sales"),
   "script"             =>"zoo_ticketsales.php",
   "function"           =>"zoo_ticketsales",
   "acl"		=>"ZOO_MONEY"),
  array( 
   "name"		=>"ZOO_XMLTICKETSALES",
   "short_name"		=>N_("xml sum of sales"),
   "script"             =>"zoo_ticketsales.php",
   "function"           =>"zoo_ticketsales",
   "acl"		=>"ZOO_MONEY"),

  array( 
   "name"		=>"ZOO_ANIMALFOLDER",
   "short_name"		=>N_("animal folder"),
   "acl"		=>"ONEFAM"  ),
  array( 
   "name"		=>"ZOO_COLOR",
   "short_name"		=>N_("table colors"),
   "acl"		=>"ONEFAM_READ"  ),
  array( 
   "name"		=>"ZOO_ROOT",
   "short_name"		=>N_("entrance"),
   "acl"		=>"ONEFAM_READ",
   "root"             => "Y"  ),
array( 		      
   "name"		=>"ONEFAM_ROOT",
   "root"             => "N"  )
)

		
?>
