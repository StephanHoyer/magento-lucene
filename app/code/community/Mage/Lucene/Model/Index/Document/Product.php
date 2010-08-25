<?php
class Mage_Lucene_Model_Index_Document_Product 
    extends Mage_Lucene_Model_Index_Document_Abstract
{
    const DOCTYPE = 'product';

    protected $_systemAttributes = array('name', 'short_content', 'url');

    /**
     * Returns collection of all searchable products
     *
     * return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    protected function getProductCollection($store)
    {
        return Mage::getModel('catalog/product')
            ->getCollection()
            ->addStoreFilter($store)
            ->setVisibility(Mage::getModel('catalog/product_visibility')->getVisibleInSearchIds())
            ->addAttributeToSelect($this->getSearchableAttributesCodes(), true)
            ->addAttributeToSelect($this->getFilterableAttributesCodes(), true);
    }

    /**
     * Indexes all products for all storeviews.
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
            foreach($this->getProductCollection($store) as $product) {
                $this->getEntitySearchModel()
                    ->setStore($store)
                    ->index($product);
            }
        }
        return $this;
    }

    /**
     * Returns instance of this class
     * 
     * @return Mage_Lucene_Model_Index_Document_Product
     **/
    protected function getEntitySearchModel()
    {
        return Mage::getModel('lucene/index_document_product');
    }

    /**
     * Returns string representation of document type
     * 
     * @return string
     **/
    protected function getDoctype()
    {
        return self::DOCTYPE;
    }

    /**
     * Adds attributes to be indexed
     * 
     * @return string
     **/
    protected function addAttributes()
    {
        $this->addField(Zend_Search_Lucene_Field::Text('name',
            $this->getSourceModel()->getName(), self::ENCODING));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('short_content', 
            $this->getSourceModel()->getShortDescription(), self::ENCODING));
        $this->addField(Zend_Search_Lucene_Field::UnIndexed('url',
            $this->getSourceModel()->getProductUrl(), self::ENCODING));
        $this->addSearchableAttributes();
        $this->addFilterableAttributes();
    }

    /**
     * Add filterable attributes and values to indexed document
     * 
     * @return string
     **/
    protected function addFilterableAttributes()
    {
        foreach($this->getFilterableAttributes() as $attribute) {
            if(in_array($attribute->getAttributeCode(), $this->_systemAttributes)) {
                continue;
            }
            if($this->getSourceModel()->getData($attribute->getAttributeCode())) {
                $value = $this->getSourceModel()
                    ->getAttributeText($attribute->getAttributeCode());
                if(!$value) {
                    $value = $this->getSourceModel()
                        ->getData($attribute->getAttributeCode());
                }
                try {
                    $this->getField($attribute->getAttributeCode())->isStored = true;
                    $this->getField($attribute->getAttributeCode())->isIndexed = true;
                } catch(Zend_Search_Lucene_Exception $e) {
                    $this->addField(Zend_Search_Lucene_Field::Keyword(
                        $attribute->getAttributeCode(),
                        $value, self::ENCODING)
                    );
                }
            }
        }
        return $this;
    }

    /**
     * Add searchable attributes and values to indexed document
     * 
     * @return string
     **/
    protected function addSearchableAttributes()
    {
        foreach($this->getSearchableAttributes() as $attribute) {
            if(in_array($attribute->getAttributeCode(), $this->_systemAttributes)) {
                continue;
            }
            if($this->getSourceModel()->getData($attribute->getAttributeCode())) {
                $value = $this->getSourceModel()
                    ->getAttributeText($attribute->getAttributeCode());
                if(!$value) {
                    $value = $this->getSourceModel()
                        ->getData($attribute->getAttributeCode());
                }
                try {
                    $this->getField($attribute->getAttributeCode())->isTokenized = true;
                    $this->getField($attribute->getAttributeCode())->isIndexed = true;
                } catch(Zend_Search_Lucene_Exception $e) {
                    $this->addField(Zend_Search_Lucene_Field::UnStored(
                        $attribute->getAttributeCode(),
                        $value, self::ENCODING)
                    );
                }
            }
        }
        return $this;
    }

    /**
     * Returns collection of all searchable Attributes
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     **/
    protected function getSearchableAttributes()
    {
        if(!isset($this->_searchableAttributes)) {
            $this->_searchableAttributes = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setEntityTypeFilter(Mage::helper('lucene')->getProductEntityTypeId());
            $this->_searchableAttributes->getSelect()
                ->where('additional_table.is_searchable = ?', 1);
        }
        return $this->_searchableAttributes;
    }

    /**
     * Returns array of codes of all searchable products attributes
     *
     * @return array
     **/
    protected function getSearchableAttributesCodes()
    {
        $return = array();
        foreach($this->getSearchableAttributes() as $attribute) {
            $return[] = $attribute->getAttributeCode();
        }
        return $return;
    }    

    /**
     * Returns collection of all filterable Attributes
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     **/
    protected function getFilterableAttributes()
    {
        if(!isset($this->_searchableAttributes)) {
            $this->_searchableAttributes = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setEntityTypeFilter(Mage::helper('lucene')->getProductEntityTypeId());
            $this->_searchableAttributes->getSelect()
                ->where('additional_table.is_filterable_in_search = ?', 1);
        }
        return $this->_searchableAttributes;
    }

    /**
     * Returns array of codes of all filterable products attributes
     *
     * @return array
     **/
    protected function getFilterableAttributesCodes()
    {
        $return = array();
        foreach($this->getFilterableAttributes() as $attribute) {
            $return[] = $attribute->getAttributeCode();
        }
        return $return;
    }    
}