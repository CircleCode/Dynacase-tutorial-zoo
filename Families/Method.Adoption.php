<?php
/**
 * Adoption comportment
 *
 * @author Anakeen 2010
 * @version $Id: Method.Adoption.php,v 1.3 2010-09-03 07:07:12 eric Exp $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package freedom-zoo
 */
/**
 */

/**
 * @begin-method-ignore
 * this part will be deleted when construct document class until end-method-ignore
 */
Class _ADOPTION extends Doc {
	/*
	 * @end-method-ignore
	 */
function postCreated() {
  if ($this->revision==0) {   
    $err=$this->setReference();
  }
  return $err;
  }

/**
 * set unique reference
 */
function setReference() {
  $this->setValue("de_reference",$this->getCurSequence());
  $err=$this->modify();
  
  return $err;
  
}
/**
 * constraint to verify entrance date and birth date
 */
function verifyDate($date) {
  $t1=FrenchDateToUnixTs($date);
  
  if ($t1 > time()) $err=_("birthday date must be set before today");
  if ($err!="") $sug[]=$this->getDate();
  return array("err"=>$err,
	       "sug"=>$sug);
  
}


function de_mail_transmitted() {
  include_once("FDL/Class.SearchDoc.php");  
  
  $s=new SearchDoc($this->dbaccess,"ZOO_ANIMAL");
  $s->addFilter(sprintf("an_espece = '%d'",$this->getValue("de_idespece")));
  $t=$s->search();

  $this->lay->setBlockData("ANIMALS",$t);		  
  $this->viewdefaultcard();  
}
/**
	* @begin-method-ignore
	* this part will be deleted when construct document class until end-method-ignore
	*/
}
/*
 * @end-method-ignore
 */
?>