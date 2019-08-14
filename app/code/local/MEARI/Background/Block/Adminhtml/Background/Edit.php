<?php

class MEARI_Background_Block_Adminhtml_Background_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'background';
        $this->_controller = 'adminhtml_background';
        
        $this->_updateButton('save', 'label', Mage::helper('background')->__('Save Image'));
        $this->_updateButton('delete', 'label', Mage::helper('background')->__('Delete Image'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('background_description') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'background_description');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'background_description');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('background_data') && Mage::registry('background_data')->getId() ) {
            return Mage::helper('background')->__("Edit Image '%s'", $this->htmlEscape(Mage::registry('background_data')->getTitle()));
        } else {
            return Mage::helper('background')->__('Add Image');
        }
    }
}