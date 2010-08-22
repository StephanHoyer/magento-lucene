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
	protected function getProductCollection()
    {
        return Mage::getModel('catalog/product')
            ->getCollection()
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
            $collection = $this
                ->getProductCollection()
                ->addStoreFilter($store)
            foreach($collection as $entity) {
				Mage::log($entity->getData());
                $this->getEntitySearchModel()
                    ->setStore($store)
                    ->index($entity);
            }
        }
        return $this;
    }

    protected function getEntitySearchModel()
    {
        return Mage::getModel('lucene/index_document_product');
    }

    protected function getDoctype()
    {
        return self::DOCTYPE;
    }

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

    protected function addFilterableAttributes()
    {
        foreach($this->getFilterableAttributes() as $attribute) {
            if(in_array(
                $attribute->getAttributeCode(), 
                $this->_systemAttributes
            )) {
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
    }

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
    }

    protected function getSourceModel()
    {
        if(!isset($this->_entityModel)) {
            $this->_entityModel = Mage::getModel('catalog/product')
                ->setStoreId($this->getStore()->getId())
                ->load($this->_id);
        }
        return $this->_entityModel;
    }

    protected function getSearchableAttributes()
    {
        if(!isset($this->_searchableAttributes)) {
            $this->_searchableAttributes = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setEntityTypeFilter($this->getProductEntityTypeId());
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

    protected function getFilterableAttributes()
    {
        if(!isset($this->_searchableAttributes)) {
            $this->_searchableAttributes = Mage::getModel('eav/entity_attribute')
                ->getCollection()
                ->setEntityTypeFilter($this->getProductEntityTypeId());
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

    /**
     * Returns typeId of product model
     * 
     * @return int
     **/
    protected function getProductEntityTypeId()
    {
        return Mage::getModel('eav/entity_type')
            ->loadByCode(Mage_Catalog_Model_Product::ENTITY)
            ->getEntityTypeId();
    }
}