<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileNotFoundException;

class FileController extends Controller
{

    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    /**
     * upload file with options
     *
     * @param Request $request
     * @return mixed
     */
    public function upload(Request $request){
        $file = $request->file('file');
        $params = $request->all();
        if($file==null){
            return $this->error(Response::HTTP_BAD_REQUEST, 'general.file.no_file', $request->all());
        }

        // specify the upload sub-folder
        $category = strtolower(ensure($params['category'], ''));

        // specify a new file name
        $rename = ensure($params['rename'], '');
        if(!empty($rename)){
            $rename .= '.'. $file->getClientOriginalExtension();
        }else{
            $rename = $file->getClientOriginalName();
        }

        // specify if it's public or not
        $public = array_key_exists('public', $params);

        $category = preg_replace('/\./','/', $category);
        $dir = ($public ? 'public' : 'files' ) . '/' . $category;
        $path = $file->storeAs($dir, $rename, 'local');
        return $this->success('general.file.uploaded', ['path'=>$path]);
    }

    public function download(Request $request) {
        $params = $request->all();
        $path = ensure($params['path'], '');

        if(empty($path||!Storage::exists($path))){
            return $this->error(Response::HTTP_NOT_FOUND, 'general.file.not_found', ['path'=>$path]);
        }

        return Storage::download($path);

    }
}
