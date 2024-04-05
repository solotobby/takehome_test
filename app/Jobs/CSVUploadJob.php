<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CSVUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     public $fileContents;

    public function __construct($fileContents)
    {
        $this->fileContents = $fileContents;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach($this->fileContents as $file){
            //check if the record exist
            
            $checks = Product::where('sku', $file[0])->first();

            if($checks){
                //update the record
                $checks->update([
                    'name' => $file[1],
                    'description' => $file[2],
                    'brand' => $file[3]
                ]);

            }else{
                //create records
                Product::create([
                    'sku' => $file[0],
                    'name' => $file[1],
                    'description' => $file[2],
                    'brand' => $file[3]
                ]);
            }

        }
    }
}
