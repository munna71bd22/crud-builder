<?php

namespace Smartapp\Cb\Commands;

use Illuminate\Console\Command;
use Artisan;

class GenerateCrud extends Command
{
    protected $signature = 'crud:generate {model}';
    protected $description = 'Generate a complete CRUD operation';

    public function handle($model)
    {
        if(empty($model) ) {
            $model = $this->argument('model');
        }

        // Generate Controller
        $controllerContents = str_replace(
            ['{{modelName}}', '{{modelVariable}}','{{modelPlural}}','{{modelVariablePlural}}'],
            [$model, lcfirst($model),lcfirst($model).'s',lcfirst($model).'s'],
            file_get_contents(__DIR__ . '/Stubs/controller.stub')
        );

        file_put_contents(app_path("Http/Controllers/{$model}Controller.php"), $controllerContents);

        // Generate Model
        $modelContents = str_replace(
            ['{{modelName}}'],
            [$model],
            file_get_contents(__DIR__ . '/Stubs/model.stub')
        );

        file_put_contents(app_path("Models/{$model}.php"), $modelContents);

        // Generate Migration
        $migrationContents = str_replace(
            ['{{modelName}}','{{tableName}}'],
            [$model,lcfirst($model).'s'],
            file_get_contents(__DIR__ . '/Stubs/migration.stub')
        );

        $fileName = date('Y_m_d_').'100001_create_'.lcfirst($model).'s_table.php';
        file_put_contents(base_path("/database/migrations/{$fileName}"), $migrationContents);

         // Generate Validation
         $validationContents = str_replace(
            ['{{modelName}}'],
            [$model],
            file_get_contents(__DIR__ . '/Stubs/validation.stub')
        );

        if (!is_dir(app_path("Http/Requests"))) {
            mkdir(app_path("Http/Requests"), 0777, true);
        }
        file_put_contents(app_path("Http/Requests/{$model}Request.php"), $validationContents);


         // Generate Create View File

         $layoutsFolderName = base_path("/resources/views/layouts");
        if (!is_dir($layoutsFolderName)) {
            mkdir($layoutsFolderName, 0777, true);

            file_put_contents($layoutsFolderName."/app.blade.php",  file_get_contents(__DIR__ . '/Stubs/app.stub'));
            
        }


         $craeteViewContents = str_replace(
            ['{{modelName}}','{{modelPlural}}'],
            [$model,lcfirst($model).'s'],
            file_get_contents(__DIR__ . '/Stubs/view_create.stub')
        );

        $folderName = base_path("/resources/views/".lcfirst($model)."s");
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        
        file_put_contents($folderName."/create.blade.php", $craeteViewContents);


         // Generate Edit View File
         $editViewContents = str_replace(
            ['{{modelName}}','{{modelPlural}}','{{modelVariable}}'],
            [$model,lcfirst($model).'s',lcfirst($model)],
            file_get_contents(__DIR__ . '/Stubs/view_edit.stub')
        );

        $folderName = base_path("/resources/views/".lcfirst($model)."s");
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        file_put_contents($folderName."/edit.blade.php", $editViewContents);

        
         // Generate Index View File
         $indexViewContents = str_replace(
            ['{{modelName}}','{{modelPlural}}','{{modelVariable}}','{{modelVariablePlural}}'],
            [$model,lcfirst($model).'s',lcfirst($model),lcfirst($model).'s'],
            file_get_contents(__DIR__ . '/Stubs/view_index.stub')
        );

        $folderName = base_path("/resources/views/".lcfirst($model)."s");
        if (!is_dir($folderName)) {
            mkdir($folderName, 0777, true);
        }
        file_put_contents($folderName."/index.blade.php", $indexViewContents);


         // Generate Route
         $routeContents = str_replace(
            ['{{modelName}}','{{modelPlural}}','{{modelVariable}}','{{modelVariablePlural}}','{{nameSpace}}'],
            [$model,lcfirst($model).'s',lcfirst($model),lcfirst($model).'s',app_path("\Http\Controllers")],
            file_get_contents(__DIR__ . '/Stubs/routes.stub')
        );

        $folderName = base_path("/routes");
        file_put_contents($folderName."/web.php", $routeContents,FILE_APPEND);

        // Migrate new table
        Artisan::call('migrate');

        return "CRUD for {$model} generated successfully.";
        //$this->info("CRUD for $model generated successfully.");

    }
}
