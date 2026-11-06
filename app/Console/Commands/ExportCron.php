<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Download;
use App\Exports\ProductExportMrp;
use DB;
use Excel;

class ExportCron extends Command
{



    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Export:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ABC';

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
    public function handle()
    {
        $last = Download::orderBy('id','DESC')->first();

          if(isset($last))

          {

               $ids = $last->id + 1;

          }else{

               $ids = 1;

          }

          $download = new Download();

          

          $file_name = $ids."-ProductExportMrp-Export.xlsx";



          $download->file_name = $file_name;

          $download->save();



          Excel::store(new ProductExportMrp(), 'public/exports/'.$file_name);
    }
}
