<?php
namespace Geeshoe\DbLib;


class DbLib
{
    private $connection = null;

    private $iniPath = null;

    public $insert = array();

    public $values = array();

    public function __construct($iniLocation)
    {
//        $this->iniPath = dirname(__DIR__, 4) . '/DbConfig.ini';
        $this->iniPath = $iniLocation;
        $settings = parse_ini_file($this->iniPath, true);

        if (!empty($settings['config']['AltPath'])) {
            $this->iniPath = $settings['config']['AltPath'];
        }
    }

    private function connect()
    {
        if (!isset($this->connection)) {
            $ini = parse_ini_file($this->iniPath, true);
            $this->connection = new \PDO(
                'mysql:dbname='.$ini['mysql']['dataBase'].
                ';host='.$ini['mysql']['hostName'].':'.$ini['mysql']['port'],
                $ini['mysql']['userName'],
                $ini['mysql']['passWord']
            );
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $this->connection;
    }

    public function executeQueryWithNoReturn($sqlStatement)
    {
        $this->connect()->exec($sqlStatement);
    }

    public function executeQueryWithSingleReturn($sqlStatement, $fetchStyle)
    {
        $result = $this->connect()->exec($sqlStatement)->fetch($fetchStyle);
        return $result;
    }

    public function executeQueryWithAllReturned($sqlStatement, $fetchStyle)
    {
        $result = $this->connect()->exec($sqlStatement)->fetchAll($fetchStyle);
        return $result;
    }

    public function manipulateDataWithNoReturn($sqlStatement, $valuesArray)
    {
        $stmt = $this->connect()->prepare($sqlStatement);

        foreach ($valuesArray as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
    }

    public function manipulateDataWithSingleReturn(
        string $sqlStatement,
        array $valuesArray,
        string $fetchStyle
    )
    {
        $stmt = $this->connect()->prepare($sqlStatement);

        foreach ($valuesArray as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $results = $stmt->fetch($fetchStyle);
        return $results;
    }

    public function manipulateDataWithAllReturned(
        string $sqlStatement,
        array $valuesArray,
        string $fetchStyle)
    {
        $stmt = $this->connect()->prepare($sqlStatement);

        foreach ($valuesArray as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        $results = $stmt->fetchAll($fetchStyle);
        return $results;
    }

    public function deleteData()
    {
    }

    public function createDataArray(string $typeOfArray, array $userSuppliedData)
    {
        foreach (array_keys($userSuppliedData) as $key) {
            if ($typeOfArray == 'insert') {
                $this->insert[] = $key;
            } elseif ($typeOfArray == 'manipulate') {
                $this->insert[] = '`' . $key . '`' . ' = :' . $key;
            }
            //@TODO - Throw exception if wrong $typeOfStatement is entered.
            $this->values[':'.$key] = $userSuppliedData[$key];
        }
    }

    public function createSqlInsertStatement(string $insertInWhatTable)
    {
        $statement = 'INSERT INTO `'.$insertInWhatTable.'`('
            . implode(', ', $this->insert) .
            ') VALUE ('
            . implode(', ', array_keys($this->values)) .
            ')';
        return $statement;
    }

    public function createSqlUpdateStatement(
        string $updateWhatTable,
        string $updateByWhatColumn,
        string $updateWhatId
    )
    {
        return 'UPDATE `'.$updateWhatTable.'` SET ' . implode(", ", $this->insert) . ' WHERE `'
            .$updateByWhatColumn.'` = ' . $updateWhatId;
    }

    public function createSqlDeleteStatement(
        string $deleteFromWhichTable,
        string $deleteByWhatColumn,
        string $deleteWhatId
    )
    {
        return 'DELETE FROM `' . $deleteFromWhichTable . '` WHERE `'
            . $deleteByWhatColumn . '` = ' . $deleteWhatId . ';';
    }
}
