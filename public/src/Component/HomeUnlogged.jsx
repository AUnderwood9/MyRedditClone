import React, { Component, Fragment } from "react";
import NavHeaderNoLogin from "./NavHeaderNoLogin";
require('isomorphic-fetch');

class HomeUnloggedd extends Component{
	constructor(props){
		super(props)
		this.state = { };
	}

	componentDidMount(){
		console.log("Not Logged In!");
	}

	render (){
		return (
			<div>
				<NavHeaderNoLogin />
					<h2>
						Hai, i'm paul!
					</h2>
					<p id="insertionPoint">
					</p>
			</div>
		);
	}
}

export default HomeUnloggedd;