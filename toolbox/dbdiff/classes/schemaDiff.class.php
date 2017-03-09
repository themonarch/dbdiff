<?php namespace toolbox;
/*
    schema-diff
    (c)2009-2010 Michał Kierat (kierate@gmail.com)
    This program is free software: you can redistribute it and/or modify
    it under the terms of version 3 of the GNU General Public License as
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
#################################################
# Options defaults
#################################################
/*$in_file1			= null;
$in_file2			= null;
$out_file			= null;
//for the moment just use text files, as specifying db params for two dbs will be a pain
//$input_mode		= 'files';//files|dbs
//$db1_name     = $db2_name     = null;
//$db1_host     = $db2_host     = 'localhost';
//$db1_user     = $db2_user     = 'root';
//$db1_port     = $db2_port     = false;
//$db1_use_pass = $db2_use_pass = false;
$options = array(
				'skip_fk_changes'	=> false,
				'skip_create_table'	=> false,
				'skip_drop_table'	=> false,
				'allow_renames'		=> true,
				);
#################################################
# Parse the options
#################################################
require_once 'Console/Getopt.php';
$con  = new Console_Getopt;
$args = $con->readPHPArgv();
array_shift($args);
$long_options = array(
					'help',
					's1', 'schema1',	//same as -1
					's2', 'schema2',	//same as -2
					'output',			//same as -o
					'skip-fk-changes',	//same as -f
					'skip-renames',		//same as -r
					'skip-create-table',//same as -c
					'skip-drop-table',	//same as -d
					'skip-full-tables', //same as both 'skip-create-table' and 'skip-drop-table' together
					);
$opts = $con->getopt2($args, 'h1:2:o:fcdtr', $long_options);
if (PEAR::isError($opts)) {
	showHelp(true);
} else {
	if(count($opts[0]) == 0) {
		//passed in just regular arguments
		$in_file1 = $opts[1][0];
		$in_file2 = $opts[1][1];
		if(isset($opts[1][2])) {
			$out_file = $opts[1][2];
		}
	} else {
		//getopt params
		foreach($opts[0] as $opt){
			switch($opt[0]) {
				case 'h';
				case '--help';
					showHelp();
					break;

				case '1':
				case '--s1':
				case '--schema1':
					$in_file1 = $opt[1];
					break;

				case '2':
				case '--s2':
				case '--schema2':
					$in_file2 = $opt[1];
					break;

				case 'o':
				case '--output':
					$out_file = $opt[1];
					break;

				case 'f':
				case '--skip-fk-changes':
					$options['skip_fk_changes'] = true;
					break;

				case 'c':
				case '--skip-create-table':
					$options['skip_create_table'] = true;
					break;

				case 'd':
				case '--skip-drop-table':
					$options['skip_drop_table'] = true;
					break;

				case 't':
				case '--skip-full-tables':
					$options['skip_create_table'] = true;
					$options['skip_drop_table'] = true;
					break;

				case 'r':
				case '--skip-renames':
					$options['allow_renames'] = false;
					break;
			}
		}
	}
}
#################################################
# Read files
#################################################
$schema1 = @file_get_contents($in_file1);
$schema2 = @file_get_contents($in_file2);
if (empty($schema1) || empty($schema2)) {
	showHelp(true);
	exit;
}
#################################################
# Get the diff
#################################################
$db1 = new Database($schema1, 1);
$db2 = new Database($schema2, 2);
$diff = $db1->getDiff($db2, $options);
#################################################
# Output Contents
#################################################
if (isset($out_file)) {
	file_put_contents($out_file, $diff);
} else {
	echo $diff;
}
exit; //end of script
#################################################
# Help
#################################################
function showHelp($err = false,$out=null) {
	if (empty($out)) {
		$out = "SCHEMA DIFF\n-----------\n";
		$out.= "This is a PHP command line utility that produces SQL differences between two MySQL database schemas, ";
		$out.= "so that applying the diff to the first database produces the second database.\n";

		if ($err) {
			$out = "Incorrect arguments passed\n\n".$out;
		}
		$out.= "\nUsage variant 1:\n";
		$out.= "  ".basename(__FILE__)." schema1 schema2 [outfile] \n";
		$out.= "  This variant is used to specify the schema files for DB1 and DB2 along ";
		$out.= "with an optional output file\n";

		$out.= "\nUsage variant 2:\n";
		$out.= "  ".basename(__FILE__)." [options] \n";
		$out.= "  This variant allows specifying additional options\n";

		$out.= "  Available options:\n";
		$out.= "  -1, --s1, --schema1 [filename] - Read first schema from file\n";
		$out.= "  -2, --s2, --schema2 [filename] - Read second schema from file\n";
		$out.= "  -o, --output [filename]        - Output to file (optional, output is normaly displayed)\n";
		$out.= "  -f, --skip-fk-changes          - Skip any foreign key changes\n";
		$out.= "  -c, --skip-create-table        - Don't check if tables are missing\n";
		$out.= "  -d, --skip-drop-table          - Don't check there are too many tables\n";
		$out.= "  -t, --skip-full-tables         - Don't show CREATE/DROP for full tables (the above two together)\n";
		$out.= "  -r, --skip-renames             - Don't allow renaming columns\n";
		$out.= "  -h, --help                     - Show this help\n";
	}

	if ($err) {
		fputs(STDERR, $out);
		exit(1);
	} else {
		echo $out;
		exit;
	}
}*/

class schemaDiff {

