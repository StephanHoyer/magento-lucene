<?php
abstract class Mage_Lucene_Model_Index_Document_Abstract extends Varien_Object
{
    /**
     * @var CONST General encoding to store values
     **/
    const ENCODING = 'UTF-8';

    /**
     * @var int Id of document related entity
     **/
    protected $_id;

    /**
     * @var Varien_Object Document related entity
     **/
    protected $_entityModel;

    /**
     * @var Mage_Lucene_Model_Index Search index
     **/
    protected $_index;

    /**
     * @var Zend_Search_Lucene_Document Document related lucene search document
     **/
    protected $_doc;
    
    /**
     * Indexes all given documents.
     * 
     * @return Mage_Lucene_Model_Index_Document_Abstract
     **/
    public function indexAll()
    {
        foreach($this->getEntityCollection() as $entity) {
            $this->getEntitySearchModel()->index($entity->getId());
        }
        return $this;
    }

    /**
     * Index entity of document with given id.
     * 
     * @param int id
     *
     * @return Mage_Lucene_Model_Index_Document_Abstract
     **/
    public function index($id)
    {
        $this->_id = $id;
        $this->delete();
        $this->addField(Zend_Search_Lucene_Field::Keyword('doctype',$this->getDoctype()));
        $this->addField(Zend_Search_Lucene_Field::Keyword('entity_id',
            $this->getSourceModel()->getId()));
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
     * Adds this document to search index.
     *
     * @return void
     **/
    protected function addDocument()
    {
        $this->getIndex()->addDocument($this->_doc);
    }

    /**
     * Function to retrive all entities of given collection.
     *
     * @return Mage_Core_Model_Mysql4_Collection_Abstract
     **/
    protected abstract function getEntityCollection();

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
    protected abstract function getSourceModel();
}