<?php

namespace SV\HidePollResults\XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * Extends \XF\Entity\Thread
 */
class Thread extends XFCP_Thread
{
    public function canHidePollResults()
    {
        $visitor = \XF::visitor();

        return $visitor->hasNodePermission($this->node_id, 'hidePollResults');
    }
}