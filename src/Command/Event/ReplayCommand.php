<?php
declare(strict_types=1);

namespace App\Command\Event;

use App\MessageConsumer\MessageConsumerChain;
use EventSauce\EventSourcing\MessageConsumer;
use EventSauce\EventSourcing\MessageRepository;
use EventSauce\EventSourcing\OffsetCursor;
use EventSauce\EventSourcing\ReplayingMessages\ReplayMessages;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:event:replay',
    description: 'Replay events from a given repository for a given consumer'
)]
final class ReplayCommand extends Command
{
    private const ARGUMENT_REPOSITORY = 'repository';
    private const OPTION_CONSUMER = 'consumer';
    private const OPTION_BATCH_SIZE = 'batch-size';

    /** @var array<string,MessageRepository> */
    private array $repositories = [];

    /** @var array<string,MessageConsumer> */
    private array $consumers = [];

    public function registerRepository(
        string $name,
        MessageRepository $repository
    ): void {
        $this->repositories[$name] = $repository;
    }

    public function registerConsumer(
        string $name,
        MessageConsumer $consumer
    ): void {
        $this->consumers[$name] = $consumer;
    }

    protected function configure(): void
    {
        $this->addArgument(
            name: self::ARGUMENT_REPOSITORY,
            mode: InputArgument::REQUIRED,
            description: 'The service identifier of the repository.',
        );
        $this->addOption(
            name: self::OPTION_CONSUMER,
            mode: InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            description: 'The full service identifier of the consumer to use.',
        );
        $this->addOption(
            name: self::OPTION_BATCH_SIZE,
            mode: InputOption::VALUE_REQUIRED,
            description: 'How many messages are consumed at-most simultaneously.',
            default: 100
        );
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ): void {
        if ($input->getOption(self::OPTION_CONSUMER) === []) {
            $io = new SymfonyStyle($input, $output);
            $consumers = [];
            $break = 'Done adding consumers.';
            $options = [...array_keys($this->consumers), $break];
            $question = new ChoiceQuestion(
                'Which consumer should be used?',
                $options
            );

            do {
                $consumer = $io->askQuestion($question);

                if (!empty($consumer) && $consumer !== $break) {
                    $consumers[] = $consumer;
                }
            } while (!empty($consumer) && $consumer !== $break);

            $input->setOption(self::OPTION_CONSUMER, $consumers);
        }
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);
        $consumers = array_map(
            fn (string $consumer) => $this->consumers[$consumer],
            $input->getOption(self::OPTION_CONSUMER)
        );

        if (empty($consumers)) {
            $io->error('Missing consumers. Nothing to replay against.');
            return self::INVALID;
        }

        $repositoryName = $input->getArgument(self::ARGUMENT_REPOSITORY);
        $repository = $this->repositories[$repositoryName] ?? null;

        if (!$repository instanceof MessageRepository) {
            $io->error(
                sprintf(
                    'Missing or invalid message repository "%s" provided.',
                    $repositoryName
                )
            );
            return self::INVALID;
        }

        $replayMessages = new ReplayMessages(
            $repository,
            new MessageConsumerChain(...$consumers)
        );

        $cursor = OffsetCursor::fromStart(
            limit: (int)$input->getOption(self::OPTION_BATCH_SIZE)
        );

        $batch = 0;

        process_batch:
        $io->write(sprintf('Batch #%d: ', ++$batch));
        $result = $replayMessages->replayBatch($cursor);
        $cursor = $result->cursor();
        $io->writeln(
            sprintf('Handled %d messages', $result->messagesHandled())
        );

        if ($result->messagesHandled() > 0) {
            goto process_batch;
        }

        return self::SUCCESS;
    }
}