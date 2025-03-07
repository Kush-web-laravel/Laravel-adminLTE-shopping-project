<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class CrudHelper

{
    public static function createRecord($modelName, Request $request, $data)
    {
        $request->validate($rules);

        $data = $request->except('productImage');

        if($request->hasField('productImage')){
            $file = $request->file('productImage');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/profile_images', $fileName, 'public');
            $data['productImage'] = $filePath;
        }

        $model = 'App\\Models\\' . $modelName;

        return $model::create(array_merge($data, ['user_id' => auth()->id()]));

    }
}