<?php

namespace SV\HidePollResults\XF\ControllerPlugin;

use XF\Mvc\Entity\Entity;

class Poll extends XFCP_Poll {

	public function getPollInput() {
		$upstream = parent::getPollInput();

		if($this->pollHideFormPresent()){
			$input = $this->filter([
				'poll' => [
					'hide_results' => 'bool',
					'until_close' => 'bool',
				]
			]);

			return array_merge($input['poll'], $upstream);
		} else {
			return $upstream;
		}
	}

	public function setupPollCreate($contentType, Entity $content) {
		$creator = parent::setupPollCreate($contentType, $content);

		$pollInput = $this->getPollInput();

		if($this->pollHideFormPresent()) {
			$creator->setOptions([
				'hide_results' => $pollInput['hide_results'],
				'until_close'  => $pollInput['until_close']
			]);
		}

		return $creator;
	}

	public function setupPollEdit(\XF\Entity\Poll $poll, $contentType, Entity $content, \XF\Poll\AbstractHandler $handler) {
		$editor = parent::setupPollEdit($poll, $contentType, $content, $handler);

		$pollInput = $this->getPollInput();

		if($this->pollHideFormPresent()) {
			$editor->setOptions([
				'hide_results' => $pollInput['hide_results'],
				'until_close'  => $pollInput['until_close']
			]);
		}

		return $editor;
	}

	protected function pollHideFormPresent() {
		return $this->filter(['poll'=> ['hide_poll_results_form'=> 'uint']])['poll']['hide_poll_results_form'];
	}
}