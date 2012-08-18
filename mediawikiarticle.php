<?php

/**
 * @package    Content.mediawikiarticle
 * 
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Mediawikiarticle Content Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.mediawikiarticle
 * @since       2.5
 */
class plgContentMediawikiarticle extends JPlugin {

	/**
	 *
	 * @var  JMediawiki 
	 */
	private $_mediawiki;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An optional associative array of configuration settings.
	 *                             Recognized key values include 'name', 'group', 'params', 'language'
	 *                             (this list is not meant to be comprehensive).
	 *
	 * @since   11.1
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$options = new JRegistry;
		$options->set('api.username', $this->params->get('username'));
		$options->set('api.password', $this->params->get('password'));
		$options->set('api.url', $this->params->get('siteurl'));

		$this->_mediawiki = new JMediawiki($options);
	}

	/**
	 * Prepare the content
	 *
	 * @param   string  $context  The context of the content being passed to the plugin.
	 * @param   object  &$row     The article object.  Note $article->text is also available
	 * @param   object  &$params  The article params
	 * @param   int     $page     The 'page' number
	 * 
	 * @return  void
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{

		// Only articles context
		if ($context != 'com_content.article')
		{
			return;
		}

		// Pull article categorized on parameters. If root, allow all.
		if ($this->params->get('catid', 0) !== 0 && $row->catid !== $this->params->get('catid', 0))
		{
			return;
		}

		try
		{
			$result = $this->_mediawiki->pages->getRevisions(array($row->title), array('content'), true);
			$content = $result->query->pages->page->revisions->rev;
		}
		catch (Exception $e)
		{
			// Maybe also log error with JLog for administrator view later?
			$content = JText::_('PLG_CONTENT_MEDIAWIKIARTICLE_ERROR_LOAD');
		}

		$row->text = $content;
	}
}
