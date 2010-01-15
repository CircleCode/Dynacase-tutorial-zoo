<?php
include_once("FDL/Class.WDoc.php");

define ("alive", "alive"); # N_("alive") N_("dead")  N_("transfered") N_("sick") N_("convalescent");
define ("dead", "dead");
define ("transfered", "transfered");
define ("sick", "sick");
define ("convalescent","convalescent");#N_("Tsick") N_("Tconvalescent") 
Class WAnimal extends WDoc {
  var $attrPrefix="WAN";
  var $firstState=alive;
  var $transitions=array(
			 "T1"=>array(),
			 "Tsick"=>array("m1"=>"SendMailToVeto",
				     "ask"=>array("wan_idveto","wan_veto"),
				     "nr"=>true),
			 "Tconvalescent"=>array("m1"=>"toHealthCard"),
			 "T3"=>array("m1"=>"A2")


			 );

  var $cycle=array(
		   array(  "e1"=>alive,
			   "e2"=>sick,
			   "t"=>"Tsick" ),

		   array(  "e1"=>alive,
			   "e2"=>transfered,
			   "t"=>"T1" ),

		   array(  "e1"=>convalescent,
			   "e2"=>dead,
			   "t"=>"T1" ),

		   array(  "e1"=>sick,
			   "e2"=>convalescent,
			   "t"=>"Tconvalescent" ),

		   array(  "e1"=>convalescent,
			   "e2"=>alive,
			   "t"=>"T1" ),

		   array(  "e1"=>sick,
			   "e2"=>dead,
			   "t"=>"T3" )

		   );


  function SendMailToVeto($newstate) {
    global $action;
    include_once("FDL/mailcard.php");
                                                                                                     
    $subject=sprintf(_("Disease of %s "),$this->doc->title);
    $zone="ZOO:DE_MAIL_SICK:S";
                                      
    $to = $this->GetRValue("WAN_IDVETO:US_MAIL");
    $cc="";
    if (!$this->doc->getHealthCardId()) $err=$this->doc->createHealthCard();

    if ($err!="") return $err;
    SetHttpVar("redirect_app","FDL");
    SetHttpVar("redirect_act","CONFIRMMAIL&state=$newstate&ulink=Y&mzone=$zone&mail_from=$from&mail_to=$to&mail_format=html&mail_subject=$subject&mid=".$this->doc->id);

    return "->";
  }

  function toHealthCard($newstate)  {
    global $action;
    include_once("FDL/mailcard.php");
    include_once("FDL/Lib.Dir.php");
    $idcarnet=$this->doc->getHealthCardId();
    
    $carnet= new_Doc($this->dbaccess,$idcarnet);
    $err=$this->doc->canEdit();
    if ($err=="") {
      SetHttpVar("redirect_app","GENERIC");
      SetHttpVar("redirect_act","GENERIC_EDIT&id=".$idcarnet);
    } else {
      SetHttpVar("redirect_app","FDL");
      SetHttpVar("redirect_act","FDL_CARD&id=".$idcarnet);      
    }
    return "";
  }

  function A2($newstate)
  {
    SetHttpVar("redirect_app","TICKET");
    SetHttpVar("redirect_act","DMEANDIED&state=$newstate&id=".$this->doc->id);
    return "->";
  }

}//class
?>
