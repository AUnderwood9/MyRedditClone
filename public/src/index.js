import React from "react";
import ReactDOM from "react-dom";
import "./style/reset.css";
import "./style/fontStyle.scss"
import "./style/style.scss";
import HomeMainRoutes from "./Component/HomeMainRoutes";

// fetch("http://localhost/MyRedditClone/api/caster/SoopaTroopa").then(function (response) {
// 	// console.log(response);
// 	return response.json();
// }).then(function (responseObject){
// 	document.getElementById("insertionPoint").innerHTML = `<p> ${responseObject["userName"]} HIT ME@ ${responseObject["email"]}</p>`;
// });

let rootElement = document.getElementById("root");

ReactDOM.render(<HomeMainRoutes/>, rootElement);