import React from 'react';
import { Link } from 'react-router-dom';
import { checkHasLoginSession } from '../../service/userService';
import LoginButton from "./Login";
import LogoutButton from "./Logout";
// import style from './authButton.module.scss';

const AuthButton = (props) => {
    if (checkHasLoginSession().hasLoginSession) {
		return <LogoutButton />;
		// return <Link className="btn btn-info" to="/logout">Logout</Link>;
    } else {
		return <LoginButton />;
		// return <Link className="btn btn-info" to="/login">Login</Link>;
    }
};

export default AuthButton;