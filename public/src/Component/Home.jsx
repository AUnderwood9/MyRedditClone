import React, { Component, Fragment } from "react";
import NavHeaderNoLogin from "./NavHeader/NavHeaderNoLogin";
import NavHeaderLogin from "./NavHeader/NavHeaderLogin";
import { checkHasLoginSession } from "../service/userService";
require('isomorphic-fetch');

class App extends Component{
	constructor(props){
		super(props)
		this.state = { hasLoginSession: false };
	}

	componentDidMount(){
		checkHasLoginSession()
		.then((response) => {
			console.log("My response");
			console.log(response);
			this.setState( { response: response.hasLoginSession ? response.loggedInUser : null } );
		})
	}

	render (){
		return (
			<Fragment>
				<NavHeaderLogin />
				<div>
					<h2>
						Hai, i'm { this.state.hasLoginSession }!
					</h2>
				</div>
			</Fragment>
		);
	}
}

export default App;