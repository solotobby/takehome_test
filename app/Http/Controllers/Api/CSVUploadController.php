<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\CSVUploadJob;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class CSVUploadController extends Controller
{
    public function index(Request $request): string {

        $request->validate([
            'csv_file' => 'required|file|mimes:csv',
        ]);

        try{
                $file = $request->file('csv_file');
                // Read the CSV file  
                $fileContents = array_map('str_getcsv', file($file->getPathname()));

                //remove the header
                unset($fileContents[0]);

                 ///queue - note i used database for the queue
                CSVUploadJob::dispatch($fileContents);

                return response()->json(['message' => 'Upload Successful'], 201);

        }catch(Exception $exception){
            //handles error exceptions
            return response()->json(['status' => false,  'error'=>$exception->getMessage(), 'message' => 'Error processing request'], 500);
       }

    }

    public function fetch($sku){

        try{
            //search for the data
            $search = Product::where('sku', $sku)->first();
            
            if(!$search){
                return response()->json([
                    'status' => 'error',
                    'message' => 'No product found',
                ], 401);
            }

            return response()->json(['status'=>'success', 'message' => 'Product details', 'data' => $search], 200);

        }catch(Exception $exception){
            //handles error exceptions
            return response()->json(['status' => false,  'error'=>$exception->getMessage(), 'message' => 'Error processing request'], 500);
       }
    }
}
