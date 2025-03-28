// source --> http://10.180.0.219/wp-content/plugins/wp-data-access/public/../assets/js/wpda_rest_api.js?ver=5.3.5 
function wpda_rest_api(path, data, callbackOk, callbackError, method = "POST") {
	jQuery.ajax({
		url: wpApiSettings.root + wpdaApiSettings.path + "/" + path,
		method: method,
		beforeSend: function (xhr) {
			xhr.setRequestHeader("X-WP-Nonce", wpApiSettings.nonce);
		},
		data: data
	}).done(function(response) {
		if (callbackOk!==undefined) {
			callbackOk(response)
		} else {
			console.error("Missing API callback. Server response:")
			console.error(response)
		}
	}).fail(function(response) {
		if (callbackError!==undefined) {
			callbackError(response)
		} else {
			console.error("Missing API callback. Server response:")
			console.error(response)
		}
	})
};