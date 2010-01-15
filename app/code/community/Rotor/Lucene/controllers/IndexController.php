<?php

class Rotor_Lucene_IndexController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $docUrl = "testURL";
        $docContent = "test Content";
        // Index erstellen
        $index = new Rotor_Lucene_Model_Index();
        //foreach(Mage::getModel('catalog/category')->getCollection() as $category) {
//            $index->indexCategory($category->getId());
        //}
        $index->indexCategory(22);
        foreach($index->find('rennrad') as $hit) {
            echo '<br />entity_id:'.$hit->entity_id;
            echo '<br />name:'.$hit->name;
            echo '<br />content:'.$hit->short_content;
            echo '<br />hit_id:'.$hit->id;
        }
    }

}