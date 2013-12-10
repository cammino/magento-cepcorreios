<?php

class Cammino_CepCorreios_Block_JavaScript extends Mage_Core_Block_Text
{
    protected function _construct()
    {
        parent::_construct();
    }
    
    protected function _toHtml()
    {
    	return "<script type=\"text/javascript\">alert('test');</script>";
    }
}