<?php

class Cammino_Cepcorreios_Block_Javascript extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
    	$this->setTemplate('cepcorreios/javascript.phtml');
        parent::_construct();
    }

    public function getRoutName() {
    	return $this->getRequest()->getRouteName();
    }
}