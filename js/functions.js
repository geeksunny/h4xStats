/**
 * Javascript functions
 **/

function inputClick(elem, val)
{
	if (elem.value == val)
	{
		elem.value = "";
		elem.style.color = "#000";
	}
}
function inputBlur(elem, val)
{
	if (elem.value == "")
	{
		elem.value = val;
		elem.style.color = "#ccc";
	}
}

/* jQuery ajax code */
$(function() {	// -Acts the same as  if it were  waiting for "document.ready"
 	$(".error").hide();	// Hides error message div's on document.ready
	$(".button#add_url").click(function() {
		// validate and processs form here.

		$(".error").hide();	// re-hides all error messages upon validation attempt, in case any were showing at the time of validation.
		var url = $("input#url_box").val();
		if (url == "" || url == "Enter a URL...") {	// if the URL field is blank, error out.
			$("label#url_error").text("You must enter a URL.").fadeIn(800);
			//$("label#url_error").show();
			$("input#url_box").focus();
			return false;
		}
		// elseif... check to make sure any data entered has "http://"?

		var dataString = "method=add&url="+url;
		//alert(dataString); return false; //debug
		$.ajax({
			type: "POST",
			url: "ajax.url.php",
			data: dataString,
			success: function(data) {
				response = $.parseJSON(data);
				//alert(response.result+" - "+response.string);//debug

				// If successful, will receive a table row to insert into the table below
				switch(response.result)
				{
					case "success":
						// Build the new table row.
						row = '<tr><td>' + response.url + '</td><td id="'+response.id+'"><img id="'+response.string+'" src="img/link.png" class="link" /><img src="img/stats.png" class="stats" /><img src="img/pause.png" class="toggle" /><img src="img/delete.png" class="delete" /></td></tr>';

						// if the table listing is hidden, display it.
						if ($("#links_table").is(":hidden"))
							$("#links_table").show();

						if ($("#links_listing").children().first().val() != null)		// if there is a child element in the list, insert the row before the first child.
							$("#links_listing").children().first().before(row);
						else							// If list is empty, append the row.
							$("#links_listing").append(row);
						// Hide the new row and fade it into view.
						$("#links_listing").children().first().hide().fadeIn(800);
						// Reset the input box
						$("#url_box").val("").focus();
					break;
					case "error":
						$("label#url_error").text("An error has occurred: \"" + response.message + "\".").fadeIn(800);
					break;
					default:
						$("label#url_error").text("An error has occurred: \"Invalid response from server\".").fadeIn(800);
					break;
				}
			},
			error: function() {
				alert("AJAX ERROR RESPONSE");
			}
		});
		return false;
	});

	$(".button#login").click(function() {
		// validate and processs form here.

		$(".error").hide();	// re-hides all error messages upon validation attempt, in case any were showing at the time of validation.
		var username = $("input#username").val();
		var password = $("input#password").val();
		if (username == "" || username == "Username" || password == "" || password == "Password") {	// if the URL field is blank, error out.
			$("label#url_error").text("You must enter a username and password.").fadeIn(800);
			//$("label#url_error").show();
			$("input#ur_box").focus();
			return false;
		}

		var dataString = "method=login&username="+username+"&password="+password;
		//alert(dataString); return false; //debug
		$.ajax({
			type: "POST",
			url: "ajax.login.php",
			data: dataString,
			success: function(data) {
				response = $.parseJSON(data);
				//alert(response.result+" - "+response.string);//debug

				// If successful, will receive a table row to insert into the table below
				switch(response.result)
				{
					case "success":
						// Redirect the user to the given page.
						window.location = response.redirect;
					break;
					case "error":
						$("label#url_error").text("An error has occurred: \"" + response.message + "\".").fadeIn(800);
					break;
					default:
						$("label#url_error").text("An error has occurred: \"Invalid response from server\".").fadeIn(800);
					break;
				}
			},
			error: function() {
				alert("AJAX ERROR RESPONSE");
			}
		});
		return false;
	});

	$(".toggle").click(function() {
		// validate and processs form here.

		// TODO: make a new error message spot for toggle/delete buttons
		$(".error").hide();	// re-hides all error messages upon validation attempt, in case any were showing at the time of validation.

		// Grab the element handler for deeper function use.
		selector = $(this);

		var id = selector.parent().attr("id");	// Grabs the ID of the link
		var dataString = "method=toggle&id=" + id;

		$.ajax({
			type: "POST",
			url: "ajax.toggle.php",
			data: dataString,
			success: function(data) {
				//alert(data); return false;	//debug
				response = $.parseJSON(data);
				//alert(response.result+" - "+response.string);//debug

				// If successful, will receive a table row to insert into the table below
				switch(response.result)
				{
					case "success":
						// Change the icon based on the result.
						if (response.state == "1")
						{
							newImg = "pause.png";
							selector.parent().parent().removeClass("paused");
						}
						else
						{
							newImg = "play.png";
							selector.parent().parent().addClass("paused");
						}
						selector.attr("src","img/" + newImg);
					break;
					case "error":
						$("label#url_error").text("An error has occurred: \"" + response.message + "\".").fadeIn(800);
					break;
					default:
						$("label#url_error").text("An error has occurred: \"Invalid response from server\".").fadeIn(800);
					break;
				}
			},
			error: function() {
				alert("AJAX ERROR RESPONSE");
			}
		});
		return false;
	});

	$(".delete").click(function() {
		// validate and processs form here.

		// Grab the element handler for deeper function use.
		selector = $(this);

		// Calls the modal confirm box, runs our AJAX code on "YES".
		confirm("Are you sure you want to delete this link?", function () {
			// TODO: make a new error message spot for toggle/delete buttons
			$(".error").hide();	// re-hides all error messages upon validation attempt, in case any were showing at the time of validation.

			var id = selector.parent().attr("id");	// Grabs the ID of the link
			var dataString = "method=delete&id=" + id;

			$.ajax({
				type: "POST",
				url: "ajax.toggle.php",
				data: dataString,
				success: function(data) {
					//alert(data); return false;	//debug
					response = $.parseJSON(data);
					//alert(response.result+" - "+response.string);//debug

					// If successful, will receive a table row to insert into the table below
					switch(response.result)
					{
						case "success":
							// Fades out the row and then removes it from the oage.
							row = selector.parent().parent();	// Grabs the row (parent of the parent of the image being clicked.)
							selector.parent().parent().fadeOut( "500", function(){$(this).remove();} );	// Fades out and then runs .remove() on the row afterwards.
						break;
						case "error":
							$("label#url_error").text("An error has occurred: \"" + response.message + "\".").fadeIn(800);
						break;
						default:
							$("label#url_error").text("An error has occurred: \"Invalid response from server\".").fadeIn(800);
						break;
					}
				},
				error: function() {
					alert("AJAX ERROR RESPONSE");
				}
			});
		});
		return false;
	});

    $(".stats").click(function(){
		var id = $(this).parent().attr("id");	// Grabs the ID of the link
		$.fancybox({
	            'width': '85%',
	            'height': '85%',
				'padding': 0,
				'centerOnScroll': true,
	            'autoScale': true,
				'onComplete': function () { $("body").css("overflow", "hidden"); $("html").css("overflow", "hidden"); },
				'onClosed': function () { $("body").css("overflow", "auto"); $("html").css("overflow", "auto"); },
				'scrolling': 'auto',
	            'transitionIn': 'fade',
	            'transitionOut': 'fade',
	            'type': 'iframe',
				'href': 'stats.php?id=' + id
	        });
   		return false;
   	});

    $(".link").click(function(){
		var new_url = $("#url_root").text()+ $(this).attr("id");
		$('#modal-link-value').attr('value',new_url);
		$('#modal-link-content').modal({
			overlayId: 'link-overlay',
			overlayClose: true,
			containerId: 'link-container'
		});
		$('#modal-link-value').select();	// Highlight the URL to be easily copied.
		return false;
   	});

	// Hides error message on click.
	$(".error").click(function() {
		$(".error").hide();
	});
});