import LoginController from './LoginController'
import RegisteredUserController from './RegisteredUserController'


const Auth = {
    LoginController: Object.assign(LoginController, LoginController),
    RegisteredUserController: Object.assign(RegisteredUserController, RegisteredUserController),
}

export default Auth