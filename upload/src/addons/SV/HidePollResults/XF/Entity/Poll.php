<?php

namespace SV\HidePollResults\XF\Entity;

use XF\Mvc\Entity\Structure;

/**
 * Extends \XF\Entity\Poll
 *
 * COLUMNS
 * @property bool hide_results
 * @property bool until_close
 */
class Poll extends XFCP_Poll
{
    /**
     * @param string|null $error
     * @return bool
     */
    public function canViewResults(&$error = null)
    {
        if (!$this->hide_results || !($this->Content instanceof Thread))
        {
            return parent::canViewResults($error);
        }

        if ($this->hide_results && $this->until_close && $this->isClosed())
        {
            return true;
        }

        $visitor = \XF::visitor();
        /** @var Thread $thread */
        $thread = $this->Content;
        $nodeId = $thread->node_id;

        if ($visitor->hasNodePermission($nodeId, 'bypassHiddenPollResults'))
        {
            return true;
        }

        $userId = $thread->user_id;
        if ($userId && $userId === $visitor->user_id &&
            $visitor->hasNodePermission($nodeId, 'bypassHiddenPollResultOwn'))
        {
            return true;
        }

        return false;
    }

    public static function getStructure(Structure $structure)
    {
        $structure = parent::getStructure($structure);

        $structure->columns['hide_results'] = ['type' => self::BOOL, 'default' => false];
        $structure->columns['until_close'] = ['type' => self::BOOL, 'default' => false];

        return $structure;
    }
}
