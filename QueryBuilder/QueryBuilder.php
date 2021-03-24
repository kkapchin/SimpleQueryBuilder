<?php
class QueryBuilder {

    private $pdo;

    private function build_query($expression, $table, $data = null, $id = null) {
        if($expression == 'select' or $expression == 'delete') {
            if($expression == 'select') {
                $expression = 'SELECT *';
            } else {
                $expression = 'DELETE';
            }
            if(empty($data)) {
                $sql = "{$expression} FROM {$table}";
                return $this->send_query($sql);
            }
            if(count($data) === 3) {

                $operators = ['=', '>', '<', '>=', '<='];
                $col = $data[0];
                $operator = $data[1];
                $value = $data[2];
                $tag = ':' . $col;

                if(in_array($operator, $operators)) {
                    $sql = "{$expression} FROM {$table} WHERE {$col}{$operator}{$tag}";
                    return $this->send_query($sql, array($tag), array($value));
                }
            }
            if(count($data) === 1 & is_numeric($data)) {
                $sql = "{$expression} FROM ({$expression} FROM {$table} ORDER BY id DESC LIMIT {$data}) AS T ORDER BY id ASC";
                return $this->send_query($sql);
            }
            return false;
        }
        if($expression == 'insert') {
            $cols = implode(',', array_keys($data));
            $tags = ':' . implode(',:', array_keys($data));
            $sql = "INSERT INTO {$table} ({$cols}) VALUES ({$tags})";
            $this->send_query($sql, explode(',', $tags), $data);
        }
        if($expression == 'update') {
            $set = '';
            foreach (array_keys($data) as $key) {
                $set .= $key . '=:' . $key . ',';
            }
            $set = rtrim($set, ',');
            $sql = "UPDATE {$table} SET {$set} WHERE id=:id";
            $tags = ':' . implode(',:', array_keys($data)) . ',:id';
            $data[] = $id;
            $this->send_query($sql, explode(',', $tags), $data);
        }
    }

    private function send_query($sql, array $tags = [], array $values = []) {
        if(empty($tags) && empty($values)) {
            $statement = $this->pdo->prepare($sql);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        if(is_array($tags) && is_array($values)) {
            $data = array_combine($tags, $values);
            $statement = $this->pdo->prepare($sql);
            $statement->execute($data);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /*
     * PDO $pdo - dependency injection for connection with database
     * */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /*
     * string $table - table name
     * returns array
     * */
    /*public function selectAll(string $table) {
        return $this->build_query('select', $table);
    }*/

    /*
     * string $table - table name
     * string $col - table column name
     * string $operator - logic operator ('=', '>', '<', '>=', '<=')
     * string/integer/float $value - a value by which result is filtered
     * returns array
     * */
    public function selectAll(string $table, $col = null, $operator = null, $value = null) {
        if(empty($col) && empty($operator) && empty($value)) {
            return $this->build_query('select', $table);
        } else if (!empty($col) && !empty($operator) && !empty($value)) {
            return $this->build_query('select', $table, array($col, $operator, $value));
        }
    }

    /*
     * string $table - table name
     * string $col - table column name
     * string/integer/float $value - a value by which result is filtered
     * returns array
     * */
    public function selectOne(string $table, $col, $value){
        $result = $this->build_query('select', $table, array($col, '=', $value));
        return array_shift($result);
    }

    /*
     * string $table - table name
     * integer $quantity - quantity of records to fetch
     * returns array
     * */
    public function selectLast(string $table, int $quantity) {
        return $this->build_query('select', $table, $quantity);
    }

    /*
     * string $table - table name
     * integer $id - record id
     * */
    public function delete(string $table, $id) {
        $this->build_query('delete', $table, ['id', '=', $id]);
    }

    /*
     * string $table - table name
     * array $values = [$key => $value]
     * */
    public function insert(string $table, array $values) {
        if(!empty($values)) {
            $this->build_query('insert', $table, $values);
        }
        return false;
    }

    /*
     * string $table - table name
     * array $values = [$key => $value]
     * integer $id - record id
     * */
    public function update($table, array $values, $id) {
        if(!empty($values) && is_numeric($id)) {
            $this->build_query('update', $table, $values, $id);
        }
    }
}
