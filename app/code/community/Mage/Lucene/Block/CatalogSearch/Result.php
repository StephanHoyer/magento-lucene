<?php
class Mage_Lucene_Block_CatalogSearch_Result extends Mage_CatalogSearch_Block_Result
{
    protected function _getProductCollection()
    {
        return Mage::getSingleton('lucene/index')->getProductCollection();
    }
}