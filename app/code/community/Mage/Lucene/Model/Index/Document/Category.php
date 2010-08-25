<?php
class Mage_Lucene_Model_Index_Document_Category extends Mage_Lucene_Model_Index_Document_Abstract
{
    const DOCTYPE = 'category';
    const SHORT_CONTENT_CHAR_COUNT = 1000;

    /**
     * Returns collection of all active categories of given store.
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     **/
    protected function getCategoryCollection($store)
    {
        $collection = Mage::getModel('catalog/category')
            ->getCategories($store->getRootCategoryId(), 0, false, true, false)
            ->setStore($store)
            ->addAttributeToSelect('description', true)
            ->addAttributeToSelect('name', true)
            ->addAttributeToSelect('image', true)
            ->addAttributeToSelect('landing_page', true)
            ->addAttributeToSelect('url_key', true)
            ;
        return $collection;
    }

    /**
     * Indexes all categories for all storeviews.
     * 
     * @return Mage_Lucene_Model_Index_Document_Abstract
     **/
    public function indexAll()
    {
        foreach(Mage::getModel('core/store')->getCollection() as $store) {
            /* Don't index admin */
            if($store->getId() == 0) {
                continue;
            };
            $this->setStore($store);

            foreach($this->getCategoryCollection($store) as $category) {
                $category->setStoreId($store->getId());
                $category->getUrlInstance()->setStore($store);
                $this->getEntitySearchModel()
                    ->setStore($store)
                    ->index($category);
            }

        }
        return $this;
    }



    /**
     * Returns instance of this class.
     *
     * @return Mage_Lucene_Model_Index_Document_Category
     **/
    protected function getEntitySearchModel()
    {
        return Mage::getModel('lucene/index_document_category');
    }

    /**
     * Returns String representation of this class' object type.
     *
     * @return String
     **/
    protected function getDoctype()
    {
        return self::DOCTYPE;
    }

    /**
     * Adds all values of related category to the search document.
     *
     * @return void
     **/
    protected function addAttributes()
    {
        $content = strip_tags($this->getStaticBlock());
        $this->addField(Zend_Search_Lucene_Field::UnStored('content', $content, self::ENCODING));
        $this->addField(Zend_Search_Lucene_Field::Text('name',
                $this->getSourceModel()->getName(), self::ENCODING));
        $this->addField(Zend_Search_Lucene_Field::Keyword('category',
                $this->getSourceModel()->getParentCategory()->getName(), self::ENCODING));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('short_content',
                substr($content, 0, self::SHORT_CONTENT_CHAR_COUNT), self::ENCODING));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('url',
                $this->getSourceModel()->getUrl(), self::ENCODING));
        if($this->getSourceModel()->getImage()) {
            try {
                $image = Mage::getModel('catalog/product_image')
                ->setBaseFile('../category/'.$this->getSourceModel()->getImage())
                ->setHeight(100)
                ->setWidth(100)
                ->resize()
                ->saveFile()
                ->getUrl();
                $this->addField(Zend_Search_Lucene_Field::UnIndexed('image', $image, self::ENCODING));
            } catch (Exception $e) {
                /* no image for category, so none will be added to index */
            }
        }
    }

    /**
     * Returns HTML content of static block of this category.
     *
     * @return String
     **/
    protected function getStaticBlock()
    {
        return Mage::app()->getLayout()->createBlock('cms/block')
            ->setBlockId($this->getSourceModel()->getLandingPage())
            ->toHtml();
    }

}