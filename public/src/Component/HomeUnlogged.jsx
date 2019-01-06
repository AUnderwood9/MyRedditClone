import React, { Component, Fragment } from "react";
import NavHeaderNoLogin from "./NavHeader/NavHeaderNoLogin";
import { checkHasLoginSession } from "../service/userService";
require('isomorphic-fetch');

class HomeUnloggedd extends Component{
	constructor(props){
		super(props)
		this.state = { hasLoginSession: false };
	}

	componentDidMount(){
		checkHasLoginSession()
		.then((response) => {
			this.setState( { hasLoginSession: response.hasLoginSession ? response.loggedInUser : null } );
		})
	}

	render (){
		const loginString = (this.state.hasLoginSession == null) ?  "sorry not there :(" : this.state.hasLoginSession;
		return (
			<div>
				<NavHeaderNoLogin />
					<h2>
						Hai, i'm paul!
					</h2>
					<p id="insertionPoint">
					Login?: 
					{ loginString }
					</p>
			</div>
		);
	}
}

export default HomeUnloggedd;