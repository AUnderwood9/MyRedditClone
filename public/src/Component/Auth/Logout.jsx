import React, { Component, Fragment } from 'react';
import * as userService from "../../service/userService";
import { Redirect } from 'react-router-dom';

class Logout extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loggedOut: false
        };
    }

    componentDidMount() {
        userService.logOutUser();
        this.setState({ loggedOut: true });
    }

    render() {
        if (this.state.loggedOut) {
            return <Redirect to="/login" />;
        } else {
			return <p>Waiting...</p>
		}
    }
}

export default Logout;