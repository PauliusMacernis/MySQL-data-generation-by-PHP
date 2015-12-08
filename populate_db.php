<?php

class PopulateDb {

    private $Pdo = null;

    public function __construct($Pdo)
    {
        $this->Pdo = $Pdo;
    }

    public function getAllTablesOfDb($dbName) {

        $PdoStatement = $this->Pdo->prepare('SELECT GROUP_CONCAT(table_name) FROM information_schema.tables WHERE table_schema=:dbName');
        $PdoStatement->bindParam(':dbName', $dbName, PDO::PARAM_STR);
        $PdoStatement->execute();
        $tablesStr = @reset($PdoStatement->fetch(PDO::FETCH_ASSOC));
        $tables = explode(',', $tablesStr);

        return $tables;
    }

    private function extractDataType($typePlainText) {

        if(!$typePlainText) {
            return null;
        }

        // @TODO: Take care of all datatypes and modifications of MySQL
        $matches = null;
        preg_match('/^(.*)(\\(\\d+\\))|(.*)/', $typePlainText, $matches);

        return !empty($matches[1]) ? $matches[1] : $matches[0];

    }

    private function extractDataTypeSize($typePlainText) {

        if(!$typePlainText) {
            return null;
        }

        // @TODO: Take care of all datatypes and modifications of MySQL
        $matches = null;
        preg_match('/^(.*)(\\(\\d+\\))|(.*)/', $typePlainText, $matches);

        if(!isset($matches[2])) {
            return null;
        }

        $returnValue = trim($matches[2], '()');

        return isset($returnValue) ? $returnValue : null;

    }


    /**
     * @param stdClass $fieldData Row of 'DESCRIBE <table_name>' by MySQL
     * @return null
     */
    public function getGeneratedFieldValue(\stdClass $fieldData) {

        $type = $this->extractDataType($fieldData->Type);
        $typeSize = $this->extractDataTypeSize($fieldData->Type);

        if(!$type) {
            return null;
        }

        $methodName = 'getTableValue' . ucfirst(strtolower($type));

        $generatedValue = null;
        if(method_exists($this, $methodName)) {
            $generatedValue = $this->$methodName($typeSize, $fieldData);
        } else {
            die('No method: ' . $methodName);
        }

        var_dump($methodName . ': ' . $generatedValue);

        return $generatedValue;
    }



    private function getTableValueDatetime($size, $fieldData) {

    }

    private function getTableValueChar($size, $fieldData) {

    }

    private function getTableValueVarchar($size, $fieldData) {

    }

    private function getTableValueText($size, $fieldData) {

    }

    private function getTableValueMediumtext($size, $fieldData) {

    }

    private function getTableValueTinyint($size, $fieldData) {

    }

    private function getTableValueSmallint($size, $fieldData) {

    }

    private function getTableValueInt($size, $fieldData) {

    }

    private function getTableValueBigint($size, $fieldData) {

    }



    public function populateTable($tableName) {
        if(!$tableName) {
            return;
        }

        $tblDescription = $this->getTableDescription($tableName);

        foreach($tblDescription as $fieldData) {
            $value = $this->getGeneratedFieldValue($fieldData);
            //var_dump($value);
            //return $value;
        }


        var_dump($tblDescription);

    }

    public function getTableDescription($tableName) {
        if(!$tableName) {
            return;
        }

        $PdoStatement = $this->Pdo->prepare('DESCRIBE ' . $tableName);
        $PdoStatement->execute();

        return $PdoStatement->fetchAll(PDO::FETCH_OBJ);

    }


}

$dbHost = "localhost";
$dbName = "pdarbais";
$dbUsername = "pdarbais";
$dbPassword = "pdarbais";
$tables = array();

$Pdb = new PopulateDb(new PDO("mysql:host=$dbHost;dbname=$dbName",$dbUsername,$dbPassword));


// Get tables
if(empty($tables)) { // Get all tables of $dbName
    $tables = $Pdb->getAllTablesOfDb($dbName);
}

foreach($tables as $tableName) {
    $Pdb->populateTable($tableName);
}



// Foreach


var_dump($tables);


#INSERT INTO `pdarbais`.`nee` (`nee_nee`, `nee_cat`, `deleted`) VALUES (1, 2, 0);