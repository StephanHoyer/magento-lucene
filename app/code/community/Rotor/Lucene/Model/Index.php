<?php
class Rotor_Lucene_Model_Index extends Zend_Search_Lucene_Proxy
{
    const INDEX_DIR = 'var/lucene/index';
    const SHORT_CONTENT_CHAR_COUNT = 1000;

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
        $query = new Zend_Search_Lucene_Search_Query_MultiTerm();
        $query->addTerm(new Zend_Search_Lucene_Index_Term($id, 'entity_id'),true);
        $query->addTerm(new Zend_Search_Lucene_Index_Term('category', 'doctype'),true);
        $hits  = $this->find($query);
        foreach ($hits as $hit) {
            $this->delete($hit->id);
        }
        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($id);
        $doc = new Zend_Search_Lucene_Document();
        $content = strip_tags($this->getCategoriesStaticBlock($category));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('content', $content));
        $doc->addField(Zend_Search_Lucene_Field::Keyword('doctype','category'));
        $doc->addField(Zend_Search_Lucene_Field::Text('name', $category->getName()));
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('short_content',
                substr($content, 0, self::SHORT_CONTENT_CHAR_COUNT)));
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $category->getUrl()));
        $doc->addField(Zend_Search_Lucene_Field::Keyword('entity_id', $category->getId()));
        $this->addDocument($doc);
    }

    protected function getCategoriesStaticBlock($category)
    {
        return Mage::app()->getLayout()->createBlock('cms/block')
            ->setBlockId($category->getLandingPage())
            ->toHtml();
    }

}