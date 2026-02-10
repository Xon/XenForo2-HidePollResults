<?php

namespace SV\HidePollResults\XF\Entity;

/**
 * @extends \XF\Entity\Thread
 */
class Thread extends XFCP_Thread
{
    public function canHidePollResults(): bool
    {
        return \XF::visitor()->hasNodePermission($this->node_id, 'hidePollResults');
    }
}