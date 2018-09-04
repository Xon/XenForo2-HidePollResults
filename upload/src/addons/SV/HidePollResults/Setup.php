<?php

namespace SV\HidePollResults;

use SV\Utils\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
    // from https://github.com/Xon/XenForo2-Utils cloned to src/addons/SV/Utils
    use InstallerHelper;
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1()
    {
        $this->schemaManager()->alterTable('xf_poll', function (Alter $table) {
            $this->addOrChangeColumn($table, 'hide_results')->type('tinyint')->length(3)->setDefault(0);
            $this->addOrChangeColumn($table, 'until_close')->type('tinyint')->length(3)->setDefault(0);
        });

        \XF::app()->simpleCache()->setValue($this->addOn->getAddOnId(), 'HidePollResults', false);
    }

    public function upgrade2000200Step1()
    {
        $this->installStep1();
    }

    public function uninstallStep1()
    {
        $this->schemaManager()->alterTable('xf_poll', function (Alter $table) {
            $table->dropColumns(['hide_results', 'until_close']);
        });
    }
}