<?php
class Mage_Lucene_Model_Filter_Value extends Varien_Object
{
    /**
     * @var String Value of this object.
     **/
    var $_value;

    /**
     * @var String Key to which this object belongs.
     **/
    var $_key;

    /**
     * @var Array(Mage_Lucene_Model_Index_Document) Array of documents 
     *        containung this value.
     **/
    var $_documents = array();

    /**
     * Sets value.
     * 
     * @param String value
     *
     * @return Mage_Lucene_Model_Filter_Value
     **/
    public function setValue($value)
    {
       $this->_value = $value;
       return $this;
    }

    /**
     * Sets key.
     * 
     * @param String key
     *
     * @return Mage_Lucene_Model_Filter_Value
     **/
    public function setKey($key)
    {
       $this->_key = $key;
       return $this;
    }

    /**
     * Returns key.
     * 
     * @return String
     **/
    public function getKey()
    {
       return $this->_key;
    }

    /**
     * Add document to documents containing this value
     * 
     * @param Mage_Lucene_Model_Index_Document document
     *
     * @return Mage_Lucene_Model_Filter_Value
     **/
    public function addDocument($document)
    {
        $this->_documents[] = $document;
        return $this;
    }

    /**
     * Returns value.
     * 
     * @return String
     **/
    public function getValue()
    {
        return $this->_value;
    }
    
    /**
     * Returns Title for this value.
     * 
     * @return String
     **/
    public function getTitle()
    {
        return $this->getValue();
    }
}
