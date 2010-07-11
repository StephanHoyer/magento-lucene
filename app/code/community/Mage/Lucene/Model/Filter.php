<?php
class Mage_Lucene_Model_Filter extends Varien_Object
{
    /**
     * @var String Key to which this object belongs.
     **/
    var $_key;

    /**
     * @var Array(Mage_Lucene_Model_Filter_Value) possible values
     **/
    var $_values = array();

    /**
     * Sets key of this filter.
     * 
     * @param String key
     *
     * @return Mage_Lucene_Model_Filter
     **/
    public function setKey($key)
    {
       $this->_key = $key;
       return $this;
    }

    /**
     * Adds and related document value to this filter.
     * 
     * @param String value
     * @param Mage_Lucene_Model_Index_Document document
     *
     * @return Mage_Lucene_Model_Filter
     **/
    public function addValue($value, $document)
    {
        if(!array_key_exists($value, $this->_values)) {
            $this->_values[$value] = Mage::getModel('lucene/filter_value')
                ->setValue($value)
                ->setKey($this->_key);
        }
        $this->_values[$value]->addDocument($document);
        return $this;
    }

    /**
     * Returns all values of this filter.
     * 
     * @return Array(Mage_Lucene_Model_Filter_Value)
     **/
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Returns Title for this value.
     * 
     * @return String
     **/
    public function getTitle()
    {
        return $this->_key;
    }
}