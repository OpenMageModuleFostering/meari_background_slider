<?php

class MEARI_Background_Adminhtml_BackgroundController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('background/images')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Images'), Mage::helper('adminhtml')->__('Manage Images'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('background/background')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('background_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('background/images');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Images'), Mage::helper('adminhtml')->__('Manage Images'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('background/adminhtml_background_edit'))
				->_addLeft($this->getLayout()->createBlock('background/adminhtml_background_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('background')->__('Image does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	public function getMediaUrl() {
		$url=Mage::getBaseUrl('media');
		return substr($url,strpos($url,'media/'));
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			if(isset($data['filename']['delete']) && $data['filename']['delete']==1) {
				$data['filename']='';
			} else {
				if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
					$dirpath='meari/background/';
					$filename="";
					try {	
						/* Starting upload */	
						$uploader = new Varien_File_Uploader('filename');
						
						// Any extention would work
						$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
						$uploader->setAllowRenameFiles(false);
						
						// Set the file upload mode 
						// false -> get the file directly in the specified folder
						// true -> get the file in the product like folders 
						//	(file.jpg will go in something like /media/f/i/file.jpg)
						$uploader->setFilesDispersion(false);
								
						// We set media as the upload dir
						$path = $this->getMediaUrl() . '/meari' ;
						if(!is_dir($path)) mkdir($path);
						$path.='/background';
						if(!is_dir($path)) mkdir($path);
						$filename=time().'.'.pathinfo($_FILES['filename']['name'],PATHINFO_EXTENSION);
						$uploader->save($path, $filename );
						
					} catch (Exception $e) {
				  
					}
				
					//this way the name is saved in DB
					$data['filename'] = $dirpath.$filename;
				} else {
					unset($data['filename']);
				}
			}
	  			
	  			
			$model = Mage::getModel('background/background');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('background')->__('Image was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('background')->__('Unable to find Image to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('background/background');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Image was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $backgroundIds = $this->getRequest()->getParam('background');
        if(!is_array($backgroundIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select image(s)'));
        } else {
            try {
                foreach ($backgroundIds as $backgroundId) {
                    $background = Mage::getModel('background/background')->load($backgroundId);
                    $background->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($backgroundIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $backgroundIds = $this->getRequest()->getParam('background');
        if(!is_array($backgroundIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select image(s)'));
        } else {
            try {
                foreach ($backgroundIds as $backgroundId) {
                    $background = Mage::getSingleton('background/background')
                        ->load($backgroundId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($backgroundIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'background.csv';
        $content    = $this->getLayout()->createBlock('background/adminhtml_background_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'background.xml';
        $content    = $this->getLayout()->createBlock('background/adminhtml_background_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}