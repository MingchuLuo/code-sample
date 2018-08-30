<?php

namespace App\Http\Controllers;

use App\Models\Common\Dictionary;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class DictionaryController extends Controller
{
    //
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function groups(Request $request) {
        $groups = $request->get('groups', []);
        if(!is_array($groups)){
            $groups = [$groups];
        }
        return collect(Dictionary::byGroups($groups)->get())->groupBy('group');
    }

    public function enums(Request $request) {
        $fileInfo =  File::allFiles(app_path('Enums'));
        $enumFiles = array_filter($fileInfo, function ($item) {
            return $item->getFilename() != 'Enum.php';
        });
        $enums = [];
        foreach($enumFiles as $enumFile) {
            $enumClassName = substr($enumFile->getFilename(), 0, strrpos($enumFile->getFilename(), '.'));
            $enums[$enumClassName] = call_user_func("App\\Enums\\".$enumClassName. "::items");
        }
        return $enums;
    }
}
