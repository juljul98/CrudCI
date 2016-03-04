$(document).ready(function(){

	var base_url = $("body").data("url");

	$(".formUser").submit(function(e){

		e.preventDefault();

		$.ajax({
			type: 'post',
			url: base_url + 'main/saveUser',
			data: $(this).serialize(),
			success: function(data) {
				if(data.success == 1) {
					$(".successAdd").show();
					load_records();
					$('#formUser').trigger("reset");
				}else {
					$(".successAdd").hide();
				}
			}
		});	

	})

	$(".editFormUser").submit(function(e){

		e.preventDefault();

		$.ajax({
			type: 'post',
			url: base_url + 'main/saveEditUser',
			data: $(this).serialize(),
			success: function(data) {
				if(data.success == 1) {
					$(".editSuccess").show();
					load_records();

					setTimeout(function(){
						$(".editSuccess").hide();
						$("#myModal").modal("hide");
					}, 2000);

				}else {
					$(".successAdd").hide();
				}
			}
		});	

	})





	load_records();

	setInterval(function(){
		load_records();
	}, 3000);

	function load_records() {

		var search = $(".searchName").val();
		var course = $(".searchCourse").val();

		$.ajax({
			type: 'post',
			url: base_url + 'main/loadRecords',
			data: {'search':search, 'course':course},
			success: function(data) {
				if(data.success == 1) {

					var data = data.result;
					var noOfLoops = data.length;
					var html = "";

					for(x = 0; x < noOfLoops; x++) {
						html += "<tr>";
							html += "<td>"+data[x].fullname+"</td>";
							html += "<td>"+data[x].email+"</td>";
							html += "<td>"+data[x].course+"</td>";
							html += "<td><button type='button' class='btn btn-info btn-lg btn-xs btnEdit' data-toggle='modal' data-target='#myModal' data-id="+data[x].userID+">Edit</button>| Delete</td>";
						html += "</tr>";
					}

					$(".loadRecords").html(html);


				}
			}
		});	
	}

	$(".loadRecords").delegate(".btnEdit","click", function() {
		var id = $(this).data("id");
		// $(".hiddenEditID").val(id);

		$.ajax({
			type: 'post',
			url: base_url + 'main/getUserRow',
			data: {'id':id},
			success: function(data) {
				if(data.success == 1) {
					var data = data.result;
					$(".hiddenID").val(data[0].userID);
					$(".editfullname").val(data[0].fullname);
					$(".editemail").val(data[0].email);
					// $(".editcourse").selected(data[0].courseID);
					$('.editcourse option[value='+data[0].course+']').attr('selected','selected');
					
				}else {
					$(".successAdd").hide();
				}
			}
		});	

	});


	$(".searchName").keyup(function(){
		load_records();
	});

	$(".searchCourse").change(function() {
		load_records();
	})



});