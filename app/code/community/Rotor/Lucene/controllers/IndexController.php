<?php

class Rotor_Lucene_IndexController extends Mage_Core_Controller_Front_Action
{
    var $_index;
    
    public function preDispatch()
    {
        $this->_index = new Rotor_Lucene_Model_Index();
    }

    public function indexAction()
    {
        foreach(Mage::getModel('catalog/category')->getCollection() as $category) {
            $this->_index->indexCategory($category->getId());
        }
    }

    public function searchAction()
    {
        foreach($this->_index->find($this->getRequest()->getParam('q')) as $hit) {
            echo '<br />entity_id:'.$hit->entity_id;
            echo '<br />name:'.$hit->name;
            echo '<br />content:'.$hit->short_content;
            echo '<br />url:'.$hit->url;
            echo '<br />hit_id:'.$hit->id;
        }
    }
}