<?php

namespace SV\HidePollResults\XF\Helper;

use SV\HidePollResults\Util\HidePollResults as HidePollResultsUtil;
use XF\Service\Poll\Creator as PollCreatorSvc;

/**
 * @since 2.2.1
 */
class Poll extends XFCP_Poll
{
    /**
     * We are directly extending this and not getPollInput() because XF does not pass $content to this method (WHY???).
     *
     * @param PollCreatorSvc $creator
     * @param array $pollInput
     *
     * @return PollCreatorSvc
     */
    public function configureCreatorFromInput(PollCreatorSvc $creator, array $pollInput)
    {
        $pollCreatorSvc = parent::configureCreatorFromInput($creator, $pollInput);

        HidePollResultsUtil::setupPollManagerSvc($pollCreatorSvc);

        return $pollCreatorSvc;
    }
}