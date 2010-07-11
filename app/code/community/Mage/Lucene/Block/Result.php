<?php
class Mage_Lucene_Block_Result extends Mage_Core_Block_Template
{
    /**
     * Returns search results for current query.
     * 
     * @return Array(Mage_Lucene_Model_Index_Document)
     **/
    public function getResults()
    {
        return Mage::getSingleton('lucene/index')->getResults();
    }

    /**
     * Returns count of results for current query.
     * 
     * @return Int
     **/
    public function getResultCount()
    {
        return count(Mage::getSingleton('lucene/index')->getResults());
    }

    /**
     * Returns current search query.
     * 
     * @return String
     **/
    public function getEscapedQueryText()
    {
        return strip_tags(Mage::getSingleton('lucene/index')->getQueryString());
    }
}