	static function getDiff($schema1, $schema2){
		$db1 = new Database($schema1, 1);
		$db2 = new Database($schema2, 2);
		return $db1->getDiff($db2, array());
	}



}
#################################################
# DB Schema Parser
#################################################
class Database
{
	private $tables = array();

	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//Modifying this array will significantly affect Column::parseDataType()
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	public static $data_types = array(
		'(BIT)(\s*\(\s*(\d+)\s*\))?', //BIT[(length)]
		'(TINYINT)(\s*\(\s*(\d+)\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //TINYINT[(length)] [UNSIGNED] [ZEROFILL]
		'(SMALLINT)(\s*\(\s*(\d+)\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //SMALLINT[(length)] [UNSIGNED] [ZEROFILL]
		'(MEDIUMINT)(\s*\(\s*(\d+)\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //MEDIUMINT[(length)] [UNSIGNED] [ZEROFILL]
		'(INT)(\s*\(\s*(\d+)\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //INT[(length)] [UNSIGNED] [ZEROFILL]
		'(INTEGER)(\s*\(\s*(\d+)\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //INTEGER[(length)] [UNSIGNED] [ZEROFILL]
		'(BIGINT)(\s*\(\s*(\d+)\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //BIGINT[(length)] [UNSIGNED] [ZEROFILL]
		'(REAL)(\s*\(\s*(\d+)\s*,\s*(\d+)\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //REAL[(length,decimals)] [UNSIGNED] [ZEROFILL]
		'(DOUBLE)(\s*\(\s*(\d+)\s*,\s*(\d+)\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //DOUBLE[(length,decimals)] [UNSIGNED] [ZEROFILL]
		'(FLOAT)(\s*\(\s*(\d+)\s*,\s*(\d+)\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //FLOAT[(length,decimals)] [UNSIGNED] [ZEROFILL]
		'(DECIMAL)(\s*\(\s*(\d+)(\s*,\s*(\d+))?\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //DECIMAL[(length[,decimals])] [UNSIGNED] [ZEROFILL]
		'(NUMERIC)(\s*\(\s*(\d+)(\s*,\s*(\d+))?\s*\))?(\s*UNSIGNED)?(\s*ZEROFILL)?', //NUMERIC[(length[,decimals])] [UNSIGNED] [ZEROFILL]
		'(DATE)', //DATE
		'(TIME)', //TIME
		'(TIMESTAMP)', //TIMESTAMP
		'(DATETIME)', //DATETIME
		'(YEAR)',//'(YEAR)(\s*\(\s*([2|4]+)\s*\))?', //YEAR[(length)] - length can be 2 or 4 only
		'(CHAR)(\s*\(\s*(\d+)\s*\))?(\s*CHARACTER SET\s*(.*?))?(\s*COLLATE\s*(.*?))?', //CHAR[(length)] [CHARACTER SET charset_name] [COLLATE collation_name]
		'(VARCHAR)\s*\(\s*(\d+)\s*\)(\s*CHARACTER SET\s*(.*?))?(\s*COLLATE\s*(.*?))?', //VARCHAR[(length)] [CHARACTER SET charset_name] [COLLATE collation_name]
		'(BINARY)(\s*\(\s*(\d+)\s*\))?', //BINARY[(length)]
		'(VARBINARY)\s*\(\s*(\d+)\s*\)', //VARBINARY(length)
		'(TINYBLOB)', //TINYBLOB
		'(BLOB)', //BLOB
		'(MEDIUMBLOB)', //MEDIUMBLOB
		'(LONGBLOB)', //LONGBLOB
		'(TINYTEXT)(\s*BINARY)?(\s*CHARACTER SET\s*(.*?))?(\s*COLLATE\s*(.*?))?', //TINYTEXT [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
		'(TEXT)(\s*BINARY)?(\s*CHARACTER SET\s*(.*?))?(\s*COLLATE\s*(.*?))?', //TEXT [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
		'(MEDIUMTEXT)(\s*BINARY)?(\s*CHARACTER SET\s*(.*?))?(\s*COLLATE\s*(.*?))?', //MEDIUMTEXT [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
		'(LONGTEXT)(\s*BINARY)?(\s*CHARACTER SET\s*(.*?))?(\s*COLLATE\s*(.*?))?', //LONGTEXT [BINARY] [CHARACTER SET charset_name] [COLLATE collation_name]
		'(ENUM)\s*\((.*)\)(\s*CHARACTER SET\s*(.*?))?(\s*COLLATE\s*(.*?))?', //ENUM(value1,value2,value3,...) [CHARACTER SET charset_name] [COLLATE collation_name]
		'(SET)\s*\((.*)\)(\s*CHARACTER SET\s*(.*?))?(\s*COLLATE\s*(.*?))?', //SET(value1,value2,value3,...) [CHARACTER SET charset_name] [COLLATE collation_name]
	);

	private $all_data_types = null;
	/**
	 * Create a db based on the schema
	 *
	 * @param string $schema schema content
	 */
	public function __construct($schema, $schema_file_number)
	{
		$this->all_data_types = implode("|", self::$data_types);

		$this->parseSchema($schema, $schema_file_number);
	}

