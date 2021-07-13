<?php


namespace Vokuro\Tasks;

use Phalcon\Cli\Task;
use TeamTNT\TNTSearch\TNTSearch;

class MainTask extends Task
{
    public function mainAction()
    {
        echo 'This is the default task and the default action' . PHP_EOL;
    }

    public function regenerateAction(int $count = 0)
    {
        echo 'This is the retenerate action' . PHP_EOL;
    }

    public function indexTablesAction()
    {
        /** @var TNTSearch $tnt */
        $tnt = $this->di->get('search');

        $indexer = $tnt->createIndex('name.index');
        $indexer->query('SELECT id, name, email FROM users;');
        //$indexer->setLanguage('german');
        $indexer->run();
    }
}
