<?php

namespace SV\HidePollResults\XF\Entity;

/*
 * Extends \XF\Entity\Poll
 */
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null poll_id
 * @property string content_type
 * @property int content_id
 * @property string question
 * @property array responses
 * @property int voter_count
 * @property bool public_votes
 * @property int max_votes
 * @property int close_date
 * @property bool change_vote
 * @property bool view_results_unvoted
 * @property bool hide_results
 * @property bool until_close
 *
 * GETTERS
 * @property \XF\Poll\AbstractHandler|null Handler
 * @property Entity|null Content
 *
 * RELATIONS
 * @property \XF\Entity\PollResponse[] Responses
 * @property \XF\Entity\PollVote[] Votes
 */
class Poll extends XFCP_Poll
{
    public function canViewResults(&$error = null)
    {
        if (!$this->hide_results)
        {
            return parent::canViewResults($error);
        }

        if (
            $this->hide_results &&
            $this->until_close &&
            $this->isClosed()
        )
        {
            return true;
        }

        if (\XF::visitor()->hasPermission('forum', 'bypassHiddenPollResults'))
        {
            return true;
        }

        if (
            $this->Content->getValue('user_id') == \XF::visitor()->user_id &&
            \XF::visitor()->hasPermission('forum', 'bypassHiddenPollResultOwn')
        )
        {
            return true;
        }

        $error = null;

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
