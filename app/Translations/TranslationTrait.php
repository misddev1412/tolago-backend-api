<?php
namespace App\Translations;

use DB;

trait TranslationTrait {
    //set translation fields function
    
    public function setTranslation($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $find = DB::table('translation_' . $this->getTable())->where($this->getForeignKey(), $data[$this->getForeignKey()])->where('locale', $data['locale']);
        if ($find->count() > 0) {
            $find->update($data);
        } else {
            DB::table('translation_' . $this->getTable())->insert($data);
        }
    }
}
