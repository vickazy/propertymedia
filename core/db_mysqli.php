<?php 
/**
* @package	  : OOP PHP
* @subpackage : Model db MySqli Object-Oriented
* @author 	  : Musafi'i (musafii.fai@outlook.com)
* @copyright  : 2017
* Default parent Model Class
*/

require_once 'config_database.php';

class Model_mysqli extends Database
{
	private $db;
	private $table;
	private $primary_id = "id";

	function __construct()
	{
		$this->db = parent::mySqli();
	}

	public function setTable($tablName)
	{
		$this->table = $tablName;
	}

	public function getTable()
	{
		return $this->table;
	}

	/**
	* @param $select  = "name, age, address" OR array("name","age","address");
	* @param $where   = array("name" => $name,"age" => $age,"address" => $address);
	* @param $orderBy =	"name ASC, age DESC" OR array("name" => "ASC","age" => "DESC");
	* @param $search  = array("name" => $search,"age",$search);
	* @param $join 	  = array(
	*							array("table[2]","table[2].id = table[1].table[2]_id",["INNER"]),
	*							array("table[3]","table[3].id = table[2].table[3]_id",["LEFT"]),
	*						);
	* @param $limit   = 10;
	* @param $offset  = 25;
	* @param $orderBy =	"name, age" OR array("name","age");
	*/
	public function findData($select=false,$where=false,$orderBy=false,$search=false,$join=false,$limit=false,$offset=false,$groupBy=false,$searchOR=false)
	{
		if ($select) {
			$select = is_array($select) ? implode(", ", $select) : $select;
		} else {
			$select = "*";
		}

		$sql = "SELECT ".$select." FROM ".$this->table." ";

		if ($join) {
			$data = null;
			foreach ($join as $val) {
				// val[0] == table
				// val[1] == field penghubung
				// val[2] == type(INNER or LEFT or RIGHT or FULL)
				$type = isset($val[2]) ? $val[2] : "INNER";
				$data .= $type." JOIN ".$val[0]." ON ".$val[1]." ";
			}
			$sql .= $data;
		}

		if ($where) {
			$sql .= " WHERE ";
			$field = null;
			foreach ($where as $key => $value) {
				$key = explode(" ", $key);
				$key = count($key) > 1 ?  $key[0]." ".($key[1] == "" ? "= " : $key[1]) : $key[0]." = ";
				$value = self::escape_quote($value);
				$field .= " AND ".$key." '".$value."'";
			}
			$sql .= substr($field,4);
		}

		if ($search) {
			$field = null;
			if ($where) {
				$sql .= " AND ( ";
				foreach ($search as $key => $value) {
					$value = self::escape_quote($value);
					$field .= "AND ".$key." LIKE '%".$value."%' ";
				}
				$sql .= substr($field, 4)." ) ";
			} else {
				$sql .= " WHERE ";
				foreach ($search as $key => $value) {
					$value = self::escape_quote($value);
					$field .= "AND ".$key." LIKE '%".$value."%' ";
				}
				$sql .= substr($field, 4);
			}
		}

		if ($searchOR) {
			$field = null;
			if ($where) {
				$sql .= " AND ( ";
				foreach ($searchOR as $key => $value) {
					$value = self::escape_quote($value);
					$field .= "OR ".$key." LIKE '%".$value."%' ";
				}
				$sql .= substr($field, 3)." ) ";
			} else {
				$sql .= " WHERE ";
				foreach ($searchOR as $key => $value) {
					$value = self::escape_quote($value);
					$field .= "OR ".$key." LIKE '%".$value."%' ";
				}
				$sql .= substr($field, 3);
			}
		}

		if ($groupBy) {
			$sql .= " GROUP BY ";
			$field = null;
			if (is_array($groupBy)) {
				foreach ($groupBy as $value) {
					$field .= ", ".$value;
				}
				$sql .= substr($field, 2);
			} else {
				$sql .= $groupBy;
			}
		}

		if ($orderBy) {
			$sql .= " ORDER BY ";
			$field = null;
			if (is_array($orderBy)) {
				foreach ($orderBy as $key => $value) {
					$field .= ", ".$key." ".$value;
				}
				$sql .= substr($field, 2);
			} else {
				$sql .= $orderBy;
			}
		}

		if ($limit) {
			$offset = $offset == true ? " OFFSET ".self::escape_quote($offset) : "";
			$sql .= " LIMIT ".$limit." ".$offset;
		}
		/*return $sql;
		exit();*/

		$result = $this->db->query($sql);
		return $result->fetch_all(MYSQLI_ASSOC);	
	}

