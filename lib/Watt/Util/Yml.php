<?
class Watt_Util_Yml
{   
  public function parseYaml($configFile)
  {
  	 if (!empty($configFile) && (strpos($configFile, "\n") === false) 
          && file_exists($configFile)) {
		if (!is_readable($configFile))
	    {
	      $error = sprintf('Configuration file "%s" does not exist or is not readable', $configFile);
	      throw new Watt_Exception($error);
	      
	    }
      }
     
   
    
	$spyc = new Watt_Util_Yml_Spyc();
   $config= $spyc->load($configFile);
   //$config = self::load($configFile);
    if ($config === false || $config === null)
    {
      // configuration couldn't be parsed
      $error = sprintf('Configuration file "%s" could not be parsed', $configFile);
      throw new Watt_Exception($error);
    }  

    return $config;
  }
    
} 
  
class Watt_Util_Yml_Spyc
{  
  private $_haveRefs;
    private $_allNodes;
    private $_lastIndent;
    private $_lastNode;
    private $_inBlock;
    private $_isInline;
    private $_dumpIndent;
    private $_dumpWordWrap;
   /**
     * Load YAML into a PHP array from an instantiated object
     *
     * The load method, when supplied with a YAML stream (string or file path), 
     * will do its best to convert the YAML into a PHP array.  Pretty simple.
     *  Usage: 
     *  <code>
     *   $parser = new Spyc;
     *   $array  = $parser->load('lucky.yml');
     *   print_r($array);
     *  </code>
     * @access public
     * @return array
     * @param string $input Path of YAML file or string containing YAML
     */
     public function load($input) {
    
      // See what type of input we're talking about
      // If it's not a file, assume it's a string
      if (!empty($input) && (strpos($input, "\n") === false) 
          && file_exists($input)) {
        $yaml = file($input);
      } else {
        $yaml = explode("\n",$input);
      }
      
      // Initiate some objects and values
      $base              = new Watt_Util_Yml_YAMLNode;
      $base->indent      = 0;
      $this->_lastIndent = 0;
      $this->_lastNode   = $base->id;
      $this->_inBlock    = false;
      $this->_isInline   = false;
      foreach ($yaml as $linenum => $line) {
        $ifchk = trim($line);

        // If the line starts with a tab (instead of a space), throw a fit.
        if (preg_match('/^(\t)+(\w+)/', $line)) {
          $err = 'ERROR: Line '. ($linenum + 1) .' in your input YAML begins'.
                 ' with a tab.  YAML only recognizes spaces.  Please reformat.';
          throw new Exception($err);
        }
        
        if ($this->_inBlock === false && empty($ifchk)) {
          continue;
        } elseif ($this->_inBlock == true && empty($ifchk)) {
          $last =& $this->_allNodes[$this->_lastNode];
          $last->data[key($last->data)] .= "\n";
        } elseif ($ifchk{0} != '#' && substr($ifchk,0,3) != '---') {
          // Create a new node and get its indent
          $node         = new Watt_Util_Yml_YAMLNode;
          $node->indent = $this->_getIndent($line);
          
          // Check where the node lies in the hierarchy
          if ($this->_lastIndent == $node->indent) {
            // If we're in a block, add the text to the parent's data
            if ($this->_inBlock === true) {
              $parent =& $this->_allNodes[$this->_lastNode];
              $parent->data[key($parent->data)] .= trim($line).$this->_blockEnd;
            } else {
              // The current node's parent is the same as the previous node's
              if (isset($this->_allNodes[$this->_lastNode])) {
                $node->parent = $this->_allNodes[$this->_lastNode]->parent;
              }
            }
          } elseif ($this->_lastIndent < $node->indent) {            
            if ($this->_inBlock === true) {
              $parent =& $this->_allNodes[$this->_lastNode];
              $parent->data[key($parent->data)] .= trim($line).$this->_blockEnd;
            } elseif ($this->_inBlock === false) {
              // The current node's parent is the previous node
              $node->parent = $this->_lastNode;
              
              // If the value of the last node's data was > or | we need to 
              // start blocking i.e. taking in all lines as a text value until 
              // we drop our indent.
              $parent =& $this->_allNodes[$node->parent];
              $this->_allNodes[$node->parent]->children = true;
              if (is_array($parent->data)) {
                $chk = $parent->data[key($parent->data)];
                if ($chk === '>') {
                  $this->_inBlock  = true;
                  $this->_blockEnd = ' ';
                  $parent->data[key($parent->data)] = 
                        str_replace('>','',$parent->data[key($parent->data)]);
                  $parent->data[key($parent->data)] .= trim($line).' ';
                  $this->_allNodes[$node->parent]->children = false;
                  $this->_lastIndent = $node->indent;
                } elseif ($chk === '|') {
                  $this->_inBlock  = true;
                  $this->_blockEnd = "\n";
                  $parent->data[key($parent->data)] =               
                        str_replace('|','',$parent->data[key($parent->data)]);
                  $parent->data[key($parent->data)] .= trim($line)."\n";
                  $this->_allNodes[$node->parent]->children = false;
                  $this->_lastIndent = $node->indent;
                }
              }
            }
          } elseif ($this->_lastIndent > $node->indent) {
            // Any block we had going is dead now
            if ($this->_inBlock === true) {
              $this->_inBlock = false;
              if ($this->_blockEnd = "\n") {
                $last =& $this->_allNodes[$this->_lastNode];
                $last->data[key($last->data)] = 
                      trim($last->data[key($last->data)]);
              }
            }
            
            // We don't know the parent of the node so we have to find it
            // foreach ($this->_allNodes as $n) {
            foreach ($this->_indentSort[$node->indent] as $n) {
              if ($n->indent == $node->indent) {
                $node->parent = $n->parent;
              }
            }
          }
        
          if ($this->_inBlock === false) {
            // Set these properties with information from our current node
            $this->_lastIndent = $node->indent;
            // Set the last node
            $this->_lastNode = $node->id;
            // Parse the YAML line and return its data
            $node->data = $this->_parseLine($line);
            // Add the node to the master list
            $this->_allNodes[$node->id] = $node;
            // Add a reference to the node in an indent array
            $this->_indentSort[$node->indent][] =& $this->_allNodes[$node->id];
            // Add a reference to the node in a References array if this node
            // has a YAML reference in it.
            if ( 
              ( (is_array($node->data)) &&
                isset($node->data[key($node->data)]) &&
                (!is_array($node->data[key($node->data)])) )
              &&
              ( (preg_match('/^&([^ ]+)/',$node->data[key($node->data)])) 
                || 
                (preg_match('/^\*([^ ]+)/',$node->data[key($node->data)])) )
            ) {
                $this->_haveRefs[] =& $this->_allNodes[$node->id];
            } elseif (
              ( (is_array($node->data)) &&
                isset($node->data[key($node->data)]) &&
                 (is_array($node->data[key($node->data)])) )
            ) {
              // Incomplete reference making code.  Ugly, needs cleaned up.
              foreach ($node->data[key($node->data)] as $d) {
                if ( !is_array($d) && 
                  ( (preg_match('/^&([^ ]+)/',$d)) 
                    || 
                    (preg_match('/^\*([^ ]+)/',$d)) )
                  ) {
                    $this->_haveRefs[] =& $this->_allNodes[$node->id];
                }
              }
            }
          }
        }
      }
      unset($node);
      
      // Here we travel through node-space and pick out references (& and *)
      $this->_linkReferences();
      
      // Build the PHP array out of node-space
      $trunk = $this->_buildArray();
      return $trunk;
    }
    
