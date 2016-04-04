<?php

namespace Sklad\components\loadPrices;

interface FileHandlerInterface
{
    public function __construct($file);
    public function handler($numLoad);
    public function setData(array $data);
    public function getData();
}