	/**
	 * Parse the schema and fill in the db object
	 *
	 * @param string $schema schema content
	 */
	public function parseSchema($schema, $schema_file_number)
	{
		//unify line endings
		$schema = preg_replace("/\r\n|\r/", "\n", $schema);

		$schema_lines = explode("\n", $schema);
		$current_table = null;
		$current_alter_table = null;
		foreach ($schema_lines as $number => $line) {
			$line = trim($line);
			//ignore lines
			if (empty($line) || //empty
				substr($line, 0, 2) == '--' || //comment
				preg_match('@^\/\*\!(.*)\*\/;$@', $line) //mysqldump type comments - e.g. /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
				) {
				continue;
			}

			//drop table
			if (preg_match('/^DROP TABLE IF EXISTS\s*(`.*`)\s*;\s*$/i', $line, $m)) {
				$table_name = trim($m[1], '`');
				$current_table = $table_name;

				if(!isset($this->tables[$table_name])) {
					$this->tables[$table_name] = new Table($table_name);
				}

				$this->tables[$table_name]->setShowDrop(true);
				continue;
			}

			//create table
			if (preg_match('/^CREATE TABLE\s*(IF NOT EXISTS)?\s*(`.*`)\s*\(\s*$/i', $line, $m)) {
				$table_name = trim($m[2], '`');
				$current_table = $table_name;

				if(!isset($this->tables[$table_name])) {
					$this->tables[$table_name] = new Table($table_name);
				}
				continue;
			}

			//primary key
			if (preg_match('/^\s*PRIMARY KEY\s*\(((`.*?`)+(,\s*(`.*`))*?\))\s*(,)?\s*$/i', $line, $m)) {
				//pks will be at index 2, 4, 6 and so on
				for($i = 2; $i < count($m); $i += 2) {
					if(strlen(trim($m[$i])) > 0) {
						$this->tables[$current_table]->addPrimaryKey($m[$i]);
					}
				}
				continue;
			}

			//engine/charset
			if (preg_match('/^\)\s*.*ENGINE\s*=\s*(\S*).*DEFAULT CHARSET\s*=\s*(\S*);$/i', $line, $m)) {
				$this->tables[$current_table]->setEngine($m[1]);
				$this->tables[$current_table]->setDefaultCharset($m[2]);
				continue;
			}

			//keys
			if (preg_match('/^\s*KEY\s*(`.*`) \((`.*`)+(,\s*(`.*`))*?\)\s*(,)?\s*$/i', $line, $m)) {
				$name = $m[1];
				$columns = array();
				for($i = 2; $i < count($m); $i += 2) {
					if(strlen(trim($m[$i])) > 0) {
						$columns[] = $m[$i];
					}
				}
				$this->tables[$current_table]->addKey($name, $columns);
				continue;
			}

			//unique keys
			if (preg_match('/^\s*UNIQUE KEY\s*(`.*`) \((`.*?`)+(,\s*(`.*`))*?\)\s*(,)?\s*$/i', $line, $m)) {
				$name = $m[1];
				$columns = array();
				for($i = 2; $i < count($m); $i += 2) {
					if(strlen(trim($m[$i])) > 0) {
						$columns[] = $m[$i];
					}
				}
				$this->tables[$current_table]->addUniqueKey($name, $columns);
				continue;
			}

			//foreign keys
			if (preg_match('/^\s*CONSTRAINT\s*(`.*`)\s*FOREIGN KEY\s*\((`.*`)\) REFERENCES (`.*`) \((`.*`)\)( ON (DELETE|UPDATE) (RESTRICT|CASCADE|SET NULL|NO ACTION))?( ON (UPDATE|DELETE) (RESTRICT|CASCADE|SET NULL|NO ACTION))?\,?$/i', $line, $m)) {
				$constraint_name = $m[1];
				$foreign_key_name = $m[2];
				$ref_table = $m[3];
				$ref_column = $m[4];
				$on_update = null;
				$on_delete = null;

				//get the on-update and on-delete values
				for($i = 5; $i < count($m); $i++) {
					if (preg_match('/^\s*ON UPDATE (.*)\s*$/', $m[$i], $update_match)) {
						$on_update = $update_match[1];
					} elseif(preg_match('/^\s*ON DELETE (.*)\s*$/', $m[$i], $delete_match)) {
						$on_delete = $delete_match[1];
					}
				}

				$this->tables[$current_table]->addForeignKey($foreign_key_name, $ref_table, $ref_column, $on_update, $on_delete, $constraint_name);
				continue;
			}

			//begin table alterations - specify table name here and the actual changes in separte lines
			if (preg_match('/^ALTER TABLE\s*(`.*?`)\s*$/i', $line, $m)) {
				$current_alter_table = trim($m[1], '`');
				continue;
			}

			//handle basic table alterations
			if (preg_match('/^(ALTER TABLE\s*`(.*?)`\s*)?\s*(ADD|CHANGE)\s*`(.*?)`\s*(' . $this->all_data_types. ')\s*(NOT NULL)?\s*(default (.*?))?(auto_increment)?(,|;)?\s*$/i', $line, $m)) {
				foreach($m as $key => $value) {
					if(strlen($value) == 0) {
						unset($m[$key]);
					}
				}
				//if the table to alter is unknown just skip this line
				if(!isset($current_alter_table) && !isset($m[2])) {
					continue;
				}

				$table_to_alter = isset($m[2]) ? $m[2] : $current_alter_table;

				//if the table to alter doesn't exist dont worry about this
				if(isset($this->tables[$table_to_alter])) {
					$column_name = $m[4];
					$data_type = $m[5]; //entire data type (e.g. INT UNSIGEND ZEROFILL or VARCHAR(255) CHARACTER SET utf8 etc.)

					$auto_increment = isset($m[137]) ? true : false; //auto_increment
					$default = isset($m[136]) ? str_replace("\\", "\\\\", $m[136]) : null; //default value
					$is_null = isset($m[134]) ? false : true; //NOT NULL

					$this->tables[$table_to_alter]->addColumn($column_name, $data_type, $is_null, $default, $auto_increment);
				}
				continue;
			}

			//alter-add-index
			if (preg_match('/^(ALTER TABLE\s*`(.*?)`\s*)?\s*ADD\s*INDEX\s*\(\s*(`.*?`)+(,\s*(`.*?`))*?\s*\)\s*(,|;)?\s*$/i', $line, $m)) {
				//if the table to alter is unknown just skip this line
				if(!isset($current_alter_table) && !isset($m[2])) {
					continue;
				}

				$table_to_alter = isset($m[2]) ? $m[2] : $current_alter_table;

				//if the table to alter doesn't exist dont worry about this
				if(isset($this->tables[$table_to_alter])) {
					$name = $m[3];
					$columns = array();
					for($i = 3; $i < count($m)-1; $i += 2) { //3, 5, 7 etc. minus the last one (comma/semicolon)
						if(strlen(trim($m[$i])) > 0) {
							$columns[] = $m[$i];
						}
					}

					$this->tables[$table_to_alter]->addKey($name, $columns);
				}
				continue;
			}

			//alter-add-unique
			if (preg_match('/^(ALTER TABLE\s*`(.*?)`\s*)?\s*ADD\s*UNIQUE\s*\(\s*(`.*?`)+(,\s*(`.*?`))*?\s*\)\s*(,|;)?\s*$/i', $line, $m)) {
				//if the table to alter is unknown just skip this line
				if(!isset($current_alter_table) && !isset($m[2])) {
					continue;
				}

				$table_to_alter = isset($m[2]) ? $m[2] : $current_alter_table;

				//if the table to alter doesn't exist dont worry about this
				if(isset($this->tables[$table_to_alter])) {
					$name = $m[3];
					$columns = array();
					for($i = 3; $i < count($m)-1; $i += 2) { //3, 5, 7 etc. minus the last one (comma/semicolon)
						if(strlen(trim($m[$i])) > 0) {
							$columns[] = $m[$i];
						}
					}

					$this->tables[$table_to_alter]->addKey($name, $columns);
				}
				continue;
			}

			//alter-add-primary key
			if (preg_match('/^(ALTER TABLE\s*`(.*?)`\s*)?\s*ADD\s*PRIMARY\s*KEY\s*\(\s*(`.*?`)+(,\s*(`.*?`))*?\s*\)\s*(,|;)?\s*$/i', $line, $m)) {
				//if the table to alter is unknown just skip this line
				if(!isset($current_alter_table) && !isset($m[2])) {
					continue;
				}

				$table_to_alter = isset($m[2]) ? $m[2] : $current_alter_table;

				//if the table to alter doesn't exist dont worry about this
				if(isset($this->tables[$table_to_alter])) {

					//pks will be at index 3, 5, 7 and so on
					for($i = 3; $i < count($m); $i += 2) {
						if(strlen(trim($m[$i])) > 0) {
							$this->tables[$current_table]->addPrimaryKey($m[$i]);
						}
					}
				}
				continue;
			}
			//alter-add-foreign key
			if (preg_match('/^(ALTER TABLE\s*`(.*?)`\s*)?\s*ADD\s*FOREIGN\s*KEY\s*\(\s*(`.*`)\s*\) REFERENCES ((`.*?`\.)?`.*?`) \(\s*(`.*`)\s*\)( ON (DELETE|UPDATE) (RESTRICT|CASCADE|SET NULL|NO ACTION))?( ON (UPDATE|DELETE) (RESTRICT|CASCADE|SET NULL|NO ACTION))?\s*(,|;)?\s*$/i', $line, $m)) {
				//if the table to alter is unknown just skip this line
				if(!isset($current_alter_table) && !isset($m[2])) {
					continue;
				}

				$table_to_alter = isset($m[2]) ? $m[2] : $current_alter_table;

				//if the table to alter doesn't exist dont worry about this
				if(isset($this->tables[$table_to_alter])) {
					$foreign_key_name = $m[3];
					$ref_table = $m[4];
					$ref_column = $m[6];
					$on_update = null;
					$on_delete = null;

					//get the on-update and on-delete values
					for($i = 7; $i < count($m); $i++) {
						if (preg_match('/^\s*ON UPDATE (.*)\s*$/', $m[$i], $update_match)) {
							$on_update = $update_match[1];
						} elseif(preg_match('/^\s*ON DELETE (.*)\s*$/', $m[$i], $delete_match)) {
							$on_delete = $delete_match[1];
						}
					}

					$this->tables[$table_to_alter]->addForeignKey($foreign_key_name, $ref_table, $ref_column, $on_update, $on_delete);
				}
				continue;
			}

			//columns
			if (preg_match('/^\s*((`.*`)|(.* ))\s*(' . $this->all_data_types. ')\s*(NOT NULL|NULL)?\s*(default (.*?))?(auto_increment)?(,)?\s*$/i', $line, $m)) {
				foreach($m as $key => $value) {
					if(strlen($value) == 0) {
						unset($m[$key]);
					}
				}

				$column_name = trim($m[1], '`');
				$data_type = $m[4]; //entire data type (e.g. INT UNSIGEND ZEROFILL or VARCHAR(255) CHARACTER SET utf8 etc.)

				$auto_increment = isset($m[136]) ? true : false; //auto_increment
				$default = isset($m[135]) ? str_replace("\\", "\\\\", $m[135]) : null; //default value
				$is_null = isset($m[133]) && $m[133] == 'NOT NULL' ? false : true; //NOT NULL

				$this->tables[$current_table]->addColumn($column_name, $data_type, $is_null, $default, $auto_increment);
				continue;
			}
			continue;
			//if we're here then the line cannot be parsed
			throw new softPublicException("!!! Unrecognised line format in schema $schema_file_number on line " . ($number+1) . ": '$line'\n");

		}
	}

