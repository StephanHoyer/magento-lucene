<?php
class Rotor_Lucene_Model_Filter_Value extends Varien_Object
{
    var $_value;
    var $_key;
    var $_documents = array();

    public function setValue($value)
    {
       $this->_value = $value;
       return $this;
    }

    public function setKey($key)
    {
       $this->_key = $key;
       return $this;
    }

    public function getKey()
    {
       return $this->_key;
    }

    public function addDocument($document)
    {
        $this->_documents[] = $document;
        return $this;
    }

    public function getValue()
    {
        return $this->_value;
    }
    
    public function getTitle()
    {
        return $this->getValue();
    }
}
