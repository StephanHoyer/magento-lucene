<?php
class Rotor_Lucene_Model_Filter extends Varien_Object
{
    var $_key;
    var $_values = array();

    public function setKey($key)
    {
       $this->_key = $key;
       return $this;
    }

    public function addValue($value, $document)
    {
        if(!array_key_exists($value, $this->_values)) {
            $this->_values[$value] = Mage::getModel('lucene/filter_value')
                ->setValue($value);
        }
        $this->_values[$value]->addDocument($document);
        return $this;
    }

    public function getValues()
    {
        return $this->_values;
    }

    public function getTitle()
    {
        return $this->_key;
    }
}