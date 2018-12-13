<?php
	Class XmlControl
	{
		public $xml;
		public function __construct($xmlobj=null)
		{
			$this->xml = $xmlobj;
		}

		public function GetXmlElements()
		{
			$args = func_get_args();
			if(func_num_args() == 1)
			{
				return $this->GetXmlElements1($args[0]);
			}
			else if(func_num_args()==2)
			{
				return $this->GetXmlElements2($args[0],$args[1]);
			}
		}

		private function GetXmlElements1($elementLabel)
		{
			if($this->xml!=null)
			{
				$elements = $this->xml->getElementsByTagname($elementLabel);
				return $elements;
			}
			else return null;
		}

		private function GetXmlElements2($xmlPath,$elementLabel)
		{
			$xmlDOC = new DOMDocument();
			$xmlDOC->load($xmlPath);
			if($xmlDOC!=null){
				$elements = $xmlDOC->getElementsByTagname($elementLabel);
				return $elements;
			}
			else
			{
				return null;
			}
		}

		public function GetXmlElementByIndex()
		{
			$args = func_get_args();
			if(func_num_args() == 2)
			{
				return $this->GetXmlElementByIndex2($args[0],$args[1]);
			}
			else if(func_num_args()==3)
			{
				return $this->GetXmlElementByIndex3($args[0],$args[1],$args[2]);
			}
		}

		private function GetXmlElementByIndex2($elementLabel,$index)
		{
			if($xml!=null){
				$element = $xml->getElementsByTagname($elementLabel)->item($index);
				return $element;
			}	
			else
			{
				return null;
			}
		}

		private function GetXmlElementByIndex3($xmlPath,$elementLabel,$index)
		{
			$xmlDOC = new DOMDocument();
			$xmlDOC->load($xmlPath);
			if($xmlDOC!=null){
				$element = $xmlDOC->getElementsByTagname($elementLabel)->item($index);
				return $element;
			}	
			else
			{
				return null;
			}
		}

		public function GetXmlAttribute()
		{
			$args = func_get_args();
			if(func_num_args() == 3)
			{
				return $this->GetXmlAttribute3($args[0],$args[1],$args[2]);
			}
			else if(func_num_args()==4)
			{
				return $this->GetXmlAttribute4($args[0],$args[1],$args[2],$args[3]);
			}
		}

		private function GetXmlAttribute3($elementLabel,$index,$attributeLabel)
		{
			if($xml!=null)
			{
				$element = $xml->getElementsByTagname($elementLabel)->item($index);
				if($element!=null)
				{
					$attribute = $element->getAttribute($attributeLabel);
					return $attribute;
				}
				else
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}

		private function GetXmlAttribute4($xmlPath,$elementLabel,$index,$attributeLabel)
		{
			$xmlDOC = new DOMDocument();
			$xmlDOC->load($xmlPath);
			if($xmlDOC!=null)
			{
				$element = $xmlDOC->getElementsByTagname($elementLabel)->item($index);
				if($element!=null)
				{
					$attribute = $element->getAttribute($attributeLabel);
					return $attribute;
				}
				else
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}

		public function GetXmlAttributes()
		{
			$args = func_get_args();
			if(func_num_args() == 2)
			{
				return $this->GetXmlAttributes2($args[0],$args[1]);
			}
			else if(func_num_args()==3)
			{
				return $this->GetXmlAttributes3($args[0],$args[1],$args[2]);
			}
		}

		private function GetXmlAttributes2($elementLabel,$attributeLabel)
		{
			if($this->xml!=null)
			{
				$elements = $this->xml->getElementsByTagname($elementLabel);
				if($elements!=null)
				{
					$attributes = array();
					for($i=0;$i<$elements.length;$i++)
					{
						$attributes[$i] = $elements->item($i)->getAttribute($attributeLabel);
					}
					return $attributes;
				}
				else
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}

		private function GetXmlAttributes3($xmlPath,$elementLabel,$attributeLabel)
		{
			$xmlDOC = new DOMDocument();
			$xmlDOC->load($xmlPath);
			if($xmlDOC!=null)
			{
				$elements = $xmlDOC->getElementsByTagname($elementLabel);
				if($elements!=null)
				{
					$attributes = array();
					for($i=0;$i<$elements.length;$i++)
					{
						$attributes[$i] = $elements->item($i)->getAttribute($attributeLabel);
					}
					return $attributes;
				}
				else
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}
	}
?>