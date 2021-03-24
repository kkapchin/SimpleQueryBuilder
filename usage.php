<?php
require_once 'QueryBuilder/QueryBuilder.php';
require_once 'QueryBuilder/Connection/Connection.php';

function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die;
}

/*
 * Step 1. Set up your connection config
 * */
Connection::setConfig()
    ->driver('mysql')
    ->host('127.0.0.1')
    ->dbname('md1')
    ->root('root')
    ->password('');

/*
 * Step 2. Create a QueryBuilder object, it needs a connection as DI
 *  */
$query = new QueryBuilder(Connection::make());

/*
 * Step 3. Execute a query as in examples below
 * */
$query->selectAll('users') // SELECT * FROM `users`;
    ->selectAll('users', 'age', '>', 18 ) // SELECT * FROM `users` WHERE `age` > 18;
    ->selectOne('users', 'age', 18 ) // returns array of only 1 row with matching conditions
    ->selectLast('users', 5 ) // returns array of 5 last rows by id and orders the rows by id ascendance
    ->delete('users', 7 ) // DELETE FROM `users` WHERE `users`.`id` = 7
    ->insert('users', [
        'name' => 'John',
        'surname' => 'Doe',
        'age' => 25]) // INSERT INTO `users` (`name`, `surname`, `age`) VALUES ('John', 'Doe', 25)
    ->update('users', ['age' => 33], 8); //UPDATE `users` SET `age` = 33 WHERE `users`.`id` = 8;
