<?php
class Mage_Lucene_Block_Form extends Mage_Core_Block_Template
{
    public function getResultUrl()
    {
        return Mage::getUrl('lucene');
    }

    public function getEscapedQueryText()
    {
        if(!Mage::getSingleton('lucene/index')->getQueryString()) return '';
        return strip_tags(Mage::getSingleton('lucene/index')->getQueryString());
    }
}