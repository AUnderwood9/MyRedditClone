import React, { Component } from "react";
import { Redirect, Link } from 'react-router-dom';
require('isomorphic-fetch');

class NavHeaderNoLogin extends Component{
	constructor(props){
		super(props);

		this.onChange = this.onChange.bind(this);
		this.logout = this.logout.bind(this);

		this.state = { loggedOut: false };
	}

	onChange(e){
		this.setState({[e.target.name]: e.target.value});
	}

	render(){
		console.log("Rendering");
		if(this.state.loggedOut)
			return <Redirect to="/login" />;
		else
			return(
				<nav className="userNavBar">
					<span className="navAuthHeaderText" >Welcome!</span>
					<span>I'm just logged in!</span>
					<Link to={{pathname: "/logout"}} >Log me out</Link>
				</nav>
			);
	}
}

export default NavHeaderNoLogin;