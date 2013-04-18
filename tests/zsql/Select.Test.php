<?php

class Select_Test extends Common_Test
{
  protected $_className = '\\zsql\\Select';
  
  public function testColumns_Expression()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->columns(new \zsql\Expression('SUM(number)'));
    $this->assertEquals('SELECT SUM(number) FROM `tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('SELECT SUM(number) FROM `tableName`', $query->toString());
  }
  
  public function testColumns_String()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->columns('columnName');
    $this->assertEquals('SELECT `columnName` FROM `tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('SELECT `columnName` FROM `tableName`', $query->toString());
  }
  
  public function testColumns_InvalidValueThrowsException()
  {
    $query = new \zsql\Select();
    try {
      $query->columns(false);
      $this->assertTrue(false); // -_-
    } catch( Exception $e ) {
      $this->assertInstanceOf('\\zsql\\Exception', $e);
    }
  }
  
  public function testDistinct()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->distinct()
      ->where('columnName', 'value');
    $this->assertEquals('SELECT DISTINCT * FROM `tableName` WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("SELECT DISTINCT * FROM `tableName` WHERE `columnName` = 'value'", $query->toString());
  }
  
  public function testDistinct_False()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->distinct(true)
      ->distinct(false)
      ->where('columnName', 'value');
    $this->assertEquals('SELECT * FROM `tableName` WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array('value'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("SELECT * FROM `tableName` WHERE `columnName` = 'value'", $query->toString());
  }
  
  public function testFrom_WithColumns()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName', array('a', 'b', 'c'));
    $this->assertEquals('SELECT `a`, `b`, `c` FROM `tableName`', $query->toString());
    $this->assertEquals(array(), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals('SELECT `a`, `b`, `c` FROM `tableName`', $query->toString());
  }
  
  public function testGroup()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->where('a', 'b')
      ->group('id');
    $this->assertEquals('SELECT * FROM `tableName` ' 
        . 'WHERE `a` = ? GROUP BY `id`', $query->toString());
    $this->assertEquals(array('b'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("SELECT * FROM `tableName` WHERE `a` = 'b' GROUP BY `id`", $query->toString());
  }
  
  public function testHint()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->where('columnOne', 'a')
      ->where('columnTwo', 'b')
      ->hint('columnTwo', 'FORCE');
    $this->assertEquals('SELECT * FROM `tableName` FORCE INDEX (`columnTwo`) ' 
        . 'WHERE `columnOne` = ? && `columnTwo` = ?', $query->toString());
    $this->assertEquals(array('a', 'b'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("SELECT * FROM `tableName` FORCE INDEX (`columnTwo`) " 
        . "WHERE `columnOne` = 'a' && `columnTwo` = 'b'", $query->toString());
  }
  
  public function testHint_Array()
  {
    $query = new \zsql\Select();
    $query
      ->from('tableName')
      ->where('columnOne', 'a')
      ->where('columnTwo', 'b')
      ->hint(array('columnTwo', 'columnThree'), 'IGNORE');
    $this->assertEquals('SELECT * FROM `tableName` IGNORE INDEX (`columnTwo`, `columnThree`) ' 
        . 'WHERE `columnOne` = ? && `columnTwo` = ?', $query->toString());
    $this->assertEquals(array('a', 'b'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("SELECT * FROM `tableName` IGNORE INDEX (`columnTwo`, `columnThree`) "
        . "WHERE `columnOne` = 'a' && `columnTwo` = 'b'", $query->toString());
  }
  
  public function testSelect()
  {
    $query = new \zsql\Select();
    $query->select('columnName')
        ->from('tableName')
        ->where('columnName', 'columnValue');
    $this->assertEquals('SELECT `columnName` FROM `tableName` ' 
        . 'WHERE `columnName` = ?', $query->toString());
    $this->assertEquals(array('columnValue'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("SELECT `columnName` FROM `tableName` "
        . "WHERE `columnName` = 'columnValue'", $query->toString());
  }
  
  public function testWhereIn_Empty()
  {
    $query = new \zsql\Select();
    $query->select('columnName')
        ->from('tableName')
        ->whereIn('columnName', array());
    $this->assertEquals('SELECT `columnName` FROM `tableName` ' 
        . 'WHERE FALSE', $query->toString());
    $this->assertEquals(array(), $query->params());
  }
  
  public function testWhereIn_String()
  {
    $query = new \zsql\Select();
    $query->select('columnName')
        ->from('tableName')
        ->whereIn('columnName', 'columnValue');
    $this->assertEquals('SELECT `columnName` FROM `tableName` ' 
        . 'WHERE `columnName` IN (?)', $query->toString());
    $this->assertEquals(array('columnValue'), $query->params());
    
    // Test interpolation
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals("SELECT `columnName` FROM `tableName` "
        . "WHERE `columnName` IN ('columnValue')", $query->toString());
  }
  
  public function test_interpolate_ThrowsException()
  {
    $query = new \zsql\Select();
    $exception = null;
    try {
      $query->table('tableName')->where('a', 'b');
      $query->interpolation();
      $query->toString();
    } catch( Exception $e ) {
      $exception = $e;
    }
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function test_interpolate_ThrowsException2()
  {
    $query = new \zsql\Select();
    $exception = null;
    try {
      $query->table('tableName')->where('a??', 'b');
      $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
      $query->toString();
    } catch( Exception $e ) {
      $exception = $e;
    }
    $this->assertInstanceOf('\\zsql\\Exception', $exception);
  }
  
  public function testQuery_WithInterpolation()
  {
    $expectedQuery = "SELECT * FROM `tableName` WHERE `columnName` = 'value'";
    $testObject = $this;
    $callback = function($actualQuery)use($expectedQuery, $testObject) {
      $testObject->assertEquals($expectedQuery, $actualQuery);
      return $actualQuery;
    };
    $query = new \zsql\Select($callback);
    $query->from('tableName')->where('columnName', 'value');
    $query->setQuoteCallback($this->_getQuoteCallback())->interpolation();
    $this->assertEquals($expectedQuery, $query->query());
  }
  
  public function testQuery_WithoutInterpolation()
  {
    $expectedQuery = "SELECT * FROM `tableName` WHERE `columnName` = ?";
    $expectedParams = array('value');
    $testObject = $this;
    $callback = function($actualQuery, $actualParams)use($expectedQuery, $expectedParams, $testObject) {
      $testObject->assertEquals($expectedQuery, $actualQuery);
      $testObject->assertEquals($expectedParams, $actualParams);
      return $actualQuery;
    };
    $query = new \zsql\Select($callback);
    $query->from('tableName')->where('columnName', 'value');
    $this->assertEquals($expectedQuery, $query->query());
    $this->assertEquals($expectedParams, $query->params());
  }
}
