require('isomorphic-fetch');
const baseUrl = "http://localhost:80/MyRedditClone/api/";

let userLoggedIn = false;

function isUserLoggedIn (){ return userLoggedIn; }

function checkHasLoginSession() {
	return fetch(baseUrl + "caster")
	.then((response) => {
		userLoggedIn = true;
		return Promise.resolve(true);
	})
	.catch((err) => {
		console.log(err);
		userLoggedIn = false;
		return Promise.resolve(false);
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
				userLoggedIn = true;
				console.log(jsonResponse);
				console.log("Logged in!");
            });
        } else if (!response.ok || response.status === 401) {
			userLoggedIn = false;
            return response.json()
            .then((jsonResponse) => {
                throw jsonResponse;
            });
        }
    });
}

function logOutUser() {
	console.log("Logging out");
	fetch(baseUrl + "logout", { method: "POST" })
	.then((response) => {
		console.log("Should be logged out now.");
		if (response.ok) {
			console.log("Logged Out!");
			userLoggedIn = false;
			return Promise.resolve(true);
		} else if(!response.ok || response.status === 401) {
			console.log("Still logged in");
			userLoggedIn = false;
			return Promise.resolve(false);
		}
	}).catch((err) => {
		console.log(err);
		userLoggedIn = false;
		return Promise.resolve(false);
	})
}

export { login, checkHasLoginSession, isUserLoggedIn, logOutUser };