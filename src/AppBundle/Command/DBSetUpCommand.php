<?php
/**
 * Project: feeio2   
 * File: DBSetUpCommand.php
 *
 * Author: Tomas Syrovy <syrovy.tom@gmail.com>
 * Date: 10.07.15
 * Version: 1.0
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DBSetUpCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('db:setup')
			->setDescription('Set up database')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{

		$command = $this->getApplication()->find('doctrine:schema:update');
		$arguments = array(
			'command' => 'doctrine:schema:update',
			'--force'  => true,
		);
		$input = new ArrayInput($arguments);
		$returnCode = $command->run($input, $output);

		$command = $this->getApplication()->find('doctrine:fixtures:load');
		$arguments = array(
			'command' => 'doctrine:fixtures:load',
		);
		$input = new ArrayInput($arguments);
		$returnCode = $command->run($input, $output);

		$output->writeln("=== DONE === ");
	}
}