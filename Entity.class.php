<?php


/**
 * Class Entity
 */
class Entity
{
    /**
     * @var null
     */
    public $table = null;
    /**
     * @var \mysqli  null
     */
    private $context = null;
    /**
     * @var null
     */
    private $whereQuery = null;
    /**
     * @var array
     */
    private $whereParam = array();
    /**
     * @var int
     */
    private $pos = -1;
    /**
     * @var int
     */
    private $limit = -1;
    /**
     * @var array
     */
    private $select = array('*');
    /**
     * @var null
     */
    private $orderBy = null;

    private $fullQuery = null;

    /**
     * Entity constructor.
     * @param $context
     */
    function __construct(&$context)
    {
        $this->context = $context;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function SetTable($table)
    {
        $this->table = $table;
        return $this;
    }


    /**
     * @param array :string|string $fields
     * @return $this
     */
    public function Select($fields)
    {
        if (!is_array($fields)) {

            $fields = explode(',', $fields);
        }
        $this->select = $fields;
        return $this;
    }

    /**
     * @param string $query
     * @param array $param
     * @return $this
     */
    public function Where($query, $param = array())
    {

        $this->whereQuery = $query;
        $this->whereParam = $param;
        return $this;
    }

    /**
     * @param  int $count
     * @return $this
     * @throws \Exception
     */
    public function Skip($count)
    {

        $this->pos = (is_numeric($count)) ? intval($count) : -1;
        if ($this->pos < 1) {
            throw new \Exception("Skip parameter must be positiv", 1);
        }
        return $this;
    }

    /**
     * @param int $count
     * @return $this
     * @throws \Exception
     */
    public function Take($count)
    {

        $this->limit = (is_numeric($count)) ? intval($count) : -1;
        if ($this->limit < 1) {
            throw new \Exception("Take parameter must be positiv", 1);
        }
        return $this;
    }

    /**
     * @param array :string|string $fields
     * @return $this
     */
    public function OrderBy($fields)
    {

        if (!is_array($fields)) {

            $fields = explode(',', $fields);
        }
        $this->orderBy = $fields;
        return $this;
    }

    /**
     * @return object
     */
    public function First()
    {
        $this->limit = 1;
        $this->generateQuery();
        return $this->EXECUTE()[0];
    }

    /**
     * @return array:object
     */
    public function ToArray()
    {
        $this->generateQuery();
        return $this->EXECUTE();
    }

    /**
     * @param string $query
     * @param array $param
     * @return array : object
     */
    public function ExecuteQuery($query, $param = array())
    {
        $this->fullQuery = $query;
        $this->whereParam = $param;
        return $this->EXECUTE();
    }

    /**
     *
     */
    private function generateQuery()
    {
        $query = 'Select ';
        $query .= implode(", ", $this->select);
        $query .= " From " . $this->table;

        if (isset($this->whereQuery)) {
            $query .= " Where " . $this->whereQuery;
        }

        if (isset($this->orderBy)) {
            $query .= " Order By  " . implode(", ", $this->orderBy);

        }
        if ($this->pos > 0 && $this->limit > 0) {
            $query .= " Limit " . ($this->pos-1) . "," . $this->limit;
        } elseif ($this->limit > 0) {
            $query .= " Limit " . $this->limit;
        }

        $this->fullQuery = $query;

    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function EXECUTE()
    {

        $whereType = "";
        $newWhereParam = array();
        if (count($this->whereParam) > 0) {
            $newWhereParam = $this->whereParam;
            $this->parseNamedParams($this->fullQuery, $newWhereParam, $whereType);
        }


        $stmt = $this->context->prepare($this->fullQuery);
        if ($stmt == false) {
            throw new \Exception($this->context->error, 1);
        }
        if ($whereType != "") {
            //    var_dump($query,array_merge(array($whereType), $newWhereParam));
            call_user_func_array(array($stmt, "bind_param"), $this->refValues(array_merge(array($whereType), $newWhereParam)));
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if($result != false)
            $result = $result->fetch_all(MYSQLI_ASSOC);
        else
            $result = true;
        $stmt->close();
        return $result;

    }

    /**
     * Provides named parameter support for mysqli. Rewrites a PDO-like query string and parameter array to work with mysqli.
     *
     * @param string $queryStr
     * @param array $params
     * @param string $type
     */
    private function parseNamedParams(&$queryStr, &$params, &$type)
    {
        $array = array();
        $types = "";
        if ($c = preg_match_all('/(:\w+)/is', $queryStr, $matches)) { // To match words starting with colon
            $list = $matches[0]; // $matches is two-dimensional array, we only need first element

            foreach ($list as $value) { // We'll replace each parameter in the query string with a '?' (as to comply with mysqli), and make sure the value is in the correct order.
                $queryStr = str_replace($value, '?', $queryStr);
                $key = trim($value, ":");
                $array[] = $params[$key];
                $types .= (gettype($params[$key]) == 'integer') ? 'i' : 's';
            }
        }
        $params = $array;
        $type = $types;
    }

    /**
     * @param $arr
     * @return array
     */
    function refValues($arr)
    {
        if (strnatcmp(phpversion(), '5.3') >= 0) //Reference is required for PHP 5.3+
        {
            $refs = array();
            foreach ($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }

}
