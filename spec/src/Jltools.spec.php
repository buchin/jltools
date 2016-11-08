<?php
use Buchin\Jltools\Jltools;

describe('Jltools', function(){
	given('jltools', function(){
		return new Jltools;
	});

	given('path', function(){
		return __DIR__ . DIRECTORY_SEPARATOR . 'sample.jl';
	});

	given('line', function(){
		return '{"album":["\"Drawing Down The Moon\" (2010)"]}';
	});

	given('output', function(){
		return __DIR__ . DIRECTORY_SEPARATOR . 'new_sample.jl';
	});

	describe("Unit Test", function(){
		
		
		describe('__construct()', function(){
			it('instantiable', function(){
				expect($this->jltools)->toBeAnInstanceOf('Buchin\Jltools\Jltools');
			});
		});

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
			given('data', function(){
				return json_encode(['a' => 'b']);
			});

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

	describe('Feature: Add Field', function(){
		beforeAll(function(){
			unlink($this->output);
		});
		context('User story:', function(){
			describe('* As a programmer', function(){});
			describe('* I want to add field to my existing jsonline file', function(){});
			describe('* So I don\'t need to rescrape the data', function(){});
		});

		context('Scenario:', function(){
			
			describe('* Programmer create new Jltools object', function(){
				it('can be initialized', function(){
					expect($this->jltools)->toBeAnInstanceOf('Buchin\Jltools\Jltools');
				});
			});

			describe('* Programmer set json line file path', function(){
				it('can be set', function(){
					expect($this->jltools->setPath($this->path))->toBe(true);
				});
			});

			describe('* Programmer add new field to existing json line', function(){
				it('include new field into output', function(){
					$this->jltools->setPath($this->path);

					expect($this->jltools->addField('size', 'L', $this->output))
					->toBeAnInstanceOf('Buchin\Jltools\Jltools');
				});

				it('save output to new file', function(){
					expect(file_exists($this->output) && filesize($this->output))->not->toBeFalsy();
				});
			});
		});
	});
});