<?php
/*
*      Copyright (c) 2014 Chi Hoang 
*      All rights reserved
*/
require_once '/usr/share/php5/PEAR/PHPUnit/Autoload.php';
require_once("main.php");

class compressTest extends PHPUnit_Framework_TestCase
{   
  public function testRLE1Compress()
  {
    $rle = new Compress\Rle("aaaabbcc","Encode");
    $rle->start();
    echo $rle;
    $this->expectOutputString('a4b2c2'.chr(0xa)); 
  }
  
  public function testRLE2Compress()
  {
    $rle = new Compress\Rle("aabb","Encode");
    $rle->start();
    echo $rle;
    $this->expectOutputString('aabb'.chr(0xa)); 
  }
  
  public function testRLE3Compress()
  {
    $rle = new Compress\Rle("a4b2c2","Decode");
    $rle->start();
    echo $rle;
    $this->expectOutputString('aaaabbcc'.chr(0xa)); 
  }
  
   public function testRLE4Compress()
  {
    $rle = new Compress\Rle("aabb","Decode");
    $rle->start();
    echo $rle;
    $this->expectOutputString('aabb'.chr(0xa)); 
  }
  
   public function testRLE5Compress()
  {
    $rle = new Compress\Rle("aabbe","Encode");
    $rle->start();
    echo $rle;
    $this->expectOutputString('aabbe'.chr(0xa)); 
  }
  
   public function testRLE6Compress()
  {
    $rle = new Compress\Rle("aabbccdeaaaaaaaaaaaaaaaedafcd","Encode");
    $rle->start();
    echo $rle;
    $this->expectOutputString('a2b2c2dea15edafcd'.chr(0xa)); 
  }
  
   public function testRLE7Compress()
  {
    $rle = new Compress\Rle("aabbccdeaaaaaaaaaaaaaaaedafcd","Encode");
    $rle->start();
    echo $rle;
    $this->expectOutputString('a2b2c2dea15edafcd'.chr(0xa)); 
  }
  


  
  
  
}
?>