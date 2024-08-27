<?php
/**
* @version      5.5.1 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
use Joomla\CMS\Pagination\PaginationObject;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die();

class Pagination extends \Joomla\CMS\Pagination\Pagination{
    
    protected function _buildDataObject()
    {
        $data = new \stdClass();

        // Build the additional URL parameters string.
        $params = '';

        if (!empty($this->additionalUrlParams)) {
            foreach ($this->additionalUrlParams as $key => $value) {
                $params .= '&' . $key . '=' . $value;
            }
        }

        $data->all = new PaginationObject(Text::_('JLIB_HTML_VIEW_ALL'), $this->prefix);

        if (!$this->viewall) {
            $data->all->base = '0';
            $data->all->link = Route::_($params . '&' . $this->prefix . 'limitstart=');
        }

        // Set the start and previous data objects.
        $data->start    = new PaginationObject(Text::_('JLIB_HTML_START'), $this->prefix);
        $data->previous = new PaginationObject(Text::_('JPREV'), $this->prefix);

        if ($this->pagesCurrent > 1) {
            $page = ($this->pagesCurrent - 2) * $this->limit;

            if ($this->hideEmptyLimitstart) {
                $data->start->link = Route::_($params . '&' . $this->prefix . 'limitstart=');
            } else {
                $data->start->link = Route::_($params . '&' . $this->prefix . 'limitstart=0');
            }

            $data->start->base    = '0';
            $data->previous->base = $page;

            if ($page === 0 && $this->hideEmptyLimitstart) {
                $data->previous->link = $data->start->link;
            } else {
                $data->previous->link = Route::_($params . '&' . $this->prefix . 'limitstart=' . $page);
            }
        }

        // Set the next and end data objects.
        $data->next = new PaginationObject(Text::_('JNEXT'), $this->prefix);
        $data->end  = new PaginationObject(Text::_('JLIB_HTML_END'), $this->prefix);

        if ($this->pagesCurrent < $this->pagesTotal) {
            $next = $this->pagesCurrent * $this->limit;
            $end  = ($this->pagesTotal - 1) * $this->limit;

            $data->next->base = $next;
            $data->next->link = Route::_($params . '&' . $this->prefix . 'limitstart=' . $next);
            $data->end->base  = $end;
            $data->end->link  = Route::_($params . '&' . $this->prefix . 'limitstart=' . $end);
        }

        $data->pages = [];
        $stop        = $this->pagesStop;

        for ($i = $this->pagesStart; $i <= $stop; $i++) {
            $offset = ($i - 1) * $this->limit;

            $data->pages[$i] = new PaginationObject($i, $this->prefix);

            if ($i != $this->pagesCurrent || $this->viewall) {
                $data->pages[$i]->base = $offset;

                if ($offset === 0 && $this->hideEmptyLimitstart) {
                    $data->pages[$i]->link = $data->start->link;
                } else {
                    $data->pages[$i]->link = Route::_($params . '&' . $this->prefix . 'limitstart=' . $offset);
                }
            } else {
                $data->pages[$i]->active = true;
            }
        }

        return $data;
    }
    
}