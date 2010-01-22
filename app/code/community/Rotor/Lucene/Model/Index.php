<?php
class Rotor_Lucene_Model_Index extends Zend_Search_Lucene_Proxy
{
    const INDEX_DIR = 'var/lucene/index';

    var $_query = '';
    var $_results;
    var $_filters = array();
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
        $this->_query = $query;
    }

    public function getQuery()
    {
        return $this->_query;
    }

    public function getResults()
    {
        if(!isset($this->_results)) {
            $this->_results = array();
            foreach($this->find($this->_query.'~'.$this->getDefaultSimilarity()) as $hit) {
                $this->_results[] = new Rotor_Lucene_Model_Index_Document($hit);
            }
        }
        return $this->_results;
    }

    public function getResultsFilters()
    {
        $this->_filters = array();
        if($this->getResults())
        {
            foreach($this->getResults() as $result) {
                foreach($result->getData() as $key => $value) {
                    if(
                        !in_array($key, $this->_excludeAttributes) &&
                        $value &&
                        is_string($value)
                    ) {
                        if(!array_key_exists($key, $this->_filters)){
                            $this->_filters[$key] = Mage::getModel('lucene/filter')
                                ->setKey($key);
                        }
                        $this->_filters[$key]->addValue($value, $result);
                    }
                }
            }
        }
        return $this->_filters;
    }


}