<?php
class Mage_Lucene_Block_Filter extends Mage_Core_Block_Template
{
    protected $_index;

    protected function getIndex()
    {
        if(!isset($this->_index)) {
            $this->_index = Mage::getSingleton('lucene/index');
        }
        return $this->_index;
    }

    public function getPossibleFilters()
    {
        return $this->getIndex()->getResultsFilters();
    }

    public function getFilterUrl($filterValue)
    {
        $filters = array(
            $filterValue->getKey() => $filterValue->getValue()
        );
        foreach(Mage::getSingleton('lucene/index')->getCurrentFilters() as
            $key => $filter) {
            $filters[$key] = $filter->getValue();
        }
        return Mage::getUrl('lucene', $filters);
    }

    public function getRemoveFilterUrl($filterValue)
    {
        $filters = array();
        foreach(Mage::getSingleton('lucene/index')->getCurrentFilters() as
            $key => $filter) {
            if($key != $filterValue->getKey()) {
                $filters[$key] = $filter->getValue();
            }
        }
        return Mage::getUrl('lucene', $filters);
    }

    public function getCurrentFilters()
    {
        return $this->getIndex()->getCurrentFiltersWOQuery();
    }
}
