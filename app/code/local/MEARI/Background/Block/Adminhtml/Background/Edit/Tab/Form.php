<?php

class MEARI_Background_Block_Adminhtml_Background_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('background_form', array('legend'=>Mage::helper('background')->__('Image information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('background')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'image', array(
          'label'     => Mage::helper('background')->__('Image'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('background')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('background')->__('Active'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('background')->__('Inactive'),
              ),
          ),
      ));
     
      $fieldset->addField('description', 'editor', array(
          'name'      => 'description',
          'label'     => Mage::helper('background')->__('Description'),
          'title'     => Mage::helper('background')->__('Description'),
          'style'     => 'width:700px; height:250px;',
          'wysiwyg'   => false,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getBackgroundData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getBackgroundData());
          Mage::getSingleton('adminhtml/session')->setBackgroundData(null);
      } elseif ( Mage::registry('background_data') ) {
          $form->setValues(Mage::registry('background_data')->getData());
      }
      return parent::_prepareForm();
  }
}