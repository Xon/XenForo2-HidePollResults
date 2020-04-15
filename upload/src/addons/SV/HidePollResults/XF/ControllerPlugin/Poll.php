<?php

namespace SV\HidePollResults\XF\ControllerPlugin;

use SV\HidePollResults\XF\Entity\Thread;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Reply\View;
use XF\Poll\AbstractHandler;

class Poll extends XFCP_Poll
{
    public function actionCreate($contentType, Entity $content, array $breadcrumbs = [])
    {
        $reply = parent::actionCreate($contentType, $content, $breadcrumbs);

        if ($reply instanceof View && $content instanceof Thread)
        {
            $reply->setParam('canHidePollResults', $content->canHidePollResults());
        }

        return $reply;
    }

    public function actionEdit($poll, array $breadcrumbs = [])
    {
        $reply = parent::actionEdit($poll, $breadcrumbs);

        if ($reply instanceof View && $poll->Content instanceof Thread)
        {
            $reply->setParam('canHidePollResults', $poll->Content->canHidePollResults());
        }

        return $reply;
    }

    public function setupPollCreate($contentType, Entity $content)
    {
        $creator = parent::setupPollCreate($contentType, $content);

        if ($this->pollHideFormPresent($content))
        {
            $pollInput = $this->filter([
                'poll' => [
                    'hide_results' => 'bool',
                    'until_close'  => 'bool',
                ]
            ]);
            $creator->setOptions([
                'hide_results' => $pollInput['poll']['hide_results'],
                'until_close'  => $pollInput['poll']['until_close']
            ]);
        }

        return $creator;
    }

    public function setupPollEdit(\XF\Entity\Poll $poll, $contentType, Entity $content, AbstractHandler $handler)
    {
        $editor = parent::setupPollEdit($poll, $contentType, $content, $handler);

        if ($this->pollHideFormPresent($content))
        {
            $pollInput = $this->filter([
                'poll' => [
                    'hide_results' => 'bool',
                    'until_close'  => 'bool',
                ]
            ]);
            $editor->setOptions([
                'hide_results' => $pollInput['poll']['hide_results'],
                'until_close'  => $pollInput['poll']['until_close']
            ]);
        }

        return $editor;
    }

    /**
     * @param Entity $content
     * @return bool
     */
    protected function pollHideFormPresent(Entity $content)
    {
        if ($content instanceof Thread && $content->canHidePollResults())
        {
            return $this->filter('poll.hide_poll_results_form', 'bool', false);
        }

        return false;
    }
}