     /**
     * Finds and returns the indentation of a YAML line
     * @access private
     * @return int
     * @param string $line A line from the YAML file
     */
     private function _getIndent($line) {
      preg_match('/^\s{1,}/',$line,$match);
      if (!empty($match[0])) {
        $indent = substr_count($match[0],' ');
      } else {
        $indent = 0;
      }
      return $indent;
    }
    
     /**
     * Parses YAML code and returns an array for a node
     * @access private
     * @return array
     * @param string $line A line from the YAML file
     */
     private function _parseLine($line) {
      $line = trim($line);  

      $array = array();

      if (preg_match('/^-(.*):$/',$line)) {
        // It's a mapped sequence
        $key         = trim(substr(substr($line,1),0,-1));
        $array[$key] = '';
      } elseif ($line[0] == '-' && substr($line,0,3) != '---') {
        // It's a list item but not a new stream
        if (strlen($line) > 1) {
          $value   = trim(substr($line,1));
          // Set the type of the value.  Int, string, etc
          $value   = $this->_toType($value);
          $array[] = $value;
        } else {
          $array[] = array();
        }
      } elseif (preg_match('/^(.+):/',$line,$key)) {
        // It's a key/value pair most likely
        // If the key is in double quotes pull it out
        if (preg_match('/^(["\'](.*)["\'](\s)*:)/',$line,$matches)) {
          $value = trim(str_replace($matches[1],'',$line));
          $key   = $matches[2];
        } else {
          // Do some guesswork as to the key and the value
          $explode = explode(':',$line);
          $key     = trim($explode[0]);
          array_shift($explode);
          $value   = trim(implode(':',$explode));
        }

        // Set the type of the value.  Int, string, etc
        $value = $this->_toType($value);
        if (empty($key)) {
          $array[]     = $value;
        } else {
          $array[$key] = $value;
        }
      }
      return $array;
    }
    
