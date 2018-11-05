require('isomorphic-fetch');
const baseUrl = "http://localhost/MyRedditClone/api/caster/"

function checkHasLoginSession() {
	return fetch(baseUrl + "hasLoginSession")
	.then((response) => {
		return response.json();
	})
	.catch((err) => {
		console.log(err);
	});
}

export { checkHasLoginSession };