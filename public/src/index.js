fetch("http://localhost:80/MyRedditClone/api/caster/SoopaTroopa").then(function (response) {
	// console.log(response.json());
	return response.json();
}).then(function (responseObject){
	document.getElementById("insertionPoint").innerHTML = `<p> ${responseObject["userName"]} HIT ME@ ${responseObject["email"]}</p>`;
});