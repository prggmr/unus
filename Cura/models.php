<?php

// Unus Resources

Unus_Model::getInstance()->registerTable('resource')
              ->registerField('resourceId', Unus_Model_Table::PRIMARY)
              ->registerField('parentId', Unus_Model_Table::ONETOONE, array('use' => 'self'))
              ->registerField('title', Unus_Model_Table::CHAR, array('max_length' => 225))
              ->registerField('level', Unus_Model_Table::INTERGER, array('max_length' => 1))
              ->registerField('roleAllow', Unus_Model_Table::TEXT)
              ->registerField('userAllow', Unus_Model_Table::TEXT)
              ->registerField('roleDeny', Unus_Model_Table::TEXT)
              ->registerField('userDeny', Unus_Model_Table::TEXT);

// Roles

Unus_Model::getInstance()->registerTable('role')
              ->registerField('roleId', Unus_Model_Table::PRIMARY)
              ->registerField('parentId', Unus_Model_Table::ONETOONE, array('use' => 'self'))
              ->registerField('title', Unus_Model_Table::CHAR, array('max_length' => 225))
              ->registerField('level', Unus_Model_Table::INTERGER, array('max_length' => 1));
