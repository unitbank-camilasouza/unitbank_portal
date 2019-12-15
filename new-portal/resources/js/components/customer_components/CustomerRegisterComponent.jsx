import React from 'react';
import ReactDOM from 'react-dom';

function CustomerRegisterComponent() {
    return (
        <div> My Customer Register </div>
    );
}

export default CustomerRegisterComponent;

if(document.getElementById("customer-register-div"))
    ReactDOM.render(<CustomerRegisterComponent />, document.getElementById("customer-register-div"));
