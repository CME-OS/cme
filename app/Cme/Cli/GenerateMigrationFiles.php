<?php
namespace Cme\Cli;

use Illuminate\Support\Facades\DB;

class GenerateMigrationFiles extends CmeCommand
{

  /**
   * The console command name.
   *
   * @var string
   */
  protected $name = 'cme:scan-db';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Scan db for changes we can use to generate migration files';

  private $typeMap = [
    'int'     => 'integer',
    'char'    => 'string',
    'varchar' => 'string',
    'date'    => 'date',
    'enum'    => 'enum',
    'double'  => 'double'
  ];

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function fire()
  {
    //load snapshot
    $snapshotFile = implode(
      DIRECTORY_SEPARATOR,
      [base_path(), 'snapshot.json']
    );

    $snapshot = json_decode(file_get_contents($snapshotFile));

    //go over every table in cme
    $result = DB::select("SHOW TABLES FROM cme");
    $json   = [];
    foreach($result as $obj)
    {
      $table = $obj->Tables_in_cme;
      if(!starts_with($table, 'list_'))
      {
        if(!isset($snapshot->$table))
        {
          echo "New table $table detected!" . PHP_EOL;
          echo "Generating migration file" . PHP_EOL;

          $this->call(
            'migrate:generate',
            ['tables' => $table]
          );
        }
        //get create code
        $columns = DB::select("SHOW COLUMNS FROM $table");
        //var_dump($columns); die;
        $columnsHash = [];
        foreach($columns as $columnObj)
        {

          $columnName = $columnObj->Field;
          if(isset($snapshot->$table->$columnName))
          {
            //if(!isset($columnObj->$columnName)

            if($snapshot->$table->$columnName != md5(json_encode($columnObj)))
            {
              echo "$columnName has changed. Generate Migration file" . PHP_EOL;

              $migrationName = sprintf(
                "add_%s_to_%s_table",
                $columnName,
                $table
              );

              $fieldProps   = [];
              $fieldProps[] = $columnObj->Field;

              $typeParts  = explode('(', $columnObj->Type);
              $otherParts = explode(' ', $typeParts[1]);

              $length = substr($otherParts[0], 0, -1);

              $type = $this->typeMap[$typeParts[0]];
              if($type == 'integer' && $columnObj->Extra == "auto_increment")
              {
                $type .= '(true)';
              }
              elseif($columnObj->Key == 'PRI')
              {
                $fieldProps[] = "primary('" . $columnObj->Field . "')";
              }

              if($type == 'string')
              {
                $type .= '(' . $length . ')';
              }
              if($type == 'enum')
              {
                $type .= '([' . $length . '])';
              }

              $fieldProps[] = $type;
              if($columnObj->Null == 'YES')
              {
                $fieldProps[] = 'nullable';
              }
              if($columnObj->Default !== null)
              {
                $fieldProps = "default('" . $columnObj->Default . "')";
              }
              elseif($columnObj->Key == 'MUL')
              {
                $fieldProps[] = "index('" . $columnObj->Field . "')";
              }

              $fields = implode(':', $fieldProps);

              $this->call(
                'generate:migration',
                ['migrationName' => $migrationName, '--fields' => $fields]
              );
            }
            $columnsHash[$columnName] = md5(json_encode($columnObj));
          }
          else
          {
            echo "New column $columnName detected!" . PHP_EOL;
            echo "Generating migration file" . PHP_EOL;
          }
        }

        $json[$table] = $columnsHash;
      }
    }

    //update snapshot
    //echo "Updating snapshot file..." . PHP_EOL;
    //file_put_contents($snapshotFile, json_encode($json));
  }
}
