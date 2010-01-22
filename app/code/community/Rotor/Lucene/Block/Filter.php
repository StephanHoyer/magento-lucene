<?php
class Rotor_Lucene_Block_Filter extends Mage_Core_Block_Template
{
    public function getAttributes()
    {
        return Mage::getSingleton('lucene/index')->getResultsFilters();
    }
}
