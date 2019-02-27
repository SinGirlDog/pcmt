function doCollect(type) {
	var urlP = document.getElementById("url").value;
	if(type == 'start') {
		if(urlP.length == 0) {
			alert('请输入网址');
			return false;
		}
		ajax({
			type: "POST",
			url: "new_file.php",
			dataType: "json",
			data: {
				"url": urlP,
				"start": '1',
				"stop": '0'
			},
			beforeSend: function() {
				window.location = 'new_file.php?status=2';
			},
			success: function(msg) {
				console.log(msg,253);
			},
			error: function() {
				console.log("error")
			}
		});
	} else if(type == 'stop') {
		ajax({
			type: "POST",
			url: "new_file.php",
			dataType: "json",
			data: {
				"url": urlP,
				"start": '0',
				"stop": '1'
			},
			beforeSend: function() {},
			success: function(msg) {
				window.location.reload();
			},
			error: function() {
				console.log("error")
			}
		});
	}
}

function ajax() {
	var ajaxData = {
		type: arguments[0].type || "GET",
		url: arguments[0].url || "",
		async: arguments[0].async || "true",
		data: arguments[0].data || null,
		dataType: arguments[0].dataType || "text",
		contentType: arguments[0].contentType || "application/x-www-form-urlencoded",
		beforeSend: arguments[0].beforeSend || function() {},
		success: arguments[0].success || function() {},
		error: arguments[0].error || function() {}
	}
	ajaxData.beforeSend()
	var xhr = createxmlHttpRequest();
	xhr.responseType = ajaxData.dataType;
	xhr.open(ajaxData.type, ajaxData.url, ajaxData.async);
	xhr.setRequestHeader("Content-Type", ajaxData.contentType);
	xhr.send(convertData(ajaxData.data));
	xhr.onreadystatechange = function() {
		if(xhr.readyState == 4) {
			if(xhr.status == 200) {
				ajaxData.success(xhr.response)
			} else {
				ajaxData.error()
			}
		}
	}
}

function createxmlHttpRequest() {
	if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else if(window.XMLHttpRequest) {
		return new XMLHttpRequest();
	}
}

function convertData(data) {
	if(typeof data === 'object') {
		var convertResult = "";
		for(var c in data) {
			convertResult += c + "=" + data[c] + "&";
		}
		convertResult = convertResult.substring(0, convertResult.length - 1)
		return convertResult;
	} else {
		return data;
	}
}