    /**
     * Finds the type of the passed value, returns the value as the new type.
     * @access private
     * @param string $value
     * @return mixed
     */
     private function _toType($value) {
      if (preg_match('/^("(.*)"|\'(.*)\')/',$value,$matches)) {        
       $value = (string)preg_replace('/(\'\'|\\\\\')/',"'",end($matches));
       $value = preg_replace('/\\\\"/','"',$value);
      } elseif (preg_match('/^\\[(.+)\\]$/',$value,$matches)) {
        // Inline Sequence

        // Take out strings sequences and mappings
        $explode = $this->_inlineEscape($matches[1]);
        
        // Propogate value array
        $value  = array();
        foreach ($explode as $v) {
          $value[] = $this->_toType($v);
        }
      } elseif (strpos($value,': ')!==false && !preg_match('/^{(.+)/',$value)) {
          // It's a map
          $array = explode(': ',$value);
          $key   = trim($array[0]);
          array_shift($array);
          $value = trim(implode(': ',$array));
          $value = $this->_toType($value);
          $value = array($key => $value);
      } elseif (preg_match("/{(.+)}$/",$value,$matches)) {
        // Inline Mapping

        // Take out strings sequences and mappings
        $explode = $this->_inlineEscape($matches[1]);

        // Propogate value array
        $array = array();
        foreach ($explode as $v) {
          $array = $array + $this->_toType($v);
        }
        $value = $array;
      } elseif (strtolower($value) == 'null' or $value == '' or $value == '~') {
        $value = NULL;
      } elseif (ctype_digit($value)) {
        $value = (int)$value;
      } elseif (in_array(strtolower($value), 
                  array('true', 'on', '+', 'yes', 'y'))) {
        $value = TRUE;
      } elseif (in_array(strtolower($value), 
                  array('false', 'off', '-', 'no', 'n'))) {
        $value = FALSE;
      } elseif (is_numeric($value)) {
        $value = (float)$value;
      } else {
        // Just a normal string, right?
        $value = trim(preg_replace('/#(.+)$/','',$value));
      }
      
      return $value;
    }
    
    /**
     * Traverses node-space and sets references (& and *) accordingly
     * @access private
     * @return bool
     */
     private function _linkReferences() {
      if (is_array($this->_haveRefs)) {
        foreach ($this->_haveRefs as $node) {
          if (!empty($node->data)) {
            $key = key($node->data);
            // If it's an array, don't check.
            if (is_array($node->data[$key])) {  
              foreach ($node->data[$key] as $k => $v) {
                $this->_linkRef($node,$key,$k,$v);
              }
            } else {
              $this->_linkRef($node,$key);
            }
          }
        } 
      }
      return true;
    }
    
    /**
     * Builds the PHP array from all the YAML nodes we've gathered
     * @access private
     * @return array
     */
     private function _buildArray() {
      $trunk = array();

      if (!isset($this->_indentSort[0])) {
        return $trunk;
      }

      foreach ($this->_indentSort[0] as $n) {
        if (empty($n->parent)) {
          $this->_nodeArrayizeData($n);
          // Check for references and copy the needed data to complete them.
          $this->_makeReferences($n);
          // Merge our data with the big array we're building
          $trunk = $this->_array_kmerge($trunk,$n->data);
        }
      }
      
      return $trunk;
    }
  
