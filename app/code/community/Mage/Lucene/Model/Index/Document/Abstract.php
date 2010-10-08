<?php
abstract class Mage_Lucene_Model_Index_Document_Abstract extends Varien_Object
{
    /**
     * @var CONST General encoding to store values
     **/
    const ENCODING = 'UTF-8';

    /**
     * @var CONST code of the attribute that contains the store id 
     **/
    const STORE_ATTRIBUTE_CODE = 'store';
    
    /**
     * @var int Id of document related entity
     **/
    protected $_id;

    /**
     * @var Varien_Object Document related entity
     **/
    protected $_sourceModel;

    /**
     * @var Mage_Lucene_Model_Index Search index
     **/
    protected $_index;

    /**
     * @var Zend_Search_Lucene_Document Document related lucene search document
     **/
    protected $_doc;
    
    /**
     * @var Mage_Core_Model_Store
     **/
    protected $_store;
    
    /**
     * Index entity of document with given id.
     * 
     * @param Mage_Core_Model_Abstract
     *
     * @return Mage_Lucene_Model_Index_Document_Abstract
     **/
    public function index($sourceModel)
    {
		$this->_sourceModel = $sourceModel;
        $this->_id = $sourceModel->getId();
        $this->delete();
        $this->addField(Zend_Search_Lucene_Field::Keyword('doctype',$this->getDoctype()));
        $this->addField(Zend_Search_Lucene_Field::Keyword('entity_id', $this->_id));
        $this->addField(Zend_Search_Lucene_Field::Keyword(self::STORE_ATTRIBUTE_CODE,
            $this->getStore()->getId()));
        $this->addAttributes();
        $this->addDocument();
        return $this;
    }

    /**
     * Removes search entry of current entity from the index.
     * 
     * @return Mage_Lucene_Model_Index_Document_Abstract
     **/
    protected function delete() 
    {
        $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
        $query->addTerm(new Zend_Search_Lucene_Index_Term($this->_id, 'entity_id'),true);
        $query->addTerm(new Zend_Search_Lucene_Index_Term($this->getDoctype(), 'doctype'),true);
        $query->addTerm(new Zend_Search_Lucene_Index_Term($this->getStore()->getId(), 
            self::STORE_ATTRIBUTE_CODE),true);
        $this->deleteByQuery($query);
        return $this;
    }

    /**
     * Returns search index object.
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
     * Helper function to delete all entries matching to query
     *
     * @param Zend_Search_Lucene_Search_Query_MultiTerm query
     * 
     * @return void
     **/
    protected function deleteByQuery($query)
    {
        foreach ($this->getIndex()->find($query) as $hit) {
            $this->getIndex()->delete($hit->id);
        }
    }

    /**
     * Add new search field to this document
     *
     * @param Zend_Search_Lucene_Field field
     * 
     * @return Mage_Lucene_Model_Index_Document_Abstract
     **/
    protected function addField($field)
    {
        if(!isset($this->_doc)) {
            $this->_doc = new Zend_Search_Lucene_Document();
        }
        $this->_doc->addField($field);
        return $this;
    }

    /**
     * Returns field by name.
     *
     * @param String name
     * 
     * @return Zend_Search_Lucene_Field
     **/
    protected function getField($fieldName)
    {
        return $this->_doc->getField($fieldName);
    }

    /**
     * Returns current indexed store.
     *
     * @return Mage_Core_Model_Store
     **/
    protected function getStore()
    {
        return $this->_store;
    }

    /**
     * Sets store to index.
     *
     * @return Mage_Lucene_Model_Index_Document_Abstract
     **/
    protected function setStore($store)
    {
        $this->_store = $store;
		return $this;
    }

    /**
     * Adds this document to search index.
     *
     * @return void
     **/
    protected function addDocument()
    {
        $this->getIndex()->addDocument($this->_doc);
    }

    /**
     * Index all documents of this type
     *
     * @return Mage_Lucene_Model_Index_Document_Abstract
     **/
    protected abstract function indexAll();

    /**
     * Function to get an instance of this object
     *
     * @return Mage_Lucene_Model_Index_Document_Abstract
     **/
    protected abstract function getEntitySearchModel();

    /**
     * Returns string representation of this object type.
     *
     * @return String
     **/
    protected abstract function getDoctype();

    /**
     * Function which should add all object specific attributes to document.
     *
     * @return void
     **/
    protected abstract function addAttributes();

    /**
     * Returns object related to this search document
     *
     * @return Varien_Object
     **/
    protected function getSourceModel()
	{
		return $this->_sourceModel;
	}
}