<?php
	class DatabaseHelper {

    private $dbConnection;

    function __construct($dbConnection) {
      $this->dbConnection = $dbConnection;
    }

    public function select($query, $bindParamTypes, $bindParams) {
      $utils = new Utils();

      $stmt = $this->dbConnection->prepare($query);

      if(!$utils->emptyString($bindParamTypes)) {
        $stmt->bind_param($bindParamTypes, ...$bindParams);
      }

      if($stmt->execute()) {
        $resultSet = $stmt->get_result();

        if($resultSet->num_rows > 0){
          $return = array();
          while($obj = $resultSet->fetch_assoc()){
            array_push($return, $obj);
          }

          mysqli_free_result($resultSet);

          return $return;
        }
      }
    }

    public function delete($table, $whereConditions, $bindParamTypes, $bindParams) {
      $utils = new Utils();

      $sql = "delete from $table";

      if(!$utils->emptyString($whereConditions)) {
        $sql = "delete from $table where $whereConditions";
      }

      $stmt = $this->dbConnection->prepare($sql);

      if(!$utils->emptyString($whereConditions)) {
        $stmt->bind_param($bindParamTypes, ...$bindParams);
      }

      $stmt->execute();
    }

    public function insert($table, $columnsList, $columnsBindParamsTypes, $columnsBindParams) {
      $utils = new Utils();
      $flattenedColumnsList = implode(",", $columnsList);
      $bindParamsPlaceholders = implode(",", $utils->repeat("?", sizeof($columnsList)));

      if(!$utils->emptyString($flattenedColumnsList)) {
        $flattenedColumnsList = "(" . $flattenedColumnsList . ")";
      }

      $sql = "insert into $table $flattenedColumnsList values ($bindParamsPlaceholders)";

      $stmt = $this->dbConnection->prepare($sql);

      $stmt->bind_param($columnsBindParamsTypes, ...$columnsBindParams);

      $stmt->execute();
    }
	}

?>
