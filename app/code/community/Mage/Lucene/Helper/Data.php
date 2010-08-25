<?php

class Mage_Lucene_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_productEntityTypeId;
    
    /**
     * Returns typeId of product model
     * 
     * @return int
     **/
    public function getProductEntityTypeId()
    {
        if(!$this->_productEntityTypeId) {
            $this->_productEntityTypeId = Mage::getModel('eav/entity_type')
                ->loadByCode(Mage_Catalog_Model_Product::ENTITY)
                ->getEntityTypeId();
        }
        return $this->_productEntityTypeId;
    }
    

}