import React, { Component } from "react";
import { checkHasSession } from "../service/userService";
require('isomorphic-fetch');

class Header extends Component{
	constructor(props){
		super(props);

		this.state = { currentUser : "" };
	}

	componentDidMount(){
		// fetch("http://localhost/MyRedditClone/api/hasLoginSession")
		// .then((response) => {
		// 	// console.log(response);
		// 	return response.json();
		// }).then((responseData) => {
		// 	console.log(responseData);
		// })

		// checkHasSession();

		fetch("http://localhost/MyRedditClone/api/caster")
			.then((response) => {
				// console.log(response);
				return response.json();
			}).then((responseData) => {
				// this.setState({
				// 	currentUser : 
				// })
				console.log(responseData);
			})
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

export default Header;