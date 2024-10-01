const render_user = {
    button:  () => {
        let collection = {
            base_button : (innerText) => {
                let button = document.createElement('button');
                button.classList.add("btn");
                button.classList.add("btn-outline-success");
                button.classList.add("login-button");
                button.type = "button";
                button.innerText = innerText;

                return button;
            },

            login : (callBack) => {
                let button = collection.base_button('Login');
                button.addEventListener("click", callBack);

                document.querySelector("#user_info_box").append(button);

                return {
                    toggle: () => {
                        if (button.classList.contains('visually-hidden')) button.hide();
                        else button.show();
                    },
                    show: () => {
                        document.querySelector('main').classList.add('blur_background');
                        button.classList.remove('visually-hidden');
                    },
                    hide: () => {
                        document.querySelector('main').classList.remove('blur_background');
                        button.classList.add('visually-hidden');
                    }
                };
            },

            logout :  (user_name, callBack) => {
                let div = document.createElement('div');

                let span = document.createElement('span');
                span.innerText = user_name;

                let button = collection.base_button('Logout');
                button.addEventListener("click", callBack);

                div.classList.add('visually-hidden');
                div.classList.add('logout');
                div.append(span);
                div.append(button);

                document.querySelector("#user_info_box").append(div);

                return {
                    toggle: () => {
                        if (div.classList.contains('visually-hidden')) div.hide();
                        else div.show();
                    },
                    show: () => {
                        div.classList.remove('visually-hidden');
                    },
                    hide: () => {
                        div.classList.add('visually-hidden');
                    }
                };
            }
        };

        return collection;
    },

    form: () => {
        let main_element;
        let collection = {
            validate: {
                email: null,
                password: null,
                status: {}
            },
            email: null,
            password: null,
            toggle: () => {
                if (main_element.classList.contains('visually-hidden')) collection.hide();
                else collection.show();
            },
            show: () => {
                document.querySelector('main').classList.add('blur_background');
                main_element.classList.remove('visually-hidden');
            },
            hide: () => {
                document.querySelector('main').classList.remove('blur_background');
                main_element.classList.add('visually-hidden');
            }
        };

        let crate_main_element = (children) => {
            let div = document.createElement('div');
            div.classList.add('login_form');
            div.classList.add('visually-hidden');
            div.append(children);

            main_element = div;
            document.querySelector('body').append(div);
        };

        let crate_input = (innerText, type, validate) => {

            let div = document.createElement('div');
            div.classList.add('mb-3');

            let label = document.createElement('label');
            label.classList.add('form-label');
            label.setAttribute('for', 'password');
            label.innerText = innerText;

            let input = document.createElement('input');
            input.setAttribute('type', type);
            input.classList.add('form-control');

            validate(() => {
                if (input.value.trim().length == 0 || (type === 'email' && !input.value.trim().includes('@'))) {
                    collection.validate.status = false;
                    collection[type] = null;
                    input.classList.add('error_border');
                } else {
                    collection.validate.status = true;
                    collection[type] = input.value.trim();
                    input.classList.remove('error_border');
                }
            });

            div.append(label);
            div.append(input);

            return div;
        };

        let crate_error_span = (message="") => {
            let span = document.createElement('span');
            span.classList.add('error');

            span.innerText = message;

            return {
                element: span,
                innerText: (innerText) => {
                    span.innerText = innerText;
                },
                toggle: () => {
                    if (span.classList.contains('visually-hidden')) collection.hide();
                    else collection.show();
                },
                show: () => {
                    span.classList.remove('visually-hidden');
                },
                hide: () => {
                    span.classList.add('visually-hidden');
                }
            };
        };

        let create_close_span = (callback) => {
            let div = document.createElement('div');
            div.classList.add('mb-3');
            div.classList.add('close');

            let span = document.createElement('span');
            span.innerText = 'x';
            span.addEventListener('click', callback);

            div.append(span);

            return div;
        };

        let crate_title = (innerText='login') => {
            let h4 = document.createElement('h4');
            h4.innerText = innerText;

            return h4;
        };

        let login_form = () => {
            let form = document.createElement('form');

            let email = crate_input('Email', 'email',  validator => collection.validate.email = validator);
            let password = crate_input('Password', 'password', validator => collection.validate.password = validator);

            let button = render_user.button().base_button('Login');

            let error_span = crate_error_span();
            let gif = loader.set();

            form.append(create_close_span(collection.hide));
            button.addEventListener('click', () => {
                collection.validate.email();
                collection.validate.password();

                if (collection.validate.status) (new User(collection.email, collection.password, collection.hide)).login(error_span, gif);
            });

            form.append(crate_title());
            form.append(email);
            form.append(password);
            form.append(button);
            form.append(gif.element);
            form.append(error_span.element);

            return form;
        };

        if (typeof main_element === 'undefined') crate_main_element(login_form());

        return collection;
    }
}