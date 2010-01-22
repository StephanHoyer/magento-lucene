<?php
class Rotor_Lucene_Model_Filter_Value extends Varien_Object
{
    var $_value;
    var $_documents = array();

    public function setValue($value)
    {
       $this->_value = $value;
       return $this;
    }

    public function addDocument($document)
    {
        $this->_documents[] = $document;
        return $this;
    }

    public function getTitle()
    {
        return $this->_value;
    }
}
