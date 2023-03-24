<?php

declare(strict_types=1);

namespace Zayon\BehatGatherContextExtension\Tests\Behat\Contexts;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class TestContext implements Context
{
    private static string $workingDir;
    private static Filesystem $filesystem;
    private static string $phpBin;
    private Process $process;

    /**
     * @BeforeFeature
     */
    public static function beforeFeature(): void
    {
        self::$workingDir = sprintf('%s/%s/', sys_get_temp_dir(), uniqid('', true));
        self::$filesystem = new Filesystem();
        self::$phpBin = self::findPhpBinary();
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(): void
    {
        self::$filesystem->remove(self::$workingDir);
        self::$filesystem->mkdir(self::$workingDir);
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(): void
    {
        self::$filesystem->remove(self::$workingDir);
    }

    /**
     * @Given /^a file named "([^"]*)" with:$/
     */
    public function aFileNamedWith(string $filename, PyStringNode $content): void
    {
        $path = self::$workingDir . '/' . $filename;

        self::$filesystem->dumpFile($path, (string) $content);
    }

    /**
     * @When /^I run behat$/
     */
    public function iRunBehat(): void
    {
        $executablePath = BEHAT_BIN_PATH;

        $this->process = new Process(
            [
                self::$phpBin,
                $executablePath,
                '--strict',
                '-vvv',
                '--no-interaction',
                '--lang=en',
            ],
            self::$workingDir
        );
        $this->process->start();
        $this->process->wait();
    }

    /**
     * @Then /^it should pass$/
     */
    public function itShouldPass(): void
    {
        if ($this->process->getExitCode() === 0) {
            return;
        }

        throw new \DomainException(
            'Behat was expecting to pass, but failed with the following output:' . \PHP_EOL . \PHP_EOL .
            $this->getProcessOutput(),
        );
    }

    /**
     * @Then /^it should fail$/
     */
    public function itShouldFail(): void
    {
        if ($this->process->getExitCode() !== 0) {
            return;
        }

        throw new \DomainException(
            'Behat was expecting to fail, but passed with the following output:' . \PHP_EOL . \PHP_EOL .
            $this->getProcessOutput(),
        );
    }

    private function getProcessOutput(): string
    {
        return $this->process->getErrorOutput() . $this->process->getOutput();
    }

    /**
     * @throws \RuntimeException
     */
    private static function findPhpBinary(): string
    {
        $phpBinary = (new PhpExecutableFinder())->find();
        if ($phpBinary === false) {
            throw new \RuntimeException('Unable to find the PHP executable.');
        }

        return $phpBinary;
    }
}
