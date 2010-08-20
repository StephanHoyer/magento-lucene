<?php 

class Mage_Lucene_Model_Indexer_Category extends Mage_Index_Model_Indexer_Abstract
{
	/**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
	{
		return Mage::helper('lucene')->__('Category search (Lucene)');
	}

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
	{
		return Mage::helper('lucene')->__('Category full text lucene index');
	}

    /**
     * Register indexer required data inside event object
     *
     * @param   Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
	{
		Mage::log('_registerEvent');
	}

    /**
     * Process event based on event state data
     *
     * @param   Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
	{
		Mage::log('_processEvent');
	}
 
	/**
	 * Fires event to reindex all lucene related indexes
	 *
	 * @return void
	 */
	public function reindexAll()
	{
		Mage::getSingleton('lucene/index_document_category')->indexAll();
	}
}