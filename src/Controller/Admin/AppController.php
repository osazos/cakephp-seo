<?php

namespace Seo\Controller\Admin;

use App\Controller\Admin\AppController as BaseController;
use Cake\Event\Event;

class AppController extends BaseController
{

    /**
     * Initialize
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Before Filter
     * @param Cake\Event\Event $event The beforeFilter event
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }
}
