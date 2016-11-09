<?php

describe("Unit", function(){
	
	describe('->add($item, $input)', function(){
		it('add $item into $input then returns it', function(){
			$input = $this->line;
			$item = ['category' => ['A']];

			$expected = '{"album":["\"Drawing Down The Moon\" (2010)"],"category":["A"]}';

			expect($this->jltools->add($item, $input))->toBe($expected);
		});
	});

	describe('->setPath()', function(){

		context('when path is empty or not set', function(){
			it('failed to save path', function(){
				$output = $this->jltools->setPath();
				
				expect($output)->toBe(false);
			});
		});

		context('when path is defined', function(){
			it('save path to variable', function(){

				$output = $this->jltools->setPath($this->path);
				expect($output)->toBe(true);
			});
		});
	});

	describe('->addField()', function(){
		context('when file not exists', function(){
			it('return false', function(){
				allow('file_exists')->toBeCalled()->andReturn(false);
				$this->jltools->setPath($this->path);
				$output = $this->jltools->addField('name', 'value', $this->output);
				expect($output)->toBe(false);
			});
		});

		context('when file exists', function(){
			it('cleans up the $output file', function(){
				allow('file_exists')->toBeCalled()->andReturn(true);
				allow('fopen')->toBeCalled();
				allow('fgets')->toBeCalled()->andReturn($this->line, false);
				allow('fclose')->toBeCalled();
				allow('fwrite')->toBeCalled();

				expect('file_put_contents')->toBeCalled()->with($this->output, '');
				$this->jltools->addField('name', 'value', $this->output);
			});

			it('open the file and add field to json', function(){
				allow('file_exists')->toBeCalled()->andReturn(true);
				allow('fopen')->toBeCalled();
				allow('fgets')->toBeCalled()->andReturn($this->line, false);
				allow('fclose')->toBeCalled();
				allow('fwrite')->toBeCalled();

				expect($this->jltools)->toReceive('addFieldToJson');
				$this->jltools->addField('name', 'value', $this->output);
			});

			it('is chainable', function(){
				allow('file_exists')->toBeCalled()->andReturn(true);
				allow('fopen')->toBeCalled();
				allow('fgets')->toBeCalled()->andReturn($this->line, false);
				allow('fclose')->toBeCalled();
				allow('fwrite')->toBeCalled();

				expect($this->jltools->addField('name', 'value', $this->output))->toBeAnInstanceOf('Buchin\Jltools\Jltools');
			});

		});
	});

	describe('->addFieldToJson()', function(){
		context('when $output file is not exists', function(){
			it('create the file for you', function(){
				allow('file_exists')->toBeCalled()->andReturn(false);
				allow($this->jltools)->toReceive('add');
				expect('file_put_contents')->toBeCalled()->with($this->output);

				$this->jltools->addFieldToJson('', '', $this->output, '');
			});
		});

		context('when $output file is ready to be written', function(){
			it('add field to json', function(){
				expect($this->jltools)->toReceive('add');
				$this->jltools->addFieldToJson('category', ['A'], $this->output, $this->line);
			});

			it('write json into $output file', function(){
				$new = '{}';

				allow('json_encode')->toBeCalled()->andReturn($new);

				expect($this->jltools)->toReceive('writeLine')->with($new, $this->output);

				$this->jltools->addFieldToJson('category', ['A'], $this->output, $this->line);
			});
		});

		afterAll(function(){
			unlink($this->output);
		});
	});

	describe('->writeLine($data, $output)', function(){
		
		context('when $output file is not exists', function(){
			it('creates the file', function(){
				allow('file_exists')->toBeCalled()->andReturn(false);

				expect('file_put_contents')->toBeCalled()->with($this->output, '');

				$this->jltools->writeLine($this->data, $this->output);
			});
		});

		context('when $output file is exist and writable', function(){
			it('appends $data with newline into $output', function(){
				allow('file_exists')->toBeCalled()->andReturn(true);
				allow('fopen')->toBeCalled()->andReturn(true);
				allow('fwrite')->toBeCalled()->with(true, $this->data . "\r\n")->andReturn(8);
				allow('fclose')->toBeCalled()->andReturn(true);

				expect($this->jltools->writeLine($this->data, $this->output))->toBe(8);
			});
		});
	});
});