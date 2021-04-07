<?php

namespace SV\HidePollResults\XF\ControllerPlugin;

use SV\HidePollResults\XF\Entity\Thread;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Reply\View;
use XF\Poll\AbstractHandler;
use SV\HidePollResults\Util\HidePollResults as HidePollResultsUtil;

class Poll extends XFCP_Poll
{
    /**
     * @param string $contentType
     * @param Entity $content
     * @param array  $breadcrumbs
     *
     * @return \XF\Mvc\Reply\AbstractReply|\XF\Mvc\Reply\Error|\XF\Mvc\Reply\Redirect|View
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
     * @param \XF\Entity\Poll $poll
     * @param array $breadcrumbs
     * @return \XF\Mvc\Reply\AbstractReply|\XF\Mvc\Reply\Error|\XF\Mvc\Reply\Redirect|View
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
     * @see \XF\Helper\Poll
     *
     * @param string $contentType
     * @param Entity $content
     * @return \XF\Service\Poll\Creator
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
     * @param \XF\Entity\Poll $poll
     * @param string          $contentType
     * @param Entity          $content
     * @param AbstractHandler $handler
     *
     * @return \XF\Service\Poll\Editor
     */
    public function setupPollEdit(\XF\Entity\Poll $poll, $contentType, Entity $content, AbstractHandler $handler)
    {
        $editor = parent::setupPollEdit($poll, $contentType, $content, $handler);

        HidePollResultsUtil::setupPollManagerSvc($editor, $this->request);

        return $editor;
    }

    /**
     * @deprecated Since 2.2.1
     *
     * @param Entity $content
     *
     * @return bool
     */
    protected function pollHideFormPresent(Entity $content)
    {
        return HidePollResultsUtil::pollHideFormPresent($content, $this->request);
    }
}
