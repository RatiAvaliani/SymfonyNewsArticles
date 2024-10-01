class User {
    role = 'PUBLIC_ACCESS';
    auth_status =null;
    username = null;
    email = null;
    static login_button = null;
    static logout_button = null;

    constructor(email, password, hide) {
        this.email = email;
        this.password = password;
        this.hide = hide;
    }

    static login_form () {
        if (typeof localStorage.email !== "undefined") {
            User.logout_button = render_user.button().logout(localStorage.email, () => User.logout());
            User.logout_button.show();
        } else {
            let form = render_user.form();
            User.login_button = render_user.button().login(form.show);
        }
    }

    static onLoad () {
        if (typeof CCOMMENTS !== "undefined" )
            CCOMMENTS.set_comments();
    }

    login(error, gif) {
        if (!this.email || !this.password) return;

        let login = new ajax('/app/login', "POST", {
            username: this.email,
            password: this.password
        });
        let authInfo = login.receive();
        gif.show();

        authInfo.then((info) => {
            gif.hide();

            if (info.code !== 200 && info.hasOwnProperty('message')) {
                error.innerText(info.message)
                error.show();
            }

            if (info.hasOwnProperty('token')) {
                this.hide();
                User.onLoad();

                User.login_button.hide();
                User.logout_button = render_user.button().logout(this.email, () => User.logout());
                User.logout_button.show();

                this.save(info.token, this.email, info.roles);
            }
        });
    }

    static logout () {
        localStorage.removeItem('token');
        localStorage.removeItem('email');
        localStorage.removeItem('roles');

        User.logout_button.hide();
        let form = render_user.form();
        User.login_button = render_user.button().login(form.show);
        User.onLoad();
    }

    save (token, email, roles) {
        localStorage.setItem('token', token);
        localStorage.setItem('email', email);
        localStorage.setItem('roles', roles);
    }
}

document.addEventListener("DOMContentLoaded", User.login_form);