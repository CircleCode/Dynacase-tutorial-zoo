<?php
include_once("FDL/Class.WDoc.php");




Class WAdoption extends WDoc {
  public $attrPrefix="WAD";
  const initialised="zoo_initialised"; # _("zoo_initialised")
  const transmited="zoo_transmited"; # _("zoo_transmited")
  const accepted="zoo_accepted"; # _("zoo_accepted")
  const refused="zoo_refused"; # _("zoo_refused")
  const realised="zoo_realised"; # _("zoo_realised")

  const Ttransmited="zoo_Ttransmited"; # _("zoo_Ttransmited")
  const Taccepted="zoo_Taccepted"; # _("zoo_Taccepted")
  const Trefused="zoo_Trefused"; # _("zoo_Trefused")
  const Tretry="zoo_Tretry"; # _("zoo_Tretry")
  const Trealised="zoo_Trealised"; # _("zoo_Trealised")

  public $firstState=self::initialised;

  public $transitions=array( self::Ttransmited =>array("m1"=>"verifyvalidatormail",
						       "m2"=>"sendTransmitedMail"),
			     self::Taccepted => array("m1"=>"",
						      "m2"=>"sendAcceptMail"),
			     self::Trefused =>array("m1"=>"notifyReject",
						    "m2"=>"sendRefusedMail",
						    "nr"=>true,
						    "ask"=>array("wad_refus")),
			     self::Trealised=>array("m1"=>"verifyEnclosDispo",
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

  public $stateactivity=array(self::initialised=>"zoo_adoption writting",
			      self::transmited=>"zoo_adoption verification"); # _("zoo_adoption writting") _("zoo_adoption verification")



  public function verifyvalidatormail() {
    $idval=$this->doc->GetValue("DE_IDVAL");
    if (! $idval) return sprintf(_("zoo:no validator defined"));
    $to = $this->doc->GetRValue("DE_IDVAL:US_MAIL");
    if (! $to) return sprintf(_("zoo:no mail for validator"));
    return "";
  }
  /**
   *
   */
  public function sendTransmitedMail($newstate ) {
    $tkeys=array();
    if ($this->doc->getRValue("de_idespece:de_protegee")== "1") {
      // get others animals
      include_once("FDL/Class.SearchDoc.php");
      $s=new SearchDoc($this->dbaccess,"ZOO_ANIMAL");
      $s->addFilter(sprintf("an_espece ='%d'",$this->doc->getValue("de_idespece")));
      $t=$s->search();
      $tanimal=array();
      foreach ($t as $animal) $tanimal[]=$this->getDocAnchor($t["id"],"mail");
      $tkeys["animals"]=implode(", ",$tanimal);
      $mt=new_doc($this->dbaccess,$this->getParamValue("WAD_MAILSECURE"));
    }  else {
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

  public function verifyEnclosDispo() {
    $ani=createDoc($this->dbaccess,"ZOO_ANIMAL",true);
    $err="";
    if ($ani) {
      $ani->setValue("an_espece",$this->doc->getValue("de_idespece"));
      $err=$ani->verifyCapacity();      
    } else {
      return _("zoo:Cannot create animal");
    }
    return $err;
  }

  public function createAnimal() {
    $ani=createDoc($this->dbaccess,"ZOO_ANIMAL",true);
    if ($ani) {
      $ani->setValue("an_nom",$this->doc->getValue("de_nom"));
      $ani->setValue("an_espece",$this->doc->getValue("de_idespece"));
      $ani->setValue("an_naissance",$this->doc->getValue("de_naissance"));
      $ani->setValue("an_photo",$this->doc->getValue("de_photo"));
      $err=$ani->add();
      if ($err=="") {
	$ani->postModify();
	$ani->refresh(); 
	$ani->addComment(sprintf(_("Creation from adoption %s [%d]"),$this->doc->getTitle(),$this->doc->id));
				   
	SetHttpVar("redirect_app","FDL");
	SetHttpVar("redirect_act","FDL_CARD&id=".$ani->id);
      }
      return $err;
    }
    return "";
  }
}
?>
