import React, { Component, Fragment } from "react";
import { BrowserRouter as Router, Route, Switch, Link } from 'react-router-dom';
import PrivateRoute from "./privateRoute";
import NavHeaderNoLogin from "./NavHeader/NavHeaderNoLogin";
import Home from "./Home";
import HomeUnlogged from "./HomeUnlogged";
import Logout from "./Auth/Logout";
require('isomorphic-fetch');

class App extends Component{
	constructor(props){
		super(props)
		this.state = { };
	}

	render (){
		return (
			<Router>
				<div className="componentMainContainer">
					<Switch>
						<PrivateRoute exact path="/" component={Home} />
						<Route path="/login" component={HomeUnlogged}/>
						<Route path="/logout" component={Logout}/>
					</Switch>
				</div>
			</Router>
		);
	}
}

export default App;