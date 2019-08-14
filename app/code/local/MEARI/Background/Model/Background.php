<?php

class MEARI_Background_Model_Background extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('background/background');
    }
}