<?php
require_once "setup.php";
require_once "assets/scripts/connect.php";

function parseScript($script) {

  $result = array();
  $delimiter = ';';
  while(strlen($script) && preg_match('/((DELIMITER)[ ]+([^\n\r])|[' . $delimiter . ']|$)/is', $script, $matches, PREG_OFFSET_CAPTURE)) {
    if (count($matches) > 2) {
      $delimiter = $matches[3][0];
      $script = substr($script, $matches[3][1] + 1);
    } else {
      if (strlen($statement = trim(substr($script, 0, $matches[0][1])))) {
        $result[] = $statement;
      }
      $script = substr($script, $matches[0][1] + 1);
    }
  }

  return $result;

}

function executeScriptFile($fileName, $dbConnection) {
  $script = file_get_contents($fileName);
  $statements = parseScript($script);
  foreach($statements as $statement) {
    echo "\nExecuting sql:\n" . $statement;
    mysqli_query($dbConnection, $statement);
    echo "\n" . mysqli_error($dbConnection);
    echo "\n----";
  }
}

echo "Executing database migrations...";

echo "\nDatabase host: " . $_ENV['DB_HOST'];

executeScriptFile(__DIR__.'/schema.sql', $BD_Connection);

echo "\nFinished migrating.";
