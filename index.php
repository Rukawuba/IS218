<!DOCTYPE HTML>
<html>
<head>
	<title>IS 218 - Final Project</title>
</head>
<body>

<?php
	$program = new program;

	class program {
		public function __construct() {
			$page = 'home';

			if(isset($_REQUEST['page'])) {
				$page = $_REQUEST['page'];
			}

			if(isset($_REQUEST['arg'])) {
				$arg = $_REQUEST['arg'];
			}

			$page = new $page($arg);
		}
	}

	abstract class page {
		public $content;
		public $con;

		function __construct($arg = NULL) {
			if($_SERVER['REQUEST_METHOD'] == 'GET') {
				$this->get();
			} else {
				$this->post();
			}
		}

		function get() {
			$this->content = $this->menu();
		}

		function post() {
			print_r($_POST);
		}

		function menu() {
			$menu = '<a href="index.php">Home</a><br><br>';

			return $menu;
		}

		function __destruct() {
			echo $this->content;
		}
	}

	class home extends page {
		function get() {
			/*
			// Importing from the general data file
			$general = new file();
			$general->file_name = "hd2011.csv";
			//echo $general->file_name . "<br>";
			$general->buildFields();
			//$general->buildArray();
			//$general->createTable();
			//$general->insertValues();
			*/
			//echo "<br>";
			/*
			// Importing from the first enrollment data file
			$enroll = new file();
			//$enroll->file_name = "effy2010.csv";
			$enroll->buildFields();
			$enroll->buildArray();
			$enroll->createTable();
			//$enroll->insertValues();
			*/
			//echo "<br>";
			/*
			// Importing from the second enrollment data file
			$enroll->file_name = "effy2011.csv";
			$enroll->buildFields();
			//echo $enroll->file_name . "<br>";
			$enroll->buildArray();
			//$enroll->insertValues();
			*/
			//echo "<br>";
			/*
			// Importing from the first finance file file
			$finance = new file();
			$finance->file_name = "f0910_f1a.csv";
			//echo $enroll->file_name . "<br>";
			//$finance->buildFields();
			//$finance->buildArray();
			//$finance->createTable();
			//$finance->insertValues();
			*/
			//echo "<br>";
			/*
			// Importing from the second finance file
			$finance->file_name = "f1011_f1a.csv";
			//echo $enroll->file_name . "<br>";
			$finance->buildFields();
			$finance->buildArray();
			//$finance->insertValues();
			*/

			$this->content = $this->menu();
		}

		function menu() {
			$menu  = '<a href="index.php?page=enrollment">Enrollment</a>';
			$menu .= '<br><a href="index.php?page=liabilities">Liabilities</a>';
			$menu .= '<br><a href="index.php?page=assets">Net Assets</a>';
			$menu .= '<br><a href="index.php?page=revenues">Revenues</a>';
			$menu .= '<br><a href="index.php?page=liabilities_students">Liabilities per Student</a>';
			$menu .= '<br><a href="index.php?page=assets_students">Net Assets per Student</a>';
			$menu .= '<br><a href="index.php?page=revenues_students">Revenues per Students</a>';
			$menu .= '<br><a href="index.php?page=colleges_ranking">Top Colleges</a>';
			$menu .= '<br><a href="index.php?page=colleges_states">Search Colleges by State</a>';

			return $menu;
		}
	}

	class enrollment extends page {}
	class liabilities extends page {}
	class assets extends page {}
	class revenues extends page {}
	class liabilities_students extends page {}
	class assets_students extends page {}
	class revenues_students extends page {}
	class colleges_ranking extends page {}
	class colleges_states extends page {}

	class file {
		public $file_name;
		public $file_array;
		public $file_fields;
		public $con;
		private $row_count;

		function __construct() {
			$this->dbConnect();
		}

		// Provides the database connection
		public function dbConnect() {
			// Account info
			include "account.php";

			// Create connection
			$this->con=mysqli_connect($hostname,$username,$password,$database);
			
			// Check connection
			if (mysqli_connect_errno($this->con)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error() . "<br>";
			} else {
				echo "Successfully connected to MySQL<br>";
			}
		}

		// Builds the fields in preparation for building the table
		public function buildFields() {
			//echo $this->file_name . " build fields<br>";
			if ($this->file_name == "hd2011.csv") {
				$this->file_fields = array("id", "name", "state");
				//echo $this->file_fields[0] . "<br>";
				//echo $this->file_fields[1] . "<br>";
				//echo $this->file_fields[2] . "<br>";
			} else if (($this->file_name == "effy2010.csv") || ($this->file_name == "effy2011.csv")) {
				$this->file_fields = array("id", "student_level", "student_count", "year");
				//echo $this->file_fields[0] . "<br>";
				//echo $this->file_fields[1] . "<br>";
				//echo $this->file_fields[2] . "<br>";
				//echo $this->file_fields[3] . "<br>";
			} else if (($this->file_name == "f0910_f1a.csv") || ($this->file_name == "f1011_f1a.csv")) {
				$this->file_fields = array("id", "liabilities", "net_assets", "revenues", "prev_year", "current_year");
				//echo $this->file_fields[0] . "<br>";
				//echo $this->file_fields[1] . "<br>";
				//echo $this->file_fields[2] . "<br>";
				//echo $this->file_fields[3] . "<br>";
				//echo $this->file_fields[4] . "<br>";
				//echo $this->file_fields[5] . "<br>";
			}

			$this->row_count = $row;
			$this->file_array = $result;
		}

		// Builds the array used to insert information
		public function buildArray() {
			//echo $this->file_name . " build array<br>";
			if ($this->file_name == "hd2011.csv") {
				// Stuff happens
				$row = -1;
				if (($handle = fopen($this->file_name, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$num = count($data);
						if ($row > -1) {			
							echo "Row " . $row . ":<br>";
							for ($col=0; $col<$num; $col++) {
								if ($col == 0) {
									$result[$row][0] = $data[$col];
									echo $data[$col] . " = " . $result[$row][0] . "<br />\n";
								} else if ($col == 1) {
									$result[$row][1] = $data[$col];
									echo $data[$col] . " = " . $result[$row][1] . "<br />\n";
								} else if ($col == 4) {
									$result[$row][2] = $data[$col];
									echo $data[$col] . " = " . $result[$row][2] . "<br />\n";
								}
							}

							echo "<br>";
						}
						$row++;
					}
					
					fclose($handle);
				}
			} else if ($this->file_name == "effy2010.csv") {
				// Stuff happens
				$row = -2;
				if (($handle = fopen($this->file_name, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$num = count($data);
						if ($row > -1) {			
							//echo "Row " . $row . ":<br>";
							for ($col=0; $col<$num; $col++) {
								if ($col == 0) {
									$result[$row][0] = $data[$col];
									//echo $this->file_fields[0] . ": " . $result[$row][0] . "<br />\n";
								} else if ($col == 1) {
									$result[$row][1] = $data[$col];
									//echo $this->file_fields[1] . ": " . $result[$row][1] . "<br />\n";
								} else if ($col == 50) {
									$result[$row][2] = $data[$col];
									//echo $this->file_fields[2] . ": " . $result[$row][2] . "<br />\n";
								}
							}

							$result[$row][3] = 2010;
							//echo $this->file_fields[3] . ": " . $result[$row][3] . "<br />\n";

							//echo "<br>";
						}
						$row++;
					}
					
					fclose($handle);
				}
			} else if ($this->file_name == "effy2011.csv") {
				// Stuff happens
				$row = -1;
				if (($handle = fopen($this->file_name, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$num = count($data);
						if ($row > -1) {			
							//echo "Row " . $row . ":<br>";
							for ($col=0; $col<$num; $col++) {
								if ($col == 0) {
									$result[$row][0] = $data[$col];
									//echo $this->file_fields[0] . ": " . $result[$row][0] . "<br />\n";
								} else if ($col == 1) {
									$result[$row][1] = $data[$col];
									//echo $this->file_fields[1] . ": " . $result[$row][1] . "<br />\n";
								} else if ($col == 4) {
									$result[$row][2] = $data[$col];
									//echo $this->file_fields[2] . ": " . $result[$row][2] . "<br />\n";
								}
							}

							$result[$row][3] = 2011;
							//echo $this->file_fields[3] . ": " . $result[$row][3] . "<br />\n";

							//echo "<br>";
						}
						$row++;
					}
					
					fclose($handle);
				}
			} else if (($this->file_name == "f0910_f1a.csv") || ($this->file_name == "f1011_f1a.csv")) {
				// Stuff happens
				$row = -1;
				if (($handle = fopen($this->file_name, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
						$num = count($data);
						if ($row > -1) {			
							//echo "Row " . $row . ":<br>";
							for ($col=0; $col<$num; $col++) {
								if ($col == 0) {
									$result[$row][0] = $data[$col];
									//echo $this->file_fields[0] . ": " . $result[$row][0] . "<br />\n";
								} else if ($col == 24) {
									if(empty($data[$col])) {
										$result[$row][1] = 0;										
									} else {
										$result[$row][1] = $data[$col];										
									}
									//echo $this->file_fields[1] . ": " . $result[$row][1] . "<br />\n";
								} else if ($col == 34) {
									if(empty($data[$col])) {
										$result[$row][2] = 0;										
									} else {
										$result[$row][2] = $data[$col];										
									}
									//echo $this->file_fields[2] . ": " . $result[$row][2] . "<br />\n";
								} else if ($col == 110) {
									if(empty($data[$col])) {
										$result[$row][3] = 0;
									} else {
										$result[$row][3] = $data[$col];
									}
									//echo $this->file_fields[3] . ": " . $result[$row][3] . "<br />\n";
								}
							}

							if ($this->file_name == "f0910_f1a.csv") {
								$result[$row][4] = 2009;
								$result[$row][5] = 2010;

								//echo $this->file_fields[4] . ": " . $result[$row][4] . "<br />\n";
								//echo $this->file_fields[5] . ": " . $result[$row][5] . "<br />\n";
							} else {
								$result[$row][4] = 2010;
								$result[$row][5] = 2011;

								//echo $this->file_fields[4] . ": " . $result[$row][4] . "<br />\n";
								//echo $this->file_fields[5] . ": " . $result[$row][5] . "<br />\n";
							}

							//echo "<br>";
						}
						$row++;
					}
					
					fclose($handle);
				}
			}

			$this->row_count = $row;
			$this->file_array = $result;
		}

		// Creates the table to insert data into
		public function createTable() {
			//echo $this->file_name . " create table<br>";
			if ($this->file_name == "hd2011.csv") {
				// Stuff happens
				$sql = "CREATE TABLE college_general (";
				$sql .= $this->file_fields[0] . " int, ";
				$sql .= $this->file_fields[1] . " varchar(255), ";
				$sql .= $this->file_fields[2] . " varchar(2)";
				$sql .= ");";
				
				echo $sql . "<br>";
				/*
				if (mysqli_query($this->con,$sql)) {
					echo "Table college_general created successfully";
				} else {
					echo "Error creating database: " . mysqli_error($this->con);
				}
				*/					
			} else if (($this->file_name == "effy2010.csv") || ($this->file_name == "effy2011.csv")) {
				// Stuff happnes
				$sql = "CREATE TABLE college_enrollment (";
				$sql .= $this->file_fields[0] . " int, ";
				$sql .= $this->file_fields[1] . " int, ";
				$sql .= $this->file_fields[2] . " int, ";
				$sql .= $this->file_fields[3] . " int";
				$sql .= ");";
				
				echo $sql . "<br>";
				/*
				if (mysqli_query($this->con,$sql)) {
					echo "Table college_enrollment created successfully";
				} else {
					echo "Error creating database: " . mysqli_error($this->con);
				}
				*/
			} else if (($this->file_name == "f0910_f1a.csv") || ($this->file_name == "f1011_f1a.csv")) {
				// Stuff happens
				$sql = "CREATE TABLE college_finances (";
				$sql .= $this->file_fields[0] . " int, ";
				$sql .= $this->file_fields[1] . " float, ";
				$sql .= $this->file_fields[2] . " float, ";
				$sql .= $this->file_fields[3] . " float, ";
				$sql .= $this->file_fields[4] . " int, ";
				$sql .= $this->file_fields[5] . " int";
				$sql .= ");";
				
				//echo $sql . "<br>";
				
				if (mysqli_query($this->con,$sql)) {
					echo "Table college_finances created successfully";
				} else {
					echo "Error creating database: " . mysqli_error($this->con);
				}
				
			}
		}

		// Inserts the data into the table
		public function insertValues() {
			//echo $this->file_name . " insert values<br>";
			if ($this->file_name == "hd2011.csv") {
				// Stuff happens
				for ($x=0; $x < $this->row_count; $x++) {
					$sql = "INSERT INTO college_general VALUES (";
					$sql .= $this->file_array[$x][0] . ",";
					$sql .= "'" . $this->file_array[$x][1] . "',";
					$sql .= "'" . $this->file_array[$x][3] . "')";
					
					//echo $sql . "<br>";
					/*
					if (mysqli_query($this->con, $sql)) {
						echo "Successfully inserted values into college_general<br>";
					} else {
						echo "Error inserting values: " . mysqli_error($this->con) . "<br>";
					}
					*/
				}
			} else if (($this->file_name == "effy2010.csv") || ($this->file_name == "effy2011.csv")) {
				// Stuff happens
				for ($x=0; $x < $this->row_count; $x++) {
					$sql = "INSERT INTO college_enrollment VALUES (";
					$sql .= $this->file_array[$x][0] . ",";
					$sql .= $this->file_array[$x][1] . ",";
					$sql .= $this->file_array[$x][2] . ",";
					$sql .= $this->file_array[$x][3] . ")";
					
					//echo $sql . "<br>";
					/*
					if (mysqli_query($this->con, $sql)) {
						echo "Successfully inserted values into college_enrollment<br>";
					} else {
						echo "Error inserting values: " . mysqli_error($this->con) . "<br>";
					}
					*/
				}			
			} else if (($this->file_name == "f0910_f1a.csv") || ($this->file_name == "f1011_f1a.csv")) {
				// Stuff happens
				//echo $this->row_count;
				for ($x=0; $x < $this->row_count; $x++) {
					$sql = "INSERT INTO college_finances VALUES (";
					$sql .= $this->file_array[$x][0] . ", ";
					$sql .= $this->file_array[$x][1] . ", ";
					$sql .= $this->file_array[$x][2] . ", ";
					$sql .= $this->file_array[$x][3] . ", ";
					$sql .= $this->file_array[$x][4] . ", ";
					$sql .= $this->file_array[$x][5] . ")";
					
					//echo $sql . "<br>";
					
					if (mysqli_query($this->con, $sql)) {
						echo "Successfully inserted values into college_finances<br>";
					} else {
						echo "Error inserting values: " . mysqli_error($this->con) . "<br>";
					}
					
				}
			}
		}
	}


?>

</body>
</html>