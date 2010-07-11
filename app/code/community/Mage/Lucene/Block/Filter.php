<?php
class Mage_Lucene_Block_Filter extends Mage_Core_Block_Template
{
    /**
     * @var Mage_Lucene_Model_Index current search index
     **/
    protected $_index;

    /**
     * Returns current search index.
     * 
     * @return Mage_Lucene_Model_Index
     **/
    protected function getIndex()
    {
        if(!isset($this->_index)) {
            $this->_index = Mage::getSingleton('lucene/index');
        }
        return $this->_index;
    }

    /**
     * Returns array of all possible filters based on current search result.
     * 
     * @return Array(Mage_Lucene_Model_Filter)
     **/
    public function getPossibleFilters()
    {
        return $this->getIndex()->getResultsFilters();
    }

    /**
     * Returns URL to result when given filter is applied to current result. 
     * (incl. already applied filters)
     * 
     * @param Mage_Lucene_Model_Filter filterValue filter to apply
     *
     * @return String
     **/
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

    /**
     * Returns URL to result when given filter is removed from current result. 
     * 
     * @param Mage_Lucene_Model_Filter filterValue filter to remove
     *
     * @return String
     **/
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

    /**
     * Returns array of all currently applied filters 
     * based on current search result.
     * 
     * @return Array(Mage_Lucene_Model_Filter)
     **/
    public function getCurrentFilters()
    {
        return $this->getIndex()->getCurrentFiltersWOQuery();
    }
}
