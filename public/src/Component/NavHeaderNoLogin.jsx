import React, { Component } from "react";
require('isomorphic-fetch');

class NavHeaderNoLogin extends Component{
	constructor(props){
		super(props);

		this.state = { currentUser : "" };
	}

	render(){
		return(
			<nav className="userNavBar">
			<span className="navAuthHeaderText" >Welcome!</span>
				<input type="text" name="loginUserName" id="login-user-name" className="navAuthInput"/>
				<input type="password" name="loginUserPassword" id="login-user-password" className="navAuthInput"/>
			</nav>
		);
	}
}

export default NavHeaderNoLogin;