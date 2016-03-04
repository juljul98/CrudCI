<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>">
 

</head>
<body data-url="<?php echo base_url(); ?>">

<div class="alert alert-success successAdd" style="display: none;">
  <strong>Success!</strong> Indicates a successful or positive action.
</div>

<form class="formUser" id="formUser">
	<p>
		<label>Fullname</label>
		<input type="text" name="fullname">
	</p>
	<p>
		<label>Email</label>
		<input type="text" name="email">
	</p>
	<select name="course">
		<option selected disabled>Select Course</option>
		<?php foreach($get_course->result() as $row)  { ?>
			<option value="<?php echo $row->courseID;?>"><?php echo $row->course; ?></option>
		<?php } ?>
	</select>

	<input type="submit" value="Save">
</form>
<br>
<input type="text" class="searchName" placeholder="Search fullname"> 

<select class="searchCourse">
	<option>All Courses</option>
	<?php foreach($get_course->result() as $row)  { ?>
		<option value="<?php echo $row->courseID;?>"><?php echo $row->course; ?></option>
	<?php } ?>
</select>

<table>
	<tr>
		<th>Fullname</th>
		<th>Email</th>
		<th>Course</th>
		<th>Action</th>
	</tr>

	<tbody class="loadRecords">

	</tbody>
</table>


<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
    	<div class="alert alert-success editSuccess" style="display: none;">
  			<strong>Success!</strong> Indicates a successful or positive action.
		</div>


      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        	<form class="editFormUser">
				<p>
					<label>Fullname</label>
					<input type="hidden" class="hiddenID" name="hiddenID">
					<input type="text" name="editfullname" class="editfullname">
				</p>
				<p>
					<label>Email</label>
					<input type="text" name="editemail" class="editemail">
				</p>
				<select name="editcourse" class="editcourse">
<!-- 					<option selected disabled>Select Course</option>
 -->					<?php foreach($get_course->result() as $row)  { ?>
						<option value="<?php echo $row->courseID;?>"><?php echo $row->course; ?></option>
					<?php } ?>
				</select>

				<input type="submit" value="Save">
			</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<script src="<?php echo base_url('assets/js/jquery-1.11.3.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>

</body>

</html>