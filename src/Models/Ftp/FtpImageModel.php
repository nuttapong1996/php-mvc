<?php

namespace App\Models\Ftp;

class FtpImageModel
{
    private $ftpServer;
    private $ftpUser;
    private $ftpPass;
    private $connId;

    public function __construct($ftpServer, $ftpUser, $ftpPass)
    {
        $this->ftpServer = $ftpServer;
        $this->ftpUser   = $ftpUser;
        $this->ftpPass   = $ftpPass;
    }


    public function getImageStream(string $remotePath)
    {

        $tempHandle = fopen('php://temp', 'r+');

        $this->connId = ftp_connect($this->ftpServer);

        if (! $this->connId) {
            throw new \Exception("ไม่สามารถเชื่อมต่อ FTPS ได้");
            return  false;
        }

        if (! ftp_login($this->connId, $this->ftpUser, $this->ftpPass)) {
            throw new \Exception("FTP login ล้มเหลว");
            return false;
        }

        ftp_pasv($this->connId, true);

        if (! ftp_fget($this->connId, $tempHandle, $remotePath, FTP_BINARY, 0)) {
            ftp_close($this->connId);
            return  false;
        }

        rewind($tempHandle);
        return $tempHandle; 
    }
}
