<?php

namespace App\Http\Handlers\DocumentBuilder;

class EssentialOnlyOffice{

    public function saveDocumentCallback($data){
        $path_for_save = $_SERVER['DOCUMENT_ROOT'].'/'.$data['key'].'.docx';
        if ($data["status"] == 2){
            $downloadUri = $data["url"];
//            for changes zip
//            $changesZip = $data["changesurl"];
            if (($new_data = file_get_contents($downloadUri))===FALSE){
                echo "Bad Response";
            } else {
                file_put_contents($path_for_save, $new_data, LOCK_EX);
                $this->saveToBucket($path_for_save);
            }
        }
        return "{\"error\":0}";
    }

    public function saveToBucket($path_for_save){
//      save to s3
//      $upload = ftp_put($conn_id, $destination_file, $path_for_save, FTP_BINARY);
    }

}