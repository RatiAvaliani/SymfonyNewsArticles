class Comments {
    static loader;
    main_element_selector = '#comments';
    main_comments_list_element = "#comments-list";
    main_element;
    textarea;
    loader;

    constructor (loader) {
        this.get_comments_element();
        this.textarea_element();
        this.set_comments();
    }

    set_comments () {
        this.loader = loader.set();

        let div = document.createElement('div');
        div.classList.add('comments-loader');
        div.append(this.loader.element);
        this.textarea.value = "";

        let comments = new ajax('/get/comment', "POST", {
            'article' : this.get_current_article_id()
        });
        this.main_element.append(div);
        this.loader.show();

        let authInfo = comments.receive();
        authInfo.then((info) => {

            this.loader.hide();
            document.querySelector(this.main_comments_list_element).innerHTML = '';

            if (info.code !== 200 && info.hasOwnProperty('message')) { console.log('no comments') }
            info.Comments.forEach((comment) => {

                let insertDate = new Date(Date.parse(comment.insertDate));
                let hour = insertDate.getHours().toString();
                hour = hour.length === 1 ? '0' + hour : hour;
                let minutes = insertDate.getMinutes().toString();
                minutes = minutes.length === 1 ? '0' + minutes : minutes;

                let year = insertDate.getFullYear();
                let month = insertDate.getMonth();
                let day = insertDate.getDate();

                insertDate = hour + ':' + minutes + ' ' + year + '/' + month + '/' + day;
                this.list_element(comment.comment, comment.email, insertDate, comment.id);
            });
        });
    }

    get_comments_element () {
        this.main_element = document.querySelector(this.main_element_selector);
    }

    login_error () {
        Swal.fire({
            icon: "error",
            title: "Login!",
            text: "You need to login to add comments!",
            confirmButtonText: "Login"
        }).then((result) => {
            if (result.isConfirmed) {
                render_user.form().show();
            }
        });
    }

    get_comment (textarea) {
        let comment = textarea.value.trim();

        if (comment.length === 0) {
            textarea.classList.add('error_border');
            return null;
        } else {
            textarea.classList.remove('error_border');
        }

        return comment;
    }

    get_current_article_id () {
        let url = window.location.href.split('/');

        return url[4];
    }

    add (textarea) {
        let comment = this.get_comment(textarea);

        if (comment) {
            let save_comment = new ajax('/api/add/comment', 'POST', {
                comment: comment,
                articleId: this.get_current_article_id()
            });
            save_comment.receive().then((info) => {
                if (info.Status === true){
                    this.set_comments();
                }
            });
        }
    }

    remove_comment (id) {
        let save_comment = new ajax('/api/remove/comment', 'POST', {
            comment: id
        });
        save_comment.receive().then((info) => {
            if (info.Status === true){
                this.set_comments();
            }
        });
    }

    list_element (content, email, datetime, id) {
        let div_main = document.createElement('div');
        div_main.classList.add('col-lg-12');

        let div = document.createElement('div');
        div.classList.add('comment');

        let span = document.createElement('div');
        span.classList.add('user');
        span.classList.add('d-inline');
        span.innerText = email + '  ' + datetime;

        if (localStorage.getItem('roles') !== null) {
            let roles = localStorage.getItem('roles');
            if (roles.includes('ROLE_ADMIN')) {
                let button = document.createElement('button');
                button.classList.add('btn');
                button.classList.add('float-end');
                button.classList.add('btn-danger');
                button.innerText = 'Remove Comment';

                button.addEventListener('click', () => {
                   this.remove_comment(id);
                });

                div.append(button);
            }
        }

        let p = document.createElement('p');
        p.classList.add('lh-base');
        p.innerText = content;

        div.append(p);
        div.append(span);
        div_main.append(div);

        document.querySelector(this.main_comments_list_element).append(div);
    }

    textarea_element () {
        let div_main = document.createElement('div');
        div_main.classList.add('col-lg-12');

        let div = document.createElement('div');
        div.classList.add('form-floating');
        div_main.append(div);

        let textarea = document.createElement('textarea');
        textarea.classList.add('form-control');
        textarea.style.height = '100';

        this.textarea = textarea;
        div.append(textarea);

        let label = document.createElement('label');
        label.innerText = 'Add Comment';

        let button = document.createElement('button');
        button.classList.add('btn');
        button.classList.add('btn-success');
        button.classList.add('mt-2');
        button.type = 'button';
        button.innerText = 'Comment';

        button.addEventListener('click', () => {
            if (typeof localStorage.email === "undefined") this.login_error();
            else this.add(textarea);
        });

        div.append(button);
        div.append(label);

        this.main_element.append(div);
    }

}

let CCOMMENTS;
document.addEventListener("DOMContentLoaded", () =>  {
    CCOMMENTS = new Comments();
});