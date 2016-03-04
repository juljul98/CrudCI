<?php 
	
	class Model extends CI_Model {

		public function get_course() {
			$sql = "SELECT * FROM tbl_course";
			$query = $this->db->query($sql);
			return $query;
		}

		public function get_saveUser($fullname, $email, $course) {

			$sql = "INSERT INTO tbl_user(`fullname`,`email`,`courseID`,`dateAdded`)VALUES(?,?,?,?)";
			$data = array($fullname, $email, $course, today());
			$this->db->query($sql,$data);
			return $this->db->affected_rows();
		}

		public function get_loadRecords($search = '', $course = 0) {

			$sql = "SELECT * FROM tbl_user u , tbl_course c WHERE u.courseID = c.courseID ";

			if($search != '') {
				$sql .= "AND u.fullname LIKE '%$search%'";
			}

			if($course != 0) {
				$sql .= "AND u.courseID = '$course'";
			}



			$query = $this->db->query($sql);
			return $query;

		}

		public function get_getUserRow($id) {
			$sql = "SELECT * FROM tbl_user u , tbl_course c WHERE u.courseID = c.courseID AND u.userID = ?";
			$data = array($id);
			$query = $this->db->query($sql, $data);
			return $query;

		}

		public function get_saveEditUser($fullname, $email, $course, $id) {
			$sql = "UPDATE tbl_user set fullname = ?, email = ?, courseID = ? WHERE userID = ?";
			$data = array($fullname, $email, $course, $id);
			$this->db->query($sql,$data);
			return $this->db->affected_rows();
		}



		
	}