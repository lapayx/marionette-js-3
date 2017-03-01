<?php


/**
 * @method Entity
 */
class DataBase
{

    /**
     * @var null
     */
    private $context = null;

    /**
     * DataBase constructor.
     * @param $context
     */
    function __construct(&$context)
    {
        $this->context = $context;

    }

    /**
     * @param string $table
     * @param $arguments
     * @return Entity
     */
    public function __call($table, $arguments)
    {
        $var = new Entity($this->context);
        $var->SetTable($table);
        return $var;

    }


    /**
     * @param string $query
     * @param array $param
     * @return array : object
     */
    public function ExercuteQuery($query, $param = array())
    {
        $var = new Entity($this->context);

        return $var->ExecuteQuery($query,$param);

    }


}