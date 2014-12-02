<?php
/*
*      Copyright (c) 2014 Chi Hoang 
*      All rights reserved
*/
require_once("main.php");

$obj = new Compress\Rle("aaaabbcc","Encode");
$obj->start();
echo $obj;
$obj->start("aabbcccccccccccde");
echo $obj;

$obj = new Compress\Rle("a4b2c2","Decode");
$obj->start();
echo $obj;

$obj = new Compress\Rle("aabb","Decode");
$obj->start();
echo $obj;

$obj = new Compress\Rle("aabbccde","Encode");
$obj->start();
echo $obj;

$obj = new Compress\Rle("aabbccdeaaaaaaaaaaaaaaae","Encode");
$obj->start();
echo $obj;




?>