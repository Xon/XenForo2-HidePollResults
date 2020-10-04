<?php

namespace SV\HidePollResults;

use SV\StandardLib\InstallerHelper;
use XF\AddOn\AbstractSetup;
use XF\AddOn\StepRunnerInstallTrait;
use XF\AddOn\StepRunnerUninstallTrait;
use XF\AddOn\StepRunnerUpgradeTrait;
use XF\Db\Schema\Alter;

class Setup extends AbstractSetup
{
    use InstallerHelper;
    use StepRunnerInstallTrait;
    use StepRunnerUpgradeTrait;
    use StepRunnerUninstallTrait;

    public function installStep1()
    {
        $sm = $this->schemaManager();

        foreach ($this->getAlterTables() as $tableName => $callback)
        {
            if ($sm->tableExists($tableName))
            {
                $sm->alterTable($tableName, $callback);
            }
        }
    }

    public function installStep2()
    {
        \XF::app()->simpleCache()->setValue($this->addOn->getAddOnId(), 'HidePollResults', false);
    }

    public function upgrade2010000Step1()
    {
        $this->installStep1();
    }

    public function upgrade2010000Step2()
    {
        $this->installStep2();
    }

    public function upgrade2010000Step3()
    {
        $this->renamePhrases([
            'the_results_of_this_poll_are_hidden_until_manual' => 'svHidePoll_poll_results_hidden_until_manual_action',
            'the_results_of_this_poll_are_hidden_until_x'      => 'svHidePoll_poll_results_hidden_until_x',
            'hide_poll_results'                                => 'svHidePoll_hide_poll_results',
            'hide_until_close'                                 => 'svHidePoll_hide_until_close',
            'results_will_be_hidden_until_poll_closes_explain' => 'svHidePoll_hide_until_close_explain',
        ]);
    }

    public function upgrade2010000Step4()
    {
        /** @var \XF\Entity\Template $template */
        $template = \XF::finder('XF:Template')
                       ->where('title', 'thread_view_hidden_poll_results')
                       ->where('style_id', 0)
                       ->fetchOne();
        if ($template)
        {
            $template->title = 'svHidePoll_poll_macros_hide_results';
            $template->save();
        }
    }

    public function uninstallStep1()
    {
        $sm = $this->schemaManager();

        foreach ($this->getRemoveAlterTables() as $tableName => $callback)
        {
            if ($sm->tableExists($tableName))
            {
                $sm->alterTable($tableName, $callback);
            }
        }
    }

    /**
     * @return array
     */
    protected function getAlterTables()
    {
        $tables = [];

        $tables['xf_poll'] = function (Alter $table) {
            $this->addOrChangeColumn($table, 'hide_results')->type('tinyint')->length(1)->setDefault(0);
            $this->addOrChangeColumn($table, 'until_close')->type('tinyint')->length(1)->setDefault(0);
        };

        return $tables;
    }

    protected function getRemoveAlterTables()
    {
        $tables = [];

        $tables['xf_poll'] = function (Alter $table) {
            $table->dropColumns(['hide_results', 'until_close']);
        };

        return $tables;
    }
}