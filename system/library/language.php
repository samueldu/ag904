<?php
class Language {
	private $default = 'portuguese-br';
	private $directory;
	private $data = array();

	public function __construct($directory = '') {
		$this->directory = $directory;
	}

	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : $key);
	}

	public function load($filename) {
		$_ = array();

		/* insere linguagem core default */
		$file = DIR_LANGUAGE_CORE . $this->default . '/' . $filename . '.php';

		if (file_exists($file)) {
			require(($file));
		}

        /* insere linguagem.php core default */
		$file = DIR_LANGUAGE_CORE . $this->default . '/' . $this->default . '.php';

		if (file_exists($file)) {
			require(($file));
		}

		/* insere linguagem core default */
		$file = DIR_LANGUAGE . $this->default . '/' . $filename . '.php';

		if (file_exists($file)) {
			require(modification($file));
		}

        /* insere linguagem core default */
        $file = DIR_LANGUAGE . $this->default . '/' . $this->default . '.php';

        if (file_exists($file)) {
            require(modification($file));
        }

		/* insere linguagem cliente linguagem */
		$file = DIR_LANGUAGE . $this->directory . '/' . $filename . '.php';

		if (file_exists($file)) {
			require(modification($file));
		}

		$this->data = array_merge($this->data, $_);

		return $this->data;
	}
}