	public function getCount($where=false,$search=false,$join=false,$searchOR=false)
	{
		$sql = "SELECT * FROM ".$this->table." ";

		if ($join) {
			$data = null;
			foreach ($join as $val) {
				// val[0] == table
				// val[1] == field penghubung
				// val[2] == type(INNER or LEFT or RIGHT or FULL)
				$type = isset($val[2]) ? $val[2] : "INNER";
				$data .= $type." JOIN ".$val[0]." ON ".$val[1]." ";
			}
			$sql .= $data;
		}

		if ($where) {
			$sql .= " WHERE ";
			$field = null;
			foreach ($where as $key => $value) {
				$key = explode(" ", $key);
				$key = count($key) > 1 ?  $key[0]." ".($key[1] == "" ? "= " : $key[1]) : $key[0]." = ";
				$value = self::escape_quote($value);
				$field .= " AND ".$key." '".$value."'";
			}
			$sql .= substr($field,4);
		}

		if ($search) {
			$field = null;
			if ($where) {
				$sql .= " AND ( ";
				foreach ($search as $key => $value) {
					$value = self::escape_quote($value);
					$field .= "AND ".$key." LIKE '%".$value."%' ";
				}
				$sql .= substr($field, 4)." ) ";
			} else {
				$sql .= " WHERE ";
				foreach ($search as $key => $value) {
					$value = self::escape_quote($value);
					$field .= "AND ".$key." LIKE '%".$value."%' ";
				}
				$sql .= substr($field, 4);
			}
		}

		if ($searchOR) {
			$field = null;
			if ($where) {
				$sql .= " AND ( ";
				foreach ($searchOR as $key => $value) {
					$value = self::escape_quote($value);
					$field .= "OR ".$key." LIKE '%".$value."%' ";
				}
				$sql .= substr($field, 3)." ) ";
			} else {
				$sql .= " WHERE ";
				foreach ($searchOR as $key => $value) {
					$value = self::escape_quote($value);
					$field .= "OR ".$key." LIKE '%".$value."%' ";
				}
				$sql .= substr($field, 3);
			}
		}

		$result = $this->db->query($sql);
		return $result->num_rows;
	}
	
	public function findDataPaging($page,$limit=10,$select=false,$where=false,$orderBy=false,$search=false,$join=false,$searchOR=false)
	{
		$offset = ($page - 1) * $limit;
		$result = self::findData($select,$where,$orderBy,$search,$join,$limit,$offset,false,$searchOR);
		return $result;
	}

	public function getCountPaging($limit=10,$where=false,$search=false,$join=false,$searchOR=false)
	{
		$result = self::getCount($where,$search,$join,$searchOR);
		$result = ceil($result / $limit);
		return $result;
	}

	public function setPrimaryId($id)
	{
		$this->primary_id = $id;
	}

	public function getById($id)
	{
		$id = self::escape_quote($id);
		$sql = "SELECT * FROM ".$this->table." WHERE ".$this->primary_id." = ".$id;
		$result = $this->db->query($sql);
		if ($result) {
			return $result->fetch_assoc();
		} else {
			return false;
		}
	}

	public function getByWhere($where,$or_where=false)
	{
		$sql = "SELECT * FROM ".$this->table." WHERE ";
		$field = null;
		foreach ($where as $key => $value) {
			$key = explode(" ", $key);
			$key = count($key) > 1 ?  $key[0]." ".($key[1] == "" ? "= " : $key[1]) : $key[0]." = ";
			$value = self::escape_quote($value);
			$field .= " AND ".$key." '".$value."'";
		}

		$sql .= substr($field,4);
		if ($or_where) {
			$field_or = null;
			foreach ($or_where as $key => $value) {
				$key = explode(" ", $key);
				$key = count($key) > 1 ?  $key[0]." ".($key[1] == "" ? "= " : $key[1]) : $key[0]." = ";
				$value = self::escape_quote($value);
				$field_or .= " OR ".$key." '".$value."'";
			}
			$sql .= " AND ( ".substr($field_or, 3)." )";
		}
			
		/*return $sql;
		exit();*/

		$result = $this->db->query($sql);
		if ($result) {
			return $result->fetch_assoc();
		} else {
			return false;
		}
	}

	public function insert($data,$table=false)
	{
		$table = $table ? $table : $this->table;
		$sql = "INSERT INTO ".$table." ";
		$fields = null;
		$values = null;
		foreach ($data as $key => $value) {
			$fields .= ", ".$key;
			$value = self::escape_quote($value);
			$values .= ", '".$value."'";
		}
		$sql .= "(".substr($fields, 2).") ";
		$sql .= "VALUES (".substr($values, 2).")";

		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $this->db->insert_id;
	}

	public function escape_quote($value)
	{
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		$value = $this->db->real_escape_string($value);
		return $value;
	}

}

?>