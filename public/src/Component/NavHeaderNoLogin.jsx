import React, { Component } from "react";
import AuthButton from "./Auth/authButton";
import * as userService from "./../service/userService";
require('isomorphic-fetch');

class NavHeaderNoLogin extends Component{
	constructor(props){
		super(props);

		this.onChange = this.onChange.bind(this);

		this.state = { currentUser : "", loginUserName: "",  loginUserPassword: "" };
	}

	login(e) {
        e.preventDefault();
        userService.login(this.state.loginUserName, this.state.loginUserPassword)
        .then(() => {
            this.setState({ redirectToReferrer: true });
        }).catch((err) => {
            if (err.message) {
                this.setState({ feedbackMessage: err.message });
            }
        });
    }

	onChange(e){
		this.setState({[e.target.name]: e.target.value});
	}

	render(){
		return(
			<nav className="userNavBar">
			<span className="navAuthHeaderText" >Welcome!</span>
				<form onSubmit={(e) => this.login(e)}>
					<label htmlFor="login-user-name">UserName: </label>
					<input type="text" name="loginUserName" id="login-user-name" className="navAuthInput" onChange={this.onChange}/>
					<label htmlFor="login-user-password">Password: </label>
					<input type="password" name="loginUserPassword" id="login-user-password" className="navAuthInput" onChange={this.onChange}/>
					<input type="submit" value="Log me in Scotty!"/>
				</form>
			</nav>
		);
	}
}

export default NavHeaderNoLogin;