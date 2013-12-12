<?php

class Cammino_CepCorreios_Block_JavaScript extends Mage_Payment_Block_Form
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