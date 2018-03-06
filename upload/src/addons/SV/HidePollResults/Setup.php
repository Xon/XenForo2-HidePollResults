<?php

namespace SV\HidePollResults;

use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;
use XF\Db\Schema\Create;

class Setup extends AbstractSetup
{
	use StepRunnerInstallTrait;
	use StepRunnerUpgradeTrait;
	use StepRunnerUninstallTrait;

	public function installStep1() {
		$this->schemaManager()->alterTable('xf_poll', function(Alter $table) {
			$table->addColumn('hide_results')->type('tinyint')->length(3)->setDefault(0);
			$table->addColumn('until_close')->type('tinyint')->length(3)->setDefault(0);
		});

		\XF::app()->simpleCache()->setValue($this->addOn->getAddOnId(), 'HidePollResults', false);
	}

	public function uninstallStep1(){
		$this->schemaManager()->alterTable('xf_poll', function(Alter $table) {
			$table->dropColumns(['hide_results', 'until_close']);
		});
	}
}