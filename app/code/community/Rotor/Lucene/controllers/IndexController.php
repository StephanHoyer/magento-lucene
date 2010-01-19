<?php

class Rotor_Lucene_IndexController extends Mage_Core_Controller_Front_Action
{
    var $_index;
    
    public function preDispatch()
    {
        $this->_index = new Rotor_Lucene_Model_Index();
        Mage::register('search_index', $this->_index);
    }

    public function indexAction()
    {
        $this->_index->setQuery($this->getRequest()->getParam('q'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function createIndexAction()
    {
        foreach(Mage::getModel('catalog/category')->getCollection() as $category) {
            $this->_index->indexCategory($category->getId());
        }
    }

}