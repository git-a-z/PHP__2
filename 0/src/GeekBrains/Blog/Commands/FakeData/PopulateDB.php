<?php

namespace GeekBrains\Blog\Commands\FakeData;

use Faker\Generator;
use GeekBrains\Blog\Comment;
use GeekBrains\Blog\Post;
use GeekBrains\Blog\Repositories\CommentsRepositoryInterface;
use GeekBrains\Blog\Repositories\PostsRepositoryInterface;
use GeekBrains\Blog\Repositories\UsersRepositoryInterface;
use GeekBrains\Blog\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDB extends Command
{
    // Внедряем генератор тестовых данных и
    // репозитории пользователей и статей
    public function __construct(
        private Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addOption(
                'users-number',
                'un',
                InputOption::VALUE_OPTIONAL,
                'Users number',
            )
            ->addOption(
                'posts-number',
                'pn',
                InputOption::VALUE_OPTIONAL,
                'Posts number',
            )
            ->addOption(
                'comments-number',
                'cn',
                InputOption::VALUE_OPTIONAL,
                'Comment number',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        // Получаем значения опций
        $usersNumber = $input->getOption('users-number');
        $usersNumber = empty($usersNumber) ? 2 : $usersNumber;
        $postsNumber = $input->getOption('posts-number');
        $postsNumber = empty($postsNumber) ? 2 : $postsNumber;
        $commentsNumber = $input->getOption('comments-number');
        $commentsNumber = empty($commentsNumber) ? 2 : $commentsNumber;

        // Создаём пользователей
        $users = [];
        for ($i = 0; $i < $usersNumber; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getUsername());
        }

        // От имени каждого пользователя
        // создаём статьи и комментарии
        foreach ($users as $user) {
            for ($i = 0; $i < $postsNumber; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());

                for ($j = 0; $j < $commentsNumber; $j++) {
                    $comment = $this->createFakeComment($post, $user);
                    $output->writeln('Comment created: ' . $comment->getText());
                }
            }
        }

        return Command::SUCCESS;
    }

    private function createFakeUser(): User
    {
        $user = User::createFrom(
            // Генерируем имя пользователя
            $this->faker->userName,
            // Генерируем пароль
            $this->faker->password,
            // Генерируем имя
            $this->faker->firstName,
            // Генерируем фамилию
            $this->faker->lastName
        );
        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $user): Post
    {
        $post = new Post(
            $user->getId(),
            // Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
            // Генерируем текст
            $this->faker->realText
        );
        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }

    private function createFakeComment(Post $post, User $user): Comment
    {
        $comment = new Comment(
            $post->getId(),
            $user->getId(),
            $this->faker->realText
        );
        // Сохраняем статью в репозиторий
        $this->commentsRepository->save($comment);
        return $comment;
    }
}