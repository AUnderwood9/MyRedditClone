import React from 'react';
import { Route, Redirect } from 'react-router-dom';
// import { isLoggedIn } from '../../services/user';
import { checkHasLoginSession, isUserLoggedIn } from "../service/userService";

const PrivateRoute = (props) => {
	// console.log(props);
    const Component = props.component;
    const propsToPass = Object.assign({}, props);
    delete propsToPass.component;

	// console.log("logged in?");
	// console.log(checkHasLoginSession());
	// checkHasLoginSession().then((result) => {
	// 	console.log(result);
	// })
	// console.log(checkHasLoginSession());
	
    return (
        <Route {...propsToPass} render={props => (
            isUserLoggedIn() ? (
                <Component {...props} />
            ) : (
                <Redirect to={{
                    pathname: '/login',
                    state: { from: props.location }
                }} />
            )
        )} />
    );
};

export default PrivateRoute;