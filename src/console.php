<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use Symfony\Bridge\Twig\Translation\TwigExtractor;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Writer\TranslationWriter;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\Loader\XliffFileLoader;

use Blog\Entity\User;
use Blog\Entity\Post;

$console = new Application($app['app.name'], 'n/a');
$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);

/****************************************************/

$console->register('db:create')
    ->setDescription('Create application database for specified environment ("dev" by default)')
    ->setCode(function(InputInterface $input, OutputInterface $output) use ($app){
        $schema = $app['db']->getSchemaManager();
        $schema->dropDatabase($app['db.options']['path']);
        $schema->createDatabase($app['db.options']['path']);

        $tables = require $app['db.schema']['tables_path'];

        foreach ( $tables as $table )
        {
            if (!$schema->tablesExist(array($table->getName())))
            {
                $schema->createTable($table);
                $output->writeln(sprintf('Create table "%s".', $table->getName()));
            }
        }
    });

/****************************************************/

$console->register('db:create-user')
    ->setDescription('Create user')
    ->setDefinition(array(
        new InputArgument('username', InputArgument::REQUIRED, 'Username'),
        new InputArgument('password', InputArgument::REQUIRED, 'Password'),
    ))    
    ->setCode(function(InputInterface $input, OutputInterface $output) use ($app){
        $user = new User();
        $user->setUsername($input->getArgument('username'));

        $salt = uniqid(mt_rand());
        $encodedPassword = $app['security.encoder.digest']->encodePassword(
            $input->getArgument('password'), $salt
        );

        $user->setSalt($salt);
        $user->setPassword($encodedPassword);

        $user->setRoles(array('ROLE_ADMIN'));

        $app['repository.user']->save($user);
        $output->writeln(sprintf('User "%s" was created.', $user->getUsername()));
    });

/****************************************************/

$console->register('translations:extract')
    ->setDescription('Extract translation keys from views to .xlf file. It will not affect messages that already exist.')
    ->setDefinition(array(
        new InputOption('--locale', '-l', InputOption::VALUE_REQUIRED, 'Locale', $app['locale'])
    ))
    ->setCode(function(InputInterface $input, OutputInterface $output) use ($app){
        /**
         * This should be done to escape "Identifier "security.authentication_providers" is not defined." error
         *
         * Another trick could be: 
         *     $app['security.authentication_providers'] = array(null);
         */
        $app->boot();

        $twigExtractor    = new TwigExtractor($app['twig']);
        $messageCatalogue = new MessageCatalogue($input->getOption('locale'));
        $loader           = new XliffFileLoader();

        foreach ( $app['twig.path'] as $path ) {
            $twigExtractor->extract(realpath($path), $messageCatalogue);
        }

        // override messages that already have translation
        foreach ( $messageCatalogue->getDomains() as $domain ) 
        {
            // translations file
            $resource = $app['translations.path'].'/'.$input->getOption('locale').'/'
                .$domain.'.'.$input->getOption('locale').'.xlf';

            // merge collected messages with old ones
            if ( file_exists($resource) ) {
                $messageCatalogue->addCatalogue($loader->load(
                    $resource, $input->getOption('locale'), $domain
                ));
            }
        }

        $localeDir = $app['translations.path'].'/'.$input->getOption('locale');
        if ( !is_dir($localeDir) ) {
            mkdir($localeDir);
        }

        $writer = new TranslationWriter();
        $writer->addDumper('xliff', new XliffFileDumper());
        $writer->writeTranslations($messageCatalogue, 'xliff', array(
            'path' => $localeDir
        ));

        $output->writeln('Done.');
    });

/****************************************************/

$console->register('translations:add-unit')
    ->setDescription('Add a translation for custom message')
    ->setDefinition(array(
        new InputArgument('source', InputArgument::REQUIRED, 'Source string'),
        new InputArgument('target', InputArgument::REQUIRED, 'Target string'),
        new InputOption('--locale', '-l', InputOption::VALUE_REQUIRED, 'Locale', $app['locale']),
        new InputOption('--domain', '-d', InputOption::VALUE_REQUIRED, 'Translation domain', 'messages')
    ))
    ->setCode(function(InputInterface $input, OutputInterface $output) use ($app){

        // full path to translation file
        $resource = $app['translations.path'].'/'.$input->getOption('locale').'/'
            .$input->getOption('domain').'.'.$input->getOption('locale').'.xlf';

        if ( file_exists($resource) ) 
        {
            $loader = new XliffFileLoader();
            $messageCatalogue = $loader->load(
                $resource, $input->getOption('locale'), $input->getOption('domain')
            );
        }
        else 
        {
            $messageCatalogue = new MessageCatalogue($input->getOption('locale'));
        }

        $messageCatalogue->add(array(
            $input->getArgument('source') => $input->getArgument('target')
        ), $input->getOption('domain'));

        $resourceDir = dirname($resource);
        if ( !is_dir($resourceDir) ) {
            mkdir($resourceDir);
        }

        $writer = new TranslationWriter();
        $writer->addDumper('xliff', new XliffFileDumper());
        $writer->writeTranslations($messageCatalogue, 'xliff', array(
            'path' => $resourceDir
        ));

        $output->writeln('Done.');
    });

/****************************************************/

$console->register('faker:populate:posts')
    ->setDescription('Generate fake posts and save it in database')
    ->setDefinition(array(
        new InputOption('--count', '-c', InputOption::VALUE_REQUIRED, 'Fakes amount', 1000)
    ))
    ->setCode(function(InputInterface $input, OutputInterface $output) use ($app){
        $output->writeln('Generating...');

        $faker = $app['faker'];

        // wrap it with a transaction
        $app['db']->beginTransaction();

        try
        {
            for ($i=0;$i < $input->getOption('count'); $i++)
            {
                $post = new Post();

                $sentence = $faker->sentence(rand(5, 8));
                // remove dot
                $post->setTitle(substr($sentence, 0, strlen($sentence) - 1));

                $content = '';
                foreach ($faker->paragraphs(rand(1,6)) as $paragraph) {
                    $content .= "\n\n".$paragraph;
                }

                $post->setContent($content);

                $post->setCreatedAt($faker->dateTimeThisDecade);
                $post->setMetaTitle($post->getTitle());
                $post->setMetaKeywords(implode(',', $faker->words(rand(4,10))));
                $post->setMetaDescription($faker->paragraph);

                $app['repository.post']->save($post);
                unset($page); 
            }

            $app['db']->commit();
            $output->writeln('Done.'); 
        } catch (\Exception $e) {
            $app['db']->rollback();
            $output->writeln('Failed.');
        }
    });

/****************************************************/

return $console;
