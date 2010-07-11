<?php
class Mage_Lucene_Block_Form extends Mage_Core_Block_Template
{
    /**
     * Returns url to get search result.
     * 
     * @return String
     **/
    public function getResultUrl()
    {
        return Mage::getUrl('lucene');
    }

    /**
     * Returns current search query.
     * 
     * @return String
     **/
    public function getEscapedQueryText()
    {
        if(!Mage::getSingleton('lucene/index')->getQueryString()) return '';
        return strip_tags(Mage::getSingleton('lucene/index')->getQueryString());
    }
}