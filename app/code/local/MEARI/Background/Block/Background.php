<?php
class MEARI_Background_Block_Background extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getBackground()     
     { 
        if (!$this->hasData('background')) {
            $this->setData('background', Mage::registry('background'));
        }
        return $this->getData('background');
        
    }
}