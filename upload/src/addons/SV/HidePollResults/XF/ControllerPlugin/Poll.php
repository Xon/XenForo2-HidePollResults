<?php
/**
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace SV\HidePollResults\XF\ControllerPlugin;

use SV\HidePollResults\Util\HidePollResults as HidePollResultsUtil;
use SV\HidePollResults\XF\Entity\Thread;
use XF\Entity\Poll as PollEntity;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\Reply\View;
use XF\Poll\AbstractHandler;
use XF\Service\Poll\Creator as PollCreatorSvc;
use XF\Service\Poll\Editor as PollEditorSvc;

class Poll extends XFCP_Poll
{
    /**
     * @param string $contentType
     * @param Entity $content
     * @param array  $breadcrumbs
     * @return AbstractReply
     */
    public function actionCreate($contentType, Entity $content, array $breadcrumbs = [])
    {
        $reply = parent::actionCreate($contentType, $content, $breadcrumbs);

        if ($reply instanceof View && $content instanceof Thread)
        {
            $reply->setParam('canHidePollResults', $content->canHidePollResults());
        }

        return $reply;
    }

    /**
     * @param PollEntity $poll
     * @param array      $breadcrumbs
     * @return AbstractReply
     */
    public function actionEdit($poll, array $breadcrumbs = [])
    {
        $reply = parent::actionEdit($poll, $breadcrumbs);

        if ($reply instanceof View && $poll->Content instanceof Thread)
        {
            $reply->setParam('canHidePollResults', $poll->Content->canHidePollResults());
        }

        return $reply;
    }

    /**
     * Since 2.2 this is no longer used for threads but instead handled via the new helper
     *
     * @param string $contentType
     * @param Entity $content
     * @return PollCreatorSvc
     * @see \XF\Helper\Poll
     */
    public function setupPollCreate($contentType, Entity $content)
    {
        $creator = parent::setupPollCreate($contentType, $content);

        HidePollResultsUtil::setupPollManagerSvc($creator, $this->request);

        return $creator;
    }

    /**
     * This is still used in XF 2.2 because when editing polls, they go through their own page similar to
     * reporting contents.
     *
     * @param PollEntity      $poll
     * @param string          $contentType
     * @param Entity          $content
     * @param AbstractHandler $handler
     * @return PollEditorSvc
     */
    protected function setupPollEdit(PollEntity $poll, $contentType, Entity $content, AbstractHandler $handler)
    {
        $editor = parent::setupPollEdit($poll, $contentType, $content, $handler);

        HidePollResultsUtil::setupPollManagerSvc($editor, $this->request);

        return $editor;
    }
}
