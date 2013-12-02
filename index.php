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

		public function dbConnect() {
			// Account info
			include "account.php";

			// Create connection
			$this->con=mysqli_connect($hostname,$username,$password,$database);
			
			// Check connection
			if (mysqli_connect_errno($this->con)) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error() . "<br>";
			}
		}

		function __destruct() {
			echo $this->content;
		}
	}

	class home extends page {
		function get() {
			$this->content = $this->menu();

			// The commented-out section was used to import the csv data to MySQL
			/*
			// Importing from the general data file
			$general = new file();
			$general->file_name = "hd2011.csv";
			$general->buildFields();
			$general->buildArray();
			//$general->createTable();
			//$general->insertValues();
			*/
			// Importing from the first enrollment data file
			/*
			$enroll = new file();
			$enroll->file_name = "effy2010.csv";
			$enroll->buildFields();
			$enroll->buildArray();
			//$enroll->createTable();
			//$enroll->insertValues();
			*/
			// Importing from the second enrollment data file
			/*
			$enroll->file_name = "effy2011.csv";
			$enroll->buildFields();
			$enroll->buildArray();
			//$enroll->insertValues();
			*/
			/*
			// Importing from the first finance file file
			$finance = new file();
			$finance->file_name = "f0910_f1a.csv";
			$finance->buildFields();
			$finance->buildArray();
			//$finance->createTable();
			//$finance->insertValues();
			*/
			/*
			// Importing from the second finance file
			$finance->file_name = "f1011_f1a.csv";
			$finance->buildFields();
			$finance->buildArray();
			//$finance->insertValues();
			*/

			// This commented-out section was used to create and fill the updated
			// college_enrollment_totals table, the college_ratios table and the
			// final college_increases table
			/*
			$this->dbConnect();
			//$this->createEnrollmentTable();
			//$this->insertEnrollmentValues(2010);
			//$this->insertEnrollmentValues(2011);
			*/
			/*
			//$this->createRatioTable();
			//$this->insertRatioValues();
			*/
			/*
			//$this->createIncreasesTable();
			//$this->insertIncreasesValues(2010, 2011);
			*/
		}

		function menu() {
			$menu  = '<a href="index.php?page=enrollment">Total Enrollment</a><br>';
			$menu .= '<a href="index.php?page=enrollment_increase">Enrollment Increase</a><br>';
			$menu .= '<a href="index.php?page=liabilities">Total Liabilities</a><br>';
			$menu .= '<a href="index.php?page=liabilities_increase">Liabilities Increase</a><br>';
			$menu .= '<a href="index.php?page=assets">Net Assets</a><br>';
			$menu .= '<a href="index.php?page=revenues">Total Revenues</a><br>';
			$menu .= '<a href="index.php?page=liabilities_students">Total Liabilities per Student</a><br>';
			$menu .= '<a href="index.php?page=assets_students">Net Assets per Student</a><br>';
			$menu .= '<a href="index.php?page=revenues_students">Total Revenues per Students</a><br>';
			$menu .= '<a href="index.php?page=colleges_ranking">Top Colleges</a><br>';
			$menu .= '<a href="index.php?page=colleges_states">Search Colleges by State</a><br><br>';

			return $menu;
		}

		function createEnrollmentTable() {
			$sql = "CREATE TABLE college_enrollment_totals (id int, student_total int, year int)";
			if (mysqli_query($this->con,$sql)) {
				echo "Table college_enrollment_totals created successfully<br>";
			} else {
				echo "Error creating table: " . mysqli_error($this->con) . "<br>";
			}
		}

		function insertEnrollmentValues($year) {
			$sql = "SELECT * FROM college_enrollment WHERE year=" . $year . " ORDER BY id";
			$result = mysqli_query($this->con, $sql);
			
			$first_run = TRUE;
			$array_row = 0;
			$last_id = 0;

			while($row = mysqli_fetch_array($result)) {
				if($first_run) {
					//echo "create new entry on row $array_row<br>";
					$array[0][0] = $row['id'];
					$array[0][1] = $row['student_count'];
					$array[0][2] = $year;

					$first_run = FALSE;
				} else if ($row['id'] == $last_id) {
					//echo "add student count on row $array_row<br>";
					$array[$array_row][1] += $row['student_count'];
				} else {
					$array_row++;
					//echo "create new entry on row $array_row<br>";
					$array[$array_row][0] = $row['id'];
					$array[$array_row][1] = $row['student_count'];
					$array[$array_row][2] = $year;
				}

				$last_id = $row['id'];
				//echo $row['id'] . " " . $row['student_count'] . " " . $row['year'] . "<br>";
			}

			//echo $array_row;
			for ($i=0; $i<$array_row; $i++) {
				//echo $array[$i][0] . " " . $array[$i][1] . " " . $array[$i][2] . "<br>";
				$sql = "INSERT INTO college_enrollment_totals VALUES (";
				$sql .= $array[$i][0] . ", ";
				$sql .= $array[$i][1] . ", ";
				$sql .= $year . ")";
				
				//echo $sql . "<br>";
				
				if (mysqli_query($this->con, $sql)) {
					echo "Successfully inserted values into college_enrollment_totals<br>";
				} else {
					echo "Error inserting values: " . mysqli_error($this->con) . "<br>";
				}
			}
		}

		function createRatioTable() {
			$sql = "CREATE TABLE college_ratios (id int, tl_ratio int, na_ratio int, tr_ratio int);";
			
			//echo $sql;
			if (mysqli_query($this->con,$sql)) {
				echo "Table college_ratios created successfully<br>";
			} else {
				echo "Error creating table: " . mysqli_error($this->con) . "<br>";
			}
		}

		function insertRatioValues(){
			$sql = "SELECT college_enrollment_totals.id, ";
			$sql .= "college_enrollment_totals.student_total, ";
			$sql .= "college_finances.liabilities, ";
			$sql .= "college_finances.net_assets, ";
			$sql .= "college_finances.revenues ";
			$sql .= "FROM college_enrollment_totals ";
			$sql .= "JOIN college_finances ON ";
			$sql .= "college_enrollment_totals.id=college_finances.id ";
			$sql .= "WHERE year=2011 AND current_year=2011";

			//echo $sql;

			$result = mysqli_query($this->con, $sql);
			$array_row = 0;

			while($row = mysqli_fetch_array($result)) {
				//echo $row['id'] . " " . $row['student_total'] . " " . $row['liabilities'] . " " . $row['net_assets'] . " " . $row['revenues'] . "<br>";
				$array[$array_row][0] = $row['id'];

				$ratio = ((double) $row['liabilities']) / ((double) $row['student_total']);
				$array[$array_row][1] = round($ratio);

				$ratio = ((double) $row['net_assets']) / ((double) $row['student_total']);
				$array[$array_row][2] = round($ratio);

				$ratio = ((double) $row['revenues']) / ((double) $row['student_total']);
				$array[$array_row][3] = round($ratio);

				//echo $array[$array_row][0] . " " . 	$array[$array_row][1] . " " . 	$array[$array_row][2] . " " . $array[$array_row][3] . "<br>";

				$array_row++;
			}

			
			for ($i = 0; $i < $array_row; $i++) {
				$sql = "INSERT INTO college_ratios VALUES (";
				$sql .= $array[$i][0] . ", ";
				$sql .= $array[$i][1] . ", ";
				$sql .= $array[$i][2] . ", ";
				$sql .= $array[$i][3] . ")";

				
				if (mysqli_query($this->con, $sql)) {
					echo "Successfully inserted values into college_ratios<br>";
				} else {
					echo "Error inserting values: " . mysqli_error($this->con) . "<br>";
				}
			}
		}

		function createIncreasesTable() {
			$sql = "CREATE TABLE college_increases (id int, enroll_increase float, liability_increase float, prev_year int, current_year int);";

			//echo $sql . "<br>";
			if (mysqli_query($this->con,$sql)) {
				echo "Table college_increases created successfully<br>";
			} else {
				echo "Error creating table: " . mysqli_error($this->con) . "<br>";
			}
		}

		function insertIncreasesValues($prev_year, $current_year) {
			$sql  = "SELECT college_general.id, college_enrollment_totals.student_total, ";
			$sql .= "college_finances.liabilities, college_enrollment_totals.year ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_enrollment_totals ON college_general.id = college_enrollment_totals.id ";
			$sql .= "JOIN college_finances ON college_general.id = college_finances.id ";
			$sql .= "WHERE college_enrollment_totals.year = college_finances.current_year ";
			$sql .= "ORDER BY college_general.id";
	
			$result = mysqli_query($this->con, $sql);
			
			$first_run = TRUE;
			$array_row = -1;
			$last_id = 0;

			//Array
			//0 - id
			//1 - enrollment 2010
			//2 - enrollment 2011
			//3 - liabilities 2010
			//4 - liabilities 2011

			while($row = mysqli_fetch_array($result)) {
				if ($row['id'] == $last_id) {
					//echo $row['year'] . " add values to row $array_row<br><br>";

					if ($row['year'] == $prev_year) {
						$array[$array_row][1] = $row['student_total'];
						$array[$array_row][3] = $row['liabilities'];
					} else if ($row['year'] == $current_year) {
						$array[$array_row][2] = $row['student_total'];
						$array[$array_row][4] = $row['liabilities'];
					}

				} else {
					$array_row++;
					//echo $row['year'] . " create new entry on row $array_row<br>";
					
					$array[$array_row][0] = $row['id'];
					$array[$array_row][1] = 0;
					$array[$array_row][2] = 0;
					$array[$array_row][3] = 0;
					$array[$array_row][4] = 0;

					if ($row['year'] == $prev_year) {
						$array[$array_row][1] = $row['student_total'];
						$array[$array_row][3] = $row['liabilities'];
					} else if ($row['year'] == $current_year) {
						$array[$array_row][2] = $row['student_total'];
						$array[$array_row][4] = $row['liabilities'];
					}

				}

				$last_id = $row['id'];
				
				//echo $row['id'] . " " . $row['student_total'] . " " . $row['liabilities'] . " " . $row['year'] . "<br>";

			}

			
			for ($i=0; $i<count($array); $i++) {			
				$sql  = "INSERT INTO college_increases VALUES (";
				$sql .= $array[$i][0] . ", ";

				$increase = 100 * ((double) ($array[$i][2] - $array[$i][1])) / ((double) $array[$i][1]);
				$sql .= number_format($increase, 2) . ", ";

				$increase = 100 * ((double) ($array[$i][4] - $array[$i][3])) / ((double) $array[$i][3]);
				$sql .= number_format($increase, 2) . ", ";

				$sql .= $prev_year . ", ";
				$sql .= $current_year . ")";

				//echo $sql . "<br>";
				
				if (mysqli_query($this->con, $sql)) {
					echo "Successfully inserted values into college_increases<br>";
				} else {
					echo "Error inserting values: " . mysqli_error($this->con) . "<br>";
				}
			}
			
		}

	}

	class enrollment extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 25 Colleges by Enrollment";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql = "SELECT college_general.name,  college_enrollment_totals.student_total ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_enrollment_totals ";
			$sql .= "ON college_general.id=college_enrollment_totals.id ";
			$sql .= "WHERE year=2011 ";
			$sql .= "ORDER BY college_enrollment_totals.student_total DESC ";
			$sql .= "LIMIT 25";
			
			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>College Name</th>
					<th>Students</th>
				</tr>';
			
			
			while($row = mysqli_fetch_array($result)) {
				$table .= '<tr>';
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['student_total'] . '</td>';
				$table .= '</tr>';
			}
			
			$table .= '</table>';
			return $table;
			
		}
	}

	class liabilities extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 25 Colleges by Total Liabilities";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql = "SELECT college_general.name, college_finances.liabilities ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_finances ";
			$sql .= "ON college_general.id=college_finances.id ";
			$sql .= "WHERE current_year=2011 ";
			$sql .= "ORDER BY college_finances.liabilities DESC ";
			$sql .= "LIMIT 25";
			
			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>College Name</th>
					<th>Total Liabilities</th>
				</tr>';
			
			
			while($row = mysqli_fetch_array($result)) {
				$table .= '<tr>';
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['liabilities'] . '</td>';
				$table .= '</tr>';
			}
			
			$table .= '</table>';
			return $table;
			
		}
	}

	class assets extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 25 Colleges by Net Assets";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql = "SELECT college_general.name, college_finances.net_assets ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_finances ";
			$sql .= "ON college_general.id=college_finances.id ";
			$sql .= "WHERE current_year=2011 ";
			$sql .= "ORDER BY college_finances.net_assets DESC ";
			$sql .= "LIMIT 25";
			
			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>College Name</th>
					<th>Net Assets</th>
				</tr>';
			
			
			while($row = mysqli_fetch_array($result)) {
				$table .= '<tr>';
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['net_assets'] . '</td>';
				$table .= '</tr>';
			}
			
			$table .= '</table>';
			return $table;
			
		}
	}

	class revenues extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 25 Colleges by Total Revenues";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql = "SELECT college_general.name, college_finances.revenues ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_finances ";
			$sql .= "ON college_general.id=college_finances.id ";
			$sql .= "WHERE current_year=2011 ";
			$sql .= "ORDER BY college_finances.revenues DESC ";
			$sql .= "LIMIT 25";
			
			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>College Name</th>
					<th>Total Revenues</th>
				</tr>';
			
			
			while($row = mysqli_fetch_array($result)) {
				$table .= '<tr>';
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['revenues'] . '</td>';
				$table .= '</tr>';
			}
			
			$table .= '</table>';
			return $table;
			
		}
	}

	class liabilities_students extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 25 Colleges by Total Liabilities per Students";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql = "SELECT college_general.name, college_finances.liabilities, ";
			$sql .= "college_enrollment_totals.student_total, college_ratios.tl_ratio ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_finances ON college_general.id=college_finances.id ";
			$sql .= "JOIN college_enrollment_totals ON college_general.id=college_enrollment_totals.id ";
			$sql .= "JOIN college_ratios ON college_general.id=college_ratios.id ";
			$sql .= "WHERE YEAR=2011 AND current_year=2011 ";
			$sql .= "ORDER BY  college_ratios.tl_ratio DESC ";
			$sql .= "LIMIT 25";
			
			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>College Name</th>
					<th>Total Liabilities (L)</th>
					<th>Students (S)</th>
					<th>Ratio (L/S)</th>
				</tr>';
			
			$array_row = 0;

			while($row = mysqli_fetch_array($result)) {
				
				$table .= '<tr>';
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['liabilities'] . '</td>';
				$table .= '<td>' . $row['student_total'] . '</td>';
				$table .= '<td>' . $row['tl_ratio'] . '</td>';
				$table .= '</tr>';
			}
			
			$table .= '</table>';
			return $table;
			
		}
	}

	class assets_students extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 25 Colleges by Net Assets per Students";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql = "SELECT college_general.name, college_finances.net_assets, ";
			$sql .= "college_enrollment_totals.student_total, college_ratios.na_ratio ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_finances ON college_general.id=college_finances.id ";
			$sql .= "JOIN college_enrollment_totals ON college_general.id=college_enrollment_totals.id ";
			$sql .= "JOIN college_ratios ON college_general.id=college_ratios.id ";
			$sql .= "WHERE YEAR=2011 AND current_year=2011 ";
			$sql .= "ORDER BY  college_ratios.na_ratio DESC ";
			$sql .= "LIMIT 25";
			
			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>College Name</th>
					<th>Net Assets (N)</th>
					<th>Students (S)</th>
					<th>Ratio (N/S)</th>
				</tr>';
			
			$array_row = 0;

			while($row = mysqli_fetch_array($result)) {
				
				$table .= '<tr>';
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['net_assets'] . '</td>';
				$table .= '<td>' . $row['student_total'] . '</td>';
				$table .= '<td>' . $row['na_ratio'] . '</td>';
				$table .= '</tr>';
			}
			
			$table .= '</table>';
			return $table;
			
		}
	}

	class revenues_students extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 25 Colleges by Total Revenues per Students";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql = "SELECT college_general.name, college_finances.revenues, ";
			$sql .= "college_enrollment_totals.student_total, college_ratios.tr_ratio ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_finances ON college_general.id=college_finances.id ";
			$sql .= "JOIN college_enrollment_totals ON college_general.id=college_enrollment_totals.id ";
			$sql .= "JOIN college_ratios ON college_general.id=college_ratios.id ";
			$sql .= "WHERE YEAR=2011 AND current_year=2011 ";
			$sql .= "ORDER BY  college_ratios.tr_ratio DESC ";
			$sql .= "LIMIT 25";
			
			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>College Name</th>
					<th>Total Revenues (R)</th>
					<th>Students (S)</th>
					<th>Ratio (R/S)</th>
				</tr>';
			
			$array_row = 0;

			while($row = mysqli_fetch_array($result)) {
				
				$table .= '<tr>';
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['revenues'] . '</td>';
				$table .= '<td>' . $row['student_total'] . '</td>';
				$table .= '<td>' . $row['tr_ratio'] . '</td>';
				$table .= '</tr>';
			}
			
			$table .= '</table>';
			return $table;
			
		}
	}

	class colleges_ranking extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 10 Colleges by Statistics";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql = "SELECT college_general.name, ";
			$sql .= "college_enrollment_totals.student_total, ";
			$sql .= "college_finances.liabilities, college_ratios.tl_ratio, ";
			$sql .= "college_finances.net_assets, college_ratios.na_ratio, ";
			$sql .= "college_finances.revenues, college_ratios.tr_ratio ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_enrollment_totals ON college_general.id=college_enrollment_totals.id ";
			$sql .= "JOIN college_finances ON college_general.id=college_finances.id ";
			$sql .= "JOIN college_ratios ON college_general.id=college_ratios.id ";
			$sql .= "WHERE year=2011 AND current_year=2011 ";
			$sql .= "ORDER BY college_enrollment_totals.student_total DESC, ";
			$sql .= "college_ratios.tl_ratio DESC, college_ratios.na_ratio DESC, college_ratios.tr_ratio DESC, ";
			$sql .= "college_finances.liabilities DESC, college_finances.net_assets DESC, college_finances.revenues DESC ";
			$sql .= "LIMIT 10";

			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>Ranking</th>
					<th>College Name</th>
					<th>Students (S)</th>
					<th>Total Liabilities (L)</th>
					<th>Liability Ratio (L/S)</th>
					<th>Net Assets (N)</th>
					<th>Asset Ratio (N/S)</th>
					<th>Total Revenues (R)</th>
					<th>Revenue Ratio (R/S)</th>
				</tr>';
			
			$rank = 1;

			while($row = mysqli_fetch_array($result)) {
				
				$table .= '<tr>';
				$table .= '<td> #' . $rank . '</td>'; 
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['student_total'] . '</td>';
				$table .= '<td>' . $row['liabilities'] . '</td>';
				$table .= '<td>' . $row['tl_ratio'] . '</td>';
				$table .= '<td>' . $row['net_assets'] . '</td>';
				$table .= '<td>' . $row['na_ratio'] . '</td>';
				$table .= '<td>' . $row['revenues'] . '</td>';
				$table .= '<td>' . $row['tr_ratio'] . '</td>';
				$table .= '</tr>';

				$rank++;
			}
			
			$table .= '</table>';
			return $table;
			
		}
	}

	class colleges_states extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Search Colleges by State";
			$this->content .= $this->retrieveData();
		}

		function post() {
			//echo $_POST['state_select'];
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= $this->displayData($_POST['state_select']);
		}

		function retrieveData() {
			$sql = "SELECT DISTINCT state FROM college_general ORDER BY state";
			$result = mysqli_query($this->con, $sql);

			$form = '<form action="index.php?page=colleges_states" method="post">';
			$form .= '<select name="state_select" id="state_select">';
			while($row = mysqli_fetch_array($result)) {
				//echo $row['state'] . "<br>";
				$form .= '<option value="' . $row['state'] . '">' . $row['state'] . '</option>';
			}
			$form .= '</select>';
			$form .= '<input type="submit" value="Search">';
			$form .= '</form>';

			return $form;
		}

		function displayData($state) {
			$sql = "SELECT * FROM college_general WHERE state='" . $state . "' ORDER BY name";
			$result = mysqli_query($this->con, $sql);

			$data = "Colleges in " . $state . ":<br>";
			$data .= "<ul>";
			while($row = mysqli_fetch_array($result)) {
				$data .= "<li>" . $row['name'] . "</li>";
			}
			$data .= "</ul>";

			return $data;
		}
	}

	class enrollment_increase extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 25 Colleges by Enrollment Growth";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql  = "SELECT college_general.name, college_increases.enroll_increase ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_increases ON college_general.id = college_increases.id ";
			$sql .= "ORDER BY college_increases.enroll_increase DESC ";
			$sql .= "LIMIT 25";

			//echo $sql . "<br>";

			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>College Name</th>
					<th>Enrollment Increase Percent</th>
				</tr>';
			
			$array_row = 0;

			while($row = mysqli_fetch_array($result)) {
				
				$table .= '<tr>';
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['enroll_increase'] . '%</td>';
				$table .= '</tr>';
			}
			
			$table .= '</table>';
			return $table;
		}
	}

	class liabilities_increase extends page {
		function get() {
			$this->dbConnect();
			$this->content = $this->menu();
			$this->content .= "Top 25 Colleges by Liabilities Growth";
			$this->content .= $this->retrieveData();
		}

		function retrieveData() {
			$sql  = "SELECT college_general.name, college_increases.liability_increase ";
			$sql .= "FROM college_general ";
			$sql .= "JOIN college_increases ON college_general.id=college_increases.id ";
			$sql .= "ORDER BY college_increases.liability_increase DESC ";
			$sql .= "LIMIT 25";

			//echo $sql . "<br>";

			$result = mysqli_query($this->con, $sql);
			
			$table = '<table border="1">
				<tr>
					<th>College Name</th>
					<th>Liabilities Increase Percent</th>
				</tr>';
			
			$array_row = 0;

			while($row = mysqli_fetch_array($result)) {
				
				$table .= '<tr>';
				$table .= '<td>' . $row['name'] . '</td>';
				$table .= '<td>' . $row['liability_increase'] . '%</td>';
				$table .= '</tr>';
			}
			
			$table .= '</table>';
			return $table;
		}
	}

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
			if ($this->file_name == "hd2011.csv") {
				$this->file_fields = array("id", "name", "state");
			} else if (($this->file_name == "effy2010.csv") || ($this->file_name == "effy2011.csv")) {
				$this->file_fields = array("id", "student_level", "student_count", "year");
			} else if (($this->file_name == "f0910_f1a.csv") || ($this->file_name == "f1011_f1a.csv")) {
				$this->file_fields = array("id", "liabilities", "net_assets", "revenues", "prev_year", "current_year");
			}

			$this->row_count = $row;
			$this->file_array = $result;
		}

		// Builds the array used to insert information
		public function buildArray() {
			if ($this->file_name == "hd2011.csv") {
				$row = -1;
				if (($handle = fopen($this->file_name, "r")) !== FALSE) {
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$num = count($data);
						if ($row > -1) {			
							//echo "Row " . $row . ":<br>";
							for ($col=0; $col<$num; $col++) {
								if ($col == 0) {
									$result[$row][0] = $data[$col];
									//echo $data[$col] . " = " . $result[$row][0] . "<br />\n";
								} else if ($col == 1) {
									$result[$row][1] = $data[$col];
									//echo $data[$col] . " = " . $result[$row][1] . "<br />\n";
								} else if ($col == 4) {
									$result[$row][2] = $data[$col];
									//echo $data[$col] . " = " . $result[$row][2] . "<br />\n";
								}
							}

							//echo "<br>";
						}
						$row++;
					}
					
					fclose($handle);
				}
			} else if ($this->file_name == "effy2010.csv") {
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
			if ($this->file_name == "hd2011.csv") {
				$sql = "CREATE TABLE college_general (";
				$sql .= $this->file_fields[0] . " int, ";
				$sql .= $this->file_fields[1] . " varchar(255), ";
				$sql .= $this->file_fields[2] . " varchar(2)";
				$sql .= ");";
				
				//echo $sql . "<br>";
				
				if (mysqli_query($this->con,$sql)) {
					echo "Table college_general created successfully";
				} else {
					echo "Error creating database: " . mysqli_error($this->con);
				}
								
			} else if (($this->file_name == "effy2010.csv") || ($this->file_name == "effy2011.csv")) {
				$sql = "CREATE TABLE college_enrollment (";
				$sql .= $this->file_fields[0] . " int, ";
				$sql .= $this->file_fields[1] . " int, ";
				$sql .= $this->file_fields[2] . " int, ";
				$sql .= $this->file_fields[3] . " int";
				$sql .= ");";
				
				//echo $sql . "<br>";
				
				if (mysqli_query($this->con,$sql)) {
					echo "Table college_enrollment created successfully";
				} else {
					echo "Error creating database: " . mysqli_error($this->con);
				}
				
			} else if (($this->file_name == "f0910_f1a.csv") || ($this->file_name == "f1011_f1a.csv")) {
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
			if ($this->file_name == "hd2011.csv") {
				for ($x=0; $x < $this->row_count; $x++) {
					$sql = "INSERT INTO college_general VALUES (";
					$sql .= $this->file_array[$x][0] . ",";
					$sql .= "'" . $this->file_array[$x][1] . "',";
					$sql .= "'" . $this->file_array[$x][3] . "')";
					
					
					if (mysqli_query($this->con, $sql)) {
						echo "Successfully inserted values into college_general<br>";
					} else {
						echo "Error inserting values: " . mysqli_error($this->con) . "<br>";
					}
					
				}
			} else if (($this->file_name == "effy2010.csv") || ($this->file_name == "effy2011.csv")) {
				for ($x=0; $x < $this->row_count; $x++) {
					$sql = "INSERT INTO college_enrollment VALUES (";
					$sql .= $this->file_array[$x][0] . ",";
					$sql .= $this->file_array[$x][1] . ",";
					$sql .= $this->file_array[$x][2] . ",";
					$sql .= $this->file_array[$x][3] . ")";
					
					//echo $sql . "<br>";
					
					if (mysqli_query($this->con, $sql)) {
						echo "Successfully inserted values into college_enrollment<br>";
					} else {
						echo "Error inserting values: " . mysqli_error($this->con) . "<br>";
					}
					
				}			
			} else if (($this->file_name == "f0910_f1a.csv") || ($this->file_name == "f1011_f1a.csv")) {
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