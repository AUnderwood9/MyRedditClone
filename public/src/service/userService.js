require('isomorphic-fetch');
const baseUrl = "http://localhost:80/MyRedditClone/api/"

function checkHasLoginSession() {
	return fetch(baseUrl + "caster")
	.then((response) => {
		return response.json();
	})
	.then((resopnseResult) => {
		return resopnseResult;
	})
	.catch((err) => {
		console.log(err);
	});
}

function login(userName, password) {
    return fetch(baseUrl + "login", {
        method: 'POST',
        body: JSON.stringify({ userName, password }),
        headers: new Headers({
            'Content-Type': 'application/json'
        })
    })
    .then((response) => {
        if (response.ok) {
            return response.json()
            .then((jsonResponse) => {
				// loggedIn = true;
				console.log(jsonResponse);
				console.log("Logged in!");
            });
        } else if (response.status === 401) {
            return response.json()
            .then((jsonResponse) => {
                throw jsonResponse;
            });
        }
    });
}

export { login, checkHasLoginSession };