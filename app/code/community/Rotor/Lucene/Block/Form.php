<?php
class Rotor_Lucene_Block_Form extends Mage_Core_Block_Template
{
    public function getResultUrl()
    {
        return Mage::getUrl('lucene');
    }

    public function getEscapedQueryText()
    {
        return strip_tags(Mage::registry('search_index')->getQuery());
    }
}