	/**
	 * Get the diff between two tables.
	 * $this is the old database. $other_db is the new database.
	 *
	 * The diff will be applied to the old db in order to produce new db
	 *
	 * @param Database $other_db new database
	 * @param array $options options to control the diff that gets returned
	 * @return string diff
	 */
	public function getDiff(Database $other_db, $options = array())
	{
		$diff_sql = '';

		//if old db has table but the new one doesn't - drop
		if(!isset($options['skip_drop_table']) || $options['skip_drop_table'] == false) {
			foreach(array_keys($this->tables) as $table_name) {
				if(!isset($other_db->tables[$table_name])) {
					$diff_sql .= "DROP TABLE `$table_name`;\n";
				}
			}
		}

		//if new db has table but old one doesn't - create
		if(!isset($options['skip_create_table']) || $options['skip_create_table'] == false) {
			foreach(array_keys($other_db->tables) as $table_name) {
				if(!isset($this->tables[$table_name])) {
					$diff_sql .= $other_db->tables[$table_name]->getCreateStatement() . "\n";
				}
			}
		}

		//get differences between tables that exist in both dbs
		foreach($this->tables as $table_name => $table) {
			if(isset($other_db->tables[$table_name])) {
				$diff = $this->tables[$table_name]->getDiff($other_db->tables[$table_name], $options);
				if(isset($diff)) {
					$diff_sql .= "$diff\n";
				}
			}
		}

		return (strlen($diff_sql) > 0) ? $diff_sql : null;
	}
}
class Table
{
	private $name = null;
	private $columns = array();
	private $engine = null; //MyISAM, InnoDB etc.
	private $default_charset = null;
	private $show_drop = false;

	private $primary_key = array(); //array is for compisite keys
	private $unique_keys = array();
	private $keys = array();
	private $constraints =  array(); //for the mo only store foreign keys here

	public function __construct($table_name)
	{
		$this->name = $table_name;
	}

	public function setShowDrop($show_drop)
	{
		$this->show_drop = $show_drop;
	}

	public function addPrimaryKey($pri_key)
	{
		$this->primary_key[] = $pri_key;
	}

	public function setEngine($engine)
	{
		$this->engine = $engine;
	}

	public function setDefaultCharset($default_charset)
	{
		$this->default_charset = $default_charset;
	}

	public function addKey($name, $column_names)
	{
		$this->keys[$name] = $column_names;
	}

	public function addUniqueKey($name, $column_names)
	{
		$this->unique_keys[$name] = $column_names;
	}

	public function addForeignKey($fk_name, $ref_table, $ref_column, $on_delete, $on_update, $constraint_name = null)
	{
		if(!isset($constraint_name)) {
			$constraint_name = $this->getNextConstraintName();
		}

		//only try to add fk if the constraint is known
		if(isset($constraint_name)) {
			$this->constraints[$constraint_name] = new ForeignKey($fk_name, $ref_table, $ref_column, $on_delete, $on_update);
		}
	}

	public function addColumn($column_name, $data_type, $is_null, $default, $auto_increment)
	{
		$this->columns[$column_name] = new Column($column_name, $data_type, $is_null, $default, $auto_increment);
	}

