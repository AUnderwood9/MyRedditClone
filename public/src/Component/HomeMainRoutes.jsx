import React, { Component, Fragment } from "react";
import { BrowserRouter as Router, Route, Switch, Link } from 'react-router-dom';
import PrivateRoute from "./privateRoute";
import NavHeaderNoLogin from "./NavHeaderNoLogin";
import Home from "./Home";
import HomeUnlogged from "./HomeUnlogged";
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
			<Router>
				<div className="componentMainContainer">
					<Switch>
						<PrivateRoute exact path="/" component={Home} />
						<Route path="/login" component={HomeUnlogged}/>
					</Switch>
				</div>
			</Router>
		);
	}
}

export default App;