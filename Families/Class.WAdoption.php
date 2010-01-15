<?php
include_once("FDL/Class.WDoc.php");


# for i18n
define ("i18n","i18n"); # N_("initialised") N_("transmited")  N_("accepted") N_("refused") N_("realised");
define ("initialised", "initialised");
define ("transmited", "transmited");
define ("accepted", "accepted");
define ("refused", "refused");
define ("realised","realised");

// transition name
# for i18n
define ("i18n","i18n"); #N_("Taccepted") N_("Ttransmited") N_("Tretry") N_("Trefused") N_("Trealised");
define ("Taccepted", "Taccepted");
define ("Ttransmited", "Ttransmited");
define ("Trefused", "Trefused");
define ("Tretry", "Tretry");
define ("Trealised", "Trealised");

Class WAdoption extends WDoc {
  var $attrPrefix="WAD";
  var $firstState=initialised;

  var $transitions=array( Ttransmited =>array("m1"=>"verifyvalidatormail",
						"m2"=>"sendTransmitedMail"),
			  Taccepted => array("m1"=>"",
					     "m2"=>"sendAcceptMail"),
			  Trefused =>array("m1"=>"notifyReject",
					   "m2"=>"sendRefusedMail",
					   "nr"=>true,
					   "ask"=>array("wad_refus")),
			  Trealised=>array("m1"=>"",
					   "m2"=>"sendRealisedMail"),			  
			  Tretry=>array("m1"=>"",
					"m2"=>"sendRetryMail"));

  var $cycle=array(array("e1"=>initialised,
			 "e2"=>transmited,
			 "t"=>Ttransmited),	  
		   array("e1"=>transmited,
			 "e2"=>accepted,
			 "t"=>Taccepted),
		   array("e1"=>transmited,
			 "e2"=>refused,
			 "t"=>Trefused),		   
		   array("e1"=>accepted,
			 "e2"=>realised,
			 "t"=>Trealised),
		   array("e1"=>transmited,
			 "e2"=>initialised,
			 "t"=>Tretry) );

  public $stateactivity=array("initialised"=>"adoption writting",
			      "transmited"=>"adoption verification"); # _("adoption writting") _("adoption verification")

  

  function verifyvalidatormail() {
    $to = $this->doc->GetRValue("DE_IDVAL:US_MAIL");
    if (! $to) return sprintf(_("no mail for validator"));
  }
  /**
   *
   */
  function sendTransmitedMail($newstate ) {
    if ($this->doc->getRValue("de_idespece:de_protegee")== "1") {
      $mt=new_doc($this->dbaccess,$this->getParamValue("WAD_MAILSECURE"));
    }  else {
      $mt=new_doc($this->dbaccess,$this->getParamValue("WAD_MAILCURRENT"));
      $this->sendTransmitedMail_detail($newstate);
    }
    if ($mt->isAlive()) {
      $err=$mt->sendDocument($this->doc);
    } else $err=_("no mail template");
    return $err;    
  }

  
  function sendTransmitedMail_detail($newstate ) {
    global $action;
    include_once("FDL/mailcard.php");
    $to = $this->doc->GetRValue("DE_IDVAL:US_MAIL");
    $cc="";
    if ($to=="") return sprintf(_("no mail for validator"));
    else {
      $subject=sprintf(_("adoption %s to validate"), $this->doc->title);
      sendCard($action,
	       $this->doc->id,
	       $to,$cc,$subject,"ZOO:DE_MAIL_TRANSMITED:S",true); 
    }
    return "";
  }
  function sendRetryMail($newstate ) {
    global $action;
    include_once("FDL/mailcard.php");
    $to = $this->doc->GetRValue("DE_IDREDAC:US_MAIL");
    $cc="";
    if ($to=="") return sprintf(_("no mail for redactor"));
    $subject=sprintf(_("adoption %s to modify"), $this->doc->title);
    sendCard($action,
             $this->doc->id,
             $to,$cc,$subject,"ZOO:DE_MAIL_RETRY:S",true);
    return "";
  }

  function sendAcceptMail($newstate ) {
    global $action;
    include_once("FDL/mailcard.php");
    $to = $this->doc->GetRValue("DE_IDREALISED:US_MAIL");
    if ($to=="") return sprintf(_("no mail for realisator"));
    $cc="";
    $subject=sprintf(_("adoption %s accepted"), $this->doc->title);
    sendCard($action,
             $this->doc->id,
             $to,$cc,$subject,"ZOO:DE_MAIL_ACCEPTED:S",true);
    return "";
  }

  function notifyReject() {
    $reason=$this->getValue("wad_refus");

    $this->doc->disableEditControl(); // no control here
    $this->doc->setValue("de_motif",$reason);
    $this->doc->modify();;
    $this->doc->enableEditControl();
  }

  function sendRefusedMail ($newstate ) {    
    $to = $this->doc->GetRValue("DE_IDDEMAND:US_MAIL");
    $cc="";

    $subject=sprintf(_("adoption %s refused"), $this->doc->title);  
    SetHttpVar("redirect_app","FDL");
    SetHttpVar("redirect_act","EDITMAIL&mail_to=$to&mzone=ZOO:DE_MAIL_REFUSED:S&mail_subject=$subject&mid=".$this->doc->id);
   
    return "";
  }


  function sendRealisedMail ($newstate ) {
    global $action;
    include_once("FDL/mailcard.php");
    $to = $this->doc->GetRValue("DE_IDVAL:US_MAIL");
    if ($to=="") return sprintf(_("no mail for validator"));
    $cc="";
    $subject=sprintf(_("adoption %s realised"), $this->doc->title);
    sendCard($action,
             $this->doc->id,
             $to,$cc,$subject,"ZOO:DE_MAIL_REALISED:S",true);
    return "";
  }

}
?>
