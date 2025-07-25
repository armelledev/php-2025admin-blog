<?php

declare (strict_types=1);
namespace Rector\Parallel\Command;

use Rector\ChangesReporting\Output\JsonOutputFormatter;
use Rector\Configuration\Option;
use Rector\FileSystem\FilePathHelper;
use RectorPrefix202507\Symfony\Component\Console\Command\Command;
use RectorPrefix202507\Symfony\Component\Console\Input\InputInterface;
use RectorPrefix202507\Symplify\EasyParallel\Exception\ParallelShouldNotHappenException;
use RectorPrefix202507\Symplify\EasyParallel\Reflection\CommandFromReflectionFactory;
/**
 * @see \Rector\Tests\Parallel\Command\WorkerCommandLineFactoryTest
 * @todo possibly extract to symplify/easy-parallel
 */
final class WorkerCommandLineFactory
{
    /**
     * @readonly
     */
    private CommandFromReflectionFactory $commandFromReflectionFactory;
    /**
     * @readonly
     */
    private FilePathHelper $filePathHelper;
    /**
     * @var string
     */
    private const OPTION_DASHES = '--';
    public function __construct(CommandFromReflectionFactory $commandFromReflectionFactory, FilePathHelper $filePathHelper)
    {
        $this->commandFromReflectionFactory = $commandFromReflectionFactory;
        $this->filePathHelper = $filePathHelper;
    }
    /**
     * @param class-string<Command> $mainCommandClass
     */
    public function create(string $mainScript, string $mainCommandClass, string $workerCommandName, InputInterface $input, string $identifier, int $port) : string
    {
        $commandArguments = \array_slice($_SERVER['argv'], 1);
        // add implicit "process" command name if missing
        if ($commandArguments !== [] && ($commandArguments[0] !== 'process' && $commandArguments[0] !== 'p') && !\defined('PHPUNIT_COMPOSER_INSTALL')) {
            $commandArguments = \array_merge(['process'], $commandArguments);
        }
        $args = \array_merge([\PHP_BINARY, $mainScript], $commandArguments);
        $workerCommandArray = [];
        $mainCommand = $this->commandFromReflectionFactory->create($mainCommandClass);
        if ($mainCommand->getName() === null) {
            $errorMessage = \sprintf('The command name for "%s" is missing', \get_class($mainCommand));
            throw new ParallelShouldNotHappenException($errorMessage);
        }
        $mainCommandName = $mainCommand->getName();
        $mainCommandNames = [$mainCommandName, $mainCommandName[0]];
        foreach ($args as $arg) {
            // skip command name
            if (\in_array($arg, $mainCommandNames, \true)) {
                break;
            }
            $workerCommandArray[] = \escapeshellarg((string) $arg);
        }
        $workerCommandArray[] = $workerCommandName;
        $mainCommandOptionNames = $this->getCommandOptionNames($mainCommand);
        $workerCommandOptions = $this->mirrorCommandOptions($input, $mainCommandOptionNames);
        $workerCommandArray = \array_merge($workerCommandArray, $workerCommandOptions);
        // for TCP local server
        $workerCommandArray[] = '--port';
        $workerCommandArray[] = $port;
        $workerCommandArray[] = '--identifier';
        $workerCommandArray[] = \escapeshellarg($identifier);
        /** @var string[] $paths */
        $paths = $input->getArgument(Option::SOURCE);
        foreach ($paths as $path) {
            $workerCommandArray[] = \escapeshellarg($path);
        }
        // set json output
        $workerCommandArray[] = self::OPTION_DASHES . Option::OUTPUT_FORMAT;
        $workerCommandArray[] = \escapeshellarg(JsonOutputFormatter::NAME);
        // disable colors, breaks json_decode() otherwise
        // @see https://github.com/symfony/symfony/issues/1238
        $workerCommandArray[] = '--no-ansi';
        if ($input->hasOption(Option::CONFIG)) {
            $workerCommandArray[] = '--config';
            /**
             * On parallel, the command is generated with `--config` addition
             * Using escapeshellarg() to ensure the --config path escaped, even when it has a space.
             *
             * eg:
             *    --config /path/e2e/parallel with space/rector.php
             *
             * that can cause error:
             *
             *    File /rector-src/e2e/parallel\" was not found
             *
             * the escaped result is:
             *
             *    --config '/path/e2e/parallel with space/rector.php'
             *
             * tested in macOS and Ubuntu (github action)
             */
            $config = (string) $input->getOption(Option::CONFIG);
            $workerCommandArray[] = \escapeshellarg($this->filePathHelper->relativePath($config));
        }
        if ($input->getOption(Option::ONLY) !== null) {
            $workerCommandArray[] = self::OPTION_DASHES . Option::ONLY;
            $workerCommandArray[] = \escapeshellarg((string) $input->getOption(Option::ONLY));
        }
        return \implode(' ', $workerCommandArray);
    }
    private function shouldSkipOption(InputInterface $input, string $optionName) : bool
    {
        if (!$input->hasOption($optionName)) {
            return \true;
        }
        // skip output format, not relevant in parallel worker command
        return $optionName === Option::OUTPUT_FORMAT;
    }
    /**
     * @return string[]
     */
    private function getCommandOptionNames(Command $command) : array
    {
        $inputDefinition = $command->getDefinition();
        $optionNames = [];
        foreach ($inputDefinition->getOptions() as $inputOption) {
            $optionNames[] = $inputOption->getName();
        }
        return $optionNames;
    }
    /**
     * Keeps all options that are allowed in check command options
     *
     * @param string[] $mainCommandOptionNames
     * @return string[]
     */
    private function mirrorCommandOptions(InputInterface $input, array $mainCommandOptionNames) : array
    {
        $workerCommandOptions = [];
        foreach ($mainCommandOptionNames as $mainCommandOptionName) {
            if ($this->shouldSkipOption($input, $mainCommandOptionName)) {
                continue;
            }
            /** @var bool|string|null $optionValue */
            $optionValue = $input->getOption($mainCommandOptionName);
            // skip clutter
            if ($optionValue === null) {
                continue;
            }
            if (\is_bool($optionValue)) {
                if ($optionValue) {
                    $workerCommandOptions[] = self::OPTION_DASHES . $mainCommandOptionName;
                }
                continue;
            }
            if ($mainCommandOptionName === 'memory-limit') {
                // symfony/console does not accept -1 as value without assign
                $workerCommandOptions[] = self::OPTION_DASHES . $mainCommandOptionName . '=' . \escapeshellarg($optionValue);
            } else {
                $workerCommandOptions[] = self::OPTION_DASHES . $mainCommandOptionName;
                $workerCommandOptions[] = \escapeshellarg($optionValue);
            }
        }
        return $workerCommandOptions;
    }
}
