<?php

declare(strict_types=1);

namespace Leovie\PhpstanBaselineGuard\Command;

use Leovie\PhpstanBaselineGuard\Result\ExpectedIgnoredErrorsResult;
use Leovie\PhpstanBaselineGuard\Result\FewerIgnoredErrorsResult;
use Leovie\PhpstanBaselineGuard\Result\NoMaxIgnoredErrorsGivenResult;
use Leovie\PhpstanBaselineGuard\Result\TooManyIgnoredErrorsResult;
use Leovie\PhpstanBaselineGuard\Service\BaselineGuardService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'phpstan-baseline-guard')]
class PhpstanBaselineGuardCommand extends Command
{
    private const ARG_BASELINE_PATH = 'baseline-path';
    private const ARG_MAX_IGNORED_ERRORS = 'max-ignored-errors';

    public function __construct(
        private readonly BaselineGuardService $baselineGuardService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            name: self::ARG_BASELINE_PATH,
            mode: InputArgument::REQUIRED,
            description: 'Absolute path to baseline neon file',
        )->addOption(
            name: self::ARG_MAX_IGNORED_ERRORS,
            shortcut: 'm',
            mode: InputArgument::OPTIONAL,
            description: 'Max allowed count of ignored errors',
            default: null,
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $baselinePath = $this->getBaselinePath($input);
        if (!file_exists($baselinePath)) {
            $io->error(sprintf('Baseline not found in "%s".', $baselinePath));

            return Command::INVALID;
        }

        $maxIgnoredErrors = $this->getMaxIgnoredErrors($input);
        $baselineGuardResult = $this->baselineGuardService->guard($baselinePath, $maxIgnoredErrors);

        return match ($baselineGuardResult::class) {
            NoMaxIgnoredErrorsGivenResult::class => $this->handleNoMaxIgnoredErrorsGivenResult($io, $baselineGuardResult),
            ExpectedIgnoredErrorsResult::class => $this->handleExpectedIgnoredErrorsResult($io, $baselineGuardResult),
            FewerIgnoredErrorsResult::class => $this->handleFewerIgnoredErrorsResult($io, $baselineGuardResult),
            TooManyIgnoredErrorsResult::class => $this->handleTooManyIgnoredErrorsResult($io, $baselineGuardResult),
        };
    }

    private function getBaselinePath(InputInterface $input): string
    {
        /** @var string $baselinePath */
        $baselinePath = $input->getArgument(self::ARG_BASELINE_PATH);

        return $baselinePath;
    }

    private function getMaxIgnoredErrors(InputInterface $input): ?int
    {
        /** @var string|null $maxIgnoredErrors */
        $maxIgnoredErrors = $input->getOption(self::ARG_MAX_IGNORED_ERRORS);

        return $maxIgnoredErrors === null
            ? null
            : (int) $maxIgnoredErrors;
    }

    private function handleNoMaxIgnoredErrorsGivenResult(SymfonyStyle $io, NoMaxIgnoredErrorsGivenResult $result): int
    {
        $io->info(sprintf(
            'Your baseline contains %d ignored errors.',
            $result->getCountOfIgnoredErrors()
        ));

        return Command::SUCCESS;
    }

    private function handleExpectedIgnoredErrorsResult(SymfonyStyle $io, ExpectedIgnoredErrorsResult $result): int
    {
        $io->success(sprintf(
            'Your baseline contains %d ignored errors. - OK',
            $result->getCountOfIgnoredErrors()
        ));

        return Command::SUCCESS;
    }

    private function handleFewerIgnoredErrorsResult(SymfonyStyle $io, FewerIgnoredErrorsResult $result): int
    {
        $io->success(sprintf(
            'Your baseline contains %d ignored errors. - OK',
            $result->getCountOfIgnoredErrors()
        ));
        $io->note(
            "Baseline contains fewer ignored errors than allowed.\n"
            . 'Consider decreasing your ' . self::ARG_MAX_IGNORED_ERRORS . ' for the next run.'
        );

        return Command::SUCCESS;
    }

    private function handleTooManyIgnoredErrorsResult(SymfonyStyle $io, TooManyIgnoredErrorsResult $result): int
    {
        $io->error(sprintf(
            "Your baseline contains %d ignored errors. That's more than you allowed.",
            $result->getCountOfIgnoredErrors()
        ));

        return Command::FAILURE;
    }
}