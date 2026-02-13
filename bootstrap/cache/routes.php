<?php

return array (
  'GET' => 
  array (
    '/' => 
    array (
      'handler' => 
      array (
        0 => 'App\\Controllers\\HomeController',
        1 => 'index',
      ),
      'middleware' => 
      array (
      ),
    ),
    '/hello' => 
    array (
      'handler' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
    ),
    '/api/ping' => 
    array (
      'handler' => 
      Closure::__set_state(array(
      )),
      'middleware' => 
      array (
      ),
    ),
  ),
);
