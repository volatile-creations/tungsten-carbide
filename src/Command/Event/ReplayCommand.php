<?php
declare(strict_types=1);

namespace App\Command\Event;

use App\MessageConsumer\MessageConsumerChain;
use ArrayObject;
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

    public function __construct(
        private readonly ArrayObject $messageRepositories,
        private readonly ArrayObject $messageConsumers,
        string $name = null
    ) {
        parent::__construct($name);
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
            description: 'How many messages are used at-most per aggregate batch.',
            default: 100
        );
    }

    protected function interact(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $io = new SymfonyStyle($input, $output);

        if ($input->getArgument(self::ARGUMENT_REPOSITORY) === null) {
            $input->setArgument(
                self::ARGUMENT_REPOSITORY,
                $io->askQuestion(
                    new ChoiceQuestion(
                        'Select a message repository',
                        array_keys($this->messageRepositories->getArrayCopy())
                    )
                )
            );
        }

        if ($input->getOption(self::OPTION_CONSUMER) === []) {
            $consumers = [];
            $break = 'Done adding consumers.';
            $options = [...array_keys($this->messageConsumers->getArrayCopy()), $break];
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
            fn (string $consumer) => $this->messageConsumers->offsetGet($consumer),
            $input->getOption(self::OPTION_CONSUMER)
        );

        if (empty($consumers)) {
            $io->error('Missing consumers. Nothing to replay against.');
            return self::INVALID;
        }

        $repositoryName = $input->getArgument(self::ARGUMENT_REPOSITORY);
        $repository = $this->messageRepositories->offsetGet($repositoryName);

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

        do {
            $io->write(
                sprintf(
                    "[%s / %s]\t",
                    number_format($cursor->offset()),
                    number_format($cursor->limit())
                )
            );

            $result = $replayMessages->replayBatch($cursor);
            $cursor = $result->cursor();

            $io->writeln(
                sprintf(
                    'Handled %d messages',
                    $result->messagesHandled()
                )
            );
        } while ($result->messagesHandled() > 0);

        return self::SUCCESS;
    }
}