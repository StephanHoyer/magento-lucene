<?php
class Rotor_Lucene_Block_Result extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getResults()
    {
        return Mage::getSingleton('lucene/index')->getResults();
    }

    public function getResultCount()
    {
        return count(Mage::getSingleton('lucene/index')->getResults());
    }

    public function getEscapedQueryText()
    {
        return strip_tags(Mage::getSingleton('lucene/index')->getQueryString());
    }
}