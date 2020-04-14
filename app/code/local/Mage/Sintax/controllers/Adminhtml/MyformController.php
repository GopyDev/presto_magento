<?php

class Mage_Sintax_Adminhtml_MyformController extends Mage_Adminhtml_Controller_Action
{
    public function listAction()
    {
        //$this->loadLayout()->renderLayout();
		// echo "hiii";
		echo $_GET["idr"];
		exit(0);
    }
	
	
	public function indexAction()
    {
        $this->loadLayout()->renderLayout();
    }
	
	public function reportmanageAction()
    {
        $this->loadLayout()->renderLayout();
    }
    
    public function postAction()
    {   
	 
	    $bbb = str_replace('|"',"\r\n",$_POST["datagth-".$_POST["getnumber"]]);
		$bbb = str_replace('|',"\r\n",$_POST["datagth-".$_POST["getnumber"]]);
	   
		 header('Content-Type: application/csv');
		 header('Content-Disposition: attachement; filename='.$_POST["namer"].'.csv');
		 echo  $bbb; exit();
	
    }
}