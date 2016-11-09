<?php namespace Buchin\Jltools;

/**
* 
*/
class Jltools
{
	private $path;
	private $output;

	public function setPath($path = null)
	{
		if(!is_null($path) && !empty($path))
			$this->path = $path;

		return !empty($this->path);
	}

	function add($item, $input)
	{
		$array = json_decode($input, true);

		foreach ($item as $key => $value) {
			$array[$key] = $value;
		}

		$json = json_encode($array);
		return $json;
	}

	public function addField($name, $value, $output = '')
	{
		if(!file_exists($this->path)) return false;
		
		file_put_contents($output, '');

		$file = fopen($this->path, "r");

		while(($line = fgets($file)) !== false){
			$this->addFieldToJson($name, $value, $output, $line);
		}

		fclose($file);

		return $this;
	}

	public function addFieldToJson($name, $value, $output, $line)
	{
		if(!file_exists($output)){
			file_put_contents($output, '');
		}

		$new_data = $this->add([$name => $value], $line);

		return $this->writeLine($new_data, $output);
	}

	public function writeLine($data, $output)
	{
		if(!file_exists($output)){
			file_put_contents($output, '');
		}

		$file = fopen($output, 'a');
		$data .= "\r\n";
		$result = fwrite($file, $data);
		fclose($file);

		return $result;
	}
}