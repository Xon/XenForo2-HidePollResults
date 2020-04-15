<?php

namespace SV\HidePollResults\XF\Entity;

/**
 * Extends \XF\Entity\Thread
 */
class Thread extends XFCP_Thread
{
    /**
     * @return bool
     */
    public function canHidePollResults()
    {
        $visitor = \XF::visitor();

        return $visitor->hasNodePermission($this->node_id, 'hidePollResults');
    }
}