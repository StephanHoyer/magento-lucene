<?php
class Rotor_Lucene_Model_Index_Document
{
    var $_data = array();

    public function __construct($hit)
    {
        $this->_data['score'] = $hit->score;
        foreach($hit->getDocument()->getFieldNames() as $field) {
            $this->_data[$field] = $hit->getDocument()->getFieldValue($field);
        }
    }

    public function getData()
    {
        return $_data;
    }
}
