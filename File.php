<?php

/**
 * File
 *
 * @author Paul Henry <paulhenry@mphwebsystems.com>
 */
class File {
    protected $_fp;
    private $_contents;

    public function __construct($filename, $mode = "r", $options = array()) {
        $options = array('filename' => $filename, 'mode' => $mode) + $options;

        $this->_config = $options;

        $this->_init();
    }

    public function _init() {
        $this->_fp = fopen($this->_config['filename'], $this->_config['mode']);

        if(!$this->_fp) {
            throw new Exception("Could not open file: " . $this->_config['filename']);
        }
    }

    public function contents() {
        $this->_contents = "";
        
        while($line = fgets($this->_fp)) {
            $this->_contents .= $line;
        }

        return $this->_contents;
    }

    public function readLine($length = 4096) {
        return fgets($this->_fp, $length);
    }

    public function readLineAsCsv($length = 4096, $delimiter = ',') {
        return fgetcsv($this->_fp, $length, $delimiter);
    }

    public function delete() {
        fclose($this->_fp);
        unlink($this->_config['filename']);
    }
}
