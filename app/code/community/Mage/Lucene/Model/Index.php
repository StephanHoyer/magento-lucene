<?php
class Mage_Lucene_Model_Index extends Zend_Search_Lucene_Proxy
{
    const INDEX_DIR = 'var/lucene/index';
    const QUERY_KEY = 'q';

    var $_currentFilters = array();
    var $_results;
    var $_query;
    var $_resultsFilters = array();
    var $_excludeAttributes = array('short_content', 'url', 'entity_id', 'image', 'name');


    protected function getDefaultSimilarity()
    {
        return '0.5';
    }

    public function __construct()
    {
        try {
            parent::__construct(new Zend_Search_Lucene(self::INDEX_DIR, false));
        } catch (Zend_Search_Lucene_Exception $e) {
            parent::__construct(new Zend_Search_Lucene(self::INDEX_DIR, true));
        }
        Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding("UTF-8");
    }

    public function setQuery($query)
    {
        $this->_currentFilters[self::QUERY_KEY] = $query;
    }

    public function addFilter($key, $value)
    {
        $this->_currentFilters[$key] = Mage::getModel('lucene/filter_value')
            ->setValue($value)
            ->setKey($key);;
    }

    public function getQuery()
    {
        if(!isset($this->_query)) {
            $this->_query = new Zend_Search_Lucene_Search_Query_MultiTerm();
            foreach($this->getCurrentFilters() as $filter) {
                if($filter->getKey() == self::QUERY_KEY) {
                    $this->_query->addTerm(
                        new Zend_Search_Lucene_Index_Term(
                            strtolower($filter->getValue())//.'~'.$this->getDefaultSimilarity()
                        ), true);
                } else {
                    $this->_query->addTerm(
                        new Zend_Search_Lucene_Index_Term(
                            $filter->getValue(),
                            $filter->getKey()
                        ), true);
                }
            }
        }
        return $this->_query;
    }

    public function getQueryString()
    {
        if(array_key_exists(self::QUERY_KEY, $this->_currentFilters)) {
            return $this->_currentFilters[self::QUERY_KEY]->getValue();
        }
    }

    public function getResults()
    {
        if(!isset($this->_results)) {
            $this->_results = array();
            foreach($this->find($this->getQuery()) as $hit) {
                $this->_results[] = new Rotor_Lucene_Model_Index_Document($hit);
            }
        }
        return $this->_results;
    }

    public function getResultsFilters()
    {
        if($this->getResults())
        {
            foreach($this->getResults() as $result) {
                foreach($result->getData() as $key => $value) {
                    if(
                        !in_array($key, $this->_excludeAttributes) &&
                        $value &&
                        is_string($value) &&
                        $this->isCurrentlyFiltered($key)
                    ) {
                        if(!array_key_exists($key, $this->_resultsFilters)){
                            $this->_resultsFilters[$key] = Mage::getModel('lucene/filter')
                                ->setKey($key);
                        }
                        $this->_resultsFilters[$key]->addValue($value, $result);
                    }
                }
            }
        }
        return $this->_resultsFilters;
    }

    protected function isCurrentlyFiltered($key)
    {
        return !array_key_exists($key, $this->getCurrentFilters());
    }

    public function getCurrentFilters()
    {
        return $this->_currentFilters;
    }

    public function getCurrentFiltersWOQuery()
    {
        $filters = $this->_currentFilters;
        unset($filters[self::QUERY_KEY]);
        return $filters;
    }

}