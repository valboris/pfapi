<?php

class ApiList implements Iterator, ArrayAccess, Countable
{
	private $_data = array();
	private $_keys = array();
	private $_current = false;
	private $_index = 0;

	function __get($name)
	{
		if( $name == "Data" )
			return $this->_data;
	}

	public function __construct(){}

	public static function FromArray($array)
	{
		$res = new ApiList();
		$res->_data = $array;
		$res->_keys = array_keys($array);
		$res->_index = 0;
		if( count($res->_keys) > 0 )
			$res->_current = $res->_keys[0];
		return $res;
	}

	public function GetArray()
	{
		return $this->_data;
	}

	/* Iterator (implements Traversable) */
	public function current(){ return $this->_data[$this->_current]; }
	public function key(){ return $this->_current; }
	public function next(){ $this->_index++; if( $this->valid() ) $this->_current = $this->_keys[$this->_index]; }
	public function rewind(){ $this->_index = 0; if(isset($this->_keys[$this->_index])) $this->_current = $this->_keys[$this->_index]; }
	public function valid(){ return isset($this->_keys[$this->_index]); }

	/* ArrayAccess */
	public function offsetExists($index){ return $index>0 && $index<count($this->_keys); }
	public function offsetGet($index){ return $this->_data[$this->_keys[$index]]; }
	public function offsetSet($index,$newval){ $this->_data[$this->_keys[$index]] = $newval; }
	public function offsetUnset($index){ unset($this->_data[$this->_keys[$index]]); }
	
	/* Countable */
	public function count(){ return count($this->_data); }


	public function ValCount($field)
	{
		$res = 0;
		foreach( $this->_data as $item )
			if( is_object($item) && isset($item->$field) )
				$res++;
		return $res;
	}

	public function ValSum($field)
	{
		$res = 0;
		foreach( $this->_data as $item )
			if( is_object($item) && isset($item->$field) && $item->$field )
			{
				$res += floatval($item->$field);
			}
		return $res;
	}

	public function ValMin($field)
	{
		$min = false;
		foreach( $this->_data as $item )
			if( is_object($item) && isset($item->$field) && $item->$field != "" )
				if( $min === false || $min > $item->$field )
					$min = $item->$field;
		return $min;
	}

	public function ValFirst($field)
	{
		foreach( $this->_data as $item )
			if( is_object($item) && isset($item->$field) && $item->$field != "" )
				return $item->$field;
		return false;
	}
}

