window.onload = function() {
	var div = document.getElementById("mydiv");
	if(div) {
		var auto = setInterval(function() {
			changeH();
		}, 100);
	}

	function changeH() {
		var div = document.getElementById("mydiv");
		console.log(div,343);
		if(div.innerHTML != '' || div.innerHTML != null) {
			if(div.getElementsByTagName("p").length > 0) {
				var firstTitle = div.getElementsByTagName('p')[0].innerHTML;
				var id = '';

			} else {
				var firstTitle = 'noPmark';
				var id = document.getElementById("lastId").value;
			}
		}
		ajax({
			type: "POST",
			url: "ajax.php",
			dataType: "json",
			data: {
				"title": firstTitle,
				"id": id
			},
			beforeSend: function() {
			},
			success: function(msg) {
				console.log(msg,365);
				if(msg.html) {
					document.getElementById("mydiv").innerHTML = msg.html + document.getElementById("mydiv").innerHTML;
				}
				console.log(msg,369);
				if(msg.end == '1') {
					window.clearTimeout(auto);
						// document.getElementById('num-box').innerHTML = document.getElementById('num-box').innerHTML + '(采集完成)';
						document.getElementById('button').innerHTML = "<input type='button' name='start' value='开&始' onclick=doCollect('start'); />";
					}
				},
				error: function() {
					console.log("error")
				}
			});
	}
}