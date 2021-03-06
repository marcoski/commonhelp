<?php

namespace Commonhelp\Rss\Serialization;

use SimpleXMLElement;

/**
 * OPML export class.
 *
 */
class Export{
    /**
     * List of feeds to exports.
     *
     * @var array
     */
    private $content = array();

    /**
     * List of required properties for each feed.
     *
     * @var array
     */
    private $requiredFields = array(
        'title',
        'site_url',
        'feed_url',
    );

    /**
     * Constructor.
     *
     * @param array $content List of feeds
     */
    public function __construct(array $content){
        $this->content = $content;
    }

    /**
     * Get the OPML document.
     *
     * @return string
     */
    public function execute(){
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><opml/>');

        $head = $xml->addChild('head');
        $head->addChild('title', 'OPML Export');

        $body = $xml->addChild('body');

        foreach ($this->content as $category => $values) {
            if (is_string($category)) {
                $this->createCategory($body, $category, $values);
            } else {
                $this->createEntry($body, $values);
            }
        }

        return $xml->asXML();
    }

    /**
     * Create a feed entry.
     *
     * @param SimpleXMLElement $parent Parent Element
     * @param array            $feed   Feed properties
     */
    public function createEntry(SimpleXMLElement $parent, array $feed){
        $valid = true;

        foreach ($this->requiredFields as $field) {
            if (!isset($feed[$field])) {
                $valid = false;
                break;
            }
        }

        if ($valid) {
            $outline = $parent->addChild('outline');
            $outline->addAttribute('xmlUrl', $feed['feed_url']);
            $outline->addAttribute('htmlUrl', $feed['site_url']);
            $outline->addAttribute('title', $feed['title']);
            $outline->addAttribute('text', $feed['title']);
            $outline->addAttribute('description', isset($feed['description']) ? $feed['description'] : $feed['title']);
            $outline->addAttribute('type', 'rss');
            $outline->addAttribute('version', 'RSS');
        }
    }

    /**
     * Create entries for a feed list.
     *
     * @param SimpleXMLElement $parent Parent Element
     * @param array            $feeds  Feed list
     */
    public function createEntries(SimpleXMLElement $parent, array $feeds){
        foreach ($feeds as $feed) {
            $this->createEntry($parent, $feed);
        }
    }

    /**
     * Create a category entry.
     *
     * @param SimpleXMLElement $parent   Parent Element
     * @param string           $category Category
     * @param array            $feeds    Feed properties
     */
    public function createCategory(SimpleXMLElement $parent, $category, array $feeds){
        $outline = $parent->addChild('outline');
        $outline->addAttribute('text', $category);
        $this->createEntries($outline, $feeds);
    }
}