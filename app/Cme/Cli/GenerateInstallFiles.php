<?php
namespace App\Cme\Cli;

use Way\Generators\Commands\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

use Way\Generators\Generator;
use Way\Generators\Filesystem\Filesystem;
use Way\Generators\Compilers\TemplateCompiler;

use Way\Generators\Syntax\DroppedTable;

use Xethron\MigrationsGenerator\Generators\SchemaGenerator;
use Xethron\MigrationsGenerator\MethodNotFoundException;
use Xethron\MigrationsGenerator\Syntax\AddToTable;
use Xethron\MigrationsGenerator\Syntax\AddForeignKeysToTable;
use Xethron\MigrationsGenerator\Syntax\RemoveForeignKeysFromTable;

use Illuminate\Config\Repository as Config;

class GenerateInstallFiles extends GeneratorCommand
{

  /**
   * The console command name.
   * @var string
   */
  protected $name = 'cme:generate-db';

  /**
   * The console command description.
   * @var string
   */
  protected $description = 'Generate DB install files for CME';

  /**
   * @var \Way\Generators\Filesystem\Filesystem
   */
  protected $file;

  /**
   * @var \Way\Generators\Compilers\TemplateCompiler
   */
  protected $compiler;

  /**
   * @var \Illuminate\Database\Migrations\MigrationRepositoryInterface $repository
   */
  protected $repository;

  /**
   * @var \Illuminate\Config\Repository $config
   */
  protected $config;

  /**
   * @var \Xethron\MigrationsGenerator\Generators\SchemaGenerator
   */
  protected $schemaGenerator;

  /**
   * Array of Fields to create in a new Migration
   * Namely: Columns, Indexes and Foreign Keys
   * @var array
   */
  protected $fields = array();

  /**
   * @var string
   */
  protected $migrationName;

  /**
   * @var string
   */
  protected $method;

  /**
   * @var string
   */
  protected $table;

  public function __construct()
  {
    $this->file     = new Filesystem();
    $generator      = new Generator($this->file);
    $this->compiler = new TemplateCompiler();
    $this->config   = config();

    parent::__construct($generator);
  }

  /**
   * Execute the console command.
   *
   * @return void
   */
  public function handle()
  {
    $this->info('Using connection: ' . $this->option('connection') . "\n");
    $this->schemaGenerator = new SchemaGenerator(
      $this->option('connection'),
      $this->option('defaultIndexNames'),
      $this->option('defaultFKNames')
    );

    $tables = $this->schemaGenerator->getTables();
    $tables = $this->removeExcludedTables($tables);
    $this->info("Setting up Tables and Index Migrations");
    $this->generate('create', $tables);
    $this->info("\nSetting up Foreign Key Migrations\n");
    $this->generate('foreign_keys', $tables);
    $this->info("\nFinished!\n");
  }

  /**
   * Generate Migrations
   *
   * @param  string $method Create Tables or Foreign Keys ['create', 'foreign_keys']
   * @param  array  $tables List of tables to create migrations for
   *
   * @throws MethodNotFoundException
   * @return void
   */
  protected function generate($method, $tables)
  {
    if($method == 'create')
    {
      $function = 'getFields';
      $prefix   = 'create';
    }
    elseif($method = 'foreign_keys')
    {
      $function = 'getForeignKeyConstraints';
      $prefix   = 'add_foreign_keys_to';
      $method   = 'table';
    }
    else
    {
      throw new MethodNotFoundException($method);
    }

    $templatePath = $this->getTemplatePath();
    foreach($tables as $table)
    {
      $this->migrationName = $prefix . '_' . $table . '_table';
      $this->method        = $method;
      $this->table         = $table;
      $this->fields        = $this->schemaGenerator->{$function}($table);
      if($this->fields)
      {
        $template = $this->compiler->compile(
          $this->file->get($templatePath),
          $this->getTemplateData()
        );

        $filePathToGenerate = $this->getFileGenerationPath();
        file_put_contents($filePathToGenerate, $template);
        $this->info("Created: {$filePathToGenerate}");
      }
    }
  }

  /**
   * The path where the file will be created
   *
   * @return string
   */
  protected function getFileGenerationPath()
  {
    $path = $this->option('path');
    if(!$path)
    {
      $path = 'app/Cme/Install';
    }
    $fileName = ucwords(camel_case($this->migrationName)) . '.php';

    return "{$path}/{$fileName}";
  }

  /**
   * Get path to template for generator
   *
   * @return string
   */
  protected function getTemplatePath()
  {
    return 'app/Cme/Install/installTable.txt';
  }

  /**
   * Fetch the template data
   *
   * @return array
   */
  protected function getTemplateData()
  {
    if($this->method == 'create')
    {
      $up   = (new AddToTable($this->file, $this->compiler))->run(
        $this->fields,
        $this->table,
        'create'
      );
      $down = (new DroppedTable)->drop($this->table);
    }
    else
    {
      $up   = (new AddForeignKeysToTable($this->file, $this->compiler))->run(
        $this->fields,
        $this->table
      );
      $down = (
      new RemoveForeignKeysFromTable(
        $this->file, $this->compiler
      )
      )->run($this->fields, $this->table);
    }

    $up = str_replace("\r\n", "\n", $up);
    $down = str_replace("\r\n", "\n", $down);

    $up   = str_replace("'{$this->table}'", "\$this->table", $up);
    $down = str_replace("'{$this->table}'", "\$this->table", $down);
    return [
      'CLASS' => ucwords(camel_case($this->migrationName)),
      'UP'    => $up,
      'DOWN'  => $down,
      'TABLE' => $this->table
    ];
  }

  /**
   * Get the console command options.
   *
   * @return array
   */
  protected function getOptions()
  {
    return [
      [
        'connection',
        'c',
        InputOption::VALUE_OPTIONAL,
        'The database connection to use.',
        $this->config->get('database.default')
      ],
      [
        'ignore',
        'i',
        InputOption::VALUE_OPTIONAL,
        'A list of Tables you wish to ignore, separated by a comma: users,posts,comments'
      ],
      [
        'path',
        'p',
        InputOption::VALUE_OPTIONAL,
        'Where should the file be created?'
      ],
      [
        'templatePath',
        'tp',
        InputOption::VALUE_OPTIONAL,
        'The location of the template for this generator'
      ],
      [
        'defaultIndexNames',
        null,
        InputOption::VALUE_NONE,
        'Don\'t use db index names for migrations'
      ],
      [
        'defaultFKNames',
        null,
        InputOption::VALUE_NONE,
        'Don\'t use db foreign key names for migrations'
      ],
    ];
  }

  /**
   * Remove all the tables to exclude from the array of tables
   *
   * @param $tables
   *
   * @return array
   */
  protected function removeExcludedTables($tables)
  {
    $excludes = $this->getExcludedTables();
    $tables   = array_diff($tables, $excludes);
    foreach($tables as $i => $table)
    {
      if(starts_with($table, 'list_'))
      {
        unset($tables[$i]);
      }
    }

    return $tables;
  }

  /**
   * Get a list of tables to exclude
   *
   * @return array
   */
  protected function getExcludedTables()
  {
    $excludes = ['migrations'];
    $ignore   = $this->option('ignore');
    if(!empty($ignore))
    {
      return array_merge($excludes, explode(',', $ignore));
    }

    return $excludes;
  }
}
