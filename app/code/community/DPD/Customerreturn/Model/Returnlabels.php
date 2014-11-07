<?php
/**
 * @package            DPD
 * @subpackage       Customer Return
 * @category            Returns
 * @author               Michiel Van Gucht
 */
class DPD_Customerreturn_Model_Returnlabels extends DPD_Shipping_Model_Returnlabels
{
	public function generateReturnLabel($orderId)
	{
		$returnId = $this->generateLabelAndSave($orderId);
		$this->generateInstructionsPdf($orderId, $returnId);
		
		$returnLabelModel = new DPD_Shipping_Model_Returnlabels;
		$returnLabelObject= $returnLabelModel->load($returnId);
		
		return $returnLabelObject;
	}
}