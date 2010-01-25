<?php
include_once("FDL/Class.WDoc.php");




Class WAdoption extends WDoc {
  public $attrPrefix="WAD";
  const initialised="initialised"; # N_("initialised")
  const transmited="transmited"; # N_("transmited")
  const accepted="accepted"; # N_("accepted")
  const refused="refused"; # N_("refused")
  const realised="realised"; # N_("realised")

  const Ttransmited="Ttransmited"; # N_("Ttransmited")
  const Taccepted="Taccepted"; # N_("Taccepted")
  const Trefused="Trefused"; # N_("Trefused")
  const Tretry="Tretry"; # N_("Tretry")
  const Trealised="Trealised"; # N_("Trealised")

  public $firstState=self::initialised;

  public $transitions=array( self::Ttransmited =>array("m1"=>"verifyvalidatormail",
						       "m2"=>"sendTransmitedMail"),
			     self::Taccepted => array("m1"=>"",
						      "m2"=>"sendAcceptMail"),
			     self::Trefused =>array("m1"=>"notifyReject",
						    "m2"=>"sendRefusedMail",
						    "nr"=>true,
						    "ask"=>array("wad_refus")),
			     self::Trealised=>array("m1"=>"",
						    "m2"=>"createAnimal"),			  
			     self::Tretry =>array("m1"=>"",
						  "m2"=>"sendRetryMail"));

  public $cycle=array(array("e1"=>self::initialised,
			    "e2"=>self::transmited,
			    "t"=>self::Ttransmited),	  
		      array("e1"=>self::transmited,
			    "e2"=>self::accepted,
			    "t"=>self::Taccepted),
		      array("e1"=>self::transmited,
			    "e2"=>self::refused,
			    "t"=>self::Trefused),		   
		      array("e1"=>self::accepted,
			    "e2"=>self::realised,
			    "t"=>self::Trealised),
		      array("e1"=>self::transmited,
			    "e2"=>self::initialised,
			    "t"=>self::Tretry) );

  public $stateactivity=array(self::initialised=>"adoption writting2",
			      self::refused=>"adoption refused",
			      self::transmited=>"adoption verification"); # _("adoption writting") _("adoption verification")



  public function verifyvalidatormail() {
    $to = $this->doc->GetRValue("DE_IDVAL:US_MAIL");
    if (! $to) return sprintf(_("no mail for validator"));
    return "";
  }
  /**
   *
   */
  public function sendTransmitedMail($newstate ) {
    $tkeys=array();
    if ($this->doc->getRValue("de_idespece:de_protegee")== "1") {
      $mt=new_doc($this->dbaccess,$this->getParamValue("WAD_MAILSECURE"));
    }  else {
      // get others animals
      include_once("FDL/Class.SearchDoc.php");
      $s=new SearchDoc($this->dbaccess,"ANIMAL");
      $s->addFilter(sprintf("an_espece ='%d'",$this->doc->getValue("de_idespece")));
      $t=$s->search();
      $tanimal=array();
      foreach ($t as $animal) $tanimal[]=$this->getDocAnchor($t["id"],"mail");
      $tkeys["animals"]=implode(", ",$tanimal);

      $mt=new_doc($this->dbaccess,$this->getParamValue("WAD_MAILCURRENT"));      
      // $this->sendTransmitedMail_detail($newstate);
    }
    if ($mt->isAlive()) {
      $err=$mt->sendDocument($this->doc,$tkeys);
    } else $err=_("no mail template");
    return $err;
  }


  public function sendTransmitedMail_detail($newstate ) {
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
  public function sendRetryMail($newstate ) {
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

  public function sendAcceptMail($newstate ) {
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

  public function notifyReject() {
    $reason=$this->getValue("wad_refus");

    $this->doc->disableEditControl(); // no control here
    $this->doc->setValue("de_motif",$reason);
    $err=$this->doc->modify();
    $this->doc->enableEditControl();
    return $err;
  }

  public function sendRefusedMail ($newstate ) {
    $to = $this->doc->GetRValue("DE_IDDEMAND:US_MAIL");
    $cc="";

    $subject=sprintf(_("adoption %s refused"), $this->doc->title);
    SetHttpVar("redirect_app","FDL");
    SetHttpVar("redirect_act","EDITMAIL&mail_to=$to&mzone=ZOO:DE_MAIL_REFUSED:S&mail_subject=$subject&mid=".$this->doc->id);
         
    return "";
  }



  public function sendRealisedMail ($newstate ) {
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

  public function createAnimal() {
    $ani=createDoc($this->dbaccess,"ANIMAL");
    if ($ani) {
      $ani->setValue("an_nom",$this->doc->getValue("de_nom"));
      $ani->setValue("an_espece",$this->doc->getValue("de_idespece"));
      $ani->setValue("an_naissance",$this->doc->getValue("de_naissance"));
      $ani->setValue("an_photo",$this->doc->getValue("de_photo"));
      $err=$ani->add();
      if ($err=="") {
	$ani->postModify();
	$ani->refresh(); 
	SetHttpVar("redirect_app","FDL");
	SetHttpVar("redirect_act","FDL_CARD&id=".$ani->id);
      }
      return $err;
    }
    return "";
  }
}
?>
