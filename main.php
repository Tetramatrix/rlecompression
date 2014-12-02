<?php
/*
*      Copyright (c) 2014 Chi Hoang 
*      All rights reserved
*/
namespace Compress;

interface Runlength
{
    public function diff($pos);
    public function encode(&$node,$char,$pos,$delta);
    public function decode(&$node,$char,$pos,$delta);
}

interface AddEncode
{
    public function start($payload);
}

interface AddDecode
{
    public function start($payload);
}

class Compress implements Runlength
{
    private $is_leaf= false;
    private $left, $right = null;
    private $char = "";
    private $pos, $delta = 0;
    public $result;    

        //if payload is given, then create a leaf
    public function __construct ($char=null, $pos=0, $delta=0)
    {    
        if ($char !== null)
        {
            $this->pos = $pos;
            $this->delta = $delta;
            if (ord($char) >= ord("0") && ord($char)<= ord("9"))
            {
                $this->char = "$".$char;
            } else
            {
                $this->char = $char;
            }
            $this->is_leaf = true; 
        }
    }
    
    public function diff($pos)
    {
       if ($diff=$pos-$this->delta<2)
       {
            return $this->char;
       } else
       {
            return $this->char.($pos-$this->delta); 
       }       
    }
    
    function decode(&$node, $char, $pos=0, $delta=0)
    {
        if (!is_object($node))
        {
            if (empty($this->result))
            {
                if (ord($char[$pos]) == ord("$"))
                {
                    $this->result=$char[$pos+1];
                    $node = new Compress($char[$pos+1], $pos+1, $delta); 
                } else
                {
                    $this->result=$char[$pos];
                }
            } else
            {
                $node = new Compress($char[$pos], $pos, $delta);    
            }
            $step=$base=0;
            for ($start=$pos+1,$i=$start;$end=strlen($char),$i<=$end;$i++)
            {
                if (ord($char[$i-1]) == ord("$") && $base==0)
                {
                    ++$i;       
                    $step.=$char[$i];
                    $base++;
                    ++$pos;
                    $node->char=$char[$pos];
                    
                } else if (ord($char[$i]) >= ord("0") && ord($char[$i])<= ord("9"))
                {
                    $step.=$char[$i];
                    $base++;
                } else
                {
                    break;
                }
            }
            $step--;
            if (!empty($char[$pos+1]) && $step<0)
            {
                ++$pos;
                
            } else if ($step>0)
            {
                $char=substr_replace($char,$step,$pos+1,$base);    
            } else if ($step==0)
            { 
                $pos+=2;   
            } else if ($step<0)
            {
                return $this->result;   
            }
        }                                                                                        
         
        if (ord($char[$pos]) != ord($node->char))
        {
            if (ord($char[$pos]) == ord("$"))
            {
                $this->result.=$char[$pos+1];
            } else
            {
                $this->result.=$char[$pos];    
            } 
            unset($this->head->right);
            $this->decode($node->left, $char,$pos, $pos);
            
        } else if ($char[$pos]===null)
        {
            return $this->result;
        } else
        {
            if (ord($char[$pos]) == ord("$"))
            {
                $this->result.=$char[$pos+1];
            } else
            {
                $this->result.=$char[$pos];    
            }   
            $this->decode($node->left, $char, $pos, $delta);        
        }
        return $this->result;
    }
    
    function encode (&$node, $char, $pos=0, $delta=0)
    {
        if (!is_object($node) && strlen($this->payload)>$pos)
        {
            //auto leaf
            $node = new Compress($char[$pos], $pos, $delta);
            if (ord($char[$pos]) >= ord("0") && ord($char[$pos])<= ord("9"))
            {
                $num=1;
            }
            $pos++;
        } else
        {
            return $this->result;    
        }
         
        if (ord($char[$pos]) != ord($node->char[$num]))
        {
            $this->result.=$node->diff($pos);
            unset($this->head->right);
            $this->encode($node->left, $char,$pos,$pos);
            
        }  else if ($pos+1 == strlen($char))
        {
            $node->char = $char[$pos];
            $this->is_leaf = false;
            $this->result.=$node->diff($pos+1);
            
        } else {

            $this->encode($node->left, $char, $pos, $delta);        
        }
        return $this->result;
    }
};


abstract class AbstractCompress extends Compress 
{
    private $component;
    public $head;
    public $payload;
    public $result;
    public $type;
    
    public function __construct($payload=null,$type=null)
    {
        try {
            $this->type=strtolower(constant(get_class($this) . '::COMPONENT_CLASS'));
    
            if ($this->type==null)
            {
                throw new Exception('Fatal error.');
            } else if ($this->type=="rle" && $type!=null)
            {
                $this->type=strtolower($type);
                $this->payload = $payload;            
                $this->result=$this->head=null;
            } else 
            {
                $this->payload = $payload;            
                $this->result=$this->head=null;
            }
        } catch (Exception $e)
        {
            echo 'Exception: ',  $e->getMessage(), "\n";
        }    
    }
    
    public function start($payload=null)
    {
        if (method_exists($this,$this->type))
        {
            if (!empty($this->payload) && empty($payload))
            {
                $this->result=$this->{$this->type}($this->head, $this->payload);
            } else
            {
                $this->payload=$payload;
                $this->result=$this->head=null;
                $this->result=$this->{$this->type}($this->head, $payload);
            }
        }
    }
    
    public function __toString()
    {
        if ($this->type=="encode" && strlen($this->result)>=strlen($this->payload))
        {
            $this->result = $this->payload;
            
        } else if ($this->type=="decode" && strlen($this->result)<=strlen($this->payload))
        {
            $this->result = $this->payload;
        }
        return $this->result.chr(0x0a);
    }
}

class Encode extends AbstractCompress implements AddEncode {
    
    const COMPONENT_CLASS = 'Encode';
    
    public function start($payload=null)
    {
        parent::start($payload);
    }
}

class Decode extends AbstractCompress implements AddDecode {

    const COMPONENT_CLASS = 'Decode';
    
    public function start($payload=null)
    {
        parent::start($payload);
    }
}

class Rle extends AbstractCompress implements AddEncode, AddDecode {
    
    const COMPONENT_CLASS = 'Rle';
     
    public function start($payload=null,$type=null)
    {
        parent::start($payload);
    }
}

?>