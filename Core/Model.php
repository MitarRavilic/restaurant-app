<?php
    namespace App\Core;

    use App\Core\DatabaseConnection;

    abstract class Model {
        private $dbc;

        final public function __construct(DatabaseConnection &$dbc){
            $this->dbc = $dbc;
        }

        protected function getFieldList(): array {
            return [];
        }

        final protected function getConnection(){
            return $this->dbc->getDatabaseConnection();
        }

        final protected function getTableName(): string {
            $rezultati = [];
            preg_match('|^.*\\\((?:[A-Z][a-z]+)+)Model$|', static::class, $rezultati);
            return substr(strtolower(preg_replace('|[A-Z]|', '_$0',  $rezultati[1])), 1);
        }

        final public function getById(int $id) {
            $table = $this->getTableName();
            $sql = "SELECT * FROM {$table} WHERE {$table}_id = ?;";
            $prep = $this->dbc->getDatabaseConnection()->prepare($sql);
            $res = $prep->execute( [ $id ] );

            $item = NULL;
            if ( $res ) {
                $itemObject = $prep->fetch(\PDO::FETCH_OBJ);
                if ($itemObject !== FALSE) {
                    $item = $itemObject;
                }
            }

            return $item;
        }

        final public function getAll(): array {
            $table = $this->getTableName();
            $sql = "SELECT * FROM {$table};";
            $prep = $this->dbc->getDatabaseConnection()->prepare($sql);
            $res = $prep->execute();

            $items = [];
            if ( $res ) {
                $items = $prep->fetchAll(\PDO::FETCH_OBJ);
            }

            return $items;
        }

        final private function checkDataValues(array &$data) {
            $fields = $this->getFieldList();

            $supportedFieldNames = array_keys($fields);
            $requestedFieldNames = array_keys($data);

            foreach ( $requestedFieldNames as $requestedFieldName ) {
                if (!in_array($requestedFieldName, $supportedFieldNames)) {
                    throw new \Exception('Field ' . $requestedFieldName . ' is not supported!');
                }

                if ( !$fields[$requestedFieldName]->isEditable() ) {
                    throw new \Exception('Field ' . $requestedFieldName . ' is not editable!');
                }
            }
        }

        final public function add(array $data) {
            $this->checkDataValues($data);

            $tableName = $this->getTableName();

            $sqlFieldNames = implode(', ', array_keys($data));

            $questionMarks = str_repeat('?,', count($data));
            $questionMarks = substr($questionMarks, 0, -1);

            $sql = "INSERT INTO {$tableName} ({$sqlFieldNames}) VALUES ({$questionMarks});";
            $prep = $this->dbc->getDatabaseConnection()->prepare($sql);
            $res = $prep->execute(array_values($data));
            if (!$res) {
                return false;
            }
            

            return $this->dbc->getDatabaseConnection()->lastInsertId();
        }

        final public function editById($id, array $data) {
            $this->checkDataValues($data);

            $table = $this->getTableName();

            $updateFieldList = [];
            $values = [];
            foreach ($data as $polje => $vrednost) {
                $updateFieldList[] = $polje . ' = ?';
                $values[] = $vrednost;
            }
            $updateFields = implode(', ', $updateFieldList);
            $values[] = $id;

            $sql = "UPDATE {$table} SET {$updateFields} WHERE {$table}_id = ?;";
            $prep = $this->dbc->getDatabaseConnection()->prepare($sql);
            return $prep->execute($values);
        }

        final public function deleteById($id) {
            $table = $this->getTableName();
            $sql = "DELETE FROM {$table} WHERE {$table}_id = ?;";
            $prep = $this->dbc->getDatabaseConnection()->prepare($sql);
            return $prep->execute( [ $id ] );
        }

        final private function isFieldValueValid(string $fieldName, $fieldValue): bool {
            $fields = $this->getFieldList();
            $supportedFieldNames = array_keys($fields);

            if (!in_array($fieldName, $supportedFieldNames)) {
                return false;
            }

            return $fields[$fieldName]->isValid($fieldValue);
        }

        final public function getByFieldName(string $fieldName, $value) {
            if (!$this->isFieldValueValid($fieldName, $value)) {
                throw new Exception('Invalid field name or value: ' . $fieldName);
            }

            $tableName = $this->getTableName();
            $sql = 'SELECT * FROM ' . $tableName . ' WHERE ' . $fieldName . ' = ?;';
            //$this->dbc
            $prep = $this->dbc->getDatabaseConnection()->prepare($sql);
            $res = $prep->execute([$value]);
            $item = NULL;
            if ($res) {
                $item = $prep->fetch(\PDO::FETCH_OBJ);
            }
            return $item;
        }
    }