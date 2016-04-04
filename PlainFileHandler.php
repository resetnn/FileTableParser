<?php

namespace resetnn\FileTableParser;

use resetnn\FileTableParser\ErrorException;

class PlainFileHandler
{
    private $_file;
    private $_data = [];
    private $_separator = '\t'; // default tab

    public function __construct($file, $separator)
    {
        $this->_file = $file;
        
        if ($separator) {
            $this->_separator = $separator;
        }
    }
        
    public function handler($startLine, $numLoad, $assoc)
    {
        
        try {
            $file = file($this->_file);
            $file = array_splice($file, $startLine - 1);
                        
            $array = [];
            $i = 0;
            foreach ($file as $row) {
                
                if ($numLoad && $i >= $numLoad) {
                    
                    break;
                }
                
                $row = trim(iconv("windows-1251", "utf-8//IGNORE", $row));
                $row2 = preg_replace('/' . $this->_separator . '/u', '###', $row);
                $cellIterator = explode('###', $row2);
                
                $item = [];
                foreach ($cellIterator as $key => $cell) {
                    
                    if (! empty($assoc)) {
                        

                        if (! empty($assoc[$key + 1])) {
                            $item[$assoc[$key + 1]] = trim($cell);
                        }
                        
                    } else {
                        $item[] = trim($cell);
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
