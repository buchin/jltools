<?php
use Buchin\Jltools\Jltools;

given('jltools', function(){
	return new Jltools;
});

given('path', function(){
	return 'jl' . DIRECTORY_SEPARATOR . 'sample.jl';
});

given('line', function(){
	return '{"album":["\"Drawing Down The Moon\" (2010)"]}';
});

given('output', function(){
	return 'jl' . DIRECTORY_SEPARATOR . 'new_sample.jl';
});

given('data', function(){
	return json_encode(['a' => 'b']);
});