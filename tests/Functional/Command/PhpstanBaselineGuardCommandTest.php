<?php

declare(strict_types=1);

namespace Leovie\PhpstanBaselineGuard\Tests\Functional\Command;

use PHPUnit\Framework\TestCase;
use Leovie\PhpstanBaselineGuard\Command\PhpstanBaselineGuardCommand;
use Leovie\PhpstanBaselineGuard\Neon\NeonDecoder;
use Leovie\PhpstanBaselineGuard\Parser\BaselineParser;
use Leovie\PhpstanBaselineGuard\Service\BaselineGuardService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;

class PhpstanBaselineGuardCommandTest extends TestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->commandTester = new CommandTester(new PhpstanBaselineGuardCommand(
            new BaselineGuardService(
                new NeonDecoder(),
                new BaselineParser()
            )
        ));
    }

    public function testCommandFailsWithoutBaselinePath(): void
    {
        self::expectException(RuntimeException::class);

        $this->commandTester->execute([]);
    }

    /** @dataProvider commandProvider */
    public function testCommand(array $expectedOutputs, int $expectedReturnCode, array $inputs): void
    {
        $this->commandTester->execute($inputs);

        self::assertSame($expectedReturnCode, $this->commandTester->getStatusCode());

        foreach ($expectedOutputs as $expectedOutput) {
            self::assertStringContainsString(
                $expectedOutput,
                $this->commandTester->getDisplay(true)
            );
        }
    }

    public function commandProvider(): array
    {
        $s = DIRECTORY_SEPARATOR;
        $baselinePath = __DIR__ . "${s}..${s}..${s}_testdata${s}baseline${s}non_empty.neon";

        return [
            'NoMaxIgnoredErrorsGiven' => [
                'expectedOutputs' => [
                    'Your baseline contains 2 ignored errors.'
                ],
                'expectedReturnCode' => Command::SUCCESS,
                'inputs' => [
                    'baseline-path' => $baselinePath
                ],
            ],
            'FewerIgnoredErrorsResult' => [
                'expectedOutputs' => [
                    'Your baseline contains 2 ignored errors. - OK',
                    'Baseline contains fewer ignored errors than allowed.',
                    'Consider decreasing your max-ignored-errors for the next run.',
                ],
                'expectedReturnCode' => Command::SUCCESS,
                'inputs' => [
                    'baseline-path' => $baselinePath,
                    '--max-ignored-errors' => 3,
                ],
            ],
            'TooManyIgnoredErrors' => [
                'expectedOutputs' => [
                    "Your baseline contains 2 ignored errors. That's more than you allowed.",
                ],
                'expectedReturnCode' => Command::FAILURE,
                'inputs' => [
                    'baseline-path' => $baselinePath,
                    '--max-ignored-errors' => 1,
                ],
            ],
            'ExpectedIgnoredErrors' => [
                'expectedOutputs' => [
                    "Your baseline contains 2 ignored errors. - OK",
                ],
                'expectedReturnCode' => Command::SUCCESS,
                'inputs' => [
                    'baseline-path' => $baselinePath,
                    '--max-ignored-errors' => 2,
                ],
            ],
        ];
    }
}