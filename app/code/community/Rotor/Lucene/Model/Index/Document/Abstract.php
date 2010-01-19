<?php
abstract class Rotor_Lucene_Model_Index_Document_Abstract extends Varien_Object
{
    protected $_id;
    protected $_entityModel;
    protected $_index;
    protected $_doc;
    
    public function indexAll()
    {
        foreach($this->getEntityCollection() as $entity) {
            $this->getEntitySearchModel()->index($entity->getId());
        }
    }

    public function index($id)
    {
        $this->_id = $id;
        $this->delete();
        $this->addField(Zend_Search_Lucene_Field::Keyword('doctype','category'));
        $this->addField(Zend_Search_Lucene_Field::Keyword('entity_id',
            $this->getSourceModel()->getId()));
        $this->addAttributes();
        $this->addDocument();
    }

    protected function delete() {
        $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
        $query->addTerm(new Zend_Search_Lucene_Index_Term($this->_id, 'entity_id'),true);
        $query->addTerm(new Zend_Search_Lucene_Index_Term($this->getDoctype(), 'doctype'),true);
        $this->deleteByQuery($query);
    }

    protected function getIndex()
    {
        if(!isset($this->_index)) {
            $this->_index = Mage::getSingleton('lucene/index');
        }
        return $this->_index;
    }

    protected function deleteByQuery($query)
    {
        foreach ($this->getIndex()->find($query) as $hit) {
            $this->getIndex()->delete($hit->id);
        }
    }

    protected function addField($field)
    {
        if(!isset($this->_doc)) {
            $this->_doc = new Zend_Search_Lucene_Document();
        }
        $this->_doc->addField($field);
    }

    protected function addDocument()
    {
        $this->getIndex()->addDocument($this->_doc);
    }

    protected abstract function getEntityCollection();

    protected abstract function getEntitySearchModel();

    protected abstract function getDoctype();

    protected abstract function addAttributes();

    protected abstract function getSourceModel();
}