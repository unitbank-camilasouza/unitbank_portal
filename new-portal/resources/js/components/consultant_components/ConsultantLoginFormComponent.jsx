import React, {Component} from "react";
import ReactDOM from 'react-dom';

class ConsultantLoginFormComponent extends Component {
    constructor(_props) {
        super(_props);
        this.props = _props;
    }

    render() {
        return (
            <div className="form-control">
                <form action={this.props.route_action} method="post">
                    <input type="hidden" name="_token" id="csrf" value={this.props.csrf}/>
                    <label htmlFor="cpf"> {this.props.login_label} </label>
                    <input type="text" name="cpf" id="cpf"
                           placeholder={this.props.placeholder_login} required autoFocus/>

                    <label htmlFor="password"> {this.props.password_label} </label>
                    <input type="text" name="password" id="password"
                           placeholder={this.props.placeholder_password} required/>

                    <input id="submit_button" type="submit" value={this.props.submit_value}
                           onClick={() => { /** TODO: put on click logic */ }}/>
                </form>
            </div>
        );
    }
}

if (document.getElementById('consultant-login-form')) {
    const element = document.getElementById('consultant-login-form');
    const props = element.dataset;
    ReactDOM.render(<ConsultantLoginFormComponent {...props}/>, element);
}
