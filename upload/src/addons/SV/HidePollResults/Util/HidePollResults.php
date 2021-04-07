<?php

namespace SV\HidePollResults\Util;

use SV\HidePollResults\XF\Entity\Thread as ExtendedThreadEntity;
use XF\Http\Request as HttpRequest;
use XF\Mvc\Entity\Entity;
use XF\Service\AbstractService;
use XF\Service\Poll\Editor as PollEditorSvc;
use XF\Service\Poll\Creator as PollCreatorSvc;

/**
 * This is better suited as a helper but because helpers were introduced in XF 2.2 and we want to maintain compatibility
 * for XenForo 2.1 we will stick to Util for now.
 *
 * But this also brings another issue of not being to extend util classes.
 *
 * @since 2.2.1
 */
class HidePollResults
{
    /**
     * @param Entity $content
     * @param HttpRequest|null $request
     *
     * @return bool
     */
    public static function pollHideFormPresent(Entity $content, HttpRequest $request = null) : bool
    {
        if (!$content instanceof ExtendedThreadEntity || !$content->canHidePollResults())
        {
            return false;
        }

        $request = $request ?: \XF::app()->request();

        return $request->filter('poll.hide_poll_results_form', 'bool', false);
    }

    /**
     * @param AbstractService|PollCreatorSvc|PollEditorSvc $pollManagerSvc
     * @param HttpRequest|null $request
     */
    public static function setupPollManagerSvc(AbstractService $pollManagerSvc, HttpRequest $request = null)
    {
        if (!static::pollHideFormPresent($pollManagerSvc->getContent(), $request))
        {
            return;
        }

        $request = $request ?: \XF::app()->request();

        $pollInput = $request->filter([
            'poll' => [
                'hide_results' => 'bool',
                'until_close'  => 'bool',
            ]
        ]);
        $pollManagerSvc->setOptions([
            'hide_results' => $pollInput['poll']['hide_results'],
            'until_close'  => $pollInput['poll']['until_close']
        ]);
    }
}