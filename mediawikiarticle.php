<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
jimport('joomla.plugin.plugin');

/**
 * Example Content Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.mediawikiarticle
 * @since		2.5
 */
class plgContentMediawikiarticle extends JPlugin
{

	private $mediawiki;

	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);

		$options = new JRegistry();
		$options->set('api.username', $this->params->get('mediawiki_username'));
		$options->set('api.password', $this->params->get('mediawiki_password'));
		$options->set('api.url', $this->params->get('mediawiki_siteurl'));

		$this->mediawiki = new JMediawiki($options);
	}

	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{

		// only articles context
		if( $context != 'com_content.article' )
		{
			return;
		}

		// only pull article categorized with wikibot
		if( $row->category_title != 'wikibot' )
		{
			return;
		}

		try{
			$result = $this->mediawiki->pages->getRevisions(array( $row->title ), array('content'), true);
			$content = $result->query->pages->page->revisions->rev;
		} catch(Exception $e) {
			$content = 'Error retrieving content based on title.';
		}

		$row->text = $content;
	}

}