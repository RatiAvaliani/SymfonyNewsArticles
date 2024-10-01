class loader {
    static set () {
        let loader = document.createElement('img');
        loader.setAttribute('src', '/assets/images/icegif.gif');
        loader.classList.add('visually-hidden');
        loader.classList.add('loader');

        return {
            element: loader,
            toggle: () => {
                if (loader.classList.contains('visually-hidden')) loader.hide();
                else loader.show();
            },
            show: () => {
                loader.classList.remove('visually-hidden');
            },
            hide: () => {
                loader.classList.add('visually-hidden');
            }
        };
    };
}