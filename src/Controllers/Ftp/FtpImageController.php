<?php

namespace App\Controllers\Ftp;

use App\Helpers\ResponseHelper;

class FtpImageController
{
    private $ftpImageModel;

    public function __construct($ftpImageModel)
    {
        $this->ftpImageModel = $ftpImageModel;
    }

    public function show(string $ImgPath)
    {


        $remoteFile = '/' . trim($ImgPath);

        if(!$this->ftpImageModel){
            return ResponseHelper::errorResponse(400, 'error',);
        }

        try {

            $stream = $this->ftpImageModel->getImageStream($remoteFile);

            if (!$stream) {
                return ResponseHelper::errorResponse(404, 'error', 'Image not found.');
            }

            // อ่านข้อมูลจาก stream มาใช้ตรวจ MIME
            $contents = stream_get_contents($stream, 1024, 0); // อ่านแค่ 1KB ก็พอ
            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $mime     = $finfo->buffer($contents);

            // reset pointer ของ stream กลับไปจุดเริ่มต้น
            rewind($stream);
            header('Content-Type: ' . $mime);
            fpassthru($stream);
            fclose($stream);
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(404, 'error', 'Image not found.');
        }
    }
}
