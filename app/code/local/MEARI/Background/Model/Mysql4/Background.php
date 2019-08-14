<?php

class MEARI_Background_Model_Mysql4_Background extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the background_id refers to the key field in your database table.
        $this->_init('background/background', 'background_id');
    }
}