    /**
     * Turns a node's data and its children's data into a PHP array
     *
     * @access private
     * @param array $node The node which you want to arrayize
     * @return boolean
     */
     private function _nodeArrayizeData(&$node) {
      if (is_array($node->data) && $node->children == true) {
        // This node has children, so we need to find them
        $childs = $this->_gatherChildren($node->id);
        // We've gathered all our children's data and are ready to use it
        $key = key($node->data);
        $key = empty($key) ? 0 : $key;
        // If it's an array, add to it of course
        if (is_array($node->data[$key])) {
          $node->data[$key] = $this->_array_kmerge($node->data[$key],$childs);
        } else {
          $node->data[$key] = $childs;
        }
      } elseif (!is_array($node->data) && $node->children == true) {
        // Same as above, find the children of this node
        $childs       = $this->_gatherChildren($node->id);
        $node->data   = array();
        $node->data[] = $childs;
      }

      // We edited $node by reference, so just return true
      return true;
    }

    /**
     * Finds the children of a node and aids in the building of the PHP array
     * @access private
     * @param int $nid The id of the node whose children we're gathering
     * @return array
     */
     private function _gatherChildren($nid) {
      $return = array();
      $node   =& $this->_allNodes[$nid];
      foreach ($this->_allNodes as $z) {
        if ($z->parent == $node->id) {
          // We found a child
          $this->_nodeArrayizeData($z);
          // Check for references
          $this->_makeReferences($z);
          // Merge with the big array we're returning
          // The big array being all the data of the children of our parent node
          $return = $this->_array_kmerge($return,$z->data);
        }
      }
      return $return;
    }
  
    /**
     * Traverses node-space and copies references to / from this object.
     * @access private
     * @param object $z A node whose references we wish to make real
     * @return bool
     */
     private function _makeReferences(&$z) {
      // It is a reference
      if (isset($z->ref)) {
        $key                = key($z->data);
        // Copy the data to this object for easy retrieval later
        $this->ref[$z->ref] =& $z->data[$key];
      // It has a reference
      } elseif (isset($z->refKey)) {
        if (isset($this->ref[$z->refKey])) {
          $key           = key($z->data);
          // Copy the data from this object to make the node a real reference
          $z->data[$key] =& $this->ref[$z->refKey];
        }
      }
      return true;
    }
  
    /**
     * Merges arrays and maintains numeric keys.
     *
     * An ever-so-slightly modified version of the array_kmerge() function posted
     * to php.net by mail at nospam dot iaindooley dot com on 2004-04-08.
     *
     * http://us3.php.net/manual/en/function.array-merge.php#41394
     *
     * @access private
     * @param array $arr1
     * @param array $arr2
     * @return array
     */
     private function _array_kmerge($arr1,$arr2) { 
      if(!is_array($arr1)) 
        $arr1 = array(); 

      if(!is_array($arr2))
        $arr2 = array(); 
    
      $keys1 = array_keys($arr1); 
      $keys2 = array_keys($arr2); 
      $keys  = array_merge($keys1,$keys2); 
      $vals1 = array_values($arr1); 
      $vals2 = array_values($arr2); 
      $vals  = array_merge($vals1,$vals2); 
      $ret   = array(); 

      foreach($keys as $key) { 
        list($unused,$val) = each($vals);
        // This is the good part!  If a key already exists, but it's part of a
        // sequence (an int), just keep addin numbers until we find a fresh one.
        if (isset($ret[$key]) and is_int($key)) {
          while (array_key_exists($key, $ret)) {
            $key++;
          }
        }  
        $ret[$key] = $val; 
      } 

      return $ret; 
    }
    
}

class Watt_Util_Yml_YAMLNode {
    /**#@+
     * @access public
     * @var string
     */ 
    public $parent;
    public $id;
    /**#@+*/
    /** 
     * @access public
     * @var mixed
     */
    public $data;
    /** 
     * @access public
     * @var int
     */
    public $indent;
    /** 
     * @access public
     * @var bool
     */
    public $children = false;

    /**
     * The constructor assigns the node a unique ID.
     * @access public
     * @return void
     */
     public function Watt_Util_Yml_YAMLNode() {
      $this->id = uniqid('');
    }
  }
?>