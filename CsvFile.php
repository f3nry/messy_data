<?php

require_once "File.php";

/**
 * CsvFile
 *
 * @author Paul Henry <paulhenry@mphwebsystems.com>
 */
class CsvFile extends File implements Iterator {

	protected $_header;
	protected $_file;
	protected $_currentRecord;
        protected $_count = null;
	
	protected $callbacks = array();

	public function __construct($filename) {
		parent::__construct($filename);

		$this->_init();
	}

	public function _init() {
		parent::_init();

		$header_row = $this->readLineAsCsv();

		$i = 0;

		foreach ($header_row as $header_column) {
			$column_name = trim(
				strtoupper(
					preg_replace('/[^A-Za-z0-9]/', '', $header_column)
				)
			);

			$this->_header[$column_name] = $i++; 
		}
	}

	public function __call($name, $params) {
		if (preg_match("/[sg]et(.*)/", $name, $found)) {
			$columnName = strtoupper($found[1]);
			
			$index = false;
			
			if(isset($this->_header[$columnName])) {
				$index = $this->_header[$columnName];
			} else if(isset($this->callbacks[$columnName])) {
				$index = $this->callbacks[$columnName];
			}
			
			if ($index !== false) {
				if ($name[0] == 'g') {
					if(is_integer($index) && isset($this->_currentRecord[$index])) {
						return $this->_currentRecord[$index];
					} else {
						if($index instanceof Closure) {
							$function = $index;
							
							return $function();
						}
					}
				} else {
					$this->_currentRecord[$index] = $params[0];
					return true;
				}
			}
		}

		return false;
	}
	
	public function registerDynamicField($field, $callback) {
		$field = trim(
				strtoupper(
					preg_replace('/[^A-Za-z0-9]/', '', $field)
				)
			);
		
		$this->callbacks[$field] = $callback;
	}

	public function getNext($mysqli = false) {
		$record = $this->readLineAsCsv();

		if ($record === false) {
			return false;
		}

		for ($i = 0; $i < count($record); $i++) {
			$record[$i] = trim($record[$i]);

			if ($mysqli) {
				$record[$i] = mysqli_real_escape_string($mysqli, $record[$i]);
			} else {
				$record[$i] = $record[$i];
			}
		}

		if (!empty($record)) {
			$this->_currentRecord = $record;
			return $this;
		} else {
			$this->_currentRecord = false;
		}
	}

	public function rewind() {
		rewind($this->_fp);
                $this->getNext();
	}

	public function current() {
		return $this->_currentRecord;
	}

	public function key() {
		return null;
	}

	public function next() {
		return $this->getNext()->current();
	}

	public function valid() {
		return $this->_currentRecord != false;
	}

        public function count() {
          if($this->_count === null) {
            $count = 0;

            while($record = $this->readLineAsCsv()) {
              $count += 1;
            }

            $this->rewind();

            $this->_count = $count;
          }

          return $this->_count;
        }

}