	/**
	 * Get a diff between two tables. This table is the old one and the
	 * function will produce sql needed to modify the old table to have
	 * the same content as the new table
	 *
	 * @param Table $other_table new table
	 * @param array $options
	 * @return string diff
	 */
	public function getDiff(Table $other_table, $options = array(
				'skip_fk_changes'	=> false,
				'skip_create_table'	=> false,
				'skip_drop_table'	=> false,
				'allow_renames'		=> true,
				))
	{
		$alter_sql = '';

		$renamed = array();

		//if old table has column but the new one doesn't - drop/rename
		foreach($this->columns as $column_name => $column) {
			if(!isset($other_table->columns[$column_name])) {
				$do_drop = true;

				//if renames are allowed
				if(isset($options['allow_renames']) && $options['allow_renames'] == true) {
					//A rename will only apply to a column that resides between two same columns in both dbs
					//AND has the exact same data type definition.
					//Everything else is considered a different field, so add/drop will be used.
					$pos_data = $this->getColumnPositionData($column_name);
					$column_other_db = $other_table->getColumnNameAtPosition($pos_data);

					if(isset($column_other_db) && $column->getDataType() == $other_table->columns[$column_other_db]->getDataType()) {
						$renamed[$column_other_db] = true;
						$do_drop = false;
						$alter_sql .= "ALTER TABLE `$this->name` CHANGE `$column_name` " . $other_table->columns[$column_other_db]->getColumnDefinition() . ";\n";
					}
				}

				if($do_drop) {
					$alter_sql .= "ALTER TABLE `$this->name` DROP `$column_name`;\n";
				}
			}
		}

		//if new table has column but old one doesn't - create
		foreach($other_table->columns as $column_name => $column) {
			if(!isset($this->columns[$column_name]) && !isset($renamed[$column_name])) {
				$alter_sql .= "ALTER TABLE `$this->name` ADD " . $column->getColumnDefinition() . $other_table->getColumnPosition($column_name) . ";\n";
				if(!$column->isNull()) {
					$alter_sql .= "--\n";
					$alter_sql .= "-- The statement above adds a NOT NULL column.\n";
					$alter_sql .= "-- To add a foreign key on this column run the statement below (adjust first):\n";
					$alter_sql .= "-- UPDATE `$this->name` SET `$column_name` = {VALID_FK_VALUE}\n";
					$alter_sql .= "--\n";
				}
			}
		}

		//get differences between columns that exist in both tables
		foreach($this->columns as $column_name => $column) {
			if(isset($other_table->columns[$column_name])) {
				$diff = $column->getDiff($other_table->columns[$column_name]);
				if(isset($diff)) {
					$alter_sql .= "ALTER TABLE `$this->name` $diff;\n";
				}
			}
		}

		if($this->engine != $other_table->engine) {
			$alter_sql .= "ALTER TABLE `$this->name` ENGINE = " . $other_table->engine . ";\n";
		}
		if($this->default_charset != $other_table->default_charset) {
			$alter_sql .= "ALTER TABLE `$this->name` DEFAULT CHARACTER SET " . $other_table->default_charset . ";\n";
		}
		//compare primary key
		if($this->primary_key != $other_table->primary_key) {
			$alter_sql .= "ALTER TABLE `$this->name` DROP PRIMARY KEY, ADD PRIMARY KEY(" . implode(", ", $other_table->primary_key) . ");\n";
		}

		//compare unique keys
		//remove keys that no longer exist in new table
		foreach($this->unique_keys as $key_name => $columns) {
			if(!isset($other_table->unique_keys[$key_name])) {
				$alter_sql .= "ALTER TABLE `$this->name` DROP INDEX $key_name;\n";
			}
		}
		//add keys that exist in new table, but not in the old
		foreach($other_table->unique_keys as $key_name => $columns) {
			if(!isset($this->unique_keys[$key_name])) {
				$alter_sql .= "ALTER TABLE `$this->name` ADD UNIQUE (" . implode(', ', $columns) . ");\n";
			}
		}
		//modify keys that exist in both tables but have changed
		foreach($this->unique_keys as $key_name => $columns) {
			if(isset($other_table->unique_keys[$key_name])) {
				if($columns != $other_table->unique_keys[$key_name]) {
					$alter_sql .= "ALTER TABLE `$this->name` DROP INDEX $key_name;\n";
					$alter_sql .= "ALTER TABLE `$this->name` ADD UNIQUE (" . implode(', ', $other_table->unique_keys[$key_name]) . ");\n";
				}
			}
		}


		//compare keys
		//remove keys that no longer exist in new table
		foreach($this->keys as $key_name => $columns) {
			if(!isset($other_table->keys[$key_name])) {
				//TODO cannot drop an index for a column that is used as a foreign key - needs to drop forign key first?
				$alter_sql .= "ALTER TABLE `$this->name` DROP INDEX $key_name;\n";
			}
		}
		//add keys that exist in new table, but not in the old
		foreach($other_table->keys as $key_name => $columns) {
			if(!isset($this->keys[$key_name])) {
				$alter_sql .= "ALTER TABLE `$this->name` ADD INDEX (" . implode(', ', $columns) . ");\n";
			}
		}
		//modify keys that exist in both tables but have changed
		foreach($this->keys as $key_name => $columns) {
			if(isset($other_table->keys[$key_name])) {
				if($columns != $other_table->keys[$key_name]) {
					$alter_sql .= "ALTER TABLE `$this->name` DROP INDEX $key_name;\n";
					$alter_sql .= "ALTER TABLE `$this->name` ADD INDEX (" . implode(', ', $other_table->keys[$key_name]) . ");\n";
				}
			}
		}

		//compare foreign keys
		if(!isset($options['skip_fk_changes']) || $options['skip_fk_changes'] == false) {
			//remove keys that no longer exist in new table
			foreach($this->constraints as $constraint_name => $constraint) {
				if($constraint instanceof ForeignKey) {
					if(!$other_table->constraintExists($constraint)) { //don't just check the constraint name as when altering table that name is auto-generated
						$alter_sql .= "ALTER TABLE `$this->name` DROP FOREIGN KEY $constraint_name;\n";
					}
				}
			}
			//add keys that exist in new table, but not in the old
			foreach($other_table->constraints as $constraint_name => $constraint) {
				if($constraint instanceof ForeignKey) {
					if(!$this->constraintExists($constraint)) {
						$alter_sql .= "ALTER TABLE `$this->name` ADD FOREIGN KEY " . $constraint->getKeyDefinition() . ";\n";
					}
				}
			}
			//modify keys that exist in both tables but have changed
			foreach($this->constraints as $constraint_name => $constraint) {
				if($constraint instanceof ForeignKey) {
					if($other_table->constraintExists($constraint) && isset($other_table->constraints[$constraint_name])) {
						if($constraint != $other_table->constraints[$constraint_name]) {
							$alter_sql .= "ALTER TABLE `$this->name` DROP FOREIGN KEY $constraint_name;\n";
							$alter_sql .= "ALTER TABLE `$this->name` ADD FOREIGN KEY " . $other_table->constraints[$constraint_name]->getKeyDefinition() . ";\n";
						}
					}
				}
			}
		}

		return (strlen($alter_sql) > 0) ? $alter_sql : null;
	}

