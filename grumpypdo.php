<?php

class GrumpyPdo extends \PDO
{
    /**
     * @var bool
     */
    private $killOnError = false; //if true causes loss of stacktrace but will stop page load. Will still log error code.
    private $echoOnError = false; //if true displays error message to user loading page
    
    /**
     * @var array
     */
    private $opt = array(
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
		PDO::ATTR_DEFAULT_FETCH_MODE => 
		PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false
	);
    
    /**
     * @var string
     */
    private $charset = "utf8";
    
    public function __construct($host, $user, $pass, $db, $opt = array())
    {
        $charset = empty($opt['charset']) ? $this->charset : $opt['charset'];
        $options = empty($opt['options']) ? $this->opt : $opt['options'];
        parent::__construct($this->getDSN($host, $db, $charset), $user, $pass, $options);
    }
    
    public function run($query, $values = array())
    {
        try {
            if (!empty($values)) {
                $stmt = $this->prepare($query);
                $stmt->execute($values);
            } else {
                $stmt = $this->query($query);
            }
        }
        catch (PDOException $e) {
            $this->handleError($e->getMessage());
            throw $e;
        }
        return $stmt;
    }
    
    private function getDSN($host, $db, $charset)
    {
        return "mysql:host={$host};dbname={$db};charset={$charset}";
    }
    
    private function handleError($error)
    {
        error_log($error);
        if ($this->echoOnError)
            echo $error;
        if ($this->killOnError)
            die();
    }
}
