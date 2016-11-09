<?php

describe('Acceptance:', function(){
	beforeAll(function(){
		unlink($this->output);
	});

	context('User story', function(){
		
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