	/**
	 * Get the column position. Used when specifying where a column should be added
	 *
	 * @param string $column_name
	 * @return string
	 */
	public function getColumnPosition($column_name)
	{
		$pos_data = $this->getColumnPositionData($column_name);

		if(!isset($pos_data['after'])) {
			return ' FIRST';
		} else {
			if(!isset($pos_data['before'])) {
				return ''; //last
			} else {
				return ' AFTER `' . $pos_data['after'] . '`'; //somewhere in the middle
			}
		}
	}

	/**
	 * Get some data about the columns position in the table. Specifically what
	 * are the neighbouring columns and whats the index of the column (in the
	 * internal array columns array).
	 *
	 * @param string $column_name
	 * @return array
	 */
	public function getColumnPositionData($column_name)
	{
		$columns = array_keys($this->columns);
		$columns_flipped = array_flip($columns);

		$current_pos = $columns_flipped[$column_name];

		$data = array(
					'position' => $current_pos,
					'after' => null,
					'before' => null,
					);

		if(isset($columns[$current_pos+1])) {
			$data['before'] = $columns[$current_pos+1];
		}
		if(isset($columns[$current_pos-1])) {
			$data['after'] = $columns[$current_pos-1];
		}

		return $data;
	}

	/**
	 * Try and find a name of a column based on the specified postion data.
	 * This is used to figure out if a column has been renamed.
	 *
	 * @param array $position_data
	 * @return string|null
	 */
	public function getColumnNameAtPosition($position_data)
	{
		$columns = array_keys($this->columns);
		$columns_flipped = array_flip($columns);

		if(isset($position_data['before']) && isset($columns_flipped[$position_data['before']])) {
			$before_pos = $columns_flipped[$position_data['before']];
		}
		if(isset($position_data['after']) && isset($columns_flipped[$position_data['after']])) {
			$after_pos = $columns_flipped[$position_data['after']];
		}

		//first or last
		if(!isset($after_pos) || !isset($before_pos) ) {
			if(!isset($after_pos)) {
				//first
				return $columns[0];
			}
			if(!isset($before_pos)) {
				//last
				return $columns[count($columns)-1];
			}
			return null;
		} else {
			if(($before_pos-1) == ($after_pos+1)) {
				return $columns[$before_pos-1];
			}

			return null;
		}
	}

	/**
	 * Get the enire CREATE TABLE statement for the current table
	 *
	 * @return string
	 */
	public function getCreateStatement()
	{
		$create = "";

		if($this->show_drop) {
			$create .= "DROP TABLE IF EXISTS `$this->name`;\n";
		}

		$create .= "CREATE TABLE `$this->name` (\n";

		//columns
		$cols = array();
		foreach($this->columns as $column_name => $column) {
			$cols[] = "  " . $column->getColumnDefinition();
		}
		$create .= implode(",\n", $cols);
		$keys = array();

		//pri key
		if(count($this->primary_key) > 0) {
			$keys[] = "  PRIMARY KEY (" . implode(", ", $this->primary_key) . ")";
		}

		//keys
		foreach($this->keys as $key_name => $columns) {
			$keys[] = "  KEY $key_name (" . implode(", ", $columns) . ")";
		}

		//unique keys
		foreach($this->unique_keys as $key_name => $columns) {
			$keys[] = "  UNIQUE KEY $key_name (" . implode(", ", $columns) . ")";
		}

		//foreign keys
		foreach($this->constraints as $constraint_name => $constraint) {
			if($constraint instanceof ForeignKey) {
				$keys[] = "  CONSTRAINT $constraint_name FOREIGN KEY " . $constraint->getKeyDefinition();
			}
		}
		if(count($keys) > 0) {
			$create .= ",\n" . implode(",\n", $keys);
		}

		$create .= "\n) ENGINE=$this->engine DEFAULT CHARSET=$this->default_charset;\n";

		return $create;
	}


	/**
	 * Get the name of the next constraint. If 20 names cannot be used (all
	 * taken) then skip.
	 *
	 * @return string|null
	 */
	public function getNextConstraintName()
	{
		$constraint_name = $this->name . "_ibfk_";

		//not going to try after 20
		for($i = 1; $i < 21; $i++) {
			if(!isset($this->constraints["`$constraint_name"."$i`"])) {
				return "`$constraint_name"."$i`";
			}
		}

		return null;
	}

	/**
	 * Check if a particular constaint exisit. The constraint name cannot be
	 * used, because it can be unknown.
	 *
	 * @param constraint object $constraint
	 * @return boolean
	 */
	public function constraintExists($constraint)
	{
		if($constraint instanceof ForeignKey) {
			foreach($this->constraints as $constraint_name => $currrent_constraint) {
				if(strtoupper($constraint->getKeyDefinition()) == strtoupper($currrent_constraint->getKeyDefinition())) {
					return true;
				}
			}
		}

		return false;
	}
}
class Column
{
	private $name = null;
	private $type = null;
	private $length = null;
	private $values = null; //for enum and set
	private $character_set = null; //for string types
	private $collation = null; //for string types
	private $attributes = null; //binary, unsigned, unsigned zerofill, on update current_timestamp
	private $is_null = false; //boolean
	private $auto_increment = false; //boolean
	private $default = null; //null or string: NULL, CURRENT_TIMESTAMP, <other value>

//	private $extra = null;
//	private $comments = null;


	/**
	 * Create a column
	 *
	 * @param string $column_name
	 * @param string $data_type
	 * @param string $is_null
	 * @param string $default
	 * @param string $auto_increment
	 */
	public function __construct($column_name, $data_type, $is_null, $default, $auto_increment)
	{
		$this->name = $column_name;
		$this->is_null = $is_null;
		$this->default = $default;
		$this->auto_increment = $auto_increment;

		$this->parseDataType($data_type);
	}

	/**
	 * Parse the data type and fill in the column object
	 *
	 * @param string $data_type
	 */
	public function parseDataType($data_type)
	{
		if (preg_match('/^(' . implode("|", Database::$data_types). ')$/i', $data_type, $m)) {
			foreach($m as $key => $value) {
				if(strlen($value) == 0) {
					unset($m[$key]);
				} else {
					$m[$key] = trim($value);
				}
			}
			$m_ordered = array_values($m); //get the keys in order

			switch (strtoupper($m_ordered[2])) { //key 2 always has the datatype
				case 'BIT':
					$this->type = 'BIT';

					//length
					$length = 1;
					if(isset($m[4])) {
						$length = $m[4];
					}
					$this->length = $length;
					break;

				case 'TINYINT':
					$this->type = 'TINYINT';

					//default length
					$length = 4;

					$attributes = array();
					//unsigned
					if(isset($m[8])) {
						$attributes[] = 'UNSIGNED';
						$length--;
					}
					//zerofill
					if(isset($m[9])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);

					//set specified length or use default
					if(isset($m[7])) {
						$length = $m[7];
					}
					$this->length = $length;
					break;


				case 'SMALLINT':
					$this->type = 'SMALLINT';

					//default length
					$length = 6;

					$attributes = array();
					//unsigned
					if(isset($m[13])) {
						$attributes[] = 'UNSIGNED';
						$length--;
					}
					//zerofill
					if(isset($m[14])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);

					//set specified length or use default
					if(isset($m[12])) {
						$length = $m[12];
					}
					$this->length = $length;
					break;


				case 'MEDIUMINT':
					$this->type = 'MEDIUMINT';

					//default length
					$length = 9;

					$attributes = array();
					//unsigned
					if(isset($m[18])) {
						$attributes[] = 'UNSIGNED';
						$length--;
					}
					//zerofill
					if(isset($m[19])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);

					//set specified length or use default
					if(isset($m[17])) {
						$length = $m[17];
					}
					$this->length = $length;
					break;


				case 'INT':
					$this->type = 'INT';

					//default length
					$length = 11;

					$attributes = array();
					//unsigned
					if(isset($m[23])) {
						$attributes[] = 'UNSIGNED';
						$length--;
					}
					//zerofill
					if(isset($m[24])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);

					//set specified length or use default
					if(isset($m[22])) {
						$length = $m[22];
					}
					$this->length = $length;
					break;


				case 'INTEGER': // synonym to INT
					$this->type = 'INTEGER';

					//default length
					$length = 11;

					$attributes = array();
					//unsigned
					if(isset($m[23])) {
						$attributes[] = 'UNSIGNED';
						$length--;
					}
					//zerofill
					if(isset($m[24])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);

					//set specified length or use default
					if(isset($m[22])) {
						$length = $m[22];
					}
					$this->length = $length;
					break;


				case 'BIGINT':
					$this->type = 'BIGINT';

					//length
					$length = 20;
					if(isset($m[32])) {
						$length = $m[32];
					}
					$this->length = $length;

					$attributes = array();
					//unsigned
					if(isset($m[33])) {
						$attributes[] = 'UNSIGNED';
					}
					//zerofill
					if(isset($m[34])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);
					break;


				case 'REAL': // synonym to DECIMAL
					$this->type = 'REAL';

					//length
					$length = null;
					if(isset($m[37]) && isset($m[38])) {
						$length = $m[37] . ", " . $m[38];
					}
					$this->length = $length;

					$attributes = array();
					//unsigned
					if(isset($m[39])) {
						$attributes[] = 'UNSIGNED';
					}
					//zerofill
					if(isset($m[40])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);
					break;


				case 'DOUBLE':
					$this->type = 'DOUBLE';

					//length
					$length = null;
					if(isset($m[43]) && isset($m[44])) {
						$length = $m[43] . ", " . $m[44];
					}
					$this->length = $length;

					$attributes = array();
					//unsigned
					if(isset($m[45])) {
						$attributes[] = 'UNSIGNED';
					}
					//zerofill
					if(isset($m[46])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);
					break;


				case 'FLOAT':
					$this->type = 'FLOAT';

					//length
					$length = null;
					if(isset($m[49]) && isset($m[50])) {
						$length = $m[49] . ", " . $m[50];
					}
					$this->length = $length;

					$attributes = array();
					//unsigned
					if(isset($m[51])) {
						$attributes[] = 'UNSIGNED';
					}
					//zerofill
					if(isset($m[52])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);
					break;


				case 'DECIMAL':
					$this->type = 'DECIMAL';

					//length
					$length = '10,0';
					if(isset($m[55])) {
						$length = $m[55];
					}
					if(isset($m[57])) {
						$length .= ', ' . $m[57];
					}
					$this->length = $length;

					$attributes = array();
					//unsigned
					if(isset($m[58])) {
						$attributes[] = 'UNSIGNED';
					}
					//zerofill
					if(isset($m[59])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);
					break;


				case 'NUMERIC':
					$this->type = 'NUMERIC';

					//length
					$length = '10,0';
					if(isset($m[62])) {
						$length = $m[62];
					}
					if(isset($m[64])) {
						$length .= ', ' . $m[64];
					}
					$this->length = $length;

					$attributes = array();
					//unsigned
					if(isset($m[65])) {
						$attributes[] = 'UNSIGNED';
					}
					//zerofill
					if(isset($m[66])) {
						$attributes[] = 'ZEROFILL';
					}
					$this->attributes = implode(' ', $attributes);
					break;


				case 'DATE':
					$this->type = 'DATE';
					break;


				case 'TIME':
					$this->type = 'TIME';
					break;


				case 'TIMESTAMP':
					$this->type = 'TIMESTAMP';
					break;


				case 'DATETIME':
					$this->type = 'DATETIME';
					break;


				case 'YEAR':
					$this->type = 'YEAR';
					break;


				case 'CHAR':
					$this->type = 'CHAR';

					//length
					$length = 1;
					if(isset($m[74])) {
						$length = $m[74];
					}
					$this->length = $length;

					//character set
					if(isset($m[76])) {
						$this->character_set = $m[76];
					}

					//collation
					if(isset($m[78])) {
						$this->collation = $m[76];
					}

					break;


				case 'VARCHAR':
					$this->type = 'VARCHAR';

					//length
					$this->length = $m[80];

					//character set
					if(isset($m[82])) {
						$this->character_set = $m[82];
					}

					//collation
					if(isset($m[84])) {
						$this->collation = $m[84];
					}
					break;


				case 'BINARY':
					$this->type = 'BINARY';

					//length
					$length = 1;
					if(isset($m[87])) {
						$length = $m[87];
					}
					$this->length = $length;
					break;


				case 'VARBINARY':
					$this->type = 'VARBINARY';

					//length
					$this->length = $m[89];
					break;


				case 'TINYBLOB':
					$this->type = 'TINYBLOB';
					$this->attributes = 'BINARY';
					break;


				case 'BLOB':
					$this->type = 'BLOB';
					$this->attributes = 'BINARY';
					break;


				case 'MEDIUMBLOB':
					$this->type = 'MEDIUMBLOB';
					$this->attributes = 'BINARY';
					break;


				case 'LONGBLOB':
					$this->type = 'LONGBLOB';
					$this->attributes = 'BINARY';
					break;


				case 'TINYTEXT':
					$this->type = 'TINYTEXT';

					//binary
					if(isset($m[96])) {
						$this->attributes = 'BINARY';
					}
					//character set
					if(isset($m[97])) {
						$this->character_set = $m[97];
					}

					//collation
					if(isset($m[99])) {
						$this->collation = $m[99];
					}
					break;


				case 'TEXT':
					$this->type = 'TEXT';

					//binary
					if(isset($m[102])) {
						$this->attributes = 'BINARY';
					}

					//character set
					if(isset($m[103])) {
						$this->character_set = $m[103];
					}

					//collation
					if(isset($m[105])) {
						$this->collation = $m[105];
					}
					break;


				case 'MEDIUMTEXT':
					$this->type = 'MEDIUMTEXT';

					//binary
					if(isset($m[108])) {
						$this->attributes = 'BINARY';
					}

					//character set
					if(isset($m[109])) {
						$this->character_set = $m[109];
					}

					//collation
					if(isset($m[111])) {
						$this->collation = $m[111];
					}
					break;


				case 'LONGTEXT':
					$this->type = 'LONGTEXT';

					//binary
					if(isset($m[114])) {
						$this->attributes = 'BINARY';
					}

					//character set
					if(isset($m[115])) {
						$this->character_set = $m[115];
					}

					//collation
					if(isset($m[117])) {
						$this->collation = $m[117];
					}
					break;


				case 'ENUM':
					$this->type = 'ENUM';

					//values
					$values = explode(',', $m[119]);
					foreach($values as $id => $value) {
						$values[$id] = trim($value);
					}
					$this->values = $values;


					//character set
					if(isset($m[121])) {
						$this->character_set = $m[121];
					}

					//collation
					if(isset($m[123])) {
						$this->collation = $m[123];
					}
					break;


				case 'SET':
					$this->type = 'SET';

					//values
					$values = explode(',', $m[125]);
					foreach($values as $id => $value) {
						$values[$id] = trim($value);
					}
					$this->values = $values;


					//character set
					if(isset($m[127])) {
						$this->character_set = $m[127];
					}

					//collation
					if(isset($m[129])) {
						$this->collation = $m[129];
					}
					break;


				default:
					break;
			}
		}
	}


