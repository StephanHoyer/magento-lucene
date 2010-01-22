<?php

class Rotor_Lucene_IndexController extends Mage_Core_Controller_Front_Action
{
    var $_index;
    
    public function indexAction()
    {
        Mage::getSingleton('lucene/index')->setQuery($this->getRequest()->getParam('q'));
        $this->loadLayout();
        $this->renderLayout();
    }

    public function createIndexAction()
    {
        Mage::getSingleton('lucene/index_document_category')->indexAll();
    }

}