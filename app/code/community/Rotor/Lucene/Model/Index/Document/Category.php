<?php
class Rotor_Lucene_Model_Index_Document_Category extends Rotor_Lucene_Model_Index_Document_Abstract
{
    const DOCTYPE = 'category';
    const SHORT_CONTENT_CHAR_COUNT = 1000;

    protected function getEntityCollection()
    {
        return Mage::getModel('catalog/category')
            ->getCollection()
            ->addIsActiveFilter();
    }

    protected function getEntitySearchModel()
    {
        return Mage::getModel('lucene/index_document_category');
    }

    protected function getDoctype()
    {
        return self::DOCTYPE;
    }

    protected function addAttributes()
    {
        $content = strip_tags($this->getStaticBlock($this->getSourceModel()));
        $this->addField(Zend_Search_Lucene_Field::UnStored('content', $content, 'utf8'));
        $this->addField(Zend_Search_Lucene_Field::Text('name',
                $this->getSourceModel()->getName(), 'utf8'));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('short_content',
                substr($content, 0, self::SHORT_CONTENT_CHAR_COUNT), 'utf8'));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('url',
                $this->getSourceModel()->getUrl(), 'utf8'));
        if($this->getSourceModel()->getImage()) {
            try {
                $image = Mage::getModel('catalog/product_image')
                ->setBaseFile('../category/'.$this->getSourceModel()->getImage())
                ->setHeight(100)
                ->setWidth(100)
                ->resize()
                ->saveFile()
                ->getUrl();
                $this->addField(Zend_Search_Lucene_Field::UnIndexed('image', $image, 'utf8'));
            } catch (Exception $e) {
                /* no image for category, so none will be added to index */
            }
        }
    }

    protected function getSourceModel()
    {
        if(!isset($this->_entityModel)) {
            $this->_entityModel = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($this->_id);
        }
        return $this->_entityModel;
    }

    protected function getStaticBlock($category)
    {
        return Mage::app()->getLayout()->createBlock('cms/block')
            ->setBlockId($this->getSourceModel()->getLandingPage())
            ->toHtml();
    }

}