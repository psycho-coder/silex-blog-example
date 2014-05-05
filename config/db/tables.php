<?php

use Doctrine\DBAL\Schema\Table;

$tables = array();

/**
 * Table: users
 */
$tables['users'] = new Table('users');
$tables['users']->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$tables['users']->addColumn('username', 'string', array('length' => 32));
$tables['users']->addColumn('password', 'string', array('length' => 255));
$tables['users']->addColumn('salt', 'string', array('length' => 255));
$tables['users']->addColumn('roles', 'string', array('length' => 255, 'notnull' => false));
$tables['users']->setPrimaryKey(array('id'));
$tables['users']->addUniqueIndex(array('username'));

/**
 * Table: posts
 */
$tables['posts'] = new Table('posts');
$tables['posts']->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$tables['posts']->addColumn('title', 'string', array('length' => 255, 'notnull' => true));
$tables['posts']->addColumn('content', 'text');
$tables['posts']->addColumn('created_at', 'date');
$tables['posts']->addColumn('slug', 'string', array('length' => 255, 'notnull' => true));
$tables['posts']->addColumn('meta_title', 'string', array('length' => 255, 'notnull' => false));
$tables['posts']->addColumn('meta_keywords', 'string', array('length' => 255, 'notnull' => false));
$tables['posts']->addColumn('meta_description', 'string', array('length' => 255, 'notnull' => false));
$tables['posts']->setPrimaryKey(array('id'));
$tables['posts']->addUniqueIndex(array('slug'));

return $tables;