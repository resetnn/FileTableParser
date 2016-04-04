<?php

namespace aciden\receivData;

use aciden\receivData\FtpUpload;
use aciden\receivData\FetchDataFile;

/**
 * 
 */
class ReceivData extends \yii\base\Component
{
    /**
     *
     * @var type 
     */
    public $ftpPasv = true;
    public $clear = true;

    /**
     *
     * @var type 
     */
    private $_uploadFilePath = '';
    private $_loadFile;
    private $_fetchData;

    /**
     * 
     * @param type $fileName
     * @param type $uploadFilePath
     */
    public function initialise($fileName, $uploadFilePath = null)
    {        
        $this->_loadFile = $fileName;
        $this->_uploadFilePath = $uploadFilePath ? $uploadFilePath : $this->_uploadFilePath;
    }

    /**
     * 
     * @param type $host
     * @param type $login
     * @param type $pas
     * @param type $ftpPath
     */
    public function uploadFtp($host, $login, $pas, $ftpPath)
    {
        $fileUpload = new FtpUpload($host, $login, $pas, $this->ftpPasv);
        $fileUpload->upload($this->_uploadFilePath, $ftpPath, $this->_loadFile);
        
    }
    
    /**
     * 
     * @param type $startLine
     * @param type $numLoadLine
     * @param type $separator
     * @return type
     */
    public function fetchPreload($startLine, $numLoadLine, $separator = null)
    {
        $this->_fetchData = new FetchDataFile($this->_uploadFilePath . '/' . $this->_loadFile, $startLine, $numLoadLine, $separator);
        
        return $this->_fetchData->getData();
    }

    /**
     * 
     * @param type $startLine
     * @param type $separator
     * @return type
     */
    public function fetchRow($startLine = 1, $separator = null)
    {
        $this->_fetchData = new FetchDataFile($this->_uploadFilePath . '/' . $this->_loadFile, $startLine, 20, $separator);
        
        return $this->_fetchData->getData();
    }
    
    /**
     * 
     * @param array $assoc
     * @param type $startLine
     * @param type $separator
     * @return type
     */
    public function fetchAssoc(array $assoc, $startLine = 1, $separator = null)
    {
        $this->_fetchData = new FetchDataFile($this->_uploadFilePath . '/' . $this->_loadFile, $startLine, 0, $separator, $assoc);
        
        return $this->_fetchData->getData();
    }
    
    /**
     * 
     */
    public function __destruct()
    {        
        if ($this->clear && file_exists(__DIR__ . '/' . $this->_uploadFilePath . '/' . $this->_loadFile)) {
            
            unlink(__DIR__ . '/' .$this->_uploadFilePath . '/' . $this->_loadFile);   
        }
    }
}
