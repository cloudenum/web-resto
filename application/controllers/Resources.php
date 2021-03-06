<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Resources extends CI_Controller {
	private $_assets_path;
	private $_uploads_path;

	public function __construct() {
		parent::__construct();
		$this->load->library('parser');
		$this->_assets_path = APPPATH . 'assets' . DIRECTORY_SEPARATOR;
		$this->_uploads_path = APPPATH . 'uploads' . DIRECTORY_SEPARATOR;
	}

	public function js($file_name, $parentFolder = '') {
		try {
			if (!$file_name || !strpos($file_name, '.js')) {
				throw new Exception('Invalid filename or not javascript', 1);
			}

			$file_path = $this->_assets_path . DIRECTORY_SEPARATOR . $parentFolder . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $file_name;
			$js_file = file_get_contents($file_path);

			if (!$js_file) throw new Exception('Failed to get file js/' . $file_name, 1);

			$this->output->set_status_header(200);
			$this->output
				->set_content_type('application/javascript')
				->set_output($js_file);
		} catch (\Exception $e) {
			log_message('debug', $e->getMessage());
			show_404();
		}
	}

	public function css($file_name, $parentFolder = '') {
		try {
			if (!$file_name || !strpos($file_name, '.css')) {
				throw new Exception('Invalid filename or not css', 1);
			}

			$file_path = $this->_assets_path . DIRECTORY_SEPARATOR . $parentFolder . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $file_name;
			$css_file = file_get_contents($file_path);

			if (!$css_file) throw new Exception('Failed to get file css/' . $file_name, 1);

			$this->output->set_status_header(200);
			$this->output
				->set_content_type('text/css')
				->set_output($css_file);
		} catch (\Exception $e) {
			log_message('debug', $e->getMessage());
			show_404();
		} catch (\Throwable $th) {
			log_message('error', $th->getMessage());
			show_404();
		}
	}

	public function img($file_name, $parentFolder = '') {
		try {
			if (!$file_name) {
				throw new Exception('Invalid filename', 1);
			}

			if ($parentFolder) {
				$file_path = $this->_assets_path . $parentFolder . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $file_name;
			} else {
				$file_path = $this->_assets_path . 'img' . DIRECTORY_SEPARATOR . $file_name;
			}

			$this->load->library('file_streamer', ['file_path' => $file_path]);
			$this->file_streamer->start();
			// $mime_type = $this->get_mime_type($file_path);
			// $img_file = file_get_contents($file_path);

			// if (!$img_file) throw new Exception('Failed to get file img/' . $file_name, 1);

		} catch (\Exception $e) {
			log_message('debug', $e->getMessage());
			show_404();
		} catch (\Throwable $th) {
			log_message('error', $th->getMessage());
			show_404();
		}
	}

	public function uploaded_file($file_name, $parentFolder = '') {
		try {
			if (!$file_name) {
				throw new Exception('Invalid filename', 1);
			}

			if ($parentFolder) {
				$file_path = $this->_uploads_path . $parentFolder . DIRECTORY_SEPARATOR . $file_name;
			} else {
				$file_path = $this->_uploads_path . $file_name;
			}

			$this->load->library('file_streamer', ['file_path' => $file_path]);
			$mime_type = $this->file_streamer->get_mime_type();
			$file_size = filesize($file_path);

			if (preg_match('/text\/.+/', $mime_type) && $file_size <= 100000) {
				$text_file = file_get_contents($file_path);
				if (!$text_file) throw new Exception("Can't get the file's content");

				$this->output
					->set_status_header(200)
					->set_header('Content-Length: ' . $file_size)
					->set_header('Expires: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT')
					->set_header('Last-Modified: ' . gmdate('D, d M Y H:i:s', @filemtime($file_path)) . ' GMT')
					->set_content_type($mime_type)
					->set_output($text_file);
			} else {
				$this->file_streamer->start();
			}
		} catch (\Exception $e) {
			log_message('debug', $e->getMessage());
			show_404();
		} catch (\Throwable $th) {
			log_message('error', $th->getMessage());
			show_404();
		}
	}

	public function helper_js() {
		try {

			$file_name = 'helper.js';
			$file_path = $this->_assets_path . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $file_name;
			$js_file = file_get_contents($file_path);

			if (!$js_file) throw new Exception('Failed to get file js/' . $file_name, 1);

			$data = [
				'base_url' => base_url(),
			];

			$output = $this->parser->parse_string($js_file, $data, true);

			if (!$output) throw new Exception('Failed to parse js/' . $file_name, 1);

			$this->output->set_status_header(200);
			$this->output
				->set_content_type('application/javascript')
				->set_output($output);
		} catch (\Exception $e) {
			log_message('debug', $e->getMessage());
			show_404();
		}
	}
}
