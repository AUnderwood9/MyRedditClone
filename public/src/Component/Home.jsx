import React, { Component, Fragment } from "react";
import NavHeaderNoLogin from "./NavHeaderNoLogin";
import { checkHasLoginSession } from "../service/userService";
require('isomorphic-fetch');

class App extends Component{
	constructor(props){
		super(props)
		this.state = { };
	}

	componentDidMount(){
		checkHasLoginSession()
		.then((response) => {
			document.getElementById("insertionPoint").innerHTML = response.hasLoginSession;
		})
	}

	render (){
		return (
			<div>
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