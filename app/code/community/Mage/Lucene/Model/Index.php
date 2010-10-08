<?php
class Mage_Lucene_Model_Index extends Zend_Search_Lucene_Proxy
{
    const INDEX_DIR = 'var/lucene/index';
    const QUERY_KEY = 'q';
    
    /**
     * String which contains all chars used to tokenize search term to multiple terms
     * @var String
     */
    const TERM_TOKENIZE_CHARS = ' ,;|';
    
    /**
     * Prefix to track prohibited search terms
     * @var String
     */
    const PROHIBITED_MARKER_PREFIX = '-';
    
    var $_currentFilters = array();
    var $_results;
    var $_query;
    var $_resultsFilters = array();
    var $_excludeAttributes = array('short_content', 'url', 'entity_id', 'image', 
        'name', Mage_Lucene_Model_Index_Document_Abstract::STORE_ATTRIBUTE_CODE);


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
	        $this->_query->addTerm(new Zend_Search_Lucene_Index_Term(Mage::app()->getStore()->getId(), 
	           Mage_Lucene_Model_Index_Document_Abstract::STORE_ATTRIBUTE_CODE),true);
            foreach($this->getCurrentFilters() as $filter) {
                if($filter->getKey() == self::QUERY_KEY) {
                    $this->addSearchTerm($filter->getValue());
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
    
    /**
     * Adds multiple search terms to query based on given string. Therfore the string is
     * seperated by TERM_EXPLODE_CHAR. If a term has a PROHIBITED_MARKER_PREFIX as prefix
     * the term will be marked as prohibited
     *  
     * @param String $terms
     */
    protected function addSearchTerm($terms)
    {
        $term = strtok($terms, self::TERM_TOKENIZE_CHARS);
        while($term) {
            $prohibited = $required = false;
            if (
                preg_match(sprintf('/^%s(.*)/', self::PROHIBITED_MARKER_PREFIX), 
                    $term, $prohibitedTerms) && 
                count($prohibitedTerms) > 0 && 
                $prohibitedTerms[1]
            ) {
                $prohibited = true;
                $term = $prohibitedTerms[1];
            } else {
                $required = true;
            }
            $this->_query->addTerm(
                new Zend_Search_Lucene_Index_Term(strtolower($term)), 
                    $required && !$prohibited
            );
            $term = strtok(self::TERM_TOKENIZE_CHARS);
        }
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
                $this->_results[] = new Mage_Lucene_Model_Index_Document($hit);
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