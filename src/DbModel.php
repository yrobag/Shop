<?php



require_once __DIR__.'/config/config.php';

abstract class DbModel
{

	private static $conn = null;

	/**
	 *
	 * @return PDO
	 */
	public static function getConnection()
	{
		if (null === self::$conn)
		{
			self::$conn = new PDO(DB_DSN, DB_USER, DB_PASS);
		}
		return self::$conn;
	}

	protected function getTableName(){
            return static::class;
        }

	public function findById($id)
	{
		$id = (int) $id;
		$table = $this->getTableName();
		$sql = "SELECT * FROM $table WHERE id=$id";
		$result = self::getConnection()->query($sql);
		$data = $result->fetch();
		return $this->fromArray($data);
	}

	public function fromArray($data)
	{
		$model = new static;
		foreach ($data as $name => $value)
		{
			$model->$name = $value;
		}
		return $model;
	}

}