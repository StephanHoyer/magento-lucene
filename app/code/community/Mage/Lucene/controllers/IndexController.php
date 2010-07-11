<?php
class Mage_Lucene_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action to run search query.
     **/
    public function indexAction()
    {
        $index = Mage::getSingleton('lucene/index');
        foreach($this->getRequest()->getParams() as $key=>$value) {
            $index->addFilter(urldecode($key), urldecode($value));
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Action to run index create.
     **/
    public function createIndexAction()
    {
        Mage::getSingleton('lucene/index_document_category')->indexAll();
//        Mage::getSingleton('lucene/index_document_product')->indexAll();
    }

}