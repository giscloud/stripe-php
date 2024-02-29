<?php

namespace Stripe\Util;

class AutoPagingIterator implements \Iterator
{
    private $lastId = null;
    private $page = null;
    private $params = array();

    public function __construct($collection, $params)
    {
        $this->page = $collection;
        $this->params = $params;
    }

    #[\ReturnTypeWillChange] // temporarily suppressed (php 8.x expects explicit return type, not phpdoc)
    public function rewind()
    {
        // Actually rewinding would require making a copy of the original page.
    }

    #[\ReturnTypeWillChange] // temporarily suppressed (php 8.x expects explicit return type, not phpdoc)
    public function current()
    {
        $item = current($this->page->data);
        $this->lastId = $item !== false ? $item['id'] : null;
        return $item;
    }

    #[\ReturnTypeWillChange] // temporarily suppressed (php 8.x expects explicit return type, not phpdoc)
    public function key()
    {
        return key($this->page->data);
    }

    #[\ReturnTypeWillChange] // temporarily suppressed (php 8.x expects explicit return type, not phpdoc)
    public function next()
    {
        $item = next($this->page->data);
        if ($item === false) {
            // If we've run out of data on the current page, try to fetch another one
            if ($this->page['has_more']) {
                $this->params = array_merge(
                    $this->params ? $this->params : array(),
                    array('starting_after' => $this->lastId)
                );
                $this->page = $this->page->all($this->params);
            } else {
                return false;
            }
        }
    }

    #[\ReturnTypeWillChange] // temporarily suppressed (php 8.x expects explicit return type, not phpdoc)
    public function valid()
    {
        $key = key($this->page->data);
        $valid = ($key !== null && $key !== false);
        return $valid;
    }
}
