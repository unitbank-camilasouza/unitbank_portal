// Author: Davi Mendes Pimentel
// last modified date: 11/12/2019

// imports all dependencies
import React, { Component } from 'react';
import ReactDOM from 'react-dom';

// component responsible by give the form front-end
class ConsultantRegisterComponent extends Component {
    constructor(props) {
        super(props);   // setup the properties by the super constructor

        this.props = props; // gets the properties passed from blade
    }

    render() {
        return (
            <form action={this.props.action_route}>
                <div>
                    My Register Consultant
                    <br/>

                    <input type="text" name="first_name" id="first_name" placeholder={this.props.placeholder_first_name}/>
                    <input type="text" name="last_name" id="last_name"/>
                    <input type="text" name="cpf" id="cpf"/>
                </div>
            </form>
        )
    }
}

export default ConsultantRegisterComponent; // export by default the function component

if(document.getElementById('register-consultant-form')) {
    const element = document.getElementById('register-consultant-form');
    const props = element.dataset;

    ReactDOM.render(<ConsultantRegisterComponent {...props} />, element)
}
