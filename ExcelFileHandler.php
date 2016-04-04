<?php

namespace resetnn\FileTableParser;

use PHPExcel_IOFactory;
use PHPExcel_Cell;

use resetnn\FileTableParser\ErrorException;

class ExcelFileHandler
{
    private $_file;
    private $_data = [];

    public function __construct($file)
    {
        $this->_file = $file;
    }
        
    public function handler($startLine, $numLoad, $assoc)
    {
        try {
            
            $inputFileType = PHPExcel_IOFactory::identify($this->_file);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $objPHPExcel = $objReader->load($this->_file);
            $objXLS = $objPHPExcel->getSheet(0);
            $array = [];
            $i = 0;
            foreach ($objXLS->getRowIterator($startLine) as $row) {
                if ($numLoad && $i >= $numLoad) {
                    
                    break;
                }                
                $cellIterator = $row->getCellIterator();
                
                $item = [];
                foreach ($cellIterator as $cell) {
                    
                    if (! empty($assoc)) {
                        $key = PHPExcel_Cell::columnIndexFromString($cell->getColumn());
                        
                        if (! empty($assoc[$key])) {
                            $item[$assoc[$key]] = trim($cell->getValue());
                        }
                    } else {
                        $item[] = trim($cell->getValue());
                    }
                }
                
                array_push($array, $item);
                $i++;
            }
            
            $this->setData($array);
            
        } catch(ErrorException $e) {
            
            
        }
    }
        
    public function setData(array $data)
    {
        $this->_data = $data;
    }
    
    public function getData()
    {
        return $this->_data;
    }
}
