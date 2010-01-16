<?php
class Rotor_Lucene_Block_Results extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getResults()
    {
        return Mage::registry('search_index')->getResults();
    }
}