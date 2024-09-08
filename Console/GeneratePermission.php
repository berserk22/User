<?php

/**
 * @author Sergey Tevs
 * @email sergey@tevs.org
 */

namespace Modules\User\Console;

use Core\Application;
use Core\Console\Command;
use Modules\User\UserTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Config\Modules;

class GeneratePermission extends Command {

    use UserTrait;

    /**
     * @var array
     */
    private array $modules = [];

    private ?Application $application;

    /**
     * @param $application
     */
    public function __construct($application) {
        $this->application = $application;
        parent::__construct($application);
    }

    protected function configure(): void {
        $this->setName('permission:generate')
            ->setDescription("Generate Permission Group and Permission");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int {

        $this->modules = Modules::getModules();

        var_dump($this->modules);

        $this->success("Successfully created permission group");
        return 1;
    }

}