	/**
	 * Get differences between column
	 *
	 * @param Column $other_column
	 * @return string
	 */
	public function getDiff(Column $other_column)
	{
		$this_col_def = $this->getColumnDefinition();
		$other_col_def = $other_column->getColumnDefinition();

		if(strtoupper($this_col_def) != strtoupper($other_col_def)) {
			return 'CHANGE `' . $this->name . '` ' . $other_col_def;
		}

		return null;
	}


	/**
	 * Get the datatype in a formatted way
	 *
	 * @return formatted data tyoe
	 */
	public function getDataType()
	{
		$data_type = '';

		switch ($this->type) {
			case 'BIT':
				$data_type .= ' ' . $this->type . '(' . $this->length . ')';
				break;

			case 'TINYINT':
			case 'SMALLINT':
			case 'MEDIUMINT':
			case 'INT':
			case 'INTEGER': // synonym to INT
			case 'BIGINT':
			case 'REAL': // synonym to DECIMAL
			case 'DOUBLE':
			case 'FLOAT':
			case 'DECIMAL':
			case 'NUMERIC':
				$data_type .= ' ' . $this->type . '(' . $this->length . ')';
				$data_type .= (!empty($this->attributes)) ? ' ' . $this->attributes : '';
				break;


			case 'DATE':
			case 'TIME':
			case 'TIMESTAMP':
			case 'DATETIME':
			case 'YEAR':
				$data_type .= ' ' . $this->type;
				break;


			case 'CHAR':
			case 'VARCHAR':
				$data_type .= ' ' . $this->type . '(' . $this->length . ')';
				$data_type .= (isset($this->character_set)) ? ' CHARACTER SET ' . $this->character_set : '';
				$data_type .= (isset($this->collation)) ? ' COLLATE ' . $this->collation : '';
				break;


			case 'BINARY':
			case 'VARBINARY':
				$data_type .= ' ' . $this->type . '(' . $this->length . ')';
				break;


			case 'TINYBLOB':
			case 'BLOB':
			case 'MEDIUMBLOB':
			case 'LONGBLOB':
//				$this->attributes = 'BINARY'; //this is a bin field but we don't really show the attribute here
				$data_type .= ' ' . $this->type;
				break;


			case 'TINYTEXT':
			case 'TEXT':
			case 'MEDIUMTEXT':
			case 'LONGTEXT':
				$data_type .= ' ' . $this->type;
				//$data_type .= (isset($this->attributes)) ? ' ' . $this->attributes : ''; //no need for BINARY attribute here
				$data_type .= (isset($this->character_set)) ? ' CHARACTER SET ' . $this->character_set : '';
				$data_type .= (isset($this->collation)) ? ' COLLATE ' . $this->collation : '';
				break;


			case 'ENUM':
			case 'SET':
				$data_type .= ' ' . $this->type . '(' . implode(', ', $this->values) . ')';
				$data_type .= (isset($this->character_set)) ? ' CHARACTER SET ' . $this->character_set : '';
				$data_type .= (isset($this->collation)) ? ' COLLATE ' . $this->collation : '';
				break;

			default:
				break;
		}


		$data_type .= ($this->is_null) ? ' NULL' : ' NOT NULL';
		$data_type .= (isset($this->default)) ? ' DEFAULT ' . str_replace("\\\\", "\\", $this->default) : '';
		$data_type .= ($this->auto_increment) ? ' AUTO_INCREMENT' : '';

		return $data_type;
	}

	/**
	 * Get column definition (name + datatype)
	 *
	 * @return string
	 */
	public function getColumnDefinition()
	{
		$def = '`' . $this->name . '`' . $this->getDataType();

		return $def;
	}

	public function isNull()
	{
		return $this->is_null;
	}
}
class ForeignKey
{
	private $name = null;
	private $ref_table = null;
	private $ref_column = null;
	private $on_update = null;
	private $on_delete = null;
	//add a foreing key to the table
	public function __construct($fk_name, $ref_table, $ref_column, $on_delete, $on_update)
	{
		$this->name = $fk_name;
		$this->ref_table = $ref_table;
		$this->ref_column = $ref_column;
		$this->on_update = $on_delete;
		$this->on_delete = $on_update;
	}

	public function getName()
	{
		return $this->name;
	}

	//get key definition
	public function getKeyDefinition()
	{
		$def = "($this->name) REFERENCES $this->ref_table ($this->ref_column)";
		if(isset($this->on_update)) {
			$def .= " ON UPDATE $this->on_update";
		}
		if(isset($this->on_delete)) {
			$def .= " ON DELETE $this->on_delete";
		}

		return $def;
	}
}