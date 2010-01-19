<?php
class Rotor_Lucene_Model_Index extends Zend_Search_Lucene_Proxy
{
    const INDEX_DIR = 'var/lucene/index';
    const SHORT_CONTENT_CHAR_COUNT = 1000;

    var $_query = '';
    var $_results;

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
    }

    public function indexCategory($id)
    {
        $this->deleteCategoryDoc($id);
        $category = $this->loadCategory($id);
        $doc = new Zend_Search_Lucene_Document();
        $content = strip_tags($this->getCategoriesStaticBlock($category));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('content', $content));
        $doc->addField(Zend_Search_Lucene_Field::Keyword('doctype','category'));
        $doc->addField(Zend_Search_Lucene_Field::Text('name', $category->getName()));
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('short_content',
                substr($content, 0, self::SHORT_CONTENT_CHAR_COUNT)));
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $category->getUrl()));
        $doc->addField(Zend_Search_Lucene_Field::Keyword('entity_id', $category->getId()));
        if($category->getImage()) {
            try {
                $image = Mage::getModel('catalog/product_image')
                ->setBaseFile('../category/'.$category->getImage())
                ->setHeight(100)
                ->setWidth(100)
                ->resize()
                ->saveFile()
                ->getUrl();
                $doc->addField(Zend_Search_Lucene_Field::UnIndexed('image', $image));
            } catch (Exception $e) {

            }
        }
        $this->addDocument($doc);
    }

    protected function deleteCategoryDoc($id)
    {
        $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
        $query->addTerm(new Zend_Search_Lucene_Index_Term($id, 'entity_id'),true);
        $query->addTerm(new Zend_Search_Lucene_Index_Term('category', 'doctype'),true);
        $this->deleteHits($this->find($query));
    }

    protected function deleteHits($hits)
    {
        foreach ($hits as $hit) {
            $this->delete($hit->id);
        }
    }
    protected function loadCategory($id)
    {
        return Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($id);
    }
    protected function getCategoriesStaticBlock($category)
    {
        return Mage::app()->getLayout()->createBlock('cms/block')
            ->setBlockId($category->getLandingPage())
            ->toHtml();
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
}