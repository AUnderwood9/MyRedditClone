import React, { Component, Fragment } from "react";
import Header from "./Header";
require('isomorphic-fetch');

class App extends Component{
	constructor(props){
		super(props)
		this.state = { };
	}

	// componentDidMount(){
	// 	fetch("http://localhost/MyRedditClone/api/caster/SoopaTroopa").then(function (response) {
	// 		return response.json();
	// 	}).then(function (responseObject){
	// 		document.getElementById("insertionPoint").innerHTML = `<p> ${responseObject.userName} HIT ME@ ${responseObject.email}</p>`;
	// 	});
	// }


	render (){
		return (
			<div className="componentMainContainer">
				<Header/>
					<h2>
						Hai, i'm paul!
					</h2>
					<p id="insertionPoint">
					</p>
			</div>
		);
	}
}

export default App;