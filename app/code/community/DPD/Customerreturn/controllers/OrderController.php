<?php
/**
 * @package            DPD
 * @subpackage       Customer Return
 * @category            Returns
 * @author               Michiel Van Gucht
 */
class DPD_Customerreturn_OrderController extends Mage_Core_Controller_Front_Action
{
    public function returnAction()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        try {
            $returnLabelObject = Mage::getModel('dpd_customerreturn/returnlabels')->generateReturnLabel($orderId);
            if (!$returnLabelObject) {
                $message = Mage::helper('dpd')->__('Something went wrong, please contact the shop administrator.');
                Mage::getSingleton('core/session')->addError($message);
                $this->_redirect('*/*/index');
            } else {
				$labelUrl = Mage::getBaseDir('media') . DS . 'dpd' . DS . 'returnlabel' . DS . $returnLabelObject->getLabelPdfUrl();
				$instructionsUrl = Mage::getBaseDir('media') . DS . 'dpd' . DS . 'returnlabel' . DS . $returnLabelObject->getLabelInstructionsUrl();
				
				$labelPdf = Zend_Pdf::load($labelUrl);
				$instructionPdf = Zend_Pdf::load($instructionsUrl);
				
				$newPdf = new Zend_Pdf();
				
				$template = clone $labelPdf->pages[0]; // cloning the page (a must do)
				$page1 = new Zend_Pdf_Page($template);
				$newPdf->pages[] = $page1;
				
				$template2 = clone $instructionPdf->pages[0];
				$page2 = new Zend_Pdf_Page($template2);
				$newPdf->pages[] = $page2;
				
                $this->_prepareDownloadResponse('return.pdf', $newPdf->render());  // file_get_contents($labelUrl));
            }
        } catch (Exception $e) {
            Mage::helper('dpd')->log($e->getMessage(), Zend_Log::ERR);
            $message = Mage::helper('dpd')->__("The file could not be downloaded, please contact the shop administrator.");
            Mage::getSingleton('core/session')->addError($message);
            $this->_redirect('*/*/index');
        }
    }
}
