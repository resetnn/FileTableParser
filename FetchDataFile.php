<?php

namespace resetnn\FileTableParser;

use PHPExcel_IOFactory;

use resetnn\FileTableParser\ErrorException;

class FetchDataFile
{
    private $_file;
    private $_fileHandler;


    private $_plainTypeFile = [
        'text/plain'
    ];
    
    private $_arcType = [
        'application/x-rar-compressed',
        'application/octet-stream',
        'application/zip',
        'application/octet-stream',
        'application/x-7z-compressed'
    ];
    
    public function __construct($file, $startLine, $numLineLoad = 0, $separator = null, $assoc = [])
    {
        $this->_file = $file;
        
        if (file_exists($this->_file)) {
                        
            if ($this->isArc()) {
                $this->unArc();
            }
            
            if ($this->isExcelFile()) {
                
                $this->_fileHandler = new ExcelFileHandler($this->_file);
                
            } elseif ($this->isPlainFile()) {
                                
                $this->_fileHandler = new PlainFileHandler($this->_file, $separator);
            }
            
            $this->_fileHandler->handler($startLine, $numLineLoad, $assoc);
            
        } else {
            
            return false;
        }
    }
    
    private function isArc()
    {
        return in_array(mime_content_type($this->_file), $this->_arcType);
    }
    
    private function unArc()
    {
        $pathInfo = pathinfo($this->_file);
        $tmpDir = $pathInfo['dirname'] . '/' . time() . '_tmp';
        mkdir($tmpDir, 0777);
        $arcName = $tmpDir . '/arc.tmp';
        rename($this->_file, $arcName);

        system('7za e ' . $arcName . ' -o' . $tmpDir . ' > /dev/null 2>&1 &');
        //system('7za e ' . $arcName . ' -o' . $tmpDir);
        
        $this->createUnArcFile($tmpDir . '/' . $pathInfo['basename'], $tmpDir);
    }
    
    private function createUnArcFile($unArcFile, $tmpDir)
    {
        if (file_exists($unArcFile)) {            
            $file = file_get_contents($unArcFile);
            file_put_contents($this->_file, $file);
            exec('rm -rf ' . $tmpDir . ' > /dev/null 2>&1 &');
        } else {
            
            sleep(2);
            
            $this->createUnArcFile($unArcFile, $tmpDir);
        }
    }

    private function isExcelFile()
    {
        return preg_match('/excel/ui', PHPExcel_IOFactory::identify($this->_file));
    }
    
    private function isPlainFile()
    {
        return in_array(mime_content_type($this->_file), $this->_plainTypeFile);
    }
    
    public function getData()
    {
        return $this->_fileHandler->getData();
    }
}
