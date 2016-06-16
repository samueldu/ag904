<?php
class Log {
	private $handle;
	public function __construct($filename) {

        if($filename)
        {
/*
        if(is_file(DIR_LOGS.$filename))
        {
            chmod (str_replace("\\","/",DIR_LOGS), 0777);
            $path = str_replace("\\","/",DIR_LOGS . $filename);
            $path = DIR_LOGS . $filename;
		    $this->handle = fopen($path, 'a');
        }
*/
        $path = DIR_LOGS . $filename;
        $path = str_replace("/","\\",DIR_LOGS . $filename);
        $path = DIR_LOGS.$filename;
        $this->handle = fopen($path, 'a');
        }
	}

	public function write($message) {
        if($this->handle)
		fwrite($this->handle, date('Y-m-d G:i:s') . ' - ' . print_r($message, true) . "\n");
	}

	public function __destruct() {
        if($this->handle)
		fclose($this->handle